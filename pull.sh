#!/bin/bash

# Zatrzymanie skryptu w przypadku napotkania jakiegokolwiek błędu np. przy instalacji NPM
set -e

# Konfiguracja logowania
LOG_DIR="storage/logs/pull"
LOG_FILE="$LOG_DIR/pull-$(date '+%Y-%m-%d-%H-%M-%S').log"
mkdir -p "$LOG_DIR"

# Przekierowanie całego wyjścia (stdout i stderr) do terminala i pliku logu jednocześnie
exec > >(tee -a "$LOG_FILE") 2>&1

echo "--------------------------------------------------------"
echo "📅 Rozpoczęcie: $(date '+%Y-%m-%d %H:%M:%S')"
echo "🚀 Rozpoczynam zaciąganie zmian i wdrażanie aplikacji..."

# Krok bezpieczeństwa: można odkomentować poniższe aby zabezpieczać lokalne hot-fixy, jeśli modyfikowano coś bezpośrednio
# git stash

echo "[1/6] 📥 Pobieranie najnowszych zmian i wymuszanie synchronizacji z main..."
git fetch origin develop
git reset --hard origin/develop

echo "[2/6] 🗄️ Uruchamianie nowych migracji bazy danych..."
# Skompresowana i zautomatyzowana metoda za pomocą --force (wymagane w APP_ENV=production)
php artisan migrate --force

echo "[3/6] 📦 Instalacja zależności backendowych (Composer)..."
# Flaga --no-interaction wyłącza dodatkowe prompt'y
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "[4/6] 📦 Instalacja zależności frontendowych (NPM)..."
npm install

echo "[5/6] 🎨 Kompilacja zasobów frontendowych (Vite)..."
npm run build

echo "[6/6] 🧹 Optymalizacja pamięci podręcznej frameworka (Cache)..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Pobieranie najnowszego tagu (jeśli istnieje) do wersji
TAG=$(git describe --tags --abbrev=0 2>/dev/null || echo "")
DATE=$(date '+%Y-%m-%d %H:%M:%S')

if [ -n "$TAG" ]; then
    V="$TAG - $DATE"
else
    V="$DATE"
fi

# Generowanie pliku wersji dla aplikacji (nadpisuje istniejący)
echo "$V" > VERSION.txt

echo "========================================="
echo "✅ Aktualizacja zakończona sukcesem!"
echo "📅 Zakończenie: $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================="