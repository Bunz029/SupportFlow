@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-4">Welcome Back, {{ Auth::user()->name }}</h1>
                
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Total Tickets -->
                    <div class="bg-blue-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-blue-700">Total Tickets</h3>
                        <p class="text-3xl font-bold">{{ $totalTickets }}</p>
                        <p class="text-sm text-blue-700">{{ $openTickets }} open tickets</p>
                    </div>
                    
                    <!-- Closed Tickets -->
                    <div class="bg-green-50 rounded-lg p-4 shadow">
                        <h3 class="text-lg font-semibold text-green-700">Resolved</h3>
                        <p class="text-3xl font-bold">{{ $closedTickets }}</p>
                        <p class="text-sm text-green-700">
                            @if($totalTickets > 0)
                                {{ round(($closedTickets / $totalTickets) * 100) }}% resolution rate
                            @else
                                0% resolution rate
                            @endif
                        </p>
                    </div>
                    
                    <!-- Create New Ticket -->
                    <div class="bg-indigo-50 rounded-lg p-4 shadow flex flex-col justify-between">
                        <h3 class="text-lg font-semibold text-indigo-700">Need Help?</h3>
                        <p class="text-sm text-indigo-700 mb-4">Create a new support ticket to get assistance from our team.</p>
                        <a href="{{ route('tickets.create') }}" class="bg-indigo-600 text-white text-center py-2 px-4 rounded hover:bg-indigo-700 transition duration-150">
                            Create New Ticket
                        </a>
                    </div>
                </div>
                
                <!-- Ticket Status Timeline -->
                <div class="bg-white rounded-lg p-4 shadow mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Ticket Status Overview</h3>
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex space-x-1 w-full">
                            @php
                                $openPercentage = $totalTickets > 0 ? ($openTickets / $totalTickets) * 100 : 0;
                                $inProgressCount = $allTickets->where('status', 'in_progress')->count();
                                $inProgressPercentage = $totalTickets > 0 ? ($inProgressCount / $totalTickets) * 100 : 0;
                                $waitingCount = $allTickets->where('status', 'waiting_customer')->count();
                                $waitingPercentage = $totalTickets > 0 ? ($waitingCount / $totalTickets) * 100 : 0;
                                $closedPercentage = $totalTickets > 0 ? ($closedTickets / $totalTickets) * 100 : 0;
                            @endphp
                            
                            <div class="h-3 bg-blue-500 rounded-l" style="width: {{ $openPercentage }}%"></div>
                            <div class="h-3 bg-yellow-500" style="width: {{ $inProgressPercentage }}%"></div>
                            <div class="h-3 bg-purple-500" style="width: {{ $waitingPercentage }}%"></div>
                            <div class="h-3 bg-green-500 rounded-r" style="width: {{ $closedPercentage }}%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 text-xs text-center">
                        <div>
                            <span class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                            Open ({{ $openTickets }})
                        </div>
                        <div>
                            <span class="inline-block w-3 h-3 bg-yellow-500 rounded-full mr-1"></span>
                            In Progress ({{ $inProgressCount }})
                        </div>
                        <div>
                            <span class="inline-block w-3 h-3 bg-purple-500 rounded-full mr-1"></span>
                            Waiting ({{ $waitingCount }})
                        </div>
                        <div>
                            <span class="inline-block w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                            Closed ({{ $closedTickets }})
                        </div>
                    </div>
                </div>
                
                <!-- My Tickets -->
                <div class="bg-gray-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">My Recent Tickets</h3>
                        <a href="{{ route('tickets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                    </div>
                    
                    @if(count($tickets) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 text-left">Ticket #</th>
                                        <th class="py-2 px-4 text-left">Subject</th>
                                        <th class="py-2 px-4 text-left">Status</th>
                                        <th class="py-2 px-4 text-left">Assigned To</th>
                                        <th class="py-2 px-4 text-left">Created</th>
                                        <th class="py-2 px-4 text-left">Last Updated</th>
                                        <th class="py-2 px-4 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
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
                                            <td class="py-2 px-4 border-b">{{ Str::limit($ticket->subject, 40) }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="px-2 py-1 rounded text-xs text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
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
                                            <td class="py-2 px-4 border-b">{{ $ticket->created_at->format('M d, Y') }}</td>
                                            <td class="py-2 px-4 border-b">{{ $ticket->updated_at->diffForHumans() }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <a href="{{ route('tickets.show', $ticket) }}" class="text-blue-600 hover:text-blue-800">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-blue-50 p-4 rounded-lg text-center">
                            <p class="text-blue-700 mb-3">You don't have any tickets yet.</p>
                            <a href="{{ route('tickets.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 inline-block">
                                Create Your First Ticket
                            </a>
                        </div>
                    @endif
                </div>
                
                <!-- Account Information -->
                <div class="bg-white rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Account Information</h3>
                        <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit Profile →</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-medium">{{ Auth::user()->name }}</p>
                            </div>
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium">{{ Auth::user()->email }}</p>
                            </div>
                            @if(Auth::user()->phone)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-medium">{{ Auth::user()->phone }}</p>
                            </div>
                            @endif
                        </div>
                        <div>
                            @if(Auth::user()->company)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Company</p>
                                <p class="font-medium">{{ Auth::user()->company }}</p>
                            </div>
                            @endif
                            @if(Auth::user()->job_title)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Job Title</p>
                                <p class="font-medium">{{ Auth::user()->job_title }}</p>
                            </div>
                            @endif
                            @if(Auth::user()->department)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Department</p>
                                <p class="font-medium">{{ Auth::user()->department }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Quick Ticket Creation -->
                <div class="bg-indigo-50 rounded-lg p-4 shadow mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-indigo-700">Quick Support Request</h3>
                        <a href="{{ route('tickets.create') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Advanced Form →</a>
                    </div>
                    <form action="{{ route('tickets.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="subject" class="block text-sm font-medium text-indigo-700">Subject</label>
                            <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Brief description of your issue" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-indigo-700">Category</label>
                                <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-indigo-700">Priority</label>
                                <select name="priority" id="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-indigo-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please provide details about your issue" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Submit Ticket
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-gray-50 rounded-lg p-4 shadow">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('tickets.create') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg p-4 flex flex-col items-center justify-center transition duration-150">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Create Ticket</span>
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 rounded-lg p-4 flex flex-col items-center justify-center transition duration-150">
                            <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>My Profile</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 