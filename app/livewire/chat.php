<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    // List of all users available for chatting (excluding current user)
    public $users = [];
    
    // Collection of messages for the currently selected conversation
    public $messages = [];
    
    // Currently selected user for active chat conversation
    public $selectedUser = null;
    
    // Current message text being composed by the user
    public $messageText = '';
    
    // List of recent conversations with unread message counts
    public $conversations = [];
    
    // Total count of unread messages across all conversations
    public $unreadCount = 0;
    
    // Total unread messages across all conversations
    public $unread = "";

    public function mount()
    {
        // Initialize component by loading users and unread message count
        $this->loadUsers();
        $this->loadConversations();
        $this->unreadCount = Auth::user()->getUnreadMessagesCount() ?? 0;
        $this->unread =ChatMessage::where('to_user_id', auth()->id())
          ->where('is_read', false)->get();
    }

    /**
     * Load all users except the currently authenticated user
     */
    public function loadUsers()
    {
        $this->users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->get();
    }

    /**
     * Load conversation list (simplified placeholder)
     */
    public function loadConversations()
    {
        if (auth()->check()) {
            // Just assign directly
            $this->conversations = auth()->user()->getConversations();
        }
    }

    /**
     * Handle user selection from the user list
     * 
     * @param int $userId The ID of the selected user
     */
    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
    }
/**
 * Mark  message as read when it becomes visible
 */
public function markMessageAsRead($messageId)
{
    $message = ChatMessage::find($messageId);
    
    if ($message && $message->to_user_id == Auth::id() && !$message->is_read) {
        $message->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        // Broadcast to sender
        try {
            broadcast(new MessageRead($message->from_user_id, Auth::id()));
        } catch (\Exception $e) {
            \Log::error('MessageRead broadcast failed: ' . $e->getMessage());
        }
        
        $this->loadMessages();
        $this->unreadCount = Auth::user()->getUnreadMessagesCount();
        $this->loadConversations();
    }
}
    /**
     * Load all messages between current user and selected user
     */
    public function loadMessages()
    {
        // Guard clause: return if no user is selected
        if (!$this->selectedUser) {
            return;
        }

        // Fetch messages where current user is either sender or receiver with selected user
        $this->messages = ChatMessage::where(function ($q) {
            $q->where('from_user_id', Auth::id())
                ->where('to_user_id', $this->selectedUser->id);
        })->orWhere(function ($q) {
            $q->where('from_user_id', $this->selectedUser->id)
                ->where('to_user_id', Auth::id());
        })->with('sender')->orderBy('created_at', 'asc')->get();
        
    }

    /**
     * Send a new message to the selected user
     */
    public function sendMessage()
    {
        // Simplified validation: ensure message text is not empty and a user is selected
        if (empty($this->messageText)) {
            return;
        }
        
        if (!$this->selectedUser) {
            return;
        }

        // Create and store the new message
        $message = ChatMessage::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $this->selectedUser->id,
            'message' => $this->messageText,
            'is_read' => false,
        ]);

        // Load sender relationship for broadcasting
        $message->load('sender');

        // Broadcast the message to the recipient via WebSockets
        try {
            broadcast(new MessageSent($message));
        } catch (\Exception $e) {
            // Log error but continue - don't block message sending if broadcasting fails
            \Log::error('Broadcast failed: ' . $e->getMessage());
        }

        // Reset message input and refresh UI
        $this->messageText = '';
        $this->loadMessages();
        $this->loadConversations();

        // Dispatch event to notify other components
        $this->dispatch('message-sent');
    }

    /**
     * Define event listeners for real-time updates
     * 
     * @return array List of event listeners
     */
    public function getListeners()
    {
        try {
            return [
                // Listen for incoming messages on current user's private channel
                'echo-private:user.' . Auth::id() . ',.message.sent' => 'handleNewMessage',
                // Listen for read receipts — fires when the other user reads our messages
                'echo-private:user.' . Auth::id() . ',.message.read' => 'handleMessageRead',
                'refreshChat' => '$refresh',
            ];
        } catch (\Exception $e) {
            return [
                'refreshChat' => '$refresh',
            ];
        }
    }

    /**
     * Handle incoming real-time messages
     * 
     * @param array $payload The message payload from WebSocket
     */
    public function handleNewMessage($payload)
    {
        // Check if message is from selected user
        if ($this->selectedUser && isset($payload['from_user_id']) && $payload['from_user_id'] == $this->selectedUser->id) {
            // If message is from currently selected user, load messages and mark as read
            $this->loadMessages();
        } else {
            // Otherwise just update conversations and unread count
            $this->loadConversations();
            $this->unreadCount = Auth::user()->getUnreadMessagesCount();
            $this->dispatch('new-message-notification', $payload);
        }

        // Dispatch event to notify about received message
        $this->dispatch('message-received');
    }

    /**
     * Handle real-time read receipts — fires when the recipient reads our messages.
     *
     * @param array $payload  Contains from_user_id (us) and to_user_id (the reader)
     */
    public function handleMessageRead($payload)
    {
        // Reload messages so the ✓✓ Read indicator updates instantly for the sender
        if ($this->selectedUser && isset($payload['to_user_id']) && $payload['to_user_id'] == $this->selectedUser->id) {
            $this->loadMessages();
        }
    }

    /**
     * Render the chat component view
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.chat');
    }
}