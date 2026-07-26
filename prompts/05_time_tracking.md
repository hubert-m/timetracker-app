# Plik: 05_time_tracking.md

## Kontekst
Wdrażamy rdzeń aplikacji: śledzenie czasu pracy (`TimeLog`). Użytkownicy mogą logować czas za pomocą wirtualnego stopera lub wpisywać go ręcznie.

## Zadania do wykonania
1. Stwórz `TimeLogController`.
2. Funkcjonalność "Stoper" (Play/Stop):
   - Endpoint `start`: Tworzy nowy rekord `TimeLog` z dzisiejszą datą i aktualnym czasem w `start_time`. Zwraca ID logu. Zabezpiecz przed uruchomieniem dwóch stoperów naraz przez jednego usera.
   - Endpoint `stop`: Przyjmuje ID aktywnego logu, ustawia `end_time` na teraz, oblicza różnicę w minutach i zapisuje do `duration_minutes`.
3. Funkcjonalność "Manualne logowanie":
   - Endpoint pozwalający na podanie: `task_id`, `date` oraz zadeklarowanie przepracowanego czasu (np. w minutach lub formacie HH:MM, który sparsujesz do całkowitej liczby minut). Zapisuje od razu gotowy rekord z `duration_minutes`.
4. Walidacja: Można logować czas tylko do zadań, do których użytkownik jest przypisany (lub jest przypisany do ich projektu nadrzędnego - zaimplementuj odpowiednią logikę w Gate/Policy).

## Kryteria akceptacji
- Obliczanie `duration_minutes` przy zatrzymywaniu stopera działa poprawnie i uwzględnia strefy czasowe.
- API pozwala na dodanie ręcznego logu z czasem.
- Brak możliwości logowania czasu do cudzych projektów.