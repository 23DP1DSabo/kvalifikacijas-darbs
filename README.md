# TaskForge

Produktivitātes izsekošanas un uzlabošanas tīmekļa vietne, kas apvieno vairākus interaktīvus rīkus, kā arī sociālo tīklu elementus. Projekts palīdz novērst prokrastināciju, plānot laiku un diskutēt par tematu.

---

## Funkcionalitāte

- **Pomodoro pulkstenis** — darba/atpūtass ciklēšanas metode, kas dod ritmu ikdienas darbam un atbrīvo laiku nepieciešamajām atpūtām. Pulkstenim ir pieejami 2 dažādi režīmi:
    - Klasiskais - darbalaiks ir 25 min, īsā atpūta ur 5 min, garā atpūta ir 15 min. Ir iespēja atkārtot ciklu pēc vajadzības.
    - Pielāgojams - lietotājs var sadalīt savu kopējo darbalaiku blokos un tiem uzstādīt sev ērtos laika periodus.
- **Uzdevumu dēlis** — Kanban stila personīgo uzdevumu un mērķu organizēšanas rīks, kas sastāv no kolonnaām un uzdevumiem.
- **Eizenhauera matrica** — Dvaita Eizenhauera izgudrotā metode, kas sastāv no 4 kvadrantiem:
    - Nesteidzams un nesvarīgs - ieteicams šādus uzdevumus atmest;
    - Nesteidzams un svarīgs - ieteicams šādus uzdevumus atlikt;
    - Steidzams un nesvarīgs - ieteicams šādus uzdevumus deleģēt;
    - Steidzams un svarīgs - ieteicams šādus uzdevumus pildīt;
- **Kalendārs** — standarta mēnešu kalendārs, kurā var atlikt savu uzdevumu beigu termiņus.
- **Forumi** — kategorizētas vietas, kur cilvēki spēj dalīties pieredzē, palīdzēt citiem vai tieši otrādāk, prasīt padomus sava darba organizēšanā.
- **Konti** — ir iespēja reģistrēties, paverot iespēju publicēt postus un komentārus forumos.

---

## Izmantotās tehnoloģijas

| Kur pielietota | Tehnoloģija |
|---|---|
| Backend | PHP 8.2+, Laravel 12 |
| Frontend | Vue 3.5, Vuetify 3.11 |
| Būvēšana | Vite 7 |
| Stilizēšana | Tailwind CSS 4, MDI Icons |
| Datubāze | SQLite |

---

## Instalēšana

Projekts diemžēl šobrīd nav pieejams tiešsaistē, tāpēc, ja jūs vēlaties palaist to:

### Nepieciešamie rīki

- PHP >= 8.2
- [Composer](https://getcomposer.org/)
- Node.js >= 18 un npm

### Soļi

```bash
# 1. Klonēt repozitoriju
git clone <repozitorija-saite>
cd my-laravel-app

# 2. Instalēt PHP atkarības
composer install

# 3. Sagatavot vidi
cp .env.example .env
php artisan key:generate

# 4. Izveidot datubāzi un palaist migrācijas
php artisan migrate

# 5. Instalēt Node atkarības
npm install
```

### Palaišana

Nepieciešami divi vienlaicīgi atvērti cmd termināļi:

```bash
# 1. cmd — Laravel izstrādes serveris
php artisan serve
```

```bash
# 2. cmd — Vite (automātiska priekšpuses atjaunošana)
npm run dev
```

Pēc tam atveriet [http://localhost:8000](http://localhost:8000) savā pārlūkprogrammā.

---

## Autors

**Daniels Šabovičs** — DP3-1  
Rīgas Valsts tehnikums, 2025/2026

---

## Pateicības

- [Laravel](https://laravel.com/)
- [Vue.js](https://vuejs.org/)
- [Vuetify](https://vuetifyjs.com/)
- [Vite](https://vite.dev/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Material Design Icons](https://pictogrammers.com/library/mdi/)
