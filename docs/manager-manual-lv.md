# Administratora rokasgrāmata — Cargo Trans

**Krievu valodā:** [manager-manual.md](manager-manual.md)

Detalizēti norādījumi darbam ar **Cargo Trans** tīmekļa lietotni: visi sadalījumi, uzlabojumi un galvenās darbības lietotājam ar pilnu piekļuvi (administrators/vadītājs).

---

## 1. Pieslēgšanās un navigācija

- Sistēmā ieiet ar **e-pastu un paroli** (kontu izveido administrators).
- Pēc pieslēgšanās atveras **galvenā lapa (Dashboard)** — dokumentu ar beidzamo termiņu tabula.
- Kreisajā pusē atrodas **sānu izvēlne**:
  - **Dashboard** — kopsavilkums par dokumentiem ar beidzamo termiņu (šoferi, vilcēji, piekabes).
  - **Transports** — apakšizvēlne: **Šoferi**, **Vilcēji**, **Piekabes**, **Pārvadātāji** (trešās puses), **Karte**, **Apkope**.
  - **Reisi un pasūtījumi** — apakšizvēlne: **Pasūtījumi**, **Reisi**.
  - **Klienti** — klientu bāze (uzņēmumi).
  - **Statistika** — apakšizvēlne: **Īpašnieka panelis**, **Pārskats** (reisi par periodu), **Odometra notikumi**, **Klienti**, **Dīkstāve**.
  - **Rēķini** — rēķinu saraksts un maksājumi.
- Galvenē: lapas virsraksts un poga **Iziet**.

Mobilajās ierīcēs izvēlne atveras ar pogu «hamburgeris» (☰); saskarne ir pielāgota mobilajiem ekrāniem.

---

## 2. Dashboard (galvenā)

Galvenajā lapā (**/dashboard**) tiek rādīta **dokumentu ar beidzamo termiņu tabula** (derīguma termiņš nākamo 30 dienu laikā).

- **Objektu veidi**: šoferi, vilcēji, piekabes.
- **Dokumenti**:
  - **šoferiem**: vadītāja apliecība (License), Code 95, atļauja (Permit), medicīniskā izziņa (Medical), deklarācija (Declaration), medicīniskā pārbaude;
  - **vilcējiem**: tehniskā apskate (Inspection), apdrošināšana (Insurance), tehniskā pase (Tech passport);
  - **piekabēm**: tehniskā apskate, apdrošināšana, TIR, tehniskā pase.
- Pieejami **meklēšana**, **kārtošana** pēc kolonnām un **rindu skaits** lapā.
- Katrā rindā redzams: objekta veids, nosaukums (šofera vārds/uzvārds vai TС marka un numurs), dokumenta veids, beigu datums, atlikušo dienu skaits, uzņēmums, statuss.

Izmantojiet Dashboard dokumentu operatīvai kontrolei. Paplašinātais dokumentu ar beidzamo termiņu saraksts un gaidāmā apkope pieejami sadaļā **Transports → Apkope** (sk. 11. sadalījumu).

---

## 3. Šoferi

- **Šoferu saraksts** — tabula ar meklēšanu un kārtošanu.
- **Pievienot šoferi** — poga «Izveidot» → forma: vārds, uzvārds, kontakti, dokumenti (vadītāja apliecība, Code 95, medicīniskā izziņa u.c.), uzņēmums, PIN mobilās lietotnes pieslēgšanai (4–6 cipari).
- **Šofera kartīte** — visu datu, dokumentu un saistību apskate.
- **Rediģēšana** — datu un dokumentu maiņa.
- Pēc vajadzības kartītē/rediģēšanā iespējams **dzēst** šoferi (ja tas paredzēts saskarnē).

Šofera PIN nepieciešams viņam, lai pieslēgtos mobilajai lietotnei (sadaļa «Šoferis»).

---

## 4. Transports

### Vilcēji

