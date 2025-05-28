@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
@endphp
@extends('layouts.app')

@section('title', 'Create Ticket')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="bg-white rounded-2xl shadow-xl p-8 border">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Create New Ticket
        </h1>
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div>
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-1">Subject</label>
                <input type="text" name="subject" id="subject" class="w-full border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" required placeholder="Brief summary of your issue">
            </div>
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" class="w-full border-gray-300 rounded-lg p-3 focus:ring-blue-500 focus:border-blue-500" rows="5" required placeholder="Describe your issue in detail..."></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" class="w-full border-gray-300 rounded-lg p-3" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1">Priority</label>
                    <select name="priority" id="priority" class="w-full border-gray-300 rounded-lg p-3" required>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="attachments" class="block text-sm font-semibold text-gray-700 mb-1">Attachments</label>
                <input type="file" name="attachments[]" id="attachments" multiple class="w-full border-gray-300 rounded-lg p-3 bg-gray-50">
                <p class="text-xs text-gray-400 mt-1">You can upload screenshots or files (max 10MB each).</p>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-lg shadow hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Submit Ticket
            </button>
        </form>
    </div>
</div>
@endsection 