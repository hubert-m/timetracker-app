# Plik: 02_autoryzacja.md

## Kontekst
Mamy gotową bazę danych. Teraz wdrażamy system autoryzacji z uwzględnieniem logowania tradycyjnego oraz Google OAuth. Użyjemy do tego paczki Laravel Socialite oraz wbudowanych mechanizmów resetu hasła (możesz oprzeć się na Laravel Breeze/Fortify, w zależności od preferowanego podejścia, ale dostarcz logikę backendową).

## Zadania do wykonania
1. Zainstaluj i skonfiguruj `laravel/socialite`.
2. Stwórz kontroler do obsługi logowania Google (`SocialiteController`):
   - Redirect do Google.
   - Callback: znajdź użytkownika po `google_id` LUB po adresie `email`. Jeśli użytkownik istnieje po emailu, ale nie ma `google_id`, zaktualizuj jego rekord o `google_id`. Jeśli nie istnieje, stwórz nowe konto z pustym hasłem.
3. Obsługa profilu użytkownika (SettingsController):
   - Metoda pozwalająca na ustawienie hasła dla konta, które go nie posiada (logowanie Google).
   - Metoda pozwalająca na zmianę hasła, jeśli jest już ustawione (wymaga podania starego hasła).
4. Skonfiguruj standardowy proces "Zapomniałem hasła" (Forgot Password) wysyłający email z linkiem do resetu.
5. Po udanej rejestracji nowego użytkownika (zarówno tradycyjnej, jak i przez Google), system musi sprawdzić tabelę `pending_invitations`. Jeśli istnieją wpisy dla jego adresu email, przypisz go do odpowiednich projektów/zadań (tabele pivot `project_user` i `task_user`) i usuń wpisy z `pending_invitations`.

## Kryteria akceptacji
- Zabezpieczone ścieżki logowania i rejestracji.
- Użytkownik Google może zalogować się, wejść w profil, ustawić hasło i od tego momentu logować się tradycyjnie.
- Event nasłuchujący na rejestrację przypisuje uprawnienia z `pending_invitations`.