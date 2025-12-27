# Fuel Tracker - Gépjármű Nyilvántartó Rendszer

## 📋 Áttekintés

A Fuel Tracker egy modern, full-stack webes alkalmazás járművek, üzemanyag-fogyasztás, szerviz tevékenységek és biztosítási adatok nyilvántartására. Ez egy **személyes gyakorló projekt**, amelyet saját használatra és a modern webes technológiák elsajátítására fejlesztettem.

Az alkalmazás Laravel 12 backend és Vue 3 frontend technológiákra épül, Inertia.js-sel és Jetstream autentikációval, lehetővé téve a legújabb fejlesztői eszközök és best practice-ek gyakorlati alkalmazását.

## ✨ Funkciók

### 🚗 Járműkezelés
- Járművek nyilvántartása (név, rendszám, típus, évjárat)
- Kilométeróra állás követése
- Átlagfogyasztás megadása
- Olajcsere ciklus nyilvántartása (km és év alapján)
- Fékfolyadék csere ciklus követése
- Műszaki érvényesség nyilvántartása

### ⛽ Üzemanyag Nyilvántartás
- Tankolási adatok rögzítése
- Üzemanyag mennyiség
- Költségek követése
- Kilométeróra állás mentése minden tankolásnál
- Járműhöz kapcsolt fogyasztási adatok

### 🔧 Szerviz Tevékenységek
- Szerviz munkák dokumentálása
- Dátum és kilométeróra állás rögzítése
- Költségek nyilvántartása
- Szerviz típus és leírás megadása

### 🛡️ Biztosítás Kezelés
- Biztosítási adatok tárolása
- Érvényesség követése
- Biztosítási típusok kezelése
- Költségek rögzítése

### 📊 Statisztikák
- Gépjármű-specifikus statisztikák
- Fogyasztási adatok elemzése
- Költségek összesítése
- Szűrhető időszakok szerint

### 👥 Felhasználókezelés
- Laravel Jetstream alapú hitelesítés
- Admin és felhasználói szerepkörök
- Admin felület felhasználók kezeléséhez

## 🛠️ Technológiai Stack

### Backend
- **Laravel 12** - PHP framework
- **Laravel Jetstream** - Autentikáció és profilkezelés
- **Laravel Sanctum** - API token autentikáció
- **Inertia.js** - Modern monolitikus architektúra
- **MySQL/SQLite** - Adatbázis

### Frontend
- **Vue 3** - JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework

## 📦 Telepítés

### Előfeltételek
- PHP 8.2 vagy újabb
- Composer
- Node.js és npm
- MySQL/SQLite adatbázis

### Telepítési Lépések

1. **Repository klónozása**
```bash
git clone <repository-url>
cd fuel_tracker_vue
```

2. **Composer függőségek telepítése**
```bash
composer install
```

3. **NPM csomagok telepítése**
```bash
npm install
```

4. **Környezeti változók beállítása**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Adatbázis konfiguráció**
Szerkeszd a `.env` fájlt és állítsd be az adatbázis kapcsolatot:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fuel_tracker
DB_USERNAME=root
DB_PASSWORD=
```

6. **Adatbázis migráció**
```bash
php artisan migrate
```

7. **Build frontend assets**
```bash
npm run build
```

### Gyors Telepítés (egyben)
```bash
composer setup
```

## 🚀 Használat

### Fejlesztői Mód

Az alkalmazás fejlesztői módban való futtatásához (egyetlen paranccsal indul a szerver, queue worker, log viewer és Vite):
```bash
composer dev
```

Ez a parancs egyszerre indítja el:
- Laravel fejlesztői szervert (http://localhost:8000)
- Queue worker-t
- Log viewer-t (Laravel Pail)
- Vite dev szervert

### Manuális Indítás

**Backend szerver indítása:**
```bash
php artisan serve
```

**Frontend development szerver:**
```bash
npm run dev
```

**Production build:**
```bash
npm run build
```

## 🧪 Tesztelés

```bash
composer test
```
vagy
```bash
php artisan test
```

## 📁 Projekt Struktúra

```
fuel_tracker_vue/
├── app/
│   ├── Actions/          # Jetstream akciók
│   ├── Http/
│   │   └── Controllers/  # API és web kontrollerek
│   │       ├── AdminUserController.php
│   │       ├── CarController.php
│   │       ├── FuelController.php
│   │       ├── ServiceController.php
│   │       ├── InsuranceController.php
│   │       └── StatisticsController.php
│   ├── Models/           # Eloquent modellek
│   │   ├── Car.php
│   │   ├── Fuel.php
│   │   ├── Service.php
│   │   ├── Insurance.php
│   │   └── User.php
│   └── Providers/
├── database/
│   ├── migrations/       # Adatbázis migrációk
│   ├── seeders/          # Adatbázis seeders
│   └── factories/        # Model factories
├── resources/
│   ├── js/               # Vue komponensek
│   ├── css/              # Stíluslapok
│   └── views/            # Blade template-ek
├── routes/
│   ├── web.php           # Web route-ok
│   ├── api.php           # API route-ok
│   └── console.php       # Artisan parancsok
└── tests/                # Unit és feature tesztek
```

## 🔐 Middleware

Az alkalmazás a következő middleware-eket használja:
- `auth:sanctum` - Sanctum autentikáció
- `verified` - Email verifikáció ellenőrzése
- `checkRole` - Admin szerepkör ellenőrzése

## 🗄️ Adatbázis Séma

### Főbb Táblák
- **users** - Felhasználók (2FA támogatással)
- **cars** - Járművek adatai
- **fuels** - Tankolási nyilvántartás
- **services** - Szerviz tevékenységek
- **insurances** - Biztosítási információk

## 🔧 Konfigurációs Fájlok

- `config/app.php` - Alkalmazás alapkonfiguráció
- `config/database.php` - Adatbázis beállítások
- `config/jetstream.php` - Jetstream konfiguráció
- `config/fortify.php` - Fortify autentikáció
- `config/sanctum.php` - Sanctum API token beállítások
- `tailwind.config.js` - Tailwind CSS konfiguráció
- `vite.config.js` - Vite build konfiguráció

## 👥 Felhasználói Szerepkörök

### Admin
- Felhasználók kezelése (létrehozás, módosítás, törlés)
- Jelszavak módosítása
- Összes funkció elérhető

### Felhasználó
- Saját járművek kezelése
- Tankolási adatok rögzítése
- Szerviz nyilvántartás
- Biztosítási adatok kezelés
- Statisztikák megtekintése

## 📝 Projekt Jellege

Ez egy **személyes, oktatási célú projekt**, amelyet saját használatra és a Laravel, Vue.js, valamint modern full-stack fejlesztési technikák gyakorlására hoztam létre. A projekt célja egyrészt a gyakorlati problémamegoldás (gépjármű nyilvántartás), másrészt a folyamatos tanulás és fejlődés a webfejlesztés területén.


## 🤝 Közreműködés

Ez egy személyes gyakorló projekt, amelyet oktatási és tanulási célokból készítettem. Külső közreműködést jelenleg nem fogadok, mivel a projekt célja a saját fejlődésem és készségeim fejlesztése.

## 📧 Kapcsolat

Kérdések esetén nyiss egy issue-t a repository-ban.

---

**Utolsó frissítés:** 2025. december 27.