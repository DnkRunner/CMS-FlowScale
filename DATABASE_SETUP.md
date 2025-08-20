# Konfiguracja bazy danych CMS-FlowScale

## Opcje bazy danych:

### 1. Neon (PostgreSQL) - Zalecane
```bash
# W Vercel dashboard, dodaj zmienne środowiskowe:
DB_HOST=ep-cool-name-123456.us-east-1.aws.neon.tech
DB_NAME=neondb
DB_USER=your_username
DB_PASSWORD=your_password
```

### 2. PlanetScale (MySQL) - Alternatywa
```bash
# W Vercel dashboard, dodaj zmienne środowiskowe:
DB_HOST=aws.connect.psdb.cloud
DB_NAME=your_database_name
DB_USER=your_username
DB_PASSWORD=your_password
```

### 3. Cyber-Folks (MySQL) - Twoja domena
```bash
# W Vercel dashboard, dodaj zmienne środowiskowe:
DB_HOST=mysql.cyberfolks.pl
DB_NAME=twoja_baza_danych
DB_USER=twoj_username
DB_PASSWORD=twoje_haslo
```

## Jak skonfigurować:

1. **Przejdź do Vercel Dashboard**
2. **Wybierz projekt** `cms-flowscale`
3. **Settings** → **Environment Variables**
4. **Dodaj zmienne:**
   - `DB_HOST` - adres serwera bazy danych
   - `DB_NAME` - nazwa bazy danych
   - `DB_USER` - nazwa użytkownika
   - `DB_PASSWORD` - hasło

5. **Redeploy** projekt

## Testowanie:

Po skonfigurowaniu sprawdź:
- https://cms-flow-scale-iibm.vercel.app/api/posts
- Powinno zwrócić JSON z listą postów (początkowo pustą)

## Struktura bazy danych:

System automatycznie utworzy tabele:
- `posts` - wpisy blogowe
- `pages` - strony statyczne  
- `categories` - kategorie (z domyślnymi)

## Bezpieczeństwo:

- Hasła są szyfrowane
- Połączenia używają SSL
- Prepared statements chronią przed SQL injection
