<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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


public function myConversations(Request $request)
{
    $token = $request->bearerToken();
    $user = $token ? User::where('api_token', $token)->first() : null;

    if (!$user) {
        return response()->json(['message' => 'Nicht eingeloggt.'], 401);
    }

    $role = $request->query('role', 'buyer');

    if ($role === 'seller') {
        $provider = DB::table('providers')->where('user_id', $user->id)->first();
        if (!$provider) {
            return response()->json([]);
        }
        $filterColumn = 'provider_id';
        $filterValue = $provider->id;
    } else {
        $filterColumn = 'buyer_id';
        $filterValue = $user->id;
    }

    $threads = DB::table('messages')
        ->select('product_id', 'provider_id', 'buyer_id')
        ->where($filterColumn, $filterValue)
        ->distinct()
        ->get();

    $result = $threads->map(function ($t) {
        $last = DB::table('messages')
            ->where('product_id', $t->product_id)
            ->where('buyer_id', $t->buyer_id)
            ->orderByDesc('created_at')
            ->first();

        $product = DB::table('products')->find($t->product_id);
        $provider = DB::table('providers')->find($t->provider_id);

        return [
            'product_id'    => $t->product_id,
            'product_title' => $product->title ?? null,
            'provider_id'   => $t->provider_id,
            'provider_name' => $provider->name ?? null,
            'buyer_id'      => $t->buyer_id,
            'last_message'  => $last->body ?? null,
            'last_sender'   => $last->sender ?? null,
            'last_at'       => $last->created_at ?? null,
        ];
    })->sortByDesc('last_at')->values();

    return response()->json($result);
}
}