- **Vilcēju saraksts** — tabula ar pamatdatiem (marka, modelis, valsts numurs, statuss, uzņēmums).
- **Pievienot vilcēju** — forma: marka, modelis, numurs, uzņēmums, dokumentu datumi (tehniskā apskate, apdrošināšana, tehniskā pase), pēc vajadzības — CAN klātbūtne (automātiskajam odometram).
- **Vilcēja kartīte** — pilni dati, dokumenti, vēsture.
- **Rediģēšana** — datu un dokumentu termiņu maiņa.

### Piekabes

- **Piekabju saraksts** — tabula (marka, numurs, tips, uzņēmums u.c.).
- **Pievienot piekabi** — tips (iesk. konteineru), marka, numurs, dokumenti (tehniskā apskate, apdrošināšana, TIR, tehniskā pase).
- **Kartīte** un **rediģēšana** — pēc analoģijas ar vilcējiem.

Transports tiek piesaistīts reisiem, izveidojot vai rediģējot reisu.

### Pārvadātāji (trešās puses / external carriers)

- **Pārvadātāju saraksts** (**/carriers**) — trešo pārvadātāju uzziņu grāmata (uzņēmumi, kontakti). Izmanto, izveidojot reisu ar pārvadātāju «trešā puse».
- **Pievienot pārvadātāju** — nosaukums, kontakti, rekvizīti.
- **Kartīte** un **rediģēšana** — datu apskate un maiņa.

Izvēloties trešās puses pārvadātāju reisa formā, var norādīt **esošu pārvadātāju no uzziņu grāmatas** vai ievadīt vilcēja/piekabes datus un fraštu manuāli (vilcēju un piekabju numuriem trešajām pusēm **nav jābūt unikāliem** sistēmā).

### Karte

- **Karte** (**/map**) — transporta attēlojums kartē (ja ir atrašanās vietas dati).

---

## 5. Klienti

- **Klientu saraksts** — uzņēmumi (klienti, sūtītāji, saņēmēji).
- **Pievienot klientu** — nosaukums, valsts, pilsēta, adrese, rekvizīti, kontakti.
- **Kartīte** un **rediģēšana** — datu apskate un maiņa.

Klienti tiek izmantoti reisos un pasūtījumos kā pasūtītāji (customer), sūtītāji (shipper) un saņēmēji (consignee).

---

## 6. Pasūtījumi (transporta pasūtījumi)

- **Pasūtījumu saraksts** (**/orders**) — transporta pasūtījumu tabula ar meklēšanu un filtriem.
- **Izveidot pasūtījumu** — forma ar pasūtījuma datiem (maršruts, kravas, klients u.c.).
- **Pasūtījuma kartīte** — pasūtījuma apskate, saistība ar reisu (ja pasūtījums jau reisā).

**Saistība pasūtījums ↔ reiss:**

- **Izveidot reisu no pasūtījuma** — no pasūtījuma kartītes var izveidot jaunu reisu; pasūtījums automātiski tiek piesaistīts reisam.
- **Pievienot esošam reisam** — pasūtījuma kartītē poga «Pievienot reisam» atver modālo logu ar piemērotu reisu sarakstu (neabeigti). Viens pasūtījums var būt piesaistīts tikai vienam reisam; pievienot var tikai tad, ja reiss vēl nav pabeigts. Pēc reisa izvēles un apstiprinājuma pasūtījums tiek pievienots izvēlētajam reisam (konsolidācija).
- **Pievienot pasūtījumu no reisa kartītes** — reisa apskatē blokā «Pasūtījumi reisā» (ja reiss nav pabeigts) pieejama viena vai vairāku pieejamo pasūtījumu izvēle un poga «Pievienot pasūtījumus reisam»; izvēlētie pasūtījumi tiek pievienoti šim reisam. Jau piesaistītu pasūtījumu var noņemt no reisa ar pogu «Noņemt no reisa».

---

## 7. Reisi

### Reisu saraksts

- **Reisi** — tabula ar filtriem un kārtošanu (pēc datuma, statusa, šofera u.c.).
- No saraksta var pāriet uz reisa **izveidi**, **apskati** vai **rediģēšanu**.

