<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketSla;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display SLA performance report.
     */
    public function slaPerformance(Request $request)
    {
        // Get date range for filtering
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();
            
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();
        
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';
        $isAgent = $user->role === 'agent';
            
        // Get overall statistics - both admins and agents can see all stats
        $totalTickets = Ticket::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $totalWithSla = TicketSla::whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        $responseBreaches = TicketSla::where('response_breached', true)
            ->whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        $resolutionBreaches = TicketSla::where('resolution_breached', true)
            ->whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();
            
        // Calculate average response and resolution times
        $avgResponseTime = TicketSla::whereNotNull('first_response_at')
            ->whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get()
            ->avg(function ($sla) {
                return $sla->first_response_at->diffInMinutes($sla->ticket->created_at);
            }) ?? 0;
            
        $avgResolutionTime = TicketSla::whereHas('ticket', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'closed');
            })
            ->get()
            ->avg(function ($sla) {
                return $sla->ticket->updated_at->diffInMinutes($sla->ticket->created_at);
            }) ?? 0;
            
        // SLA performance by category - both admins and agents should see this
        $categoryPerformance = Category::withCount([
                'tickets as total_tickets' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'tickets as breached_tickets' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->whereHas('ticketSla', function ($q) {
                            $q->where(function ($sq) {
                                $sq->where('response_breached', true)
                                    ->orWhere('resolution_breached', true);
                            });
                        });
                }
            ])
            ->get()
            ->map(function ($category) {
                $category->breach_rate = $category->total_tickets > 0
                    ? round(($category->breached_tickets / $category->total_tickets) * 100, 2)
                    : 0;
                return $category;
            });
            
        // SLA performance by agent - both admins and agents should see this
        $agentPerformance = User::where('role', 'agent')
            ->withCount([
                'assignedTickets as total_tickets' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                },
                'assignedTickets as response_breached' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->whereHas('ticketSla', function ($q) {
                            $q->where('response_breached', true);
                        });
                },
                'assignedTickets as resolution_breached' => function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->whereHas('ticketSla', function ($q) {
                            $q->where('resolution_breached', true);
                        });
                }
            ])
            ->get()
            ->map(function ($agent) {
                $agent->breach_rate = $agent->total_tickets > 0
                    ? round((($agent->response_breached + $agent->resolution_breached) / ($agent->total_tickets * 2)) * 100, 2)
                    : 0;
                return $agent;
            });
            
        // SLA performance over time (by week) - both admins and agents should see this
        $timePerformance = DB::table('ticket_sla')
            ->join('tickets', 'ticket_sla.ticket_id', '=', 'tickets.id')
            ->whereBetween('tickets.created_at', [$startDate, $endDate])
            ->select([
                DB::raw('YEARWEEK(tickets.created_at) as week'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(IF(response_breached = 1, 1, 0)) as response_breached'),
                DB::raw('SUM(IF(resolution_breached = 1, 1, 0)) as resolution_breached')
            ])
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(function ($item) {
                $item->response_rate = $item->total > 0
                    ? round(($item->response_breached / $item->total) * 100, 2)
                    : 0;
                    
                $item->resolution_rate = $item->total > 0
                    ? round(($item->resolution_breached / $item->total) * 100, 2)
                    : 0;
                    
                // Convert week number to date range string
                $year = substr($item->week, 0, 4);
                $week = substr($item->week, 4);
                $firstDayOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek()->format('M d');
                $lastDayOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek()->format('M d');
                $item->week_label = "{$firstDayOfWeek} - {$lastDayOfWeek}";
                
                return $item;
            });
            
        return view('reports.sla-performance', compact(
            'startDate',
            'endDate',
            'totalTickets',
            'totalWithSla',
            'responseBreaches',
            'resolutionBreaches',
            'avgResponseTime',
            'avgResolutionTime',
            'categoryPerformance',
            'agentPerformance',
            'timePerformance',
            'isAdmin',
            'isAgent'
        ));
    }
} 