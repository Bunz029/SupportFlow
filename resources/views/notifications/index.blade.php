@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Notifications') }}
        </h2>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @if($notifications->count() > 0)
                            <div class="mb-4 flex justify-between items-center">
                                <div class="flex space-x-2">
                                    <a href="{{ route('notifications.index') }}" class="px-3 py-1 rounded-md text-sm {{ request()->query('filter') === null ? 'bg-blue-100 text-blue-800' : 'text-gray-800 hover:bg-gray-100' }}">
                                        All
                                    </a>
                                    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="px-3 py-1 rounded-md text-sm {{ request()->query('filter') === 'unread' ? 'bg-blue-100 text-blue-800' : 'text-gray-800 hover:bg-gray-100' }}">
                                        Unread
                                    </a>
                                    <a href="{{ route('notifications.index', ['filter' => 'read']) }}" class="px-3 py-1 rounded-md text-sm {{ request()->query('filter') === 'read' ? 'bg-blue-100 text-blue-800' : 'text-gray-800 hover:bg-gray-100' }}">
                                        Read
                                    </a>
                                </div>
                                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                        Mark all as read
                                    </button>
                                </form>
                            </div>
                            <div class="space-y-4">
                                @foreach($notifications as $notification)
                                    <div class="p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-white border-l-4 border-blue-500' }} rounded-lg shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $notification->data['message'] }}
                                                    </p>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $notification->created_at->format('M d, Y h:i A') }}
                                                    </span>
                                                </div>
                                                @if(isset($notification->data['comment_preview']))
                                                    <p class="mt-1 text-sm text-gray-500">
                                                        {{ $notification->data['comment_preview'] }}
                                                    </p>
                                                @endif
                                                @if(isset($notification->data['old_status']) && isset($notification->data['new_status']))
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        Status changed from <span class="font-medium">{{ $notification->data['old_status'] }}</span> to <span class="font-medium">{{ $notification->data['new_status'] }}</span>
                                                    </p>
                                                @endif
                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="ml-4">
                                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                                        @if($notification->read_at)
                                                            View
                                                        @else
                                                            Mark as read & View
                                                        @endif
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                <div class="mt-4">
                                    {{ $notifications->links() }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p class="mt-2 text-gray-500">No notifications to display</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
   
</x-app-layout> 