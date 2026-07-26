# Plik: 10_powiadomienia_email.md

## Kontekst
Użytkownicy często zapominają wyłączać timery lub logować przepracowany czas. Wdrożymy Cron Task / Schedulera wysyłającego zautomatyzowane e-maile.

## Zadania do wykonania
1. **Konfiguracja Scheduler'a**:
   - W pliku `routes/console.php` (lub w klasie Schedulera) zdefiniuj zadanie (Command), które będzie wywoływane codziennie o konkretnej godzinie, np. o 18:00 (`->dailyAt('18:00')`).
2. **Logika powiadomień biznesowych**:
   - Skrypt ma pobrać wszystkich użytkowników, którzy:
     a) Mają przypisane do siebie zadania LUB należą do projektów.
     b) Na bieżący dzień kalendarzowy zadeklarowali `0` zaraportowanych minut w `TimeLog` LUB posiadają niezamknięty stoper (gdzie `end_time` jest wciąż null).
   - Wygeneruj klasę mailable (`php artisan make:mail DailyTimeReminder`).
   - Stwórz szablon wiadomości w Blade tłumaczący cel komunikatu (np. "Cześć [Imię], zauważyliśmy, że dzisiaj jeszcze nie zaraportowałeś swojego czasu, lub Twój stoper dalej tyka...").
3. **Zabezpieczenie kolejek (Queues)**:
   - Wysyłka e-maili nie powinna blokować głównego procesu, ustaw mail na dziedziczenie po `ShouldQueue`, tak by system zakolejkował wysyłki.
   - Poinstruuj w komentarzach, jak na lokalnym środowisku testować wysyłkę e-maili (np. Log driver lub Mailpit).

## Kryteria akceptacji
- Komenda `php artisan schedule:run` lub odpowiedni wywoływacz poprawne odpala logikę wyłapywania użytkowników bez wypracowanego czasu.
- Powiadomienia w formie Mailable pomyślnie używają kolejek (Queue).
- Mechanizm nie obciąża bazy niepotrzebnymi iteracjami N+1 przy weryfikowaniu wpisów z danego dnia.
