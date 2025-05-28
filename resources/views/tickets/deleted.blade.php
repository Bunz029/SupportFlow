@extends('layouts.app')

@section('title', 'Ticket Deleted')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="text-center py-6">
                <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                
                <h1 class="mt-4 text-3xl font-bold text-gray-800">Ticket Has Been Deleted</h1>
                
                <p class="mt-4 text-lg text-gray-600">
                    The ticket #{{ $ticket->ticket_number }} has been deleted by an administrator.
                </p>
                
                <div class="mt-6 max-w-md mx-auto bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-700">Ticket Information</h2>
                    <dl class="mt-3 text-left">
                        <div class="py-2 grid grid-cols-3">
                            <dt class="text-sm font-medium text-gray-500">Subject:</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $ticket->subject }}</dd>
                        </div>
                        <div class="py-2 grid grid-cols-3 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Status:</dt>
                            <dd class="text-sm text-gray-900 col-span-2">
                                <span class="px-2 py-1 rounded-full text-xs text-red-700 bg-red-100">
                                    Deleted
                                </span>
                            </dd>
                        </div>
                        <div class="py-2 grid grid-cols-3 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Created:</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $ticket->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div class="py-2 grid grid-cols-3 border-t border-gray-200">
                            <dt class="text-sm font-medium text-gray-500">Deleted:</dt>
                            <dd class="text-sm text-gray-900 col-span-2">{{ $ticket->deleted_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div class="mt-8">
                    <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Tickets
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 