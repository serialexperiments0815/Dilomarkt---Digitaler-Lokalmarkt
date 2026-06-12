<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $providers = [
            ['name'=>'Baumarkt Müller',    'initials'=>'BM','address'=>'Musterstraße 12', 'city'=>'Wuppertal','zip'=>'42103','lat'=>51.2562,'lng'=>7.1508,'type'=>'Baumarkt',  'since'=>1998,'verified'=>1],
            ['name'=>'Profi-Werkzeug GmbH','initials'=>'PW','address'=>'Industrieweg 5',  'city'=>'Wuppertal','zip'=>'42107','lat'=>51.2637,'lng'=>7.1362,'type'=>'Fachhandel','since'=>2005,'verified'=>1],
            ['name'=>'Holzfachhandel Koch', 'initials'=>'HK','address'=>'Waldstraße 88',   'city'=>'Solingen', 'zip'=>'42651','lat'=>51.1800,'lng'=>7.0800,'type'=>'Fachhandel','since'=>1987,'verified'=>1],
            ['name'=>'Farbenwelt Lange',   'initials'=>'FL','address'=>'Bergstraße 23',   'city'=>'Remscheid','zip'=>'42853','lat'=>51.1800,'lng'=>7.1900,'type'=>'Fachhandel','since'=>2001,'verified'=>0],
        ];
        foreach ($providers as $p) {
            DB::table('providers')->insert(array_merge($p, ['created_at'=>now(),'updated_at'=>now()]));
        }

        $products = [
            ['provider_id'=>1,'title'=>'Klinker-Restposten',    'price'=>89.00, 'icon'=>'🧱','category'=>'Baumaterial',   'description'=>'Ca. 200 Stück verfügbar. Geeignet für Außenwände und Gartenmauern.','stock'=>200],
            ['provider_id'=>2,'title'=>'Bohrmaschine 18V',      'price'=>64.00, 'icon'=>'⚙️','category'=>'Werkzeug',      'description'=>'Akkubohrmaschine 18V, 2 Akkus, Ladegerät inkl. Leichte Gebrauchsspuren.','stock'=>3],
            ['provider_id'=>3,'title'=>'Zaunlatten 180 cm',     'price'=>3.20,  'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Kiefernholz, druckimprägniert. VB ab 50 Stück, Einzelabnahme möglich.','stock'=>320],
            ['provider_id'=>4,'title'=>'Wandfarbe Weiß 10L',    'price'=>22.00, 'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Dispersionsfarbe, innen. Restbestand aus Geschäftsauflösung.','stock'=>12],
            ['provider_id'=>1,'title'=>'Betonmischer 140L',     'price'=>180.00,'icon'=>'🏗️','category'=>'Baumaterial',   'description'=>'Elektrischer Betonmischer 140L, 500W. Einwandfreier Zustand.','stock'=>1],
            ['provider_id'=>2,'title'=>'Winkelschleifer 125mm', 'price'=>35.00, 'icon'=>'🔧','category'=>'Werkzeug',      'description'=>'900W, inkl. 5 Trennscheiben. Gebraucht, technisch einwandfrei.','stock'=>2],
            ['provider_id'=>3,'title'=>'OSB-Platten 18mm',      'price'=>14.50, 'icon'=>'📦','category'=>'Holz & Platten','description'=>'250x125cm, Zuschnitt möglich. 40 Platten.','stock'=>40],
            ['provider_id'=>1,'title'=>'Badarmatur Chrom',      'price'=>45.00, 'icon'=>'🚿','category'=>'Sanitär',       'description'=>'Einhebelmischer, neuwertig, originalverpackt.','stock'=>4],
            ['provider_id'=>2,'title'=>'LED Feuchtraumleuchte', 'price'=>18.00, 'icon'=>'💡','category'=>'Elektro',       'description'=>'60cm, 18W, IP65. Restposten, 20 Stück.','stock'=>20],
        ];
        foreach ($products as $p) {
            DB::table('products')->insert(array_merge($p, ['created_at'=>now(),'updated_at'=>now()]));
        }
    }
}
