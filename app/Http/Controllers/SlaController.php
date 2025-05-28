<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketSla;
use App\Services\SlaMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SlaController extends Controller
{
    protected $slaService;

    public function __construct(SlaMonitoringService $slaService)
    {
        $this->slaService = $slaService;
    }

    /**
     * Display SLA dashboard with at-risk and breached tickets
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get tickets based on user role
        if (strtolower($user->role) === 'admin') {
            // Admin can see all tickets
            $atRiskTickets = $this->slaService->getTicketsAtRisk(4, 8);
            
            $breachedTickets = TicketSla::with(['ticket', 'ticket.user', 'ticket.agent', 'sla'])
                ->where(function ($query) {
                    $query->where('response_breached', true)
                        ->orWhere('resolution_breached', true);
                })
                ->whereHas('ticket', function ($query) {
                    $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
                })
                ->orderBy('response_due_at')
                ->get();
        } elseif (strtolower($user->role) === 'agent') { // Check explicitly by role name
            // Show all SLA data to agents just like admins
            $atRiskTickets = [
                'response_at_risk' => TicketSla::with(['ticket', 'ticket.user', 'ticket.agent', 'sla'])
                    ->whereHas('ticket', function ($query) {
                        $query->whereIn('status', ['open', 'in_progress']);
                    })
                    ->whereNull('first_response_at')
                    ->where('response_breached', false)
                    ->where('response_due_at', '>=', now())
                    ->where('response_due_at', '<=', now()->addHours(4))
                    ->get(),
                    
                'resolution_at_risk' => TicketSla::with(['ticket', 'ticket.user', 'ticket.agent', 'sla'])
                    ->whereHas('ticket', function ($query) {
                        $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
                    })
                    ->where('resolution_breached', false)
                    ->where('resolution_due_at', '>=', now())
                    ->where('resolution_due_at', '<=', now()->addHours(8))
                    ->get(),
            ];
            
            // Show all breached tickets to agents, just like admins see
            $breachedTickets = TicketSla::with(['ticket', 'ticket.user', 'ticket.agent', 'sla'])
                ->where(function ($query) {
                    $query->where('response_breached', true)
                        ->orWhere('resolution_breached', true);
                })
                ->whereHas('ticket', function ($query) {
                    $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
                })
                ->orderBy('response_due_at')
                ->get();
        } else {
            // Clients should not access this page
            abort(403, 'Unauthorized action.');
        }
        
        // Get SLA performance statistics
        $totalTickets = Ticket::count();
        $totalWithSla = TicketSla::count();
        
        $responseBreaches = TicketSla::where('response_breached', true)->count();
        $resolutionBreaches = TicketSla::where('resolution_breached', true)->count();
        
        $responseBreachRate = $totalWithSla > 0 ? round(($responseBreaches / $totalWithSla) * 100, 2) : 0;
        $resolutionBreachRate = $totalWithSla > 0 ? round(($resolutionBreaches / $totalWithSla) * 100, 2) : 0;
        
        return view('sla.dashboard', compact(
            'atRiskTickets',
            'breachedTickets',
            'totalTickets',
            'totalWithSla',
            'responseBreaches',
            'resolutionBreaches',
            'responseBreachRate',
            'resolutionBreachRate'
        ));
    }
    
    /**
     * Show details for a specific ticket SLA
     */
    public function show(TicketSla $ticketSla)
    {
        // Check if current user has permission to view the ticket
        $user = Auth::user();
        $ticket = $ticketSla->ticket;
        
        if ($user->id !== 1 && $ticket->agent_id !== $user->id && $ticket->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $ticketSla->load(['ticket', 'ticket.user', 'ticket.agent', 'ticket.comments', 'sla']);
        
        return view('sla.show', compact('ticketSla'));
    }
} 