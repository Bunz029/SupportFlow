@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
    case '64':
        $width = 'w-64';
        break;
    case '96':
        $width = 'w-96';
        break;
    default:
        $width = 'w-' . $width;
        break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click.stop="open = !open">
        <button class="flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 hover:border-blue-300 focus:outline-none focus:text-blue-700 focus:border-blue-300 transition duration-150 ease-in-out">
            <div class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-blue-600 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center font-semibold shadow-sm">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </div>
        </button>
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
         style="display: none;"
         @click.outside="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }} shadow-xl">
            <div class="max-h-96 overflow-y-auto">
                @forelse(auth()->user()->unreadNotifications()->take(5)->get() as $notification)
                    <div class="border-b last:border-0 {{ $notification->read_at ? 'bg-gray-50' : 'bg-white' }}">
                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-gray-800 hover:bg-blue-50 transition duration-150">
                                <div class="flex justify-between">
                                    <p class="font-medium">{{ $notification->data['message'] }}</p>
                                    <span class="text-xs text-gray-600 ml-2">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                                @if(isset($notification->data['comment_preview']))
                                    <p class="text-xs text-gray-700 mt-1">{{ Str::limit($notification->data['comment_preview'], 50) }}</p>
                                @endif
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-4 py-3 text-sm text-gray-700">
                        No unread notifications
                    </div>
                @endforelse

                <div class="border-t px-4 py-3 bg-gray-50">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View all notifications
                        </a>
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-gray-700 hover:text-gray-900 font-medium">
                                Mark all as read
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 