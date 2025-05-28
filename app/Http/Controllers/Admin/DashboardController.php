<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get ticket statistics
        $ticketCount = Ticket::whereMonth('created_at', Carbon::now()->month)->count();
        
        // Calculate SLA breach percentage
        $totalTickets = Ticket::count();
        $breachedTickets = Ticket::where('sla_breached', true)->count();
        $slaBreachPercentage = $totalTickets > 0 ? ($breachedTickets / $totalTickets) * 100 : 0;

        // Calculate average response time in hours
        $avgResponseTime = Ticket::whereNotNull('first_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_response_time')
            ->value('avg_response_time') ?? 0;

        // Get agent performance metrics
        $agentPerformance = User::where('role', 'agent')
            ->withCount(['assignedTickets as resolved_tickets' => function ($query) {
                $query->where('status', 'resolved');
            }])
            ->get()
            ->map(function ($agent) {
                $feedbackScore = \App\Models\Feedback::where('agent_id', $agent->id)
                    ->avg('rating') ?? 0;
                $feedbackCount = \App\Models\Feedback::where('agent_id', $agent->id)
                    ->count();
                $avgResponseTime = $agent->assignedTickets()
                    ->whereNotNull('first_response_at')
                    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, first_response_at)) as avg_response_time')
                    ->value('avg_response_time') ?? 0;
                return [
                    'name' => $agent->name,
                    'resolved_tickets' => $agent->resolved_tickets,
                    'avg_response_time' => round($avgResponseTime, 1),
                    'satisfaction_score' => round($feedbackScore, 1),
                    'feedback_count' => $feedbackCount,
                ];
            });

        // Get category distribution
        $categoryDistribution = Category::withCount('tickets')
            ->get()
            ->map(function ($category) use ($totalTickets) {
                return [
                    'name' => $category->name,
                    'percentage' => $totalTickets > 0 ? 
                        round(($category->tickets_count / $totalTickets) * 100, 1) : 0
                ];
            });

        // Get SLA compliance by priority
        $slaPriorities = DB::table('tickets')
            ->select('priority',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN sla_breached = 0 THEN 1 ELSE 0 END) as compliant'))
            ->groupBy('priority')
            ->get()
            ->map(function ($priority) {
                return [
                    'name' => ucfirst($priority->priority),
                    'compliance_rate' => $priority->total > 0 ? 
                        round(($priority->compliant / $priority->total) * 100, 1) : 100
                ];
            });

        return view('admin.dashboard', compact(
            'ticketCount',
            'slaBreachPercentage',
            'avgResponseTime',
            'agentPerformance',
            'categoryDistribution',
            'slaPriorities'
        ));
    }
} 