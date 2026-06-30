<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::table('products')->truncate();
        DB::table('providers')->truncate();
        DB::table('users')->truncate();

        DB::statement('PRAGMA foreign_keys = ON');

        // --- Seller-Accounts (User je Provider, gleiche Reihenfolge wie unten) ---
        $sellerEmails = [
            'mueller@baumarkt-test.de', 'pw@profiwerkzeug-test.de', 'koch@holzfachhandel-test.de',
            'lange@farbenwelt-test.de', 'info@baustoffzentrum-test.de', 'depot@werkzeugdepot-test.de',
            'krause@sanitaer-test.de', 'goertz@elektro-test.de', 'rheinland@baumarkt-test.de',
            'mehr@holzundmehr-test.de', 'fischer@farben-test.de', 'bergisch@baudiscount-test.de',
        ];

        $userIds = [];
        foreach ($sellerEmails as $i => $email) {
            $userIds[] = DB::table('users')->insertGetId([
                'first_name' => 'Verkäufer',
                'last_name' => 'Test ' . ($i + 1),
                'name' => 'Verkäufer Test ' . ($i + 1),
                'email' => $email,
                'password' => Hash::make('Passwort123!'),
                'role' => 'seller',
                'email_verified_at' => now(),
                'verification_code' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // Login für alle Test-Seller: <email oben> / Passwort123!

        $providers = [
            ['name'=>'Baumarkt Müller',        'initials'=>'BM','address'=>'Musterstraße 12',   'city'=>'Wuppertal', 'zip'=>'42103','lat'=>51.2562,'lng'=>7.1508,'type'=>'Baumarkt',  'since'=>1998,'verified'=>1],
            ['name'=>'Profi-Werkzeug GmbH',    'initials'=>'PW','address'=>'Industrieweg 5',    'city'=>'Wuppertal', 'zip'=>'42107','lat'=>51.2637,'lng'=>7.1362,'type'=>'Fachhandel','since'=>2005,'verified'=>1],
            ['name'=>'Holzfachhandel Koch',    'initials'=>'HK','address'=>'Waldstraße 88',     'city'=>'Solingen',  'zip'=>'42651','lat'=>51.1800,'lng'=>7.0800,'type'=>'Fachhandel','since'=>1987,'verified'=>1],
            ['name'=>'Farbenwelt Lange',       'initials'=>'FL','address'=>'Bergstraße 23',     'city'=>'Remscheid', 'zip'=>'42853','lat'=>51.1800,'lng'=>7.1900,'type'=>'Fachhandel','since'=>2001,'verified'=>0],
            ['name'=>'Baustoff-Zentrum NRW',   'initials'=>'BZ','address'=>'Kölner Straße 44',  'city'=>'Düsseldorf','zip'=>'40210','lat'=>51.2217,'lng'=>6.7762,'type'=>'Baumarkt',  'since'=>1995,'verified'=>1],
            ['name'=>'Werkzeug Depot Dortmund','initials'=>'WD','address'=>'Ruhrstraße 17',     'city'=>'Dortmund',  'zip'=>'44135','lat'=>51.5139,'lng'=>7.4653,'type'=>'Fachhandel','since'=>2010,'verified'=>1],
            ['name'=>'Sanitär Krause',         'initials'=>'SK','address'=>'Hafenweg 3',        'city'=>'Essen',     'zip'=>'45127','lat'=>51.4556,'lng'=>7.0116,'type'=>'Fachhandel','since'=>2003,'verified'=>0],
            ['name'=>'Elektro Görtz',          'initials'=>'EG','address'=>'Kaiserstraße 91',   'city'=>'Duisburg',  'zip'=>'47051','lat'=>51.4344,'lng'=>6.7623,'type'=>'Fachhandel','since'=>1999,'verified'=>1],
            ['name'=>'Baumarkt Rheinland',     'initials'=>'BR','address'=>'Aachener Straße 12','city'=>'Köln',      'zip'=>'50667','lat'=>50.9333,'lng'=>6.9500,'type'=>'Baumarkt',  'since'=>2000,'verified'=>1],
            ['name'=>'Holz & Mehr GmbH',       'initials'=>'HM','address'=>'Fichtenweg 7',      'city'=>'Wuppertal', 'zip'=>'42119','lat'=>51.2700,'lng'=>7.1800,'type'=>'Fachhandel','since'=>2008,'verified'=>1],
            ['name'=>'Farben Fischer',         'initials'=>'FF','address'=>'Südstraße 55',      'city'=>'Wuppertal', 'zip'=>'42277','lat'=>51.2800,'lng'=>7.2100,'type'=>'Fachhandel','since'=>1994,'verified'=>0],
            ['name'=>'Bau-Discount Bergisch',  'initials'=>'BD','address'=>'Hauptstraße 100',   'city'=>'Remscheid', 'zip'=>'42853','lat'=>51.1850,'lng'=>7.1950,'type'=>'Baumarkt',  'since'=>2012,'verified'=>1],
        ];

        $providerIds = [];
        foreach ($providers as $i => $p) {
            $providerIds[] = DB::table('providers')->insertGetId(array_merge($p, [
                'user_id' => $userIds[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Helper: $pid($n) gibt die echte DB-id des n-ten Providers zurück (1-indexiert wie vorher)
        $pid = fn($n) => $providerIds[$n - 1];

        $products = [
            // Baumaterial — je Artikel min. 1 Alternative bei anderem Anbieter
            ['provider_id'=>$pid(1), 'title'=>'Klinker-Restposten',          'price'=>89.00,  'icon'=>'🧱','category'=>'Baumaterial','description'=>'Ca. 200 Stück verfügbar. Geeignet für Außenwände und Gartenmauern.','stock'=>200],
            ['provider_id'=>$pid(9), 'title'=>'Klinker Sortiment rot',       'price'=>82.50,  'icon'=>'🧱','category'=>'Baumaterial','description'=>'Klinkersteine, rot gebrannt. 180 Stück verfügbar.','stock'=>180],

            ['provider_id'=>$pid(1), 'title'=>'Betonmischer 140L',          'price'=>180.00, 'icon'=>'🏗️','category'=>'Baumaterial','description'=>'Elektrischer Betonmischer 140L, 500W. Einwandfreier Zustand.','stock'=>1],
            ['provider_id'=>$pid(12),'title'=>'Betonmischer 125L gebraucht','price'=>149.00, 'icon'=>'🏗️','category'=>'Baumaterial','description'=>'Elektrischer Betonmischer 125L. Gebraucht, voll funktionsfähig.','stock'=>2],

            ['provider_id'=>$pid(5), 'title'=>'Pflastersteine grau 10x10',  'price'=>0.89,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Betonpflaster, 6cm stark. Abnahme ab 100 Stück. Restposten.','stock'=>850],
            ['provider_id'=>$pid(9), 'title'=>'Pflastersteine grau 10x20',  'price'=>0.95,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Betonpflaster, 8cm stark. Geringe Lagermenge.','stock'=>200],

            ['provider_id'=>$pid(5), 'title'=>'Dachziegel Tonrot',          'price'=>1.20,   'icon'=>'🏠','category'=>'Baumaterial','description'=>'Gebrauchte Dachziegel, gereinigt. Ca. 300 Stück.','stock'=>300],
            ['provider_id'=>$pid(1), 'title'=>'Dachziegel Tonrot Neu',      'price'=>1.65,   'icon'=>'🏠','category'=>'Baumaterial','description'=>'Neue Dachziegel, Tonrot. Palettenware.','stock'=>120],

            ['provider_id'=>$pid(9), 'title'=>'Ytong-Steine 20cm',          'price'=>3.50,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Porenbetonsteine, Restpalette. 40 Stück.','stock'=>40],
            ['provider_id'=>$pid(5), 'title'=>'Ytong-Steine 24cm',          'price'=>3.95,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Porenbetonsteine, stärkere Variante. 30 Stück.','stock'=>30],

            ['provider_id'=>$pid(9), 'title'=>'Betonpflaster Anthrazit',    'price'=>1.10,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Verlegeformat 20x10cm, 6cm. 500 Stück.','stock'=>500],
            ['provider_id'=>$pid(12),'title'=>'Betonpflaster Anthrazit XL', 'price'=>1.25,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'Großformat 30x15cm, 8cm. 250 Stück.','stock'=>250],

            ['provider_id'=>$pid(12),'title'=>'Zementsäcke 25kg',           'price'=>6.50,   'icon'=>'🏗️','category'=>'Baumaterial','description'=>'Portlandzement CEM I. 30 Säcke verfügbar.','stock'=>30],
            ['provider_id'=>$pid(1), 'title'=>'Zementsäcke 25kg Schnellbinder', 'price'=>7.80, 'icon'=>'🏗️','category'=>'Baumaterial','description'=>'Schnellbindender Zement. 20 Säcke verfügbar.','stock'=>20],

            ['provider_id'=>$pid(12),'title'=>'Kalksandstein KS-L',         'price'=>2.80,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'20x20x10cm. Restposten 150 Stück.','stock'=>150],
            ['provider_id'=>$pid(9), 'title'=>'Kalksandstein KS-L 24cm',    'price'=>3.10,   'icon'=>'🧱','category'=>'Baumaterial','description'=>'24x24x10cm, höhere Tragfähigkeit. 90 Stück.','stock'=>90],

            // Werkzeug
            ['provider_id'=>$pid(2), 'title'=>'Bohrmaschine 18V',           'price'=>64.00,  'icon'=>'⚙️','category'=>'Werkzeug','description'=>'Akkubohrmaschine 18V, 2 Akkus, Ladegerät inkl. Leichte Gebrauchsspuren.','stock'=>3],
            ['provider_id'=>$pid(6), 'title'=>'Bohrmaschine 18V Pro',       'price'=>72.00,  'icon'=>'⚙️','category'=>'Werkzeug','description'=>'Akkubohrmaschine 18V, bürstenlos, 1 Akku. Neuwertig.','stock'=>5],

            ['provider_id'=>$pid(2), 'title'=>'Winkelschleifer 125mm',      'price'=>35.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'900W, inkl. 5 Trennscheiben. Gebraucht, technisch einwandfrei.','stock'=>2],
            ['provider_id'=>$pid(6), 'title'=>'Winkelschleifer 125mm Akku', 'price'=>59.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'Akkubetrieben, 18V, ohne Akku. Wenig genutzt.','stock'=>3],

            ['provider_id'=>$pid(6), 'title'=>'Kreissäge 1400W',            'price'=>95.00,  'icon'=>'🪚','category'=>'Werkzeug','description'=>'185mm Sägeblatt, Parallelanschlag. Guter Zustand.','stock'=>1],
            ['provider_id'=>$pid(2), 'title'=>'Kreissäge 1200W kompakt',    'price'=>78.00,  'icon'=>'🪚','category'=>'Werkzeug','description'=>'165mm Sägeblatt, leichtgängig. Wenig benutzt.','stock'=>2],

            ['provider_id'=>$pid(6), 'title'=>'Stichsäge Bosch PST',        'price'=>42.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'650W, inkl. 3 Sägeblätter. Wenig genutzt.','stock'=>2],
            ['provider_id'=>$pid(12),'title'=>'Stichsäge Makita',           'price'=>49.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'720W, variable Hubzahl, inkl. Koffer.','stock'=>1],

            ['provider_id'=>$pid(6), 'title'=>'Schlagbohrmaschine 750W',    'price'=>38.00,  'icon'=>'⚙️','category'=>'Werkzeug','description'=>'Kabelgebunden, 13mm Bohrfutter. Sehr guter Zustand.','stock'=>4],
            ['provider_id'=>$pid(9), 'title'=>'Schlagbohrmaschine 850W',    'price'=>45.00,  'icon'=>'⚙️','category'=>'Werkzeug','description'=>'Kabelgebunden, 16mm Bohrfutter, stärker. Neuwertig.','stock'=>2],

            ['provider_id'=>$pid(2), 'title'=>'Werkzeugkoffer 108-teilig',  'price'=>55.00,  'icon'=>'🧰','category'=>'Werkzeug','description'=>'Kompletter Satz, Koffer leicht beschädigt, Werkzeug vollständig.','stock'=>1],
            ['provider_id'=>$pid(6), 'title'=>'Werkzeugkoffer 94-teilig',   'price'=>48.00,  'icon'=>'🧰','category'=>'Werkzeug','description'=>'Kleinerer Satz, originalverpackt.','stock'=>4],

            ['provider_id'=>$pid(12),'title'=>'Niveau-Laser Selbstnivell.', 'price'=>70.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'Kreuzlinienlaser, Stativ inkl. Restposten Ausstellung.','stock'=>3],
            ['provider_id'=>$pid(9), 'title'=>'Niveau-Laser Profi',        'price'=>95.00,  'icon'=>'🔧','category'=>'Werkzeug','description'=>'Kreuzlinienlaser mit Empfänger, größere Reichweite.','stock'=>1],

            ['provider_id'=>$pid(9), 'title'=>'Schubkarre 80L verzinkt',    'price'=>28.00,  'icon'=>'🛒','category'=>'Werkzeug','description'=>'Stahl, luftbereifter Reifen. 5 Stück.','stock'=>5],
            ['provider_id'=>$pid(2), 'title'=>'Schubkarre 100L verzinkt',   'price'=>34.00,  'icon'=>'🛒','category'=>'Werkzeug','description'=>'Größeres Volumen, doppelter Reifen. 3 Stück.','stock'=>3],

            // Holz & Platten
            ['provider_id'=>$pid(3), 'title'=>'Zaunlatten 180cm',           'price'=>3.20,   'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Kiefernholz, druckimprägniert. VB ab 50 Stück.','stock'=>320],
            ['provider_id'=>$pid(10),'title'=>'Zaunlatten 150cm',           'price'=>2.60,   'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Kiefernholz, kürzere Variante. 200 Stück.','stock'=>200],

            ['provider_id'=>$pid(3), 'title'=>'OSB-Platten 18mm',           'price'=>14.50,  'icon'=>'📦','category'=>'Holz & Platten','description'=>'250x125cm, Zuschnitt möglich. 40 Platten.','stock'=>40],
            ['provider_id'=>$pid(10),'title'=>'OSB-Platten 15mm',           'price'=>11.90,  'icon'=>'📦','category'=>'Holz & Platten','description'=>'250x125cm, dünnere Variante. 60 Platten.','stock'=>60],

            ['provider_id'=>$pid(10),'title'=>'Douglasie Terrassendielen',  'price'=>8.90,   'icon'=>'🪵','category'=>'Holz & Platten','description'=>'4m lang, 145x21mm. 60 Stück, Restposten.','stock'=>60],
            ['provider_id'=>$pid(3), 'title'=>'Douglasie Terrassendielen B-Ware', 'price'=>6.50, 'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Leichte optische Mängel, sonst einwandfrei.','stock'=>40],

            ['provider_id'=>$pid(10),'title'=>'Spanplatten 16mm 260x60',    'price'=>11.00,  'icon'=>'📦','category'=>'Holz & Platten','description'=>'Melaminbeschichtet weiß. 25 Stück.','stock'=>25],
            ['provider_id'=>$pid(12),'title'=>'Spanplatten 19mm 260x60',    'price'=>13.50,  'icon'=>'📦','category'=>'Holz & Platten','description'=>'Stärkere Variante, melaminbeschichtet.','stock'=>15],

            ['provider_id'=>$pid(10),'title'=>'Leimholzplatte Buche 80x40', 'price'=>24.00,  'icon'=>'🪵','category'=>'Holz & Platten','description'=>'18mm stark, beidseitig geschliffen. 8 Stück.','stock'=>8],
            ['provider_id'=>$pid(3), 'title'=>'Leimholzplatte Eiche 80x40', 'price'=>32.00,  'icon'=>'🪵','category'=>'Holz & Platten','description'=>'19mm stark, hochwertiger.','stock'=>5],

            ['provider_id'=>$pid(3), 'title'=>'Dachlatten 3x5cm 4m',        'price'=>2.10,   'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Nadelholz, gehobelt. 200 Stück.','stock'=>200],
            ['provider_id'=>$pid(10),'title'=>'Dachlatten 4x6cm 4m',        'price'=>2.85,   'icon'=>'🪵','category'=>'Holz & Platten','description'=>'Stärkeres Profil, sägerau. 150 Stück.','stock'=>150],

            ['provider_id'=>$pid(12),'title'=>'Gipsfaserplatten 12.5mm',    'price'=>9.80,   'icon'=>'📦','category'=>'Holz & Platten','description'=>'120x60cm. Restposten 35 Platten.','stock'=>35],
            ['provider_id'=>$pid(3), 'title'=>'Gipsfaserplatten 10mm',      'price'=>8.20,   'icon'=>'📦','category'=>'Holz & Platten','description'=>'120x60cm, dünnere Variante. 50 Platten.','stock'=>50],

            // Farben & Lacke
            ['provider_id'=>$pid(4), 'title'=>'Wandfarbe Weiß 10L',         'price'=>22.00,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Dispersionsfarbe, innen. Restbestand aus Geschäftsauflösung.','stock'=>12],
            ['provider_id'=>$pid(11),'title'=>'Wandfarbe Weiß 5L Premium',  'price'=>16.50,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Hohe Deckkraft, kleineres Gebinde.','stock'=>20],

            ['provider_id'=>$pid(4), 'title'=>'Fassadenfarbe Grau 15L',     'price'=>48.00,  'icon'=>'🪣','category'=>'Farben & Lacke','description'=>'Wetterschutzfarbe, außen. 8 Eimer.','stock'=>8],
            ['provider_id'=>$pid(11),'title'=>'Fassadenfarbe Weiß 12L',     'price'=>39.00,  'icon'=>'🪣','category'=>'Farben & Lacke','description'=>'Wetterschutzfarbe, außen, kleineres Gebinde.','stock'=>10],

            ['provider_id'=>$pid(11),'title'=>'Holzlasur Nussbaum 5L',      'price'=>19.00,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Seidenmatt, für innen und außen. 6 Stück.','stock'=>6],
            ['provider_id'=>$pid(4), 'title'=>'Holzlasur Eiche 5L',         'price'=>21.50,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Seidenmatt, UV-beständig.','stock'=>4],

            ['provider_id'=>$pid(11),'title'=>'Grundierung Universal 10L',  'price'=>27.00,  'icon'=>'🪣','category'=>'Farben & Lacke','description'=>'Tiefengrund für Innen- und Außenbereich. 4 Eimer.','stock'=>4],
            ['provider_id'=>$pid(4), 'title'=>'Grundierung Universal 5L',   'price'=>15.50,  'icon'=>'🪣','category'=>'Farben & Lacke','description'=>'Kleineres Gebinde, gleiche Eigenschaften.','stock'=>9],

            ['provider_id'=>$pid(11),'title'=>'Buntlack RAL 3000 750ml',    'price'=>8.50,   'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Feuerrot, glänzend. Restposten 15 Dosen.','stock'=>15],
            ['provider_id'=>$pid(4), 'title'=>'Buntlack RAL 5010 750ml',    'price'=>9.20,   'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Enzianblau, glänzend.','stock'=>10],

            ['provider_id'=>$pid(4), 'title'=>'Parkettlack seidenmatt 5L',  'price'=>34.00,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Wasserbasis, geruchsarm. 3 Stück.','stock'=>3],
            ['provider_id'=>$pid(11),'title'=>'Parkettlack glänzend 2.5L',  'price'=>19.00,  'icon'=>'💧','category'=>'Farben & Lacke','description'=>'Wasserbasis, kleineres Gebinde.','stock'=>6],

            // Sanitär
            ['provider_id'=>$pid(1), 'title'=>'Badarmatur Chrom',           'price'=>45.00,  'icon'=>'🚿','category'=>'Sanitär','description'=>'Einhebelmischer, neuwertig, originalverpackt.','stock'=>4],
            ['provider_id'=>$pid(7), 'title'=>'Badarmatur Matt Schwarz',    'price'=>59.00,  'icon'=>'🚿','category'=>'Sanitär','description'=>'Einhebelmischer, modernes Design.','stock'=>2],

            ['provider_id'=>$pid(7), 'title'=>'Duschkabine 80x80cm',        'price'=>220.00, 'icon'=>'🚿','category'=>'Sanitär','description'=>'Viertelkreis, Klarglas 5mm. Ausstellungsstück, minimale Kratzer.','stock'=>1],
            ['provider_id'=>$pid(1), 'title'=>'Duschkabine 90x90cm',        'price'=>260.00, 'icon'=>'🚿','category'=>'Sanitär','description'=>'Viertelkreis, Klarglas 6mm. Neuwertig.','stock'=>1],

            ['provider_id'=>$pid(7), 'title'=>'Waschbecken Keramik weiß',   'price'=>55.00,  'icon'=>'🪠','category'=>'Sanitär','description'=>'Unterbauwaschbecken 60cm. Neuwertig, keine Schäden.','stock'=>3],
            ['provider_id'=>$pid(5), 'title'=>'Waschbecken Keramik oval',   'price'=>62.00,  'icon'=>'🪠','category'=>'Sanitär','description'=>'Aufsatzwaschbecken 50cm, ovale Form.','stock'=>2],

            ['provider_id'=>$pid(7), 'title'=>'Heizkörper Typ 22 600x800',  'price'=>68.00,  'icon'=>'🌡️','category'=>'Sanitär','description'=>'Kompaktheizkörper, weiß. Originalverpackt. 5 Stück.','stock'=>5],
            ['provider_id'=>$pid(1), 'title'=>'Heizkörper Typ 21 500x800',  'price'=>58.00,  'icon'=>'🌡️','category'=>'Sanitär','description'=>'Kompaktheizkörper, kleinere Bauform.','stock'=>3],

            ['provider_id'=>$pid(7), 'title'=>'WC-Sitz Softclose weiß',     'price'=>28.00,  'icon'=>'🪠','category'=>'Sanitär','description'=>'Absenkautomatik, abnehmbar. 4 Stück.','stock'=>4],
            ['provider_id'=>$pid(5), 'title'=>'WC-Sitz Softclose schwarz',  'price'=>32.00,  'icon'=>'🪠','category'=>'Sanitär','description'=>'Absenkautomatik, matt schwarz.','stock'=>2],

            ['provider_id'=>$pid(5), 'title'=>'Spültisch Edelstahl 2-Bec.', 'price'=>89.00,  'icon'=>'🚿','category'=>'Sanitär','description'=>'60x50cm, Unterbauspüle mit Ablauf. 2 Stück.','stock'=>2],
            ['provider_id'=>$pid(7), 'title'=>'Spültisch Edelstahl 1-Bec.', 'price'=>59.00,  'icon'=>'🚿','category'=>'Sanitär','description'=>'50x40cm, einfache Spüle mit Ablauf.','stock'=>3],

            // Elektro
            ['provider_id'=>$pid(2), 'title'=>'LED Feuchtraumleuchte 60cm', 'price'=>18.00,  'icon'=>'💡','category'=>'Elektro','description'=>'18W, IP65. Restposten 20 Stück.','stock'=>20],
            ['provider_id'=>$pid(8), 'title'=>'LED Feuchtraumleuchte 120cm','price'=>26.00,  'icon'=>'💡','category'=>'Elektro','description'=>'36W, IP65, längere Variante.','stock'=>10],

            ['provider_id'=>$pid(8), 'title'=>'Unterputz-Steckdosen 10er',  'price'=>14.00,  'icon'=>'🔌','category'=>'Elektro','description'=>'Schuko, weiß, inkl. Rahmen. 10 Sets.','stock'=>10],
            ['provider_id'=>$pid(9), 'title'=>'Unterputz-Steckdosen 5er',   'price'=>8.00,   'icon'=>'🔌','category'=>'Elektro','description'=>'Schuko, anthrazit, inkl. Rahmen.','stock'=>15],

            ['provider_id'=>$pid(8), 'title'=>'Kabelkanal 40x25mm 2m',      'price'=>3.80,   'icon'=>'📦','category'=>'Elektro','description'=>'Weiß, selbstklebend. 50 Stück.','stock'=>50],
            ['provider_id'=>$pid(5), 'title'=>'Kabelkanal 60x40mm 2m',      'price'=>5.20,   'icon'=>'📦','category'=>'Elektro','description'=>'Größerer Querschnitt, weiß.','stock'=>30],

            ['provider_id'=>$pid(8), 'title'=>'Sicherungsautomat B16 3-pol','price'=>22.00,  'icon'=>'⚡','category'=>'Elektro','description'=>'Leitungsschutzschalter, neuwertig. 8 Stück.','stock'=>8],
            ['provider_id'=>$pid(9), 'title'=>'Sicherungsautomat B10 1-pol','price'=>9.50,   'icon'=>'⚡','category'=>'Elektro','description'=>'Leitungsschutzschalter, einpolig.','stock'=>12],

            ['provider_id'=>$pid(8), 'title'=>'NYM-J 3x1.5mm² 50m',         'price'=>65.00,  'icon'=>'🔌','category'=>'Elektro','description'=>'Installationskabel, neu, originalverpackt. 3 Rollen.','stock'=>3],
            ['provider_id'=>$pid(2), 'title'=>'NYM-J 5x1.5mm² 50m',         'price'=>89.00,  'icon'=>'🔌','category'=>'Elektro','description'=>'Installationskabel, 5-adrig.','stock'=>2],

            ['provider_id'=>$pid(9), 'title'=>'LED Baustrahler 50W',        'price'=>32.00,  'icon'=>'💡','category'=>'Elektro','description'=>'IP65, Schwenkarm, inkl. Stecker. 6 Stück.','stock'=>6],
            ['provider_id'=>$pid(8), 'title'=>'LED Baustrahler 100W',       'price'=>52.00,  'icon'=>'💡','category'=>'Elektro','description'=>'IP65, mit Stativ, stärkere Leistung.','stock'=>3],

            ['provider_id'=>$pid(5), 'title'=>'Verlängerungskabel 25m',     'price'=>29.00,  'icon'=>'🔌','category'=>'Elektro','description'=>'3x1.5mm², Schuko, orange. 4 Stück.','stock'=>4],
            ['provider_id'=>$pid(9), 'title'=>'Verlängerungskabel 10m',     'price'=>14.00,  'icon'=>'🔌','category'=>'Elektro','description'=>'3x1.5mm², Schuko, kürzere Variante.','stock'=>8],
        ];

        foreach ($products as $p) {
            DB::table('products')->insert(array_merge($p, ['created_at'=>now(),'updated_at'=>now()]));
        }

        DB::table('users')->insert([
            'first_name' => 'Käufer',
            'last_name' => 'Test',
            'name' => 'Käufer Test',
            'email' => 'kaeufer@test.de',
            'password' => Hash::make('Passwort123!'),
            'role' => 'buyer',
            'email_verified_at' => now(),
            'verification_code' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}