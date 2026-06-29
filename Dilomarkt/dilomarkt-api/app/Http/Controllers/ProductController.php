<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller {

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $R = 6371;
        $dLat = ($lat2 - $lat1) * M_PI / 180;
        $dLng = ($lng2 - $lng1) * M_PI / 180;
        $a = sin($dLat/2)**2 + cos($lat1 * M_PI/180) * cos($lat2 * M_PI/180) * sin($dLng/2)**2;
        return round($R * 2 * asin(sqrt($a)), 1);
    }

    private function resolvePlz(string $plz): array {
        $coords = [
            '42103' => [51.2562, 7.1508],
            '42107' => [51.2637, 7.1362],
            '42119' => [51.2700, 7.1800],
            '42277' => [51.2800, 7.2100],
            '42651' => [51.1800, 7.0800],
            '42853' => [51.1800, 7.1900],
            '40210' => [51.2217, 6.7762],
            '44135' => [51.5139, 7.4653],
            '45127' => [51.4556, 7.0116],
            '47051' => [51.4344, 6.7623],
            '50667' => [50.9333, 6.9500],
        ];
        return $coords[$plz] ?? [51.2562, 7.1508];
    }

    public function index(Request $request) {
        [$lat, $lng] = $this->resolvePlz($request->input('plz', '42103'));
        $radius   = (float) $request->input('radius', 20);
        $q        = $request->input('q', '');
        $category = $request->input('category', '');
        $maxPrice = (float) $request->input('max_price', 9999);
        $type     = $request->input('type', '');

        $query = DB::table('products')
            ->join('providers', 'products.provider_id', '=', 'providers.id')
            ->select(
                'products.id', 'products.title', 'products.price',
                'products.icon', 'products.category', 'products.description', 'products.stock',
                'providers.id as provider_id', 'providers.name as provider',
                'providers.type as provider_type', 'providers.verified',
                'providers.lat', 'providers.lng'
            )
            ->where('products.price', '<=', $maxPrice);

        if ($q)        $query->where('products.title', 'like', "%$q%");
        if ($category) $query->where('products.category', $category);
        if ($type)     $query->where('providers.type', $type);

        $results = $query->get()->map(function ($p) use ($lat, $lng, $radius) {
            $p->distance = $this->haversine($lat, $lng, $p->lat, $p->lng);
            unset($p->lat, $p->lng);
            return $p;
        })->filter(fn($p) => $p->distance <= $radius)
          ->sortBy('distance')
          ->values();

        return response()->json($results);
    }

    public function show(int $id) {
        $product = DB::table('products')
            ->join('providers', 'products.provider_id', '=', 'providers.id')
            ->select('products.*', 'providers.name as provider', 'providers.id as provider_id',
                     'providers.address', 'providers.city', 'providers.zip',
                     'providers.type as provider_type', 'providers.verified',
                     'providers.since', 'providers.initials')
            ->where('products.id', $id)
            ->first();

        if (!$product) return response()->json(['error' => 'Not found'], 404);

        $alternatives = DB::table('products')
            ->join('providers', 'products.provider_id', '=', 'providers.id')
            ->select('products.id', 'products.title', 'products.price', 'providers.name as provider')
            ->where('products.category', $product->category)
            ->where('products.id', '!=', $id)
            ->where('products.provider_id', '!=', $product->provider_id)  // <- das fehlte
            ->orderBy('products.price')
            ->get();

        return response()->json(['product' => $product, 'alternatives' => $alternatives]);
    }
}