### Reisa izveide

Dati aizpilda soli pa solim:

1. **Ekspeditors** — uzņēmuma (expeditor) un bankas izvēle rekvizītiem.
2. **Pārvadātājs**:
   - savs transports (ekspeditors vai cita iekšējā uzņēmuma);
   - vai **trešā puse (third party)** — **pārvadātāja izvēle no uzziņu grāmatas** (/carriers) vai datu ievade manuāli; norāda vilcēju, piekabi un frašta summu. Trešo pušu vilcēju un piekabju numuri var atkārtoties (nav unikāli sistēmā). Reisam tiek pievienoti izdevumi apakšpārvadātājam.
3. **Transports** — šoferis, vilcējs, piekabe (vai trešās puses dati: vilcēja/piekabes marka/numurs, frašts).
4. **Maršruts (soļi)** — iekraušanas un izkraušanas punkti:
   - soļa tips: iekraušana (loading) / izkraušana (unloading);
   - valsts, pilsēta, adrese, datums un laiks;
   - soļu secību var mainīt (vilkt un nomest rediģējot).
5. **TIR / muitas** — starptautiskos pārvadājumos iespējams ieslēgt muitas noformēšanu (TIR) un norādīt **TIR robežpunktu** (pārejas/muitas punkta adrese).
6. **Kravas (cargos)** — viena vai vairākas:
   - klients (customer), sūtītājs (shipper), saņēmējs (consignee);
   - frašta cena, PVN;
   - **kravas pozīcijas (items)**: apraksts, iepakojumi, paletes, svars (tīrais/bruto), tilpums, iekraušanas metri, pēc vajadzības — bīstama kravas, temperatūra, muitas kods (customs_code) u.c.;
   - kravas piesaistīšana **iekraušanas un izkraušanas soļiem**.
7. Konteineru reisiem — konteineru numurs un zīmogi (ja izmantoti formā).

Pēc saglabāšanas tiek izveidoti reiss, maršruta soļi, kravas un pozīcijas; ar trešās puses pārvadātāju — pēc vajadzības uzņēmums, vilcējs, piekabe un izdevumi. Vienā reisā var būt **vairāki pasūtījumi/kravas** (konsolidācija); pasūtījumus pievieno no sadaļas **Pasūtījumi** ar «Pievienot reisam», izveidojot reisu no pasūtījuma vai **reisa kartītē** blokā «Pasūtījumi reisā».

### Reisa apskate

Lapā tiek rādīts:

- **Galvene**: reisa numurs, statuss, shēma (savs/trešā puse), ekspeditors, pārvadātājs, transports (šoferis, vilcējs, piekabe), ar third party — fiksēta samaksa.
- **Kopsavilkuma rādītāji**: kopējais svars, tilpums, iekraušanas metri, frašts ar PVN/bez, preces vērtība, summa pēc piegādātāja rēķiniem u.c.
- **Maršruta redaktors** (akordeons «Maršruts») — **Trip Route Editor**: soļu secības maiņa vilkot, jaunās secības saglabāšana.
- **Pasūtījumi reisā** — bloks ar reisam piesaistīto pasūtījumu sarakstu: pie katra — saite uz pasūtījuma kartīti un poga «Noņemt no reisa». Ja reiss **nav pabeigts**, zemāk tiek rādīts pieejamo pasūtījumu saraksts; var izvēlēties vienu vai vairākus pasūtījumus un nospiest **«Pievienot pasūtījumus reisam»** — izvēlētie pasūtījumi tiek pievienoti reisam (konsolidācija). Pasūtījumus var pievienot gan no **reisa kartītes**, gan no pasūtījuma kartītes («Pievienot reisam»).
- **Kravas pēc klientiem** — kravu grupas ar detaliem datiem:
  - katrai kravai: sūtītājs → saņēmējs, iepakojumi, paletes, svars, tilpums, frašts, preces vērtība, piegādātāja rēķins;
  - ar šo kravu saistītie maršruta punkti (iekraušana/izkraušana) ar datumu un laiku.
