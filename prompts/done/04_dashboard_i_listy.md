# Plik: 04_dashboard_i_listy.md

## Kontekst
Mamy autoryzację i zarządzanie zasobami. Teraz musimy przygotować dane dla Dashboardu wyświetlanego zaraz po zalogowaniu.

## Zadania do wykonania
1. Stwórz `DashboardController`.
2. Zbuduj zapytania Eloquent (lub Resource'y), które zwrócą na widok/API:
   - Listę **Ulubionych Projektów** zalogowanego użytkownika.
   - Listę **Ulubionych Zadań** zalogowanego użytkownika.
   - Obie listy powinny zawierać podstawowe statystyki (np. liczba przypisanych osób do projektu, czas przepracowany w tasku - jeśli zaimplementujesz to jako relacje).
3. Zoptymalizuj zapytania pod kątem N+1 (użyj `with()`).

## Kryteria akceptacji
- Kontroler zwraca wyłącznie projekty i zadania oznaczone jako ulubione przez zalogowanego użytkownika.
- Wykorzystano Eager Loading do uniknięcia problemu N+1.