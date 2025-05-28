@extends('layouts.app')

@section('title', 'Submit Feedback')

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Submit Feedback for Ticket #{{ $ticket->ticket_number }}</h1>
        <form method="POST" action="{{ route('feedback.store', $ticket) }}">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">How would you rate our support?</label>
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer">
                            <input type="radio" name="rating" value="{{ $i }}" class="hidden" @if(old('rating') == $i) checked @endif>
                            <svg class="w-8 h-8 {{ old('rating') == $i ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </label>
                    @endfor
                </div>
            </div>
            <div class="mb-6">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Additional Comments (Optional)</label>
                <textarea id="comment" name="comment" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Tell us more about your experience..."></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold text-lg shadow hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Submit Feedback
            </button>
        </form>
    </div>
</div>
@endsection 