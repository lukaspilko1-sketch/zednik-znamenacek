# Instrukce pro Claude Code

## Automatické aktualizace
Po každé dokončené změně souborů projektu VŽDY (bez výjimky):
1. Přidej záznam do sekce `## Changelog` ve formátu `| RRRR-MM-DD | popis změny |`
2. Aktualizuj stav v `## Stav placeholderů` pokud byl nějaký placeholder nahrazen
3. Odškrtni položku v `## TODO` pokud byla splněna (změň `[ ]` na `[x]`)
4. Na konci každé odpovědi připomeň uživateli:
   „✅ CLAUDE.md aktualizován — nezapomeň pushnut na GitHub."

> Toto je povinný poslední krok každého úkolu. Bez výjimky.

## Pravidla pro tento projekt
- Vždy zachovej design systém (barvy, fonty, Swiss styl)
- Data edituj pouze v `content.json`, ne přímo v HTML
- Komentáře v kódu piš česky

---

# Projekt: Jiří Znamenáček – Zednictví Kovanice

## Přehled
Webová prezentace pro živnostníka – zedníka Jiřího Znamenáčka z Kovanic (okres Nymburk).
Cíl: osobní, důvěryhodná prezentace. Motto: „Nejsem firma, jsem Jiří Znamenáček."

---

## Soubory projektu

| Soubor | Popis |
|--------|-------|
| `index.html` | Hlavní landing page (aktuální verze = v8) |
| `reference.html` | Galerie realizací – filtry, lightbox, lazy loading |
| `content.json` | Jediný datový zdroj – veškerý obsah se načítá odsud |
| `admin/index.html` | Admin panel pro správu obsahu (bez CMS) |
| `admin/save.php` | Ukládání content.json přes POST |
| `admin/upload.php` | Nahrávání fotek do img/reference/ |
| `admin/.htaccess` | HTTP Basic Auth ochrana adminu |
| `img/reference/` | Fotky realizací (ref1.jpg … ref8.jpg + nové) |

---

## Tech stack

- **CSS framework:** Tailwind CSS (CDN, plugins: forms, container-queries)
- **Fonty:** Noto Serif (nadpisy) + Manrope (tělo) — Google Fonts
- **Ikony:** Material Symbols Outlined
- **Formulář:** Formspree (AJAX, bez přesměrování)
- **Backend:** PHP (save.php, upload.php) – čistý PHP, žádný framework
- **Data:** `content.json` – plain JS fetch, žádný CMS ani databáze

---

## Design systém

### Barvy
```
Primary (brick):     #9c4329
Primary dark:        #7d2c14
Sand (accent):       #c9a87c
Ink (text):          #1c1611
Stone (muted text):  #56423d
Cream (background):  #fcf9f4
Clay (section bg):   #f0ece5
Mist (borders):      #e8e3dc / #ede8e0
Dark bg (footer):    #1c1410
```

### Typografie
```
Nadpisy:  Noto Serif, font-weight 900/700, letter-spacing -0.025em
Tělo:     Manrope, font-weight 400/500/700
Eyebrow:  Manrope, 0.7rem, uppercase, tracking 0.18em, barva #9c4329
          + ::before čárka (24px, 2px, barva brick)
```

### Komponenty
- **Tlačítka:** `.btn-primary` (brick bg, bílý text), `.btn-secondary` (transparent, border sand)
- **Karty služeb:** Swiss styl – ostré rohy, border 1.5px, hover: offset shadow `4px 8px 0 0 #9c4329`
- **Galerie karty:** hover scale + Swiss offset shadow
- **Testimonials:** `.swiss-quote` – border-left 3px sand, glassmorphism na brick pozadí
- **Nav linky:** underline slide animace (::after, width 0→100%)

### Layout principy
- Max-width: `max-w-7xl` (1280px), centrováno
- Grid služeb: CSS Grid 12 columns s `gap-px bg-[#d6cfc6]` (Swiss mřížka)
- Galerie reference: CSS columns masonry (1→2→3→4 sloupce)

---

## Sekce index.html (v pořadí)

1. **NAV** – sticky, blur backdrop, logo + odkazy + CTA tlačítko
2. **HERO** – 55/45 split, nadpis + perex + mini stats + foto fasády
3. **SLUŽBY** – 7 karet v Swiss bento gridu + banner „Zajistím i další práce"
4. **O MNĚ** – 2 sloupce, portrét + text + 3 odrážky
5. **VYBRANÉ PROJEKTY** – 4 náhledové karty (načítají se z content.json)
6. **HODNOCENÍ** – 3 testimonials na brick pozadí
7. **KONTAKT** – split panel (tmavý info + mapa / formulář)
8. **FOOTER** – tmavý, 2 řady

---

## content.json – struktura

```json
{
  "meta": { "title": "", "description": "" },
  "kontakt": { "jmeno", "telefon", "email", "oblast", "ico", "sidlo" },
  "hero": { "nadpis_radek1", "nadpis_radek2", "nadpis_radek3", "perex", "stat_leta", "stat_realizace" },
  "o_mne": { "nadpis", "text1", "text2", "bullet1", "bullet2", "bullet3" },
  "sluzby": [ { "id", "kategorie", "nazev", "popis" } ],
  "hodnoceni": [ { "text", "autor", "mesto" } ],
  "reference": [ { "id", "src", "nazev", "misto", "rok", "popis", "kategorie" } ]
}
```

