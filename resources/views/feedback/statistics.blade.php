@extends('layouts.app')

@section('title', 'Feedback Statistics')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-4">Feedback Statistics</h1>
                <div class="mb-4">
                    <strong>Overall Rating:</strong> {{ $overallRating ?? 'N/A' }}
                </div>
                <div class="mb-4">
                    <strong>Total Feedback:</strong> {{ $totalFeedback ?? 0 }}
                </div>
                <div class="mb-4">
                    <strong>Rating Breakdown:</strong>
                    <ul>
                        @foreach ($ratingBreakdown as $row)
                            <li>Rating {{ $row->rating }}: {{ $row->count }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-4">
                    <strong>Agent Ratings:</strong>
                    <ul>
                        @foreach ($agentRatings as $agent)
                            <li>{{ $agent->agent->name ?? 'N/A' }}: Avg {{ number_format($agent->average_rating, 2) }} ({{ $agent->count }} feedbacks)</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 