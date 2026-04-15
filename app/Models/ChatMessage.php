<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table='chat_messages';
    protected $fillable=[
        'from_user_id',
        'to_user_id',
        'message',
        'is_read',
        'read_at',
    ];
    protected $casts = [
        "is_read"=>'boolean',
        'read_at'=>'datetime'
    ];

    public function sender(){
        return $this->belongsTo(User::class,'from_user_id');
    }

    public function reciever(){
        return $this->belongsTo(User::class,'to_user_id');
    }

    public function markedAsRead()
    {
        if(!$this->is_read){
            $this->update([
                'is_read'=>true,
                'read_at'=>now(),
            ]);
        }
    }
}
