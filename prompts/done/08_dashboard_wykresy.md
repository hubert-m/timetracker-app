# Plik: 08_dashboard_wykresy.md

## Kontekst
Użytkownicy mogą obecnie przeglądać surowe dane statystyczne. Chcemy wizualnie udoskonalić aplikację, dodając nowoczesne wykresy i wskaźniki (KPI) na stronie Dashboardu.

## Zadania do wykonania
1. **Biblioteka wykresów**:
   - Wybierz i zainstaluj/dołącz bibliotekę do generowania wykresów (np. Chart.js lub ApexCharts).
2. **Przygotowanie agregacji na backendzie**:
   - Zaktualizuj `DashboardController` i zbuduj odpowiednie struktury danych do zasilania wykresów (np. formatowane tablice dla wykresu liniowego).
3. **Zaimplementuj następujące widżety/wykresy na dashboardzie**:
   - Wykres kołowy lub donat (Pie/Doughnut Chart) obrazujący podział przepracowanego czasu pomiędzy różnymi projektami w bieżącym miesiącu.
   - Wykres słupkowy (Bar Chart) lub liniowy ilustrujący ilość przepracowanych godzin przez zalogowanego użytkownika w poszczególnych dniach z ostatnich 14 dni.
   - Blok z podsumowaniem statystyk w formie kafelków (KPI): Całkowity zaraportowany czas w tym miesiącu (w formacie HH:MM), Liczba aktywnych projektów użytkownika.
4. **Asynchroniczne ładowanie (Opcjonalnie)**:
   - Rozważ pobieranie danych do wykresów asynchronicznie poprzez osobny endpoint XHR/Fetch, aby odciążyć renderowanie głównego widoku.

## Kryteria akceptacji
- Wykresy renderują się bezbłędnie z danymi pochodzącymi bezpośrednio z bazy danych.
- Dashboard wygląda nowocześnie, kafelki informacyjne poprawnie sumują godziny (zmiana na HH:MM z minut).
- Kod odpowiedzialny za wyliczanie danych wykresów jest zoptymalizowany (pojedyncze zapytania wykorzystujące instrukcje grupujące Eloquent np. `groupBy('date')`).
