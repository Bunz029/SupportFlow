<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Sla;
use App\Models\Ticket;
use App\Models\TicketSla;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\RoleHelper;
use App\Models\Comment;


class DashboardController extends Controller
{
    /**
     * Show the appropriate dashboard based on user role.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (RoleHelper::hasRole($user, 'admin')) {
            return $this->adminDashboard();
        } elseif (RoleHelper::hasRole($user, 'agent')) {
            return $this->agentDashboard();
        } else {
            return $this->clientDashboard();
        }
    }
    
    /**
     * Admin dashboard with overall system statistics.
     */
    private function adminDashboard()
    {
        // Total counts
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $totalUsers = User::count();
        $totalAgents = User::where('role', 'agent')->count();
        $totalClients = User::where('role', 'client')->count();
        
        // Ticket statistics
        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
            
        $ticketsByPriority = Ticket::selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->get();
            
        $ticketsByCategory = Ticket::selectRaw('category_id, count(*) as count')
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get();
            
        // SLA statistics
        $totalSlaBreaches = TicketSla::where('response_breached', true)
            ->orWhere('resolution_breached', true)
            ->count();
            
        $slaBreachPercentage = $totalTickets > 0 
            ? round(($totalSlaBreaches / $totalTickets) * 100, 2) 
            : 0;
            
        // Response time statistics
        $averageResponseTime = DB::table('ticket_sla')
            ->whereNotNull('first_response_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, ticket_sla.created_at, first_response_at)) as avg_time'))
            ->first();
            
        // Recent tickets
        $recentTickets = Ticket::with(['user', 'agent', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Agent performance
        $agentPerformance = User::where('role', 'agent')
            ->withCount(['assignedTickets as resolved_count' => function ($query) {
                $query->where('status', 'closed');
            }])
            ->withCount(['assignedTickets as total_count'])
            ->get()
            ->map(function ($agent) {
                $agent->resolution_rate = $agent->total_count > 0 
                    ? round(($agent->resolved_count / $agent->total_count) * 100, 2) 
                    : 0;
                return $agent;
            });
            
        // Get average ratings
        $agentRatings = Feedback::selectRaw('agent_id, AVG(rating) as average_rating, COUNT(*) as count')
            ->whereNotNull('agent_id')
            ->groupBy('agent_id')
            ->get()
            ->keyBy('agent_id');
            
        foreach ($agentPerformance as $agent) {
            $agent->average_rating = isset($agentRatings[$agent->id]) 
                ? round($agentRatings[$agent->id]->average_rating, 2) 
                : null;
            $agent->feedback_count = isset($agentRatings[$agent->id]) 
                ? $agentRatings[$agent->id]->count 
                : 0;
        }
            
        return view('dashboard.admin', compact(
            'totalTickets',
            'openTickets',
            'totalUsers',
            'totalAgents',
            'totalClients',
            'ticketsByStatus',
            'ticketsByPriority',
            'ticketsByCategory',
            'totalSlaBreaches',
            'slaBreachPercentage',
            'averageResponseTime',
            'recentTickets',
            'agentPerformance'
        ));
    }
    
    /**
     * Agent dashboard with assigned tickets and performance metrics.
     */
    private function agentDashboard()
    {
        $user = Auth::user();
        
        // Assigned tickets
        $assignedTickets = Ticket::with(['user', 'category'])
            ->where('agent_id', $user->id)
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        // Unassigned tickets
        $unassignedTickets = Ticket::with(['user', 'category'])
            ->whereNull('agent_id')
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Agent statistics
        $totalAssigned = Ticket::where('agent_id', $user->id)->count();
        $resolved = Ticket::where('agent_id', $user->id)->where('status', 'closed')->count();
        $pending = Ticket::where('agent_id', $user->id)
            ->whereIn('status', ['open', 'in_progress', 'waiting_customer'])
            ->count();
            
        // Resolution rate
        $resolutionRate = $totalAssigned > 0 
            ? round(($resolved / $totalAssigned) * 100, 2) 
            : 0;
            
        // SLA breaches
        $slaBreaches = TicketSla::whereHas('ticket', function ($query) use ($user) {
                $query->where('agent_id', $user->id);
            })
            ->where(function ($query) {
                $query->where('response_breached', true)
                    ->orWhere('resolution_breached', true);
            })
            ->count();
            
        // Feedback statistics
        $feedbackCount = Feedback::where('agent_id', $user->id)->count();
        $averageRating = Feedback::where('agent_id', $user->id)->avg('rating');
        
        // SLA at risk
        $ticketsAtRisk = Ticket::with(['user', 'category'])
            ->where('agent_id', $user->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->whereHas('ticketSla', function ($query) {
                $query->where('response_breached', false)
                    ->where('resolution_breached', false)
                    ->where(function ($q) {
                        $q->whereRaw('response_due_at <= DATE_ADD(NOW(), INTERVAL 3 HOUR)')
                            ->orWhereRaw('resolution_due_at <= DATE_ADD(NOW(), INTERVAL 6 HOUR)');
                    });
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        return view('dashboard.agent', compact(
            'assignedTickets',
            'unassignedTickets',
            'totalAssigned',
            'resolved',
            'pending',
            'resolutionRate',
            'slaBreaches',
            'feedbackCount',
            'averageRating',
            'ticketsAtRisk'
        ));
    }
    
    /**
     * Client dashboard with their tickets and knowledge base access.
     */
    private function clientDashboard()
    {
        $user = Auth::user();
        
        // User's tickets - get all tickets for the user
        $allTickets = Ticket::with(['agent', 'category'])
            ->where('user_id', $user->id)
            ->get();
            
        // Recent tickets for display in the table
        $tickets = $allTickets->sortByDesc('created_at')->take(5);

        // Get recent activity (comments, status changes)
        $recentActivity = Comment::with(['ticket', 'user'])
            ->whereHas('ticket', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('is_private', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Ticket statistics
        $totalTickets = $allTickets->count();
        $openTickets = $allTickets->whereIn('status', ['open', 'in_progress', 'waiting_customer'])->count();
        $closedTickets = $allTickets->where('status', 'closed')->count();
            
        // Recent knowledge base articles
        $recentArticles = Article::where('visibility', 'public')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
            
        // Categories for creating new tickets
        $categories = Category::all();
        
        return view('dashboard.client', compact(
            'tickets',
            'allTickets',
            'recentActivity',
            'totalTickets',
            'openTickets',
            'closedTickets',
            'recentArticles',
            'categories'
        ));
    }
} 