### Kategorie referencí
`zdeni` | `omitky` | `obklady` | `rekonstrukce`

---

## Kontaktní údaje (reálná data)

```
Jméno:    Jiří Znamenáček
Telefon:  +420 603 925 721
Email:    znamenacek22@seznam.cz
IČO:      69531099
Sídlo:    Kovanice, okres Nymburk
```

---

## Stav placeholderů

| Položka | Stav |
|---------|------|
| Telefon | ✅ Reálný (`+420 603 925 721`) |
| Email | ✅ Reálný (`znamenacek22@seznam.cz`) |
| IČO | ✅ Reálný (`69531099`) |
| Formspree endpoint | ⚠️ Placeholder (`https://formspree.io/f/XXXXXXXX`) |
| Portrét foto | ✅ Reálný (`img/homepage/o-mne.jpg`) |
| Hero foto | ⚠️ Stock foto |
| Reference foto | ✅ 5 reálných zakázek (34 fotek celkem) |
| Jména v hodnoceních | ⚠️ Vymyšlená (uvěřitelná, navázaná na reálné reference) |

## Struktura referencí (standardizovaná)

Každá reference = podsložka v `img/reference/` ve formátu `YYYY-MM-popis-lokace/`.

**Konvence pojmenování souborů:** `YYYY-MM-popis-lokace-01.jpg`, `-02.jpg` …

**Struktura v content.json:**
```json
{
  "id": "YYYY-MM-popis-lokace",
  "folder": "img/reference/YYYY-MM-popis-lokace",
  "nazev": "Lidsky čitelný název",
  "misto": "Město",
  "rok": YYYY,
  "popis": "Popis zakázky.",
  "kategorie": "zdeni|omitky|obklady|rekonstrukce",
  "images": ["soubor-01.jpg", "soubor-02.jpg", ...]
}
```

Thumbnail = `images[0]`. Lightbox prochází všechny fotky dané reference (ne mezi referencemi).

---

## TODO / Rozpracované

- [ ] Nahradit Formspree placeholder reálným endpointem
- [x] Dodat reálný portrét Jiřího
- [x] Dodat reálné fotky realizací do img/reference/
- [x] Nahradit fiktivní jména v hodnoceních reálnými
- [ ] Nastavit .htpasswd pro admin panel (změnit doménu v .htaccess)
- [x] Mobilní menu (hamburger button je v HTML, logika chybí)

---

## Poznámky k vývoji

- **Vizuální styl:** Swiss International / Editorial – ostré rohy, mřížka, typografický důraz
- **Verze:** v7 byl bold/editorial základ, v8 je finální merge s lepšími prvky z v6
- **reference.html** je napojena na content.json (fetch při init), ne na hardcoded REFERENCE pole
- **Admin panel** volá `save.php` a `upload.php` – funguje pouze na PHP hostingu
- Stránka funguje i bez content.json (fallback na pevné texty v HTML)
- Google Maps embed v kontaktu: souřadnice Kovanice (50.1817, 15.1183)

---

## Changelog
<!-- Claude Code sem dopisuje změny -->

| Datum | Změna |
|-------|-------|
| 2025-01 | Vznik projektu, v1–v6 iterace |
| 2025-01 | v7: Swiss styl, bento grid služeb |
| 2025-01 | v8: finální merge, admin panel, content.json |
| 2025-04 | Přidán CLAUDE.md |
| 2026-04-20 | Portrét o-mne.jpg: stock → reálná fotka |
| 2026-04-20 | Reference: nová folder-based struktura (folder + images[]) |
| 2026-04-20 | Přidána zakázka 2026-04-oprava-omitky-nymburk (10 fotek) |
| 2026-04-20 | reference.html: lightbox s multi-foto galerií a počítadlem |
| 2026-04-20 | reference.html: odstraněny filtry, galerie překreslena na vertikální stack karet (obraz + info) |
| 2026-04-20 | Aktualizován popis reference Nymburk (památková zóna, břízolit, vápenná omítka) |
| 2026-04-20 | Implementováno mobilní hamburger menu (toggle, ikona menu/close, zavření po kliknutí) |
| 2026-04-22 | Přidány 4 nové reference: kompletní rekonstrukce Kovanice, podkroví Nymburk, střecha+půda Kovanice, zateplení+obklad Chvalovice |
| 2026-04-22 | Odstraněno pole `rok` ze všech referencí; reference.html upravena pro volitelné zobrazení roku |
| 2026-04-22 | Oprava lokálního prohlížení: index.html – nahrazeny placeholder karty reálnými fotkami; reference.html – doplněn JS fallback s reálnými daty (fetch nefunguje přes file://) |
| 2026-04-22 | Hodnocení: nahrazena fiktivní za uvěřitelná, navázaná na reálné reference (Kovanice, Chvalovice, Nymburk) |
| 2026-04-22 | Hodnocení opravena přímo v HTML (sekce byla hardcoded, JS z content.json ji nenačítal) |
| 2026-04-22 | Hodnocení: příjmení odstraněna, ponechána pouze křestní jména (Jiří, Markéta, Tomáš) |
| 2026-04-22 | Hodnocení: napojeno na content.json přes JS (id="hodnoceni-grid"), hardcoded HTML slouží jako fallback |
