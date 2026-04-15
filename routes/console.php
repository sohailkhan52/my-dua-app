<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Chat extends Component
{
    public $users = [];
    public $messages = [];
    public $selectedUser = null;
    public $messageText = '';
    public $conversations = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadUsers();
        $this->loadConversations();
        $this->unreadCount = Auth::user()->getUnreadMessagesCount();
    }

    public function loadUsers()
    {
        $this->users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'email')
            ->get();
    }

    public function loadConversations()
    {
        $this->conversations = Auth::user()->getConversations();
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
        $this->markMessagesAsRead();
    }

    public function loadMessages()
    {
        if (!$this->selectedUser) return;

        $messages = ChatMessage::where(function($q) {
            $q->where('from_user_id', Auth::id())
              ->where('to_user_id', $this->selectedUser->id);
        })->orWhere(function($q) {
            $q->where('from_user_id', $this->selectedUser->id)
              ->where('to_user_id', Auth::id());
        })->with('sender')->orderBy('created_at', 'asc')->get();

        $this->messages = $messages;
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:1000',
            'selectedUser' => 'required',
        ]);

        $message = ChatMessage::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $this->selectedUser->id,
            'message' => $this->messageText,
            'is_read' => false,
        ]);

        $message->load('sender');
        
        // Broadcast the message
        broadcast(new MessageSent($message));
        
        $this->messageText = '';
        $this->loadMessages();
        $this->loadConversations();
        
        $this->dispatch('message-sent');
    }

    public function markMessagesAsRead()
    {
        if (!$this->selectedUser) return;

        ChatMessage::where('from_user_id', $this->selectedUser->id)
            ->where('to_user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $this->unreadCount = Auth::user()->getUnreadMessagesCount();
        $this->loadConversations();
        
        $this->dispatch('messages-read');
    }

    public function getListeners()
    {
        return [
            'echo-private:user.' . Auth::id() . ',message.sent' => 'handleNewMessage',
            'refreshChat' => '$refresh',
        ];
    }

    public function handleNewMessage($payload)
    {
        // Check if message is from selected user
        if ($this->selectedUser && $payload['from_user_id'] == $this->selectedUser->id) {
            $this->loadMessages();
            $this->markMessagesAsRead();
        } else {
            $this->loadConversations();
            $this->unreadCount = Auth::user()->getUnreadMessagesCount();
            $this->dispatch('new-message-notification', $payload);
        }
        
        $this->dispatch('message-received');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}