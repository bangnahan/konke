#!/bin/bash
# ==============================================================================
# Script Otomatisasi Deploy CloudPanel: budayariau.jelatix.com
# ==============================================================================
set -e

# Cek apakah file .env sudah ada, jika belum salin dari .env.example
if [ ! -f .env ]; then
    echo "⚙️  [0/6] Menyiapkan file .env dari .env.example..."
    cp .env.example .env
fi

echo "🚀 [1/6] Mengaktifkan Mode Maintenance Sementara..."
php artisan down --secret="adminganteng-bypass" --render="errors::503" || true

echo "📦 [2/6] Memperbarui Dependensi Composer..."
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

# Generate app key jika masih kosong
php artisan key:generate --force --no-interaction

echo "🗄️ [3/6] Menyiapkan Database & Menjalankan Migrasi..."
touch database/database.sqlite
php artisan migrate --force
php artisan db:seed --force

echo "🔗 [4/6] Memeriksa Storage Link..."
php artisan storage:link || true

echo "⚡ [5/6] Mengoptimalkan Cache Konfigurasi, Route, dan View..."
php artisan optimize:clear
php artisan optimize

echo "🔒 [6/6] Memperbaiki Izin Akses Folder..."
chmod -R 775 storage bootstrap/cache database
chmod 664 database/database.sqlite 2>/dev/null || true

echo "🟢 Mematikan Mode Maintenance..."
php artisan up

echo "=============================================================================="
echo "✅ DEPLOY BERHASIL!"
echo "Aplikasi Anda sekarang aktif di: https://budayariau.jelatix.com"
echo "Login Admin: https://budayariau.jelatix.com/adminganteng/login"
echo "=============================================================================="

