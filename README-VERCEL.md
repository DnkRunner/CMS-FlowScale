# 🚀 CMS Blogowy - Wdrożenie na Vercel + Neon + domena .pl

## 📦 Co otrzymujesz

- ✅ **CMS blogowy** na wzór WordPressa
- ✅ **Gotowy do Vercel** (serverless)
- ✅ **Baza danych Neon** (PostgreSQL)
- ✅ **Domena .pl** (konsumenckaugoda.pl)
- ✅ **Panel administracyjny**
- ✅ **Responsywny design**

## 🎯 Plan wdrożenia

### 1. GitHub Repository
### 2. Vercel Deployment  
### 3. Neon Database
### 4. Domena .pl (DNS)

---

## 📋 Krok 1: GitHub Repository

### 1.1 Utwórz nowe repo na GitHub
```bash
# Nazwa: cms-konsumenckaugoda
# Public/Private: Private (zalecane)
# README: Tak
```

### 1.2 Wgraj pliki
```bash
# Rozpakuj cms-vercel-neon.zip
# Wgraj wszystkie pliki do repo
git add .
git commit -m "Initial CMS commit"
git push origin main
```

---

## 🌐 Krok 2: Vercel Deployment

### 2.1 Połącz z Vercel
1. **Wejdź na:** [vercel.com](https://vercel.com)
2. **Zaloguj się** (GitHub)
3. **"New Project"**
4. **Importuj repo:** `cms-konsumenckaugoda`
5. **Framework Preset:** `Other`
6. **Root Directory:** `./` (domyślne)
7. **Build Command:** `echo "Build completed"`
8. **Output Directory:** `./` (domyślne)

### 2.2 Konfiguracja Vercel
- **Project Name:** `cms-konsumenckaugoda`
- **Deploy**

---

## 🗄️ Krok 3: Neon Database

### 3.1 Utwórz bazę na Neon
1. **Wejdź na:** [neon.tech](https://neon.tech)
2. **"Create Project"**
3. **Nazwa:** `cms-konsumenckaugoda`
4. **Region:** `Frankfurt` (najbliżej Polski)
5. **Database:** `cms_db`
6. **User:** `cms_user`
7. **Password:** `Wygeneruj silne hasło`

### 3.2 Pobierz connection string
1. **Dashboard** → **Connection Details**
2. **Skopiuj:** `postgresql://user:pass@host:port/database`

---

## ⚙️ Krok 4: Konfiguracja Vercel

### 4.1 Environment Variables
1. **Vercel Dashboard** → **Settings** → **Environment Variables**
2. **Dodaj:**
   ```
   Name: DATABASE_URL
   Value: postgresql://user:pass@host:port/database
   Environment: Production, Preview, Development
   ```

### 4.2 Redeploy
1. **Deployments** → **Redeploy** (ostatni deployment)

---

## 🌍 Krok 5: Domena .pl (konsumenckaugoda.pl)

### 5.1 Dodaj domenę w Vercel
1. **Settings** → **Domains**
2. **Add Domain:** `konsumenckaugoda.pl`
3. **Add Domain:** `www.konsumenckaugoda.pl`

### 5.2 Konfiguracja DNS
W panelu rejestratora domeny (gdzie kupiłeś konsumenckaugoda.pl):

**Dodaj rekordy DNS:**
```
Type: A
Name: @
Value: 76.76.19.36

Type: CNAME  
Name: www
Value: cms-konsumenckaugoda.vercel.app
```

**Lub użyj Vercel Nameservers:**
```
ns1.vercel-dns.com
ns2.vercel-dns.com
ns3.vercel-dns.com
ns4.vercel-dns.com
```

---

## 🎯 Krok 6: Instalacja CMS

### 6.1 Uruchom instalator
1. **Wejdź na:** `https://konsumenckaugoda.pl/installer_vercel`
2. **Wypełnij formularz:**
   - **Database URL:** `postgresql://user:pass@host:port/database`
   - **Admin Email:** `admin@konsumenckaugoda.pl`
   - **Admin Login:** `admin`
   - **Admin Password:** `SilneHaslo123!`

### 6.2 Sprawdź instalację
1. **Panel admin:** `https://konsumenckaugoda.pl/admin/`
2. **Strona główna:** `https://konsumenckaugoda.pl/`

---

## 🔧 Struktura plików

```
cms-vercel-neon/
├── vercel.json              # Konfiguracja Vercel
├── package.json             # Dependencies
├── installer_vercel.php     # Instalator dla Vercel
├── db_vercel.php           # Adapter bazy danych
├── migrations_postgres/     # Migracje PostgreSQL
├── admin/                  # Panel administracyjny
├── public/                 # Strona publiczna
└── assets/                 # CSS/JS
```

---

## 🎨 Funkcje CMS

- ✅ **Zarządzanie wpisami** (CRUD)
- ✅ **Zarządzanie stronami**
- ✅ **Zarządzanie mediami**
- ✅ **System komentarzy**
- ✅ **Ustawienia motywu**
- ✅ **Analityka**
- ✅ **SEO**
- ✅ **Responsywny design**

---

## 🔒 Bezpieczeństwo

- ✅ **Hasła hashowane** (password_hash)
- ✅ **Walidacja danych wejściowych**
- ✅ **Zabezpieczone sesje**
- ✅ **HTTPS** (automatycznie przez Vercel)

---

## 📱 Responsywność

- ✅ **Mobile-first design**
- ✅ **Bootstrap CSS**
- ✅ **Adaptacyjne obrazy**
- ✅ **Touch-friendly interface**

---

## 🚀 Gotowe!

Po wykonaniu wszystkich kroków:

- **Strona:** `https://konsumenckaugoda.pl`
- **Admin:** `https://konsumenckaugoda.pl/admin/`
- **Login:** `admin`
- **Hasło:** `SilneHaslo123!`

---

## 🆘 Wsparcie

**Problemy z Vercel:**
- Sprawdź Environment Variables
- Sprawdź Build Logs
- Redeploy aplikacji

**Problemy z Neon:**
- Sprawdź Connection String
- Sprawdź uprawnienia użytkownika
- Sprawdź region bazy danych

**Problemy z DNS:**
- Poczekaj 24-48h na propagację
- Sprawdź rekordy DNS
- Skontaktuj się z rejestratorem

---

## 🎯 Bonus: Automatyczne aktualizacje

Vercel automatycznie:
- ✅ **Deployuje** przy każdym push na GitHub
- ✅ **Tworzy preview** dla każdego PR
- ✅ **Optymalizuje** obrazy i CSS
- ✅ **Zapewnia** globalny CDN

**Gotowe! 🎉**
