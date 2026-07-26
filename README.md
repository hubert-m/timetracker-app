# TimeTracker App

Prosta aplikacja do śledzenia czasu z podziałem na projekty i zadania. Posiada logowanie (tradycyjne + Google OAuth) oraz zaawansowany system zaproszeń.

## Środowisko deweloperskie (Lokalne uruchomienie)

Aplikacja dostarcza gotowe skrypty do uruchomienia zarówno backendu (artisan serve) jak i frontendu (vite) na środowisku lokalnym.
W zależności od używanego systemu operacyjnego wybierz odpowiedni skrypt:

- **macOS / Linux:** Uruchom skrypt `./run_develop.sh` (skrypt automatycznie otworzy nowe zakładki w terminalu i uruchomi środowisko).
- **Windows:** Uruchom skrypt `run_develop.bat`.

Pamiętaj, by przed pierwszym uruchomieniem:
1. Sklonować repozytorium.
2. Uruchomić `composer install` oraz `npm install`.
3. Skopiować `.env.example` do `.env` (`cp .env.example .env`).
4. Uzupełnić zmienne (więcej w sekcji Zmienne Konfiguracyjne).
5. Wykonać migracje bazy danych poleceniem `php artisan migrate`.

## Zmienne Konfiguracyjne (.env)

Do poprawnego działania logowania przez Google, na środowisku (zarówno lokalnym, jak i na serwerze produkcyjnym) konieczne jest uzupełnienie w pliku `.env` następujących stałych. Uzyskasz je z konsoli Google Cloud Platform (w sekcji API & Services > Credentials):

```dotenv
GOOGLE_CLIENT_ID="twój-client-id-z-google"
GOOGLE_CLIENT_SECRET="twój-client-secret-z-google"
GOOGLE_REDIRECT_URI="http://localhost:8000/auth/google/callback" # Adres callbacku, odpowiednio zmieniony na produkcji, np. https://twojadomena.pl/auth/google/callback
```

## Uruchamianie testów

W aplikacji znajdują się testy jednostkowe/integracyjne (PHPUnit / Pest), które testują m.in. operacje biznesowe projektów oraz uprawnienia zaproszeń. Aby upewnić się, że wszystko działa, wystarczy wykonać polecenie:

```bash
php artisan test
```

## Wdrożenie (Deployment) na serwerze produkcyjnym

Aplikacja jest przygotowana do szybkiego aktualizowania (Continuous Deployment) przy użyciu skryptu `pull.sh`.
Skrypt ten odpowiada za:
1. Zaciągnięcie zmian z repozytorium (branch develop/main).
2. Wykonanie migracji bazy danych (w trybie force).
3. Instalację i aktualizację zależności backendowych (composer) i frontendowych (npm).
4. Kompilację zasobów (npm run build).
5. Optymalizację cache'y Laravela.
6. **Uruchomienie testów** upewniając się, że nowa wersja niczego nie psuje.

Wystarczy wejść do katalogu z projektem na swoim serwerze produkcyjnym i wykonać:
```bash
./pull.sh
```
Zalecane jest stworzenie aliasu w systemie lub podpięcie tego skryptu pod hooki gita po stronie serwera/CI. Wszelkie operacje wdrażania zostaną automatycznie zapisane w folderze z logami: `storage/logs/pull/`.
