@extends('layouts.app')

@section('title', 'SLA Performance Report')

@push('styles')
<style>
    @media print {
        /* Hide non-printable elements */
        .no-print {
            display: none !important;
        }
        
        /* Reset background colors and shadows for better printing */
        body {
            background: white !important;
            margin: 0 !important;
            padding: 20px !important;
        }
        
        .shadow, .shadow-sm, .shadow-lg {
            box-shadow: none !important;
        }
        
        /* Ensure tables break properly across pages */
        table { 
            page-break-inside: auto !important; 
        }
        tr { 
            page-break-inside: avoid !important; 
            page-break-after: auto !important; 
        }
        thead { 
            display: table-header-group !important; 
        }
        
        /* Preserve background colors for status indicators */
        .bg-red-100, .bg-yellow-100, .bg-green-100 {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Add report header for printing */
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
        }
        
        /* Adjust layout for print */
        .max-w-7xl {
            max-width: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
        }
        
        /* Ensure text is readable when printed */
        .text-gray-500 {
            color: #374151 !important;
        }
        
        /* Add page breaks before major sections */
        .page-break-before {
            page-break-before: always !important;
        }
    }
</style>
@endpush

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Print Header (only visible when printing) -->
        <div class="print-header" style="display: none;">
            <h1 class="text-2xl font-bold">SLA Performance Report</h1>
            <p class="text-gray-600">{{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}</p>
            <p class="text-gray-600">Generated on {{ now()->format('M d, Y H:i:s') }}</p>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-semibold">SLA Performance Report</h1>
                    
                    <div class="flex space-x-4">
                        <!-- Print Button -->
                        <button onclick="window.print()" class="no-print inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Report
                        </button>
                    
                    <!-- Date Range Filter -->
                        <form action="{{ route('reports.sla-performance') }}" method="GET" class="no-print flex space-x-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="self-end">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Apply Filter
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
                
                <!-- Overall Statistics - visible to both admin and agent -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Overall SLA Performance</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 shadow">
                            <h3 class="text-sm font-semibold text-blue-700">Tickets with SLA</h3>
                            <p class="text-2xl font-bold">{{ $totalWithSla }} / {{ $totalTickets }}</p>
                            <p class="text-sm text-blue-700">
                                {{ $totalTickets > 0 ? round(($totalWithSla / $totalTickets) * 100, 2) : 0 }}% coverage
                            </p>
                        </div>
                        
                        <div class="bg-red-50 rounded-lg p-4 shadow">
                            <h3 class="text-sm font-semibold text-red-700">Response SLA Breaches</h3>
                            <p class="text-2xl font-bold">{{ $responseBreaches }}</p>
                            <p class="text-sm text-red-700">
                                {{ $totalWithSla > 0 ? round(($responseBreaches / $totalWithSla) * 100, 2) : 0 }}% breach rate
                            </p>
                        </div>
                        
                        <div class="bg-orange-50 rounded-lg p-4 shadow">
                            <h3 class="text-sm font-semibold text-orange-700">Resolution SLA Breaches</h3>
                            <p class="text-2xl font-bold">{{ $resolutionBreaches }}</p>
                            <p class="text-sm text-orange-700">
                                {{ $totalWithSla > 0 ? round(($resolutionBreaches / $totalWithSla) * 100, 2) : 0 }}% breach rate
                            </p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4 shadow">
                            <h3 class="text-sm font-semibold text-green-700">Average Response Time</h3>
                            <p class="text-2xl font-bold">
                                @php
                                    $hours = floor($avgResponseTime / 60);
                                    $minutes = $avgResponseTime % 60;
                                    echo $hours . 'h ' . $minutes . 'm';
                                @endphp
                            </p>
                            <p class="text-sm text-green-700">
                                Resolution: 
                                @php
                                    $hours = floor($avgResolutionTime / 60);
                                    $minutes = $avgResolutionTime % 60;
                                    echo $hours . 'h ' . $minutes . 'm';
                                @endphp
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- SLA by Category -->
                <div class="mb-8 page-break-before">
                    <h2 class="text-lg font-semibold mb-4">SLA Performance by Category</h2>
                    
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Tickets
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        SLA Breaches
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Breach Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($categoryPerformance as $category)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $category->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->total_tickets }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $category->breached_tickets }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $category->breach_rate > 20 ? 'bg-red-100 text-red-800' : 
                                               ($category->breach_rate > 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $category->breach_rate }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- SLA by Agent -->
                <div class="mb-8 page-break-before">
                    <h2 class="text-lg font-semibold mb-4">SLA Performance by Agent</h2>
                    
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Agent
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Tickets
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Response Breaches
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Resolution Breaches
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Overall Breach Rate
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($agentPerformance as $agent)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $agent->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $agent->total_tickets }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $agent->response_breached }}
                                        <span class="text-xs text-gray-400">
                                            ({{ $agent->total_tickets > 0 ? round(($agent->response_breached / $agent->total_tickets) * 100, 2) : 0 }}%)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $agent->resolution_breached }}
                                        <span class="text-xs text-gray-400">
                                            ({{ $agent->total_tickets > 0 ? round(($agent->resolution_breached / $agent->total_tickets) * 100, 2) : 0 }}%)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $agent->breach_rate > 20 ? 'bg-red-100 text-red-800' : 
                                               ($agent->breach_rate > 10 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $agent->breach_rate }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- SLA Over Time -->
                <div class="page-break-before">
                    <h2 class="text-lg font-semibold mb-4">SLA Performance Over Time</h2>
                    
                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Week
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Tickets
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Response Breaches
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Resolution Breaches
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Overall Performance
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($timePerformance as $week)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $week->week_label }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $week->total }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $week->response_breached }} ({{ $week->response_rate }}%)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $week->resolution_breached }} ({{ $week->resolution_rate }}%)
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $overallRate = ($week->response_rate + $week->resolution_rate) / 2;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="h-2.5 rounded-full {{ $overallRate > 20 ? 'bg-red-500' : ($overallRate > 10 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                                    style="width: {{ min(100, max(5, 100 - $overallRate)) }}%"></div>
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ round(100 - $overallRate, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 