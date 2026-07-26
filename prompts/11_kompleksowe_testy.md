# Plik: 11_kompleksowe_testy.md

## Kontekst
W poprzednich krokach dostarczyliśmy nowe widoki, endpointy API do raportów, integracje wykresów i eksportów oraz zadania planowane. Aby zachować jakość aplikacji, niezbędne jest uzupełnienie pokrycia testowego.

## Zadania do wykonania
1. **Testy dla nowych funkcjonalności (Feature Tests)**:
   - Stwórz i zaimplementuj `DashboardTest.php`: sprawdzający m.in. czy endpoint dla dashboardu autoryzuje zalogowanych użytkowników i czy format zwrotny poprawnie zawiera ulubione statystyki bez N+1.
   - Zaktualizuj `ReportTest.php`: sprawdź czy PDF i CSV generują się przy zapytaniach dla dozwolonych userów (np. assertStatus(200), assertHeader 'content-type').
   - Napisz `ReminderCommandTest.php` lub podobny test badający, czy zadanie konsolowe wyłapuje właściwych użytkowników z pustym logiem w danym dniu i czy poprawnie wrzuca Mailable na fałszywą kolejkę (`Mail::fake()`).
2. **Wzmocnienie testów jednostkowych (Unit Tests)**:
   - Dodaj testy do przeliczania czasu (upewnij się, że algorytmy przeliczające minuty na `HH:MM` w ReportService działają perfekcyjnie).
3. **Audyt po refaktoryzacji**:
   - Zweryfikuj dotychczas napisane testy dla `TimeLog` i `Project` - upewniając się, że nowe dodane kody powiązane z Schedulere'm nie popsuły wcześniejszej funkcjonalności (uruchom pełen zestaw).

## Kryteria akceptacji
- Pokrycie kluczowych aspektów aplikacji: autoryzacja, operacje pobierania CSV/PDF oraz test logiki planowanych zadań (Scheduler).
- `php artisan test` zwraca 100% poprawności (wszystkie testy zielone).
- Kod testów używa fabryk (Factories) oraz mechanizmów oszukiwania np. `Mail::fake()`, `Queue::fake()` a także manipulacji czasem `Carbon::setTestNow()`.
