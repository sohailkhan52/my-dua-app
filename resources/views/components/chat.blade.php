<div>
    <div class="card shadow-sm" style="height: 600px;">
        <!-- Chat Header -->
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">💬 Real-time Chat</h5>
                <!-- Display unread message count badge if there are any unread messages -->
                @if($unreadCount > 0)
                    <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                @endif
            </div>
        </div>

        <div class="card-body p-0 h-100">
            <div class="row g-0 h-100">
                <!-- Users List Sidebar -->
                <div class="col-md-4 col-lg-3 border-end bg-light h-100" style="overflow-y: auto;">
                    <div class="p-3">
                        <h6 class="fw-bold mb-3">Users</h6>
                        <div class="list-group list-group-flush">
                            <!-- Loop through all available users and display them -->
                            @foreach($users as $user)
                                <a href="javascript:void(0)" 
                                   class="list-group-item list-group-item-action {{ $selectedUser && $selectedUser->id == $user->id ? 'active' : '' }}"
                                   wire:click="selectUser({{ $user->id }})">
                                    <div class="d-flex align-items-center">
                                        <!-- User Avatar (Initials) -->
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle bg-secondary bg-opacity-25 text-dark d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <strong>{{ strtoupper(substr($user->name, 0, 1)) }}</strong>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="fw-bold">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Recent Conversations Section -->
                        @if(count($conversations) > 0)
                            <h6 class="fw-bold mt-4 mb-3">Recent Chats</h6>
                            <div class="list-group list-group-flush">
                                <!-- Loop through recent conversations and display them -->
                                @foreach($conversations as $conv)
                                    <a href="javascript:void(0)" 
                                       class="list-group-item list-group-item-action {{ $selectedUser && $selectedUser->id == $conv['user']->id ? 'active' : '' }}"
                                       wire:click="selectUser({{ $conv['user']->id }})">
                                        <div class="d-flex align-items-center">
                                            <!-- Conversation User Avatar with Unread Badge -->
                                            <div class="flex-shrink-0 position-relative">
                                                <div class="rounded-circle bg-secondary bg-opacity-25 text-dark d-flex align-items-center justify-content-center" 
                                                     style="width: 40px; height: 40px;">
                                                    <strong>{{ strtoupper(substr($conv['user']->name, 0, 1)) }}</strong>
                                                </div>
                                                <!-- Unread message count badge for this conversation -->
                                                @if($conv['unread_count'] > 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $conv['unread_count'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="fw-bold">{{ $conv['user']->name }}</div>
                                                <!-- Last message preview -->
                                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">
                                                    {{ Str::limit($conv['last_message']?->message ?? 'No messages', 30) }}
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="col-md-8 col-lg-9 h-100 d-flex flex-column">
                    @if($selectedUser)
                        <!-- Chat Header with Selected User Info -->
                        <div class="p-3 border-bottom bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="fs-5">{{ $selectedUser->name }}</strong>
                                </div>
                                <!-- Button to manually mark all messages as read -->
                                <button wire:click="markMessagesAsRead" class="btn btn-sm btn-outline-info">
                                    Mark as Read
                                </button>
                            </div>
                        </div>

                        <!-- Messages Container (Scrollable Area) -->
                        <div class="flex-grow-1 p-3 bg-light" style="overflow-y: auto;" id="messagesContainer">
                            <!-- Loop through all messages in the current conversation -->
                            @foreach($messages as $message)
                                <!-- Message bubble - aligned right for current user, left for other users -->
                                <div class="mb-3 d-flex {{ $message->from_user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="{{ $message->from_user_id == auth()->id() ? 'bg-primary text-white' : 'bg-white' }} rounded-3 p-3 shadow-sm" 
                                         style="max-width: 70%;">
                                        <!-- Message Header: Sender Name and Time -->
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="small">{{ $message->sender->name }}</strong>
                                            <small class="{{ $message->from_user_id == auth()->id() ? 'text-white-50' : 'text-muted' }} ms-2">
                                                {{ $message->created_at->format('H:i') }}
                                            </small>
                                        </div>
                                        <!-- Message Content -->
                                        <div class="mb-1">
                                            {{ $message->message }}
                                        </div>
                                        <!-- Message Status Indicator (Only for current user's messages) -->
                                        @if($message->from_user_id == auth()->id())
                                            <div class="text-end mt-1">
                                                @if($message->is_read)
                                                    <small class="{{ $message->from_user_id == auth()->id() ? 'text-white-50' : 'text-success' }}">✓✓ Read</small>
                                                @else
                                                    <small class="{{ $message->from_user_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">✓ Sent</small>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input Area -->
                        <div class="p-3 border-top bg-white">
                            <div class="input-group">
                                <input type="text" 
                                       wire:model="messageText" 
                                       wire:keydown.enter="sendMessage"
                                       placeholder="Type your message..."
                                       class="form-control">
                                <button wire:click="sendMessage" class="btn btn-primary">
                                    Send
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- No User Selected - Display placeholder message -->
                        <div class="h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center text-muted">
                                <i class="fas fa-comments fa-4x mb-3"></i>
                                <p class="mb-0">Select a user to start chatting</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Auto-scroll messages container to the bottom
    function scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    // Scroll on initial load
    document.addEventListener('DOMContentLoaded', scrollToBottom);

    // Scroll whenever Livewire re-renders the component
    document.addEventListener('livewire:navigated', scrollToBottom);
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => {
            requestAnimationFrame(scrollToBottom);
        });
    });
</script>