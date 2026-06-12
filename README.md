# Dilomarkt – Digitaler Lokalmarkt

Dilomarkt ist ein spezialisierter Marktplatz, der lokale Anbieter von Baumaterialien und Werkzeugen mit regionalen Nachfragern verbindet. Entwickelt von Studenten, adressiert die Plattform die Ineffizienzen großer, unspezialisierter Portale und stärkt lokale Wertschöpfungsketten.

## Tech Stack

- **Server:** PHP mit Laravel-Framework
- **Datenbank:** SQLite
- **Client:** Vue.js (HTML, CSS, JavaScript)
- **Client-Server-Kommunikation:** REST API
- **Weiteres:** Responsive Webdesign

## Installation

```bash
git clone https://github.com/serialexperiments0815/Dilomarkt---Digitaler-Lokalmarkt/
```

### Backend (Laravel)

```bash
cd Dilomarkt---Digitaler-Lokalmarkt/Dilomarkt/dilomarkt-api
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

### Frontend (Vue)

```bash
cd Dilomarkt---Digitaler-Lokalmarkt/Dilomarkt
npm install
```

## Verwendung

**Terminal 1 – Backend starten:**
```bash
cd dilomarkt-api
php artisan serve
```
API läuft auf `http://localhost:8000`

**Terminal 2 – Frontend starten:**
```bash
cd Dilomarkt
npm run dev
```
App läuft auf `http://localhost:5173`

## API Endpunkte

| Method | Endpunkt | Beschreibung |
|--------|----------|--------------|
| GET | `/api/products` | Produkte mit Geo-Filter (PLZ, Radius, Kategorie) |
| GET | `/api/products/{id}` | Einzelprodukt mit Preisvergleich |
| GET | `/api/providers` | Alle Anbieter |
| GET | `/api/providers/{id}` | Anbieterprofil mit Statistiken |

### Query Parameter für `/api/products`

| Parameter | Beispiel | Beschreibung |
|-----------|----------|--------------|
| `plz` | `42103` | Standort (NRW) |
| `radius` | `20` | Umkreis in km |
| `q` | `Klinker` | Suchbegriff |
| `category` | `Werkzeug` | Kategorie |
| `max_price` | `100` | Maximaler Preis |
| `type` | `Fachhandel` | Anbietertyp |