- **Dokumenti par kravu**:
  - **CMR** — numurs, poga «Ģenerēt CMR»: tiek izveidots PDF un piesaistīts kravai;
  - **Rēķins (Invoice)** — rēķina ģenerēšana par kravu, PDF saglabāšana.
- **Izdevumi par reisu** — izdevumu saraksts (degviela, maksas ceļi, stāvvieta u.c.), pēc vajadzības pievienošana/rediģēšana no vadītāja puses.

Šoferis izpilda reisu mobilajā lietotnē (soļu statusi, odometrs, dokumentu augšupielāde, izdevumi). Vadītājs šeit kontrolē maršrutu, kravas un dokumentus.

**Publiska izsekošana:** saite **/track/{token}** ļauj nodot klientam vai partnerim piekļuvi reisa izsekošanai **bez pieslēgšanās** sistēmai (token tiek piesaistīts reisam).

### Reisa rediģēšana

Tādi paši bloki kā izveidē: ekspeditors, pārvadātājs, transports, maršruta soļi, kravas un pozīcijas. Izmaiņas tiek saglabātas esošajā reisā. Soļu secību ērtāk mainīt **reisa apskatē** caur maršruta redaktoru.

---

## 8. Statistika

### Īpašnieka panelis (**/stats/owner**)

- **KPI** izvēlētajam periodam: reisi, nobraukums, ieņēmumi (frašts), **izdevumi** (iesk. ceļa un **apkope/remonti**), peļņa, debitoru parādi, dīkstāve.
- Bloks **«Dokumenti ar beidzamo termiņu»** — īss dokumentu saraksts ar beidzamo termiņu (meklēšana, kārtošana pēc datuma/nosaukuma, lapošana). Divas kolonnas: TС/šofera nosaukums un dokumenta veids ar datumu (piemēram: «Tehniskā apskate · 13.05.2025»).
- Bloks **«Gaidāmā apkope»** — tikai **savi** vilcēji un piekabes (ne trešās puses), kuriem norādīta nākamā apkope pēc datuma vai pēc km nākamo 30 dienu laikā. Rādītas atzīmes **«Pēc datuma»** un **«Pēc km»**. Kārtošana un meklēšana, divu kolonnu skats: TС nosaukums | veids un datums/nobraukums.
- Saite «Skatīt visu» ved uz lapu **/maintenance**.

### Pārskats (**/stats**)

- Tabula par reisiem izvēlētajā periodā: datumi, šoferis, vilcējs, nobraukums, **izdevumi** u.c.
- **Izdevumi (Izdevumi)** tiek rādīti ar sadalījumu: **Ceļa izdevumi** (ceļa izdevumi par reisiem) un **Apkope** (apkopes/remontu izmaksas par periodu). Kopējā summa — šo divu sastāvdaļu summa.
- Filtri pēc perioda (datums no/līdz, ātrie diapazoni), meklēšana, kārtošana.
- Izmanto reisu, nobraukuma un izdevumu struktūras analīzei.

### Odometra notikumi (**/stats/events**)

- Odometra notikumu tabula: izbraukšana/atgriešanās garāžā, notikumi pēc maršruta soļiem, šofera izdevumi (degviela, AdBlue) ar odometra piesaisti.
- Filtri: notikuma tips, šoferis, vilcējs, periods (datums no/līdz).
- Noderīgi nobraukuma un izdevumu piesaistes kilometrāžai pārbaudei.

### Statistika pēc klientiem (**/stats/clients**)

- Kopsavilkums pēc klientiem par periodu (apjomi, reisi, summas).

### Dīkstāve (**/stats/downtime**)

- Transporta dīkstāves uzskaite un pārskati.

---

## 9. Rēķini (Invoices)

