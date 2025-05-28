@extends('layouts.app')

@section('title', 'Feedback Moderation')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Feedback Moderation</h1>
            <div class="flex gap-4">
                <span class="px-3 py-1 rounded text-sm text-green-700 bg-green-100">Approved</span>
                <span class="px-3 py-1 rounded text-sm text-red-700 bg-red-100">Flagged</span>
                <span class="px-3 py-1 rounded text-sm text-gray-700 bg-gray-100">Pending</span>
            </div>
        </div>

        @if(isset($moderationEnabled))
            <div class="mb-4 p-4 {{ $moderationEnabled ? 'bg-blue-50 border border-blue-200 text-blue-700' : 'bg-yellow-50 border border-yellow-200 text-yellow-700' }} rounded-lg">
                @if($moderationEnabled)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Moderation is enabled. New feedback requires approval before being visible.</span>
                    </div>
                @else
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Moderation is disabled. All feedback is automatically approved. <a href="{{ route('admin.settings.index') }}" class="underline font-medium">Change settings</a></span>
                    </div>
                @endif
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-6">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($feedbacks as $feedback)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('tickets.show', $feedback->ticket_id) }}" class="text-blue-600 hover:underline">#{{ $feedback->ticket->ticket_number ?? $feedback->ticket_id }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $feedback->user->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $feedback->agent->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-5 h-5 {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-md">{{ $feedback->comment }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $feedback->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($feedback->flagged)
                                <span class="px-2 py-1 rounded text-xs text-red-700 bg-red-100">Flagged</span>
                            @elseif($feedback->approved)
                                <span class="px-2 py-1 rounded text-xs text-green-700 bg-green-100">Approved</span>
                            @else
                                <span class="px-2 py-1 rounded text-xs text-gray-700 bg-gray-100">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-2">
                                @if(!$feedback->approved)
                                    <form method="POST" action="{{ route('admin.feedback.approve', $feedback) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                            Approve
                                        </button>
                                    </form>
                                @endif
                                @if($feedback->flagged)
                                    <form method="POST" action="{{ route('admin.feedback.unflag', $feedback) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                            Unflag
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.feedback.flag', $feedback) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                            Flag
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>
@endsection 