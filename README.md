# CMS Blogowy - System Instalacyjny na wzór WordPressa

## Opis

Ten CMS blogowy został zbudowany na wzór systemu instalacyjnego WordPressa. Oferuje prosty proces instalacji podobny do WordPressa, ale z własną implementacją.

## Struktura systemu

```
cms/
├── index.php              # Główny plik - sprawdza instalację i przekierowuje
├── installer.php          # Instalator systemu
├── wp-config-sample.php   # Przykładowy plik konfiguracyjny
├── config.php            # Plik konfiguracyjny (tworzony podczas instalacji)
├── .htaccess             # Reguły przekierowań URL
├── admin/                # Panel administracyjny
│   ├── index.php         # Dashboard
│   ├── login.php         # Logowanie
│   ├── posts/            # Zarządzanie wpisami
│   ├── pages/            # Zarządzanie stronami
│   ├── media.php         # Zarządzanie mediami
│   ├── theme.php         # Ustawienia motywu
│   └── system/           # Systemowe funkcje
├── public/               # Strona publiczna
│   ├── index.php         # Główna strona
│   └── blog.php          # Strona bloga
├── migrations/           # Migracje bazy danych
├── assets/               # Pliki CSS/JS
├── media/                # Przesłane pliki
└── storage/              # Logi i pliki tymczasowe
```

## Proces instalacji (jak w WordPressie)

### 1. Pobranie i rozpakowanie
- Pobierz paczkę ZIP z systemem
- Rozpakuj na serwer (FTP, SSH, panel hostingowy)

### 2. Uruchomienie instalatora
- Wejdź w domenę - system automatycznie przekieruje do `installer.php`
- Instalator sprawdza czy `config.php` istnieje
- Jeśli nie ma, wyświetla formularz konfiguracyjny

### 3. Konfiguracja bazy danych
Instalator prosi o:
- Host bazy danych (localhost, mysql.domena.pl)
- Nazwę bazy danych
- Użytkownika bazy danych
- Hasło bazy danych
- Prefix tabel (opcjonalnie, domyślnie `cms_`)

### 4. Dane administratora
- E-mail administratora
- Login administratora
- Hasło administratora (min. 8 znaków)

### 5. Tworzenie systemu
Instalator:
- Testuje połączenie z bazą danych
- Tworzy plik `config.php` z danymi
- Uruchamia migracje (tworzy tabele)
- Tworzy konto administratora
- Przekierowuje do panelu administracyjnego

### 6. Pierwsze uruchomienie
Po instalacji:
- System automatycznie loguje administratora
- Przekierowuje do panelu `admin/`
- Od tej pory domena ładuje normalną stronę

## Co dzieje się przy każdym wejściu

### Jeśli system nie jest zainstalowany:
1. `index.php` sprawdza czy istnieje `config.php`
2. Jeśli nie ma - przekierowuje do `installer.php`
3. Instalator wyświetla formularz konfiguracyjny

### Jeśli system jest zainstalowany:
1. `index.php` przekierowuje do `public/`
2. `public/index.php` ładuje konfigurację
3. Sprawdza URL i ładuje odpowiednią treść
4. Generuje HTML z motywu i wysyła do przeglądarki

## Funkcje systemu

- **Zarządzanie wpisami** - tworzenie, edycja, usuwanie wpisów
- **Zarządzanie stronami** - tworzenie stron statycznych
- **Zarządzanie mediami** - przesyłanie i zarządzanie plikami
- **System komentarzy** - moderacja komentarzy
- **Ustawienia motywu** - personalizacja wyglądu
- **Analityka** - statystyki odwiedzin
- **SEO** - optymalizacja dla wyszukiwarek

## Wymagania systemowe

- PHP 7.4 lub nowszy
- MySQL 5.7 lub nowszy / MariaDB 10.2 lub nowszy
- Moduł mod_rewrite (dla ładnych URL-i)
- Uprawnienia do zapisu w katalogu

## Bezpieczeństwo

- Pliki konfiguracyjne są zabezpieczone przez `.htaccess`
- Hasła są hashowane z użyciem `password_hash()`
- Sesje są zabezpieczone
- Wszystkie dane wejściowe są walidowane i escapowane

## Aktualizacje

System obsługuje automatyczne aktualizacje przez panel administracyjny w sekcji `admin/system/update.php`.

## Wsparcie

W przypadku problemów sprawdź:
1. Logi błędów w `storage/logs/`
2. Uprawnienia do zapisu w katalogu
3. Konfigurację serwera (mod_rewrite, PHP, MySQL)
