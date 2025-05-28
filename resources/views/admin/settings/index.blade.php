@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Configure system-wide settings and defaults</p>
    </div>

    <div class="bg-white shadow-sm rounded-lg divide-y divide-gray-200">
        <!-- SLA Settings -->
        <div class="p-6">
            <form action="{{ route('admin.settings.update-sla') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <h2 class="text-lg font-medium text-gray-900">SLA Configuration</h2>
                    <p class="mt-1 text-sm text-gray-500">Set response time targets for different ticket priorities</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="high_priority_sla" class="block text-sm font-medium text-gray-700">High Priority Response Time (hours)</label>
                        <input type="number" name="high_priority_sla" id="high_priority_sla" 
                               value="{{ $settings['high_priority_sla'] ?? 2 }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="medium_priority_sla" class="block text-sm font-medium text-gray-700">Medium Priority Response Time (hours)</label>
                        <input type="number" name="medium_priority_sla" id="medium_priority_sla" 
                               value="{{ $settings['medium_priority_sla'] ?? 8 }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="low_priority_sla" class="block text-sm font-medium text-gray-700">Low Priority Response Time (hours)</label>
                        <input type="number" name="low_priority_sla" id="low_priority_sla" 
                               value="{{ $settings['low_priority_sla'] ?? 24 }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="resolution_time_sla" class="block text-sm font-medium text-gray-700">Default Resolution Time (hours)</label>
                        <input type="number" name="resolution_time_sla" id="resolution_time_sla" 
                               value="{{ $settings['resolution_time_sla'] ?? 72 }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update SLA Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Notification Settings -->
        <div class="p-6">
            <form action="{{ route('admin.settings.update-notifications') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div>
                    <h2 class="text-lg font-medium text-gray-900">Notification Settings</h2>
                    <p class="mt-1 text-sm text-gray-500">Configure system notification preferences</p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_on_ticket_creation" id="notify_on_ticket_creation"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ $settings['notify_on_ticket_creation'] ?? true ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="notify_on_ticket_creation" class="text-sm font-medium text-gray-700">
                                Notify agents on new ticket creation
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_on_sla_breach" id="notify_on_sla_breach"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ $settings['notify_on_sla_breach'] ?? true ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="notify_on_sla_breach" class="text-sm font-medium text-gray-700">
                                Send alerts for SLA breaches
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_on_feedback" id="notify_on_feedback"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ $settings['notify_on_feedback'] ?? true ? 'checked' : '' }}>
                        </div>
                        <div class="ml-3">
                            <label for="notify_on_feedback" class="text-sm font-medium text-gray-700">
                                Notify on customer feedback submission
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Update Notification Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 