- **Rēķinu saraksts** — tabula ar numuriem, datumiem, summām, maksājuma statusu (apmaksāts/daļēji/neapmaksāts).
- Filtri: meklēšana, statuss (visi / apmaksāts / daļēji / neapmaksāts), kārtošana.
- **Atvērt PDF** — ģenerētā rēķina apskate vai lejupielāde.
- **Ievadīt maksājumu** — maksājuma datums un summa izvēlētajam rēķinam; atlikuma summa tiek pārrēķināta.

Rēķini par kravām tiek izveidoti no reisa kartītes (rēķina ģenerēšanas poga par kravu).

---

## 10. CMR un rēķini par reisu

- **CMR** tiek ģenerēts **reisa apskatē** katrai kravai: poga «Ģenerēt CMR» → tiek izveidots PDF, saglabāts un piesaistīts kravai. CMR numuru var iestatīt/rediģēt formā.
- **Rēķins (Invoice)** par kravu ģenerēts turpat: rēķina ģenerēšanas poga → tiek izveidots rēķina PDF, ieraksts parādās sadaļā **Rēķini**. Tālāka darbība ar apmaksu — sadaļā «Rēķini».

---

## 11. Parka apkope (Maintenance)

Sadaļa **Transports → Apkope** (**/maintenance**) apvieno dokumentu un gaidāmās apkopes kontroli.

### Dokumenti ar beidzamo termiņu

- Lapā **/maintenance** tiek rādīts pilns **visu dokumentu ar beidzamo termiņu** saraksts (šoferi, vilcēji, piekabes): tehniskā apskate, apdrošināšana, vadītāja apliecība, Code 95, TIR, tehniskā pase, medicīniskā izziņa u.c.
- Pieejami **meklēšana**, **kārtošana** (pēc datuma, pēc nosaukuma), **lapošana** un rindu skaita izvēle lapā.
- Darbvirsmas režīmā — tabula, mobilajās — kartītes.

### Gaidāmā apkope (pēc datuma un pēc nobraukuma)

- Bloks **«Gaidāmā apkope»** rāda **jūsu uzņēmuma** vilcējus un piekabes (ne trešās puses), kuriem norādīta nākamā apkope pēc datuma vai pēc km nākamo 30 dienu laikā.
- Katrai pozīcijai pieejama poga **«Veikt apkopi»** — pāreja uz veiktās apkopes ieraksta izveides formu ar jau aizpildītu transportlīdzekli.
- Kārtošana un meklēšana pēc saraksta.

### Apkopes ierakstu žurnāls

- **Ierakstu žurnāls** (**/maintenance/records**) — visu veiktās apkopes/remontu ierakstu saraksts: transports (marka, numurs), datums, odometrs, apraksts, **izmaksas**.
- Augšā tiek rādīta **izmaksu summa** pēc izvēlētajiem filtriem (periods, meklēšana). Summa tiek pārrēķināta, mainot periodu vai meklēšanu.
- Filtri: **periods** (datums no / līdz), **meklēšana** pēc transporta nosaukuma. Kārtošana: **pēc datuma** vai **pēc transporta nosaukuma**.
- Klikšķis uz rindas atver ieraksta kartīti ar detaļām.

### Apkopes ieraksta izveide un rediģēšana

- **Izveidot ierakstu** (**/maintenance/records/create**) — forma: **vilcēja vai piekabes** izvēle (obligāti viens no diviem), izpildes datums, odometrs (ja ir), darbu apraksts, izmaksas (neobligāti, ja darbi veikti ar saviem līdzekļiem).
- **Nākamā apkope**: obligāti aizpildīt **vienu no laukiem** — «Nākamās apkopes datums» vai «Nākamās apkopes nobraukums (km)». Šīs vērtības tiek saglabātas transportlīdzekļa kartītē un tiek rādītas gaidāmās apkopes blokā un vilcēja/piekabes kartītē.
- Ieraksta **rediģēšana** — tie paši lauki; saglabājot tiek atjaunināts nākamās apkopes datums/nobraukums izvēlētajam TС.

Remontu un apkopes izmaksas tiek ieskaitītas **finanšu rādītājos** (īpašnieka panelis, statistika par reisiem — sk. zemāk).

