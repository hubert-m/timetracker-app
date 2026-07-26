# Plik: 01_setup_bazy_i_modeli.md

## Kontekst
Jesteś zaawansowanym programistą Laravel. Rozpoczynamy budowę aplikacji do śledzenia czasu pracy w zadaniach z podziałem na projekty. W tym kroku musisz przygotować fundamenty bazy danych oraz modele Eloquent.

## Wymagania
1. Zaktualizuj migrację tabeli `users` (domyślną z Laravela):
   - Dodaj nullable string `google_id`.
   - Hasło (`password`) musi być nullable (dla użytkowników logujących się tylko przez Google, którzy jeszcze nie ustawili hasła).
2. Stwórz migracje i modele dla:
   - `Project` (title, description, timestamps).
   - `Task` (project_id, title, description, timestamps).
   - `TimeLog` (user_id, task_id, date, start_time (nullable), end_time (nullable), duration_minutes (integer), timestamps).
3. Stwórz tabele pivot i relacje:
   - `project_user` (project_id, user_id).
   - `task_user` (task_id, user_id).
   - Tabela do zaproszeń: `pending_invitations` (email, invitable_type [Project/Task], invitable_id, timestamps). Będzie służyć do przetrzymywania uprawnień dla emaili, których jeszcze nie ma w bazie.
   - Tabela dla ulubionych: `favorites` (user_id, favoritable_type, favoritable_id) - polimorficzna dla projektów i zadań.
4. Zdefiniuj wszystkie relacje (hasMany, belongsTo, belongsToMany, morphToMany) w modelach `User`, `Project`, `Task`, `TimeLog`.

## Kryteria akceptacji
- Wszystkie migracje wykonują się bez błędów.
- Modele posiadają odpowiednie właściwości `$fillable` oraz zdefiniowane metody relacji.
- Zwróć tylko kod migracji oraz modeli. Nie twórz jeszcze kontrolerów.