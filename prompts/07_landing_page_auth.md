# Plik: 07_landing_page_auth.md

## Kontekst
Mamy gotową logikę backendową autoryzacji oraz zarządzanie czasem. Teraz czas na przygotowanie interfejsu dla osób niezalogowanych, który zachęci ich do skorzystania z aplikacji. Zbudujemy estetyczny Landing Page.

## Zadania do wykonania
1. **Widok Landing Page (`welcome.blade.php`)**:
   - Zastąp domyślny ekran Laravela nowoczesnym designem (rekomendowany Tailwind CSS).
   - Zbuduj sekcję "Hero", która będzie witała użytkowników opisem aplikacji (np. "Zaawansowane śledzenie czasu dla Twojego zespołu").
2. **Dynamiczne formularze w sekcji Hero**:
   - W sekcji Hero umieść komponent (możesz użyć Alpine.js lub standardowego JS) przełączający widok między logowaniem a rejestracją bez przeładowania strony.
   - Domyślnie ma być wyświetlany formularz logowania.
   - Poniżej standardowych pól logowania/rejestracji zaimplementuj wyraźny i estetyczny przycisk "Zaloguj się przez Google" / "Zarejestruj się przez Google" (wykorzystujący utworzony wcześniej `SocialiteController`).
3. **Refaktoryzacja tras**:
   - Zadbaj o to, by użytkownicy zalogowani, którzy wejdą na stronę główną `/`, byli automatycznie przekierowywani na `/dashboard`.
   - Zintegruj stworzony wcześniej kontroler profilu/zalogowania, upewniając się, że walidacja zwraca odpowiednie komunikaty błędów pod polami formularza.

## Kryteria akceptacji
- Landing Page działa poprawnie wizualnie na komputerach i urządzeniach mobilnych (RWD).
- Przełączanie logowanie/rejestracja odbywa się płynnie w jednym widoku (bez odświeżania okna).
- Logowanie przez Google widnieje jako jedna z głównych opcji akcji.