---

## 12. Profils un iziešana

- **Profils** — savu datu apskate un pēc vajadzības maiņa (ja ieviesta).
- **Iziet** — poga izvēlnē galvenē labajā pusē; pabeidz sesiju un novirza uz pieslēgšanās lapu.

---

## Īsa atgādinājuma tabula pa sadalījumiem

| Sadalījums | Galvenās darbības |
|------------|-------------------|
| **Dashboard** | Dokumenti ar beidzamo termiņu šoferiem, vilcējiem, piekabēm (meklēšana, kārtošana) |
| **Šoferi** | Saraksts, izveide, kartīte, rediģēšana, PIN lietotnei |
| **Vilcēji / Piekabes** | Saraksts, izveide, kartīte, rediģēšana, dokumenti, nākamā apkope pēc datuma/km |
| **Pārvadātāji** | Trešo pārvadātāju uzziņu grāmata; izvēle, izveidojot reisu «trešā puse» |
| **Apkope** | Dokumenti ar beidzamo termiņu (visi veidi), gaidāmā apkope, apkopes ierakstu žurnāls, ieraksta forma (datums, odometrs, apraksts, izmaksas, nākamā apkope) |
| **Pasūtījumi** | Pasūtījumu saraksts; reisa izveide no pasūtījuma; pievienošana reisam no pasūtījuma vai reisa kartītes (bloks «Pasūtījumi reisā») |
| **Reisi** | Saraksts, izveide, apskate (maršruts, **pasūtījumi reisā** — pievienot/noņemt pasūtījumus, kravas, CMR, rēķini, izdevumi), rediģēšana; vairāki pasūtījumi vienā reisā |
| **Statistika → Īpašnieka panelis** | KPI, dokumenti ar beidzamo termiņu, gaidāmā apkope (savi TС), izdevumu sadalījums (ceļš + apkope) |
| **Statistika → Pārskats** | Reisi par periodu, nobraukums, izdevumi (ceļa izdevumi + apkope) |
| **Statistika → Notikumi / Klienti / Dīkstāve** | Odometra notikumi, statistika pēc klientiem, dīkstāve |
| **Rēķini** | Rēķinu saraksts, PDF atvēršana, maksājumu ievade |
| **Izsekošana** | Publiska saite /track/{token} reisa apskatei bez pieslēgšanās |

---

## Saikne ar šofera lietotni

- Šoferis pieslēdzas **mobilajai lietotnei** ar **PIN** (tiek norādīts šofera kartītē).
- Lietotnē šoferis: atzīmē izbraukšanu/atgriešanos garāžā ar odometru, maina soļu statusus (iekraušana/izkraušana), augšupielādē dokumentus par soļiem, pievieno izdevumus par reisu.
- Vadītājs tīmekļa saskarnē redz reisus, maršrutu, kravas, ģenerētos CMR un rēķinus, izdevumus un odometra notikumus statistikā. Pēc vajadzības maršrutu var koriģēt (maršruta redaktors reisa apskatē).

## Mobilā versija

- Administratora saskarne ir pielāgota mobilajām ierīcēm: sānu izvēlne atveras ar pogu «hamburgeris», tabulas mazos ekrānos tiek rādītas kā kartītes, formām un pogām ir pietiekams izmērs nospiešanai. Lapas Apkope, Īpašnieka panelis, Pasūtījumi (modālais logs «Pievienot reisam») un pārējie sadalījumi korekti darbojas viedtālruņos un planšetdatoros.

---

*Rokasgrāmatas versija: 2.0. Cargo Trans — administratora/vadītāja tīmekļa kabinets. Ņemts vērā: trešo pārvadātāju uzziņu grāmata, pasūtījumu konsolidācija reisā, parka apkope (apkope, dokumenti ar beidzamo termiņu, ierakstu žurnāls, izdevumu sadalījums), īpašnieka panelis, publiska reisa izsekošana, mobilā versija.*
