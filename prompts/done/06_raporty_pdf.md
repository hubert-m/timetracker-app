# Plik: 06_raporty_pdf.md

## Kontekst
Ostatnim etapem jest generowanie raportów w formacie PDF. Wykorzystamy paczkę `barryvdh/laravel-dompdf`.

## Zadania do wykonania
1. Zainstaluj `barryvdh/laravel-dompdf`.
2. Stwórz `ReportController` i odpowiedni serwis (np. `ReportService`) do agregacji danych.
3. Zbuduj endpoint przyjmujący parametry filtrujące:
   - `date_from` i `date_to` (domyślnie pełne miesiące).
   - `project_id` (opcjonalnie).
   - `user_id` (opcjonalnie).
4. Logika filtrowania - przygotuj zbiór danych pozwalający wygenerować 4 warianty raportu:
   - Raport dla całego projektu (wszyscy pracownicy).
   - Raport dla całego projektu dla wybranego pracownika.
   - Raport dla pracownika (ze wszystkich jego projektów).
   - Raport dla pracownika z zawężeniem do konkretnego projektu.
5. Przygotuj prosty widok Blade (np. `reports.pdf`), który odbiera te zagregowane dane, wyświetla tabelę (Data, Pracownik, Zadanie, Przepracowany Czas, Suma godzin) i generuje widok.
6. Kontroler zwraca pobieranie pliku PDF (`return Pdf::loadView(...)->download('raport.pdf')`).

## Kryteria akceptacji
- System poprawnie agreguje czas (zamienia zsumowane minuty na format Godziny:Minuty na raporcie).
- Użytkownik może wygenerować raport za konkretny miesiąc.
- Dokument PDF renderuje się bez błędów.