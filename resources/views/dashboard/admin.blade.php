@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-4">Admin Dashboard</h1>
                
                <div class="flex flex-wrap gap-4 mb-6">
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Category Management
                    </a>
                    <a href="{{ route('admin.knowledge-base.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-semibold shadow hover:bg-green-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Knowledge Base Management
                    </a>
                </div>
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Tickets -->
                    <div class="bg-blue-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-blue-700">Total Tickets</h3>
                        <p class="text-3xl font-bold">{{ $totalTickets }}</p>
                        <p class="text-sm text-blue-700">{{ $openTickets }} open tickets</p>
                    </div>
                    
                    <!-- Total Users -->
                    <div class="bg-green-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-green-700">Total Users</h3>
                        <p class="text-3xl font-bold">{{ $totalUsers }}</p>
                        <p class="text-sm text-green-700">{{ $totalClients }} clients, {{ $totalAgents }} agents</p>
                    </div>
                    
                    <!-- SLA Compliance -->
                    <div class="bg-orange-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-orange-700">SLA Breaches</h3>
                        <p class="text-3xl font-bold">{{ $totalSlaBreaches }}</p>
                        <p class="text-sm text-orange-700">{{ $slaBreachPercentage }}% breach rate</p>
                    </div>
                    
                    <!-- Response Time -->
                    <div class="bg-purple-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-purple-700">Avg. Response Time</h3>
                        <p class="text-3xl font-bold">{{ isset($averageResponseTime->avg_time) ? round($averageResponseTime->avg_time / 60, 1) : 0 }} hrs</p>
                        <p class="text-sm text-purple-700">First response time</p>
                    </div>
                </div>
                
                <!-- Ticket Distribution -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- By Status -->
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Tickets by Status</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-right">Count</th>
                                        <th class="py-2 px-4 text-right">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticketsByStatus as $status)
                                        @php
                                            $percentage = $totalTickets > 0 ? round(($status->count / $totalTickets) * 100, 1) : 0;
                                            $statusColor = match($status->status) {
                                                'open' => 'blue',
                                                'in_progress' => 'yellow',
                                                'waiting_customer' => 'purple',
                                                'closed' => 'green',
                                                'rejected' => 'red',
                                                default => 'gray'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                                    {{ ucfirst(str_replace('_', ' ', $status->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b text-right">{{ $status->count }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- By Priority -->
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Tickets by Priority</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Priority</th>
                                        <th class="py-2 px-4 text-right">Count</th>
                                        <th class="py-2 px-4 text-right">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticketsByPriority as $priority)
                                        @php
                                            $percentage = $totalTickets > 0 ? round(($priority->count / $totalTickets) * 100, 1) : 0;
                                            $priorityColor = match($priority->priority) {
                                                'low' => 'green',
                                                'medium' => 'blue',
                                                'high' => 'orange',
                                                'urgent' => 'red',
                                                default => 'gray'
                                            };
                                        @endphp
                                        <tr>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                                    {{ ucfirst($priority->priority) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b text-right">{{ $priority->count }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ $percentage }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Tickets -->
                <div class="bg-gray-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Recent Tickets</h3>
                        <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">ID</th>
                                    <th class="py-2 px-4 text-left">Subject</th>
                                    <th class="py-2 px-4 text-left">Customer</th>
                                    <th class="py-2 px-4 text-left">Assigned To</th>
                                    <th class="py-2 px-4 text-left">Status</th>
                                    <th class="py-2 px-4 text-left">Priority</th>
                                    <th class="py-2 px-4 text-left">Created</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                    @php
                                        $statusColor = match($ticket->status) {
                                            'open' => 'blue',
                                            'in_progress' => 'yellow',
                                            'waiting_customer' => 'purple',
                                            'closed' => 'green',
                                            'rejected' => 'red',
                                            default => 'gray'
                                        };
                                        $priorityColor = match($ticket->priority) {
                                            'low' => 'green',
                                            'medium' => 'blue',
                                            'high' => 'orange',
                                            'urgent' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                        <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 30) }}</td>
                                        <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                        <td class="py-2 px-4 border-b">
                                            @if($ticket->agent)
                                                <span class="px-2 py-1 rounded text-xs text-green-700 bg-green-100">
                                                    {{ $ticket->agent->name }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 rounded text-xs text-gray-600 bg-gray-100">
                                                    Unassigned
                                                </span>
                                            @endif
                                        </td>
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
                                        <td class="py-2 px-4 border-b">{{ $ticket->created_at->diffForHumans() }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('tickets.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Agent Performance -->
                <div class="bg-gray-50 rounded-lg p-4 shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Agent Performance</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">Agent</th>
                                    <th class="py-2 px-4 text-right">Total Tickets</th>
                                    <th class="py-2 px-4 text-right">Resolved</th>
                                    <th class="py-2 px-4 text-right">Resolution Rate</th>
                                    <th class="py-2 px-4 text-right">Avg. Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agentPerformance as $agent)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $agent->name }}</td>
                                        <td class="py-2 px-4 border-b text-right">{{ $agent->total_count }}</td>
                                        <td class="py-2 px-4 border-b text-right">{{ $agent->resolved_count }}</td>
                                        <td class="py-2 px-4 border-b text-right">{{ $agent->resolution_rate }}%</td>
                                        <td class="py-2 px-4 border-b text-right">
                                            @if($agent->average_rating)
                                                <span class="flex items-center justify-end">
                                                    {{ $agent->average_rating }}
                                                    <svg class="w-4 h-4 text-yellow-500 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </span>
                                            @else
                                                No ratings
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg font-semibold shadow hover:bg-blue-600 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 