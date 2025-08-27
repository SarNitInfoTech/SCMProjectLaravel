<div class="container mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">All Notifications</h1>
        @if($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black text-sm font-medium px-4 py-2 rounded">Mark All Read</button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="bg-white p-6 rounded shadow text-center text-gray-500">
            No notifications found.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($notifications as $notification)
                <div class="flex items-center bg-white p-4 shadow rounded">
                    <div class="avatar avatar-md me-2 avatar-rounded flex-shrink-0 {{ $notification->bg_color ?? 'bg-primary' }} text-white h-12 w-12 flex items-center justify-center">
                        <i class="{{ $notification->icon ?? 'la la-bell' }} text-xl"></i>
                    </div>
                    <div class="ms-3 flex-1">
                        <a href="{{ $notification->link ?? '#' }}" class="text-gray-800 font-semibold hover:text-primary">
                            {{ $notification->title }}
                        </a>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ $notification->link ?? '#' }}" class="text-gray-400 hover:text-gray-700">
                            <i class="las la-angle-right text-lg"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>