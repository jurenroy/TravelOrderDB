<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnreadMessage extends Model
{
    use HasFactory;
    // Specify the table name if it's not the plural form of the model name
    protected $table = 'unread_messages';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // Disable timestamps if you don't want to use created_at and updated_at
    public $timestamps = true;

    // Define which attributes are mass assignable
    protected $fillable = [
        'name_id',
        'message_id',
    ];
}
