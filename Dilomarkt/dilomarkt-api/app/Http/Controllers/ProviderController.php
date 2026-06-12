<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ProviderController extends Controller {

    public function index() {
        return response()->json(DB::table('providers')->get());
    }

    public function show(int $id) {
        $provider = DB::table('providers')->where('id', $id)->first();
        if (!$provider) return response()->json(['error' => 'Not found'], 404);

        $products = DB::table('products')
            ->where('provider_id', $id)
            ->get();

        $stats = [
            'product_count' => $products->count(),
            'total_stock'   => $products->sum('stock'),
            'min_price'     => $products->min('price'),
        ];

        return response()->json(['provider' => $provider, 'products' => $products, 'stats' => $stats]);
    }
}
