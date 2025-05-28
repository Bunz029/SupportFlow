@extends('layouts.app')

@section('title', 'SLA Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">SLA Details: {{ $ticketSla->ticket->ticket_number }}</h1>
                    <a href="{{ route('tickets.show', $ticketSla->ticket) }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        View Ticket
                    </a>
                </div>
                
                <!-- SLA Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">Ticket Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Ticket Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->ticket->ticket_number }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm">
                                    @php
                                        $statusColor = match($ticketSla->ticket->status) {
                                            'open' => 'blue',
                                            'in_progress' => 'yellow',
                                            'waiting_customer' => 'purple',
                                            'closed' => 'green',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                        {{ ucfirst(str_replace('_', ' ', $ticketSla->ticket->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Priority</dt>
                                <dd class="mt-1 text-sm">
                                    @php
                                        $priorityColor = match($ticketSla->ticket->priority) {
                                            'low' => 'green',
                                            'medium' => 'blue',
                                            'high' => 'orange',
                                            'urgent' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs text-{{ $priorityColor }}-700 bg-{{ $priorityColor }}-100">
                                        {{ ucfirst($ticketSla->ticket->priority) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->ticket->category->name }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Subject</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->ticket->subject }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Customer</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->ticket->user->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Agent</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->ticket->agent ? $ticketSla->ticket->agent->name : 'Unassigned' }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-gray-700 mb-3">SLA Information</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">SLA Policy</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->sla->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Response Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->sla->response_time_hours }} hours</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Resolution Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticketSla->sla->resolution_time_hours }} hours</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Response Due</dt>
                                <dd class="mt-1 text-sm">
                                    @if($ticketSla->response_breached)
                                        <span class="text-red-600 font-medium">{{ $ticketSla->response_due_at->format('M d, Y H:i') }}</span>
                                        <span class="px-2 py-1 ml-1 rounded text-xs text-red-700 bg-red-100">Breached</span>
                                    @else
                                        {{ $ticketSla->response_due_at->format('M d, Y H:i') }}
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Resolution Due</dt>
                                <dd class="mt-1 text-sm">
                                    @if($ticketSla->resolution_breached)
                                        <span class="text-red-600 font-medium">{{ $ticketSla->resolution_due_at->format('M d, Y H:i') }}</span>
                                        <span class="px-2 py-1 ml-1 rounded text-xs text-red-700 bg-red-100">Breached</span>
                                    @else
                                        {{ $ticketSla->resolution_due_at->format('M d, Y H:i') }}
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">First Response</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($ticketSla->first_response_at)
                                        {{ $ticketSla->first_response_at->format('M d, Y H:i') }}
                                    @else
                                        <span class="text-yellow-600">Not yet responded</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Time to First Response</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($ticketSla->first_response_at)
                                        @php
                                            $diffInMinutes = $ticketSla->first_response_at->diffInMinutes($ticketSla->ticket->created_at);
                                            $hours = floor($diffInMinutes / 60);
                                            $minutes = $diffInMinutes % 60;
                                            $responseTime = $hours . 'h ' . $minutes . 'm';
                                        @endphp
                                        {{ $responseTime }}
                                    @else
                                        <span class="text-yellow-600">Pending</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
                
                <!-- SLA Timeline -->
                <div class="bg-gray-50 rounded-lg p-4 shadow mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Timeline</h3>
                    
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Ticket <span class="font-medium text-gray-900">created</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $ticketSla->ticket->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">SLA <span class="font-medium text-gray-900">assigned</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $ticketSla->created_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($ticketSla->first_response_at)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">First <span class="font-medium text-gray-900">response sent</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $ticketSla->first_response_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            @if($ticketSla->response_breached)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Response SLA <span class="font-medium text-red-600">breached</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-red-600">
                                                {{ $ticketSla->response_due_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            @if($ticketSla->resolution_breached)
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Resolution SLA <span class="font-medium text-red-600">breached</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-red-600">
                                                {{ $ticketSla->resolution_due_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                            
                            @if($ticketSla->ticket->status === 'closed')
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-500">Ticket <span class="font-medium text-gray-900">resolved</span></p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $ticketSla->ticket->updated_at->format('M d, Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 