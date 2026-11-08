# PBKK Minggu 10

## How to run

1. Clone repository
```
git clone https://github.com/Haaqill/PBKK-minggu10.git
cd PBKK-minggu10
```

2. Install dependencies
```
composer install
npm install
```

3. Buat file `.env` dari `.env.example` dan generate application key
```
cp .env.example .env
php artisan key:generate
```

4. Run migration dan seeder
```
php artisan migrate:fresh --seed
```

5. Build frontend asset
```
npm run build
```

6. Jalankan server
```
php artisan serve
```

Aplikasi web bisa diakses melalui `http://127.0.0.1:8000`
