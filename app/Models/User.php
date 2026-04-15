<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject  // ← ADD THIS
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory,HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'token',
        'token_expires',
        'google_token',
        'google_id',
        'google_refresh_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'token',
        'google_token' => 'encrypted',
        'google_refresh_token' => 'encrypted',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

        public function tasks(){
        return $this->HasMany(Task::class);
    }

    //---->chat used function

    public function sentMessage()
    {
        return $this->hasMany(ChatMessage::class,'from_user_id');
    }
    public function recievedMessage()
    {
        return $this->hasMany(ChatMessage::class,'to_user_id');
    }
    public function getUnreadMessagesCount()
    {
        return $this->recievedMessage()->where('is_read',false)->count();
    }

    public function getConversations()
    {
        $userId = $this->id;

        $conversationUsers=ChatMessage::where('from_user_id',$userId)
        ->orWhere('to_user_id',$userId)
        ->with(['sender','reciever'])
        ->get()
        ->map(function($message)use ($userId){
            return $message->from_user_id==$userId  ?
            $message->receiver: $message->sender;
        })
        ->unique('id')
        ->values();

        $conversations=[];

        foreach ($conversations as $user) {
            $lastMessage =ChatMessage::where(function($q) use ($userId,$user){
                $q->where('from_user_id',$userId)->where('to_user_id');
                })->orWhere(function($q) use ($userId,$user){
                    $q->where('from_user_id',$userId)->where('to_user_id',$userId);
                    })->latest()->first();

                    $unreadCount = ChatMessage::where('from_user_id',$user->id)
                    ->where('to_user_id',$userId)
                    ->where("is_read",false)->count();
                    $conversations[]=[
                        'user'=>$user,
                        'last_message'=>$lastMessage,
                        'unread_count'=>$unreadCount,
                    ];
                }
                return $conversations;
        }
    

    //chat used function<---
}