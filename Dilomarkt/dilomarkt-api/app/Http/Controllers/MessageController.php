<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = Message::where('product_id', $request->product_id)
            ->where('buyer_id', $request->buyer_id)
            ->orderBy('created_at')
            ->get();
        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $msg = Message::create($request->validate([
            'product_id'  => 'required|exists:products,id',
            'provider_id' => 'required|exists:providers,id',
            'buyer_id'    => 'required|integer',
            'sender'      => 'required|in:buyer,seller',
            'body'        => 'required|string|max:1000',
        ]));
        return response()->json($msg, 201);
    }
}