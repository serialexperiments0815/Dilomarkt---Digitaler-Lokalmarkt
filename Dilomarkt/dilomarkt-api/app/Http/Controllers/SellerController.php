<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SellerController extends Controller
{
    /** Resolve the authenticated seller from the Bearer token. */
    private function auth(Request $request): ?User
    {
        $token = $request->bearerToken();
        if (! $token) return null;
        return User::where('api_token', $token)->where('role', 'seller')->first();
    }

    /** Map common German zip codes to coordinates. */
    private function resolvePlz(string $plz): array
    {
        $coords = [
            '42103' => [51.2562, 7.1508], '42107' => [51.2637, 7.1362],
            '42119' => [51.2700, 7.1800], '42277' => [51.2800, 7.2100],
            '42651' => [51.1800, 7.0800], '42853' => [51.1800, 7.1900],
            '40210' => [51.2217, 6.7762], '44135' => [51.5139, 7.4653],
            '45127' => [51.4556, 7.0116], '47051' => [51.4344, 6.7623],
            '50667' => [50.9333, 6.9500],
        ];
        return $coords[$plz] ?? [51.2562, 7.1508];
    }

    /** GET /api/seller/shop — returns shop info + products for the authenticated seller. */
    public function getShop(Request $request)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $shop = DB::table('providers')->where('user_id', $user->id)->first();
        if (! $shop) {
            return response()->json(['shop' => null, 'products' => []]);
        }

        $products = DB::table('products')->where('provider_id', $shop->id)->orderBy('id')->get();
        return response()->json(['shop' => $shop, 'products' => $products]);
    }

    /** POST /api/seller/shop — create or update the seller's shop profile. */
    public function upsertShop(Request $request)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'type'    => ['required', 'in:Fachhandel,Baumarkt'],
            'address' => ['required', 'string', 'max:200'],
            'city'    => ['required', 'string', 'max:100'],
            'zip'     => ['required', 'string', 'max:10'],
        ]);

        [$lat, $lng] = $this->resolvePlz($request->zip);
        $initials    = strtoupper(mb_substr($request->name, 0, 2));
        $existing    = DB::table('providers')->where('user_id', $user->id)->first();

        if ($existing) {
            DB::table('providers')->where('id', $existing->id)->update([
                'name'       => $request->name,
                'type'       => $request->type,
                'address'    => $request->address,
                'city'       => $request->city,
                'zip'        => $request->zip,
                'lat'        => $lat,
                'lng'        => $lng,
                'initials'   => $initials,
                'updated_at' => now(),
            ]);
            $shop = DB::table('providers')->find($existing->id);
        } else {
            $id = DB::table('providers')->insertGetId([
                'user_id'    => $user->id,
                'name'       => $request->name,
                'type'       => $request->type,
                'address'    => $request->address,
                'city'       => $request->city,
                'zip'        => $request->zip,
                'lat'        => $lat,
                'lng'        => $lng,
                'initials'   => $initials,
                'since'      => now()->year,
                'verified'   => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $shop = DB::table('providers')->find($id);
        }

        return response()->json(['message' => 'Shop gespeichert.', 'shop' => $shop]);
    }

    /** POST /api/seller/products — add a new product to the seller's shop. */
    public function addProduct(Request $request)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $shop = DB::table('providers')->where('user_id', $user->id)->first();
        if (! $shop) return response()->json(['message' => 'Kein Shop eingerichtet.'], 422);

        $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'icon'        => ['required', 'string', 'max:8'],
        ]);

        $id = DB::table('products')->insertGetId([
            'provider_id' => $shop->id,
            'title'       => $request->title,
            'price'       => $request->price,
            'category'    => $request->category,
            'stock'       => $request->stock,
            'description' => $request->description,
            'icon'        => $request->icon,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['message' => 'Produkt hinzugefügt.', 'product' => DB::table('products')->find($id)]);
    }

    /** PUT /api/seller/products/{id} — update one of the seller's products. */
    public function updateProduct(Request $request, int $id)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $shop = DB::table('providers')->where('user_id', $user->id)->first();
        if (! $shop) return response()->json(['message' => 'Kein Shop eingerichtet.'], 422);

        $product = DB::table('products')->where('id', $id)->where('provider_id', $shop->id)->first();
        if (! $product) return response()->json(['message' => 'Produkt nicht gefunden.'], 404);

        $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string'],
            'stock'       => ['required', 'integer', 'min:0'],
            'description' => ['required', 'string'],
            'icon'        => ['required', 'string', 'max:8'],
        ]);

        DB::table('products')->where('id', $id)->update([
            'title'       => $request->title,
            'price'       => $request->price,
            'category'    => $request->category,
            'stock'       => $request->stock,
            'description' => $request->description,
            'icon'        => $request->icon,
            'updated_at'  => now(),
        ]);

        return response()->json(['message' => 'Produkt aktualisiert.', 'product' => DB::table('products')->find($id)]);
    }

    /** DELETE /api/seller/products/{id} — remove one of the seller's products. */
    public function deleteProduct(Request $request, int $id)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $shop = DB::table('providers')->where('user_id', $user->id)->first();
        if (! $shop) return response()->json(['message' => 'Kein Shop eingerichtet.'], 422);

        $deleted = DB::table('products')->where('id', $id)->where('provider_id', $shop->id)->delete();
        if (! $deleted) return response()->json(['message' => 'Produkt nicht gefunden.'], 404);

        return response()->json(['message' => 'Produkt gelöscht.']);
    }

    /** GET /api/seller/messages — returns all buyer conversations for this seller's shop. */
    public function getMessages(Request $request)
    {
        $user = $this->auth($request);
        if (! $user) return response()->json(['message' => 'Nicht autorisiert.'], 401);

        $shop = DB::table('providers')->where('user_id', $user->id)->first();
        if (! $shop) return response()->json(['conversations' => []]);

        // All messages for this shop ordered newest first
        $rows = DB::table('messages')
            ->join('products', 'messages.product_id', '=', 'products.id')
            ->leftJoin('users', 'messages.buyer_id', '=', 'users.id')
            ->where('messages.provider_id', $shop->id)
            ->select(
                'messages.product_id',
                'messages.buyer_id',
                'messages.body as last_message',
                'messages.sender as last_sender',
                'messages.created_at as last_at',
                'products.title as product_title',
                DB::raw("COALESCE(users.first_name || ' ' || users.last_name, 'Unbekannt') as buyer_name")
            )
            ->orderBy('messages.created_at', 'desc')
            ->get();

        // Keep only the most recent message per (product_id, buyer_id) pair
        $seen = [];
        $conversations = [];
        foreach ($rows as $row) {
            $key = $row->product_id . '_' . $row->buyer_id;
            if (! isset($seen[$key])) {
                $seen[$key] = true;
                $conversations[] = $row;
            }
        }

        return response()->json(['conversations' => $conversations]);
    }
}
