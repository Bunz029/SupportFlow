@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'SLA Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-semibold">SLA Dashboard</h1>
                    <a href="{{ route('reports.sla-performance') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        SLA Performance Report
                    </a>
                </div>
                
                <!-- SLA Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Total Tickets with SLA -->
                    <div class="bg-blue-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-blue-700">Total Tickets</h3>
                        <p class="text-3xl font-bold">{{ $totalTickets }}</p>
                        <p class="text-sm text-blue-700">{{ $totalWithSla }} with SLA</p>
                    </div>
                    
                    <!-- Response Breaches -->
                    <div class="bg-red-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-red-700">Response Breaches</h3>
                        <p class="text-3xl font-bold">{{ $responseBreaches }}</p>
                        <p class="text-sm text-red-700">{{ $responseBreachRate }}% breach rate</p>
                    </div>
                    
                    <!-- Resolution Breaches -->
                    <div class="bg-orange-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-orange-700">Resolution Breaches</h3>
                        <p class="text-3xl font-bold">{{ $resolutionBreaches }}</p>
                        <p class="text-sm text-orange-700">{{ $resolutionBreachRate }}% breach rate</p>
                    </div>
                    
                    <!-- At Risk Tickets -->
                    <div class="bg-yellow-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-yellow-700">At Risk Tickets</h3>
                        <p class="text-3xl font-bold">
                            {{ count($atRiskTickets['response_at_risk']) + count($atRiskTickets['resolution_at_risk']) }}
                        </p>
                        <p class="text-sm text-yellow-700">
                            {{ count($atRiskTickets['response_at_risk']) }} response, 
                            {{ count($atRiskTickets['resolution_at_risk']) }} resolution
                        </p>
                    </div>
                </div>
                
                <!-- Breached SLAs -->
                <div class="bg-red-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-red-700">Breached SLAs</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            Needs Immediate Attention
                        </span>
                    </div>
                    
                    @if(Auth::user()->role === 'agent')
                    <div class="bg-blue-50 p-3 rounded-md mb-4 text-sm">
                        <p class="text-blue-800">You can see all breached SLAs system-wide to help the team address critical issues.</p>
                    </div>
                    @endif
                    
                    @if(count($breachedTickets) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-red-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Ticket #</th>
                                        <th class="py-2 px-4 text-left">Subject</th>
                                        <th class="py-2 px-4 text-left">Customer</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Priority</th>
                                        <th class="py-2 px-4 text-left">Breach Type</th>
                                        <th class="py-2 px-4 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($breachedTickets as $ticketSla)
                                        @php
                                            $ticket = $ticketSla->ticket;
                                            $statusColor = match($ticket->status) {
                                                'open' => 'blue',
                                                'in_progress' => 'yellow',
                                                'waiting_customer' => 'purple',
                                                'closed' => 'green',
                                                default => 'gray'
                                            };
                                            $priorityColor = match($ticket->priority) {
                                                'low' => 'green',
                                                'medium' => 'blue',
                                                'high' => 'orange',
                                                'urgent' => 'red',
                                                default => 'gray'
                                            };
                                            
                                            $breachType = [];
                                            if ($ticketSla->response_breached) {
                                                $breachType[] = 'Response';
                                            }
                                            if ($ticketSla->resolution_breached) {
                                                $breachType[] = 'Resolution';
                                            }
                                        @endphp
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                            <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-red-700 bg-red-100">
                                                    {{ implode(', ', $breachType) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No SLA breaches. Great job!</p>
                    @endif
                </div>
                
                <!-- Response SLA At Risk -->
                <div class="bg-yellow-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-yellow-700">Response SLA At Risk</h3>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            Needs Attention Soon
                        </span>
                    </div>
                    
                    @if(Auth::user()->role === 'agent')
                    <div class="bg-blue-50 p-3 rounded-md mb-4 text-sm">
                        <p class="text-blue-800">You can see all tickets at risk of response SLA breach system-wide to help coordinate timely responses.</p>
                    </div>
                    @endif
                    
                    @if(count($atRiskTickets['response_at_risk']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-yellow-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Ticket #</th>
                                        <th class="py-2 px-4 text-left">Subject</th>
                                        <th class="py-2 px-4 text-left">Customer</th>
                                        <th class="py-2 px-4 text-left">Priority</th>
                                        <th class="py-2 px-4 text-left">Due In</th>
                                        <th class="py-2 px-4 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($atRiskTickets['response_at_risk'] as $ticketSla)
                                        @php
                                            $ticket = $ticketSla->ticket;
                                            $priorityColor = match($ticket->priority) {
                                                'low' => 'green',
                                                'medium' => 'blue',
                                                'high' => 'orange',
                                                'urgent' => 'red',
                                                default => 'gray'
                                            };
                                            $dueIn = now()->diffInMinutes($ticketSla->response_due_at);
                                            $dueFormatted = floor($dueIn / 60) . 'h ' . ($dueIn % 60) . 'm';
                                        @endphp
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                            <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b font-semibold text-yellow-600">
                                                {{ $dueFormatted }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No tickets at risk of response SLA breach.</p>
                    @endif
                </div>
                
                <!-- Resolution SLA At Risk -->
                <div class="bg-orange-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-orange-700">Resolution SLA At Risk</h3>
                        <span class="bg-orange-100 text-orange-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            Plan Resolution Soon
                        </span>
                    </div>
                    
                    @if(Auth::user()->role === 'agent')
                    <div class="bg-blue-50 p-3 rounded-md mb-4 text-sm">
                        <p class="text-blue-800">You can see all tickets at risk of resolution SLA breach system-wide to help prioritize resolutions.</p>
                    </div>
                    @endif
                    
                    @if(count($atRiskTickets['resolution_at_risk']) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-orange-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Ticket #</th>
                                        <th class="py-2 px-4 text-left">Subject</th>
                                        <th class="py-2 px-4 text-left">Customer</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Priority</th>
                                        <th class="py-2 px-4 text-left">Due In</th>
                                        <th class="py-2 px-4 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($atRiskTickets['resolution_at_risk'] as $ticketSla)
                                        @php
                                            $ticket = $ticketSla->ticket;
                                            $statusColor = match($ticket->status) {
                                                'open' => 'blue',
                                                'in_progress' => 'yellow',
                                                'waiting_customer' => 'purple',
                                                'closed' => 'green',
                                                default => 'gray'
                                            };
                                            $priorityColor = match($ticket->priority) {
                                                'low' => 'green',
                                                'medium' => 'blue',
                                                'high' => 'orange',
                                                'urgent' => 'red',
                                                default => 'gray'
                                            };
                                            $dueIn = now()->diffInMinutes($ticketSla->resolution_due_at);
                                            $dueFormatted = floor($dueIn / 60) . 'h ' . ($dueIn % 60) . 'm';
                                        @endphp
                                        <tr>
                                            <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                            <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                                    {{ ucfirst($ticket->priority) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b font-semibold text-orange-600">
                                                {{ $dueFormatted }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No tickets at risk of resolution SLA breach.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 