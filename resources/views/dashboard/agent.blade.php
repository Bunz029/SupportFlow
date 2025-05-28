@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', 'Agent Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-4">Agent Dashboard</h1>
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Assigned -->
                    <div class="bg-blue-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-blue-700">Assigned Tickets</h3>
                        <p class="text-3xl font-bold">{{ $totalAssigned }}</p>
                        <p class="text-sm text-blue-700">{{ $pending }} pending</p>
                    </div>
                    
                    <!-- Resolved Tickets -->
                    <div class="bg-green-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-green-700">Resolved</h3>
                        <p class="text-3xl font-bold">{{ $resolved }}</p>
                        <p class="text-sm text-green-700">{{ $resolutionRate }}% resolution rate</p>
                    </div>
                    
                    <!-- SLA Breaches -->
                    <div class="bg-orange-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-orange-700">SLA Breaches</h3>
                        <p class="text-3xl font-bold">{{ $slaBreaches }}</p>
                        <p class="text-sm text-orange-700">
                            @if($totalAssigned > 0)
                                {{ round(($slaBreaches / $totalAssigned) * 100, 1) }}% breach rate
                            @else
                                0% breach rate
                            @endif
                        </p>
                    </div>
                    
                    <!-- Feedback -->
                    <div class="bg-purple-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-purple-700">Feedback</h3>
                        <p class="text-3xl font-bold">
                            @if($averageRating)
                                <span class="flex items-center">
                                    {{ round($averageRating, 1) }}
                                    <svg class="w-6 h-6 text-yellow-500 ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </span>
                            @else
                                -
                            @endif
                        </p>
                        <p class="text-sm text-purple-700">{{ $feedbackCount }} ratings received</p>
                    </div>
                </div>
                
                <!-- SLA At Risk Tickets -->
                @if(count($ticketsAtRisk) > 0)
                <div class="bg-red-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-red-700">SLA At Risk</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            Needs Immediate Attention
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-red-100">
                                <tr>
                                    <th class="py-2 px-4 text-left">ID</th>
                                    <th class="py-2 px-4 text-left">Subject</th>
                                    <th class="py-2 px-4 text-left">Customer</th>
                                    <th class="py-2 px-4 text-left">Priority</th>
                                    <th class="py-2 px-4 text-left">Created</th>
                                    <th class="py-2 px-4 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ticketsAtRisk as $ticket)
                                    @php
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
                                            <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $ticket->created_at->diffForHumans() }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <!-- Assigned Tickets -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- My Tickets -->
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">My Tickets</h3>
                            <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                        </div>
                        @if(count($assignedTickets) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-4 text-left">ID</th>
                                            <th class="py-2 px-4 text-left">Subject</th>
                                            <th class="py-2 px-4 text-left">Status</th>
                                            <th class="py-2 px-4 text-left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignedTickets as $ticket)
                                            @php
                                                $statusColor = match($ticket->status) {
                                                    'open' => 'blue',
                                                    'in_progress' => 'yellow',
                                                    'waiting_customer' => 'purple',
                                                    'closed' => 'green',
                                                    default => 'gray'
                                                };
                                            @endphp
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                                <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 30) }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
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
                            <p class="text-gray-500 text-center py-4">No tickets assigned to you at the moment.</p>
                        @endif
                    </div>
                    
                    <!-- Unassigned Tickets -->
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Unassigned Tickets</h3>
                            <a href="{{ route('tickets.index') }}?status=open&agent=unassigned" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                        </div>
                        @if(count($unassignedTickets) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-4 text-left">ID</th>
                                            <th class="py-2 px-4 text-left">Subject</th>
                                            <th class="py-2 px-4 text-left">Customer</th>
                                            <th class="py-2 px-4 text-left">Created</th>
                                            <th class="py-2 px-4 text-left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unassignedTickets as $ticket)
                                            <tr>
                                                <td class="py-2 px-4 border-b">{{ $ticket->ticket_number }}</td>
                                                <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 20) }}</td>
                                                <td class="py-2 px-4 border-b">{{ $ticket->user->name }}</td>
                                                <td class="py-2 px-4 border-b">{{ $ticket->created_at->diffForHumans() }}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">No unassigned tickets at the moment.</p>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-50 rounded-lg p-4 shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('tickets.index') }}?status=open" class="bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg p-4 flex flex-col items-center justify-center transition duration-150">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>View Open Tickets</span>
                        </a>
                        
                        <a href="{{ route('knowledgebase.index') }}" class="bg-green-100 hover:bg-green-200 text-green-800 rounded-lg p-4 flex flex-col items-center justify-center transition duration-150">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Knowledge Base</span>
                        </a>
                        
                        <a href="{{ route('feedback.statistics') }}" class="bg-yellow-100 hover:bg-yellow-200 text-yellow-800 rounded-lg p-4 flex flex-col items-center justify-center transition duration-150">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            <span>View Feedback</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 