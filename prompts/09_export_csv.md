# Plik: 09_export_csv.md

## Kontekst
Nasza aplikacja potrafi już generować atrakcyjne raporty w formacie PDF. Klienci biznesowi potrzebują jednak również możliwości zrzutu surowych danych do arkuszy kalkulacyjnych (Excel). Wdrażamy eksport do formatu CSV.

## Zadania do wykonania
1. **Rozbudowa widoku raportów**:
   - W interfejsie raportów (tam, gdzie filtruje się dane przed pobraniem PDF), obok przycisku "Pobierz PDF" dodaj nowy przycisk "Pobierz CSV".
2. **Backend Exportu**:
   - Możesz wykorzystać wbudowane funkcje PHP do zapisu strumieniowego CSV (np. `fputcsv` piszący do wyjścia `php://output`) lub użyć znanej paczki (np. `Maatwebsite/Laravel-Excel` / `Spatie/SimpleExcel`).
   - Stwórz metodę `exportCsv` w klasie `ReportController` (lub użyj tego samego endpointa dodając parametr np. `?format=csv`).
3. **Format pliku CSV**:
   - Plik musi używać poprawnego kodowania (zalecane UTF-8 z BOM, aby Excel w Windows poprawnie radził sobie z polskimi znakami) lub rozdzielenia przy użyciu średników (`;`).
   - Zachowaj dokładnie te same parametry filtrujące co dla PDF (miesiąc, pracownik, projekt).
   - Kolumny CSV: `Data`, `Pracownik`, `Projekt`, `Zadanie`, `Przepracowane Minuty`, `Przepracowany Czas (HH:MM)`. (Dla CSV warto oddzielić minuty do łatwiejszych sumowań w Excelu).

## Kryteria akceptacji
- Pobieranie PDF i CSV używają tej samej logiki agregacyjnej w `ReportService`.
- Pobrany CSV otwiera się bez problemów z krzakami kodowania w Excelu lub innych aplikacjach.
- Zachowana jest restrykcja dostępu.
