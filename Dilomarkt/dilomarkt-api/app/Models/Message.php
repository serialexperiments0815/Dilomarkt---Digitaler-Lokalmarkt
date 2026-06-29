<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['product_id', 'provider_id', 'buyer_id', 'sender', 'body'];
}