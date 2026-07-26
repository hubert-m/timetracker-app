# Plik: 03_projekty_zadania_uprawnienia.md

## Kontekst
Zaimplementuj logikę biznesową dla Projektów i Zadań (CRUD) oraz system przypisywania użytkowników do tych zasobów.

## Zadania do wykonania
1. Stwórz kontrolery: `ProjectController` i `TaskController`.
2. Dodawanie/Edycja: Użytkownik może tworzyć projekty i zadania. Twórca automatycznie zostaje przypisany do projektu/zadania (dodany do pivota).
3. System zaproszeń (przypisywanie po emailu) - stwórz API/metody do przypisywania:
   - Input: `email`, `resource_type` (Project/Task), `resource_id`.
   - Jeśli email istnieje w bazie `users`: dodaj powiązanie do odpowiedniej tabeli pivot (`project_user` lub `task_user`).
   - Jeśli email NIE istnieje w bazie: dodaj wpis do tabeli `pending_invitations`.
4. Ulubione:
   - Stwórz endpointy pozwalające na przełączanie (toggle) statusu "ulubione" (gwiazdka) dla Projektów i Zadań dla zalogowanego użytkownika (zapis w tabeli polimorficznej `favorites`).

## Kryteria akceptacji
- Użytkownik widzi tylko projekty i zadania, do których jest przypisany.
- Mechanizm przypisywania obsługuje zarówno istniejących, jak i nieistniejących użytkowników bez rzucania błędów.
- Polimorficzna relacja ulubionych działa poprawnie (można dodawać/usuwać z ulubionych).