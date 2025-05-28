@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('title', Auth::user()->isAdmin() ? 'All Tickets' : (Auth::user()->isAgent() ? 'Assigned & Unassigned Tickets' : 'My Tickets'))

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-3xl font-extrabold text-gray-900">
            @if(Auth::user()->isAdmin())
                All Tickets
            @elseif(Auth::user()->isAgent())
                Assigned & Unassigned Tickets
            @else
                My Tickets
            @endif
        </h1>
        @if(Auth::user()->isClient())
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Create Ticket
            </a>
        @endif
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
        <form method="GET" action="{{ route('tickets.index') }}" class="mb-8 flex flex-wrap gap-4 items-end bg-white p-4 rounded-xl shadow border">
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-600">Status</label>
                <select name="status" id="status" class="form-select mt-1 block w-full border-gray-300 rounded-lg">
                    <option value="">All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @if(request('status')==$status) selected @endif>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="priority" class="block text-xs font-semibold text-gray-600">Priority</label>
                <select name="priority" id="priority" class="form-select mt-1 block w-full border-gray-300 rounded-lg">
                    <option value="">All</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority }}" @if(request('priority')==$priority) selected @endif>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="category_id" class="block text-xs font-semibold text-gray-600">Category</label>
                <select name="category_id" id="category_id" class="form-select mt-1 block w-full border-gray-300 rounded-lg">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @if(request('category_id')==$category->id) selected @endif>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(Auth::user()->isAgent())
            <div>
                <label for="assignment" class="block text-xs font-semibold text-gray-600">Assignment</label>
                <select name="assignment" id="assignment" class="form-select mt-1 block w-full border-gray-300 rounded-lg">
                    <option value="">All</option>
                    <option value="assigned" @if(request('assignment')=='assigned') selected @endif>Assigned to me</option>
                    <option value="unassigned" @if(request('assignment')=='unassigned') selected @endif>Unassigned</option>
                </select>
            </div>
            @endif
            <div class="flex items-end h-full">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition font-semibold">Filter</button>
            </div>
        </form>
    @endif
    @if($tickets->count())
        <div class="overflow-x-auto mt-4 bg-white rounded-2xl shadow border">
            <table class="min-w-full bg-white border rounded-2xl">
                <thead>
                    <tr class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
                        <th class="px-6 py-3 border-b">#</th>
                        <th class="px-6 py-3 border-b">Subject</th>
                        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                            <th class="px-6 py-3 border-b">Client</th>
                        @endif
                        <th class="px-6 py-3 border-b">Assigned To</th>
                        <th class="px-6 py-3 border-b">Status</th>
                        <th class="px-6 py-3 border-b">Priority</th>
                        <th class="px-6 py-3 border-b">Created</th>
                        <th class="px-6 py-3 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-6 py-4 border-b font-mono text-sm text-gray-700">{{ $ticket->ticket_number }}</td>
                            <td class="px-6 py-4 border-b font-semibold text-gray-900">{{ Str::limit($ticket->subject, 40) }}</td>
                            @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
                                <td class="px-6 py-4 border-b text-gray-700">{{ $ticket->user->name ?? '-' }}</td>
                            @endif
                            <td class="px-6 py-4 border-b text-gray-700">
                                @if($ticket->agent)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $ticket->agent->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                        Unassigned
                                    </span>
                            @endif
                            </td>
                            <td class="px-6 py-4 border-b">
                                @php
                                    $statusColors = [
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'waiting_customer' => 'bg-purple-100 text-purple-800',
                                        'closed' => 'bg-green-100 text-green-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$ticket->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-b">
                                @php
                                    $priorityColors = [
                                        'low' => 'bg-green-100 text-green-800',
                                        'medium' => 'bg-blue-100 text-blue-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'urgent' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $priorityColors[$ticket->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 border-b text-gray-500">{{ $ticket->created_at->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-4 border-b">
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold px-3 py-1 rounded hover:bg-blue-100 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0A9 9 0 11 3 12a9 9 0 0118 0z"/></svg>
                                    View
                                </a>
                                @if(Auth::user()->isAgent() && is_null($ticket->agent_id))
                                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="agent_id" value="{{ Auth::id() }}">
                                        <button type="submit" class="ml-2 inline-flex items-center gap-1 text-green-600 hover:text-green-800 font-semibold px-3 py-1 rounded hover:bg-green-100 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Claim
                                        </button>
                                    </form>
                                @endif
                                
                                @if(Auth::user()->isAdmin())
                                    <!-- Reject button -->
                                    @if($ticket->status !== 'rejected')
                                        <button onclick="document.getElementById('reject-modal-{{ $ticket->id }}').classList.remove('hidden')" 
                                            class="ml-2 inline-flex items-center gap-1 text-orange-600 hover:text-orange-800 font-semibold px-3 py-1 rounded hover:bg-orange-100 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                            Reject
                                        </button>
                                    @endif
                                    
                                    <!-- Delete button -->
                                    <button onclick="document.getElementById('delete-modal-{{ $ticket->id }}').classList.remove('hidden')" 
                                        class="ml-2 inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-semibold px-3 py-1 rounded hover:bg-red-100 transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                    
                                    <!-- Reject Modal -->
                                    <div id="reject-modal-{{ $ticket->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
                                        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                                            <form action="{{ route('admin.tickets.reject', $ticket) }}" method="POST">
                                                @csrf
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start">
                                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                        </div>
                                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                                Reject Ticket
                                                            </h3>
                                                            <div class="mt-2">
                                                                <p class="text-sm text-gray-500">
                                                                    You are about to reject ticket #{{ $ticket->ticket_number }}. Please provide a reason for rejection:
                                                                </p>
                                                                <textarea name="rejection_reason" rows="3" class="mt-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Reason for rejection" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Reject Ticket
                                                    </button>
                                                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('reject-modal-{{ $ticket->id }}').classList.add('hidden')">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div id="delete-modal-{{ $ticket->id }}" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
                                        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <div class="sm:flex sm:items-start">
                                                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                                                            Delete Ticket
                                                        </h3>
                                                        <div class="mt-2">
                                                            <p class="text-sm text-gray-500">
                                                                Are you sure you want to delete ticket #{{ $ticket->ticket_number }}? This action cannot be undone.
                                                            </p>
                                                            @if($ticket->status !== 'rejected')
                                                            <p class="text-sm text-red-500 mt-2">
                                                                <strong>Note:</strong> The client and agent will be notified that this ticket has been rejected before deletion.
                                                            </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('delete-modal-{{ $ticket->id }}').classList.add('hidden')">
                                                    Cancel
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-8 flex justify-center">
            {{ $tickets->links('pagination::tailwind') }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow border p-8 text-center text-gray-500 text-lg mt-8">
            <svg class="mx-auto mb-2 w-10 h-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 018 0v2m-4-4V7m0 0a4 4 0 10-8 0 4 4 0 008 0z"/></svg>
            No tickets found.
        </div>
    @endif
</div>
@endsection 