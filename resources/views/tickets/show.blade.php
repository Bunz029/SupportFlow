@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
@endphp
@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6">
    <!-- Ticket Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-800">Ticket #{{ $ticket->ticket_number }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    {{ $ticket->status == 'open' ? 'bg-blue-100 text-blue-800' : 
                      ($ticket->status == 'in_progress' ? 'bg-indigo-100 text-indigo-800' : 
                      ($ticket->status == 'waiting_customer' ? 'bg-yellow-100 text-yellow-800' : 
                      'bg-gray-100 text-gray-800')) }}">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                      ($ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                      ($ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 
                      'bg-green-100 text-green-800')) }}">
                    {{ ucfirst($ticket->priority) }}
                </span>
            </div>
            <p class="mt-2 text-lg text-gray-600 font-medium">{{ $ticket->subject }}</p>
        </div>
        
        @if(Auth::user()->isAdmin() || Auth::user()->isAgent())
        <div class="mt-4 md:mt-0">
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="flex flex-wrap gap-2 items-center">
                @csrf
                @method('PUT')
                <select name="status" class="form-select border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="open" @if($ticket->status=='open') selected @endif>Open</option>
                    <option value="in_progress" @if($ticket->status=='in_progress') selected @endif>In Progress</option>
                    <option value="waiting_customer" @if($ticket->status=='waiting_customer') selected @endif>Waiting on Customer</option>
                    <option value="closed" @if($ticket->status=='closed') selected @endif>Closed</option>
                    @if(Auth::user()->isAdmin())
                    <option value="rejected" @if($ticket->status=='rejected') selected @endif>Rejected</option>
                    @endif
                </select>
                @if(Auth::user()->isAdmin())
                <select name="priority" class="form-select border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="low" @if($ticket->priority=='low') selected @endif>Low</option>
                    <option value="medium" @if($ticket->priority=='medium') selected @endif>Medium</option>
                    <option value="high" @if($ticket->priority=='high') selected @endif>High</option>
                    <option value="urgent" @if($ticket->priority=='urgent') selected @endif>Urgent</option>
                </select>
                <select name="agent_id" class="form-select border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Unassigned</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @if($ticket->agent_id == $agent->id) selected @endif>{{ $agent->name }}</option>
                    @endforeach
                </select>
                @endif
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                        Update Ticket
                    </button>
                    @if(Auth::user()->isAdmin())
                    <button 
                        type="button"
                        onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                        class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                        Delete Ticket
                    </button>
                    @endif
                </div>
            </form>

            @if(Auth::user()->isAdmin())
            <!-- Delete Modal -->
            <div id="delete-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
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
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('delete-modal').classList.add('hidden')">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            @endif

            @if(Auth::user()->isAgent() && !$ticket->agent_id)
            <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="mt-2">
                @csrf
                @method('PUT')
                <input type="hidden" name="agent_id" value="{{ Auth::id() }}">
                <button type="submit" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Claim Ticket
                </button>
            </form>
            @endif
        </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Ticket Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Ticket Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-800">Ticket Information</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->category->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Assigned Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($ticket->agent)
                                    <span class="px-2 py-1 rounded-full text-xs text-green-700 bg-green-100">
                                        {{ $ticket->agent->name }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-xs text-gray-600 bg-gray-100">
                                        Not assigned yet - We'll get to you soon!
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Ticket Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-800">Description</h2>
                </div>
                <div class="p-6">
                    <div class="prose max-w-none">
                        {{ $ticket->description }}
                    </div>
                </div>
            </div>

            <!-- Ticket Attachments -->
            @if($ticket->attachments->count())
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-800">Attachments</h2>
                </div>
                <div class="px-6 py-4">
                    <ul class="divide-y divide-gray-200">
                        @foreach($ticket->attachments as $attachment)
                            <li class="py-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                <a href="{{ route('tickets.attachments.download', [$ticket, $attachment]) }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                                    {{ $attachment->filename }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Comments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-800">Comments ({{ $ticket->comments->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-200">
                    @if($ticket->comments->count())
                        @foreach($ticket->comments as $comment)
                            <div class="p-6">
                                <div class="flex space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-xl font-medium text-white">{{ substr($comment->user->name, 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-700 space-y-4">
                                            <p>{{ $comment->message }}</p>
                                        </div>
                                        @if($comment->attachments && $comment->attachments->count())
                                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                                @foreach($comment->attachments as $attachment)
                                                    <a href="{{ route('tickets.attachments.download', [$ticket, $attachment]) }}" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                                                        <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                        </svg>
                                                        {{ $attachment->filename }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500">No comments yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add Comment Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-600">
                    <h2 class="text-lg font-medium text-white">Add a Comment</h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <textarea name="message" id="message" rows="4" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                placeholder="Write your reply..." required></textarea>
                        </div>
                        <div class="flex justify-start">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Post Comment
                            </button>
                        </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Attachments
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload files</span>
                                            <input id="file-upload" name="attachments[]" type="file" class="sr-only" multiple>
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF, PDF up to 10MB
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Knowledge Base Search -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-medium text-gray-800">Knowledge Base</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('knowledgebase.index') }}" method="GET">
                        <label for="kb-search" class="block text-sm font-medium text-gray-700 mb-2">
                            Search for solutions:
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" name="q" id="kb-search" 
                                class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" 
                                placeholder="Search the Knowledge Base...">
                            <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feedback Section -->
            @if(Auth::user()->isClient() && $ticket->status === 'closed')
                @if($ticket->feedback)
                    <!-- Show submitted feedback -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h2 class="text-lg font-medium text-gray-800">Your Feedback</h2>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-7 h-7 {{ $i <= $ticket->feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                                <span class="ml-3 text-sm text-gray-600">({{ $ticket->feedback->rating }}/5)</span>
                            </div>
                            @if($ticket->feedback->comment)
                                <div class="mt-2 text-gray-700 border-l-4 border-blue-200 pl-4">{{ $ticket->feedback->comment }}</div>
                            @endif
                            <div class="mt-2 text-xs text-gray-400">Submitted {{ $ticket->feedback->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @else
                    <!-- Feedback Form -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                            <h2 class="text-lg font-medium text-gray-800">Provide Feedback</h2>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('feedback.store', $ticket) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        How would you rate our support?
                                    </label>
                                    <div class="flex items-center gap-2" id="star-rating-group">
                                        @for($i = 1; $i <= 5; $i++)
                                            <input type="radio" name="rating" value="{{ $i }}" id="star-{{ $i }}" class="hidden star-input" @if(old('rating') == $i) checked @endif required>
                                            <label for="star-{{ $i }}" class="cursor-pointer" aria-label="{{ $i }} star">
                                                <svg data-star="{{ $i }}" class="w-9 h-9 star-svg {{ old('rating') >= $i ? 'text-yellow-400' : 'text-gray-300' }} transition cursor-pointer focus:ring-2 focus:ring-blue-400" fill="currentColor" viewBox="0 0 20 20" tabindex="0">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="feedback-comment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Additional Comments (Optional)
                                    </label>
                                    <textarea id="feedback-comment" name="comment" rows="3" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring focus:ring-green-200 focus:ring-opacity-50" 
                                        placeholder="Tell us more about your experience..."></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                        Submit Feedback
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Agent Feedback View -->
            @if((Auth::user()->isAgent() && $ticket->agent_id === Auth::id() && $ticket->feedback) || (Auth::user()->isAdmin() && $ticket->feedback))
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-8">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h2 class="text-lg font-medium text-gray-800">Customer Feedback</h2>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-7 h-7 {{ $i <= $ticket->feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                            <span class="ml-3 text-sm text-gray-600">({{ $ticket->feedback->rating }}/5)</span>
                        </div>
                        @if($ticket->feedback->comment)
                            <div class="mt-2 text-gray-700 border-l-4 border-blue-200 pl-4">{{ $ticket->feedback->comment }}</div>
                        @endif
                        <div class="mt-2 text-xs text-gray-400">Submitted {{ $ticket->feedback->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-svg');
        const inputs = document.querySelectorAll('.star-input');
        let selected = 0;

        function updateStars(rating) {
            stars.forEach((star, idx) => {
                if (idx < rating) {
                    star.classList.add('text-yellow-400');
                    star.classList.remove('text-gray-300');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        stars.forEach((star, idx) => {
            const input = document.getElementById('star-' + (idx + 1));
            star.addEventListener('mouseover', function() {
                updateStars(idx + 1);
            });
            star.addEventListener('mouseout', function() {
                updateStars(selected);
            });
            star.addEventListener('click', function() {
                input.checked = true;
                selected = idx + 1;
                updateStars(selected);
            });
            input.addEventListener('change', function() {
                selected = idx + 1;
                updateStars(selected);
            });
        });
        // On page load, set stars if old('rating') exists
        const checkedInput = document.querySelector('.star-input:checked');
        if (checkedInput) {
            selected = parseInt(checkedInput.value);
            updateStars(selected);
        }
    });
</script>
@endsection