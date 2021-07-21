Úlohy:

Zadanie je zamerané na preberanie údajov z iných zdrojov zverejňovaných na web stránkach bez použitia API (využívanie API je naplánované na iné zadanie). Údaje zo stránok preberajte prioritne pomocou CURL. 
Vytvorte webovú aplikáciu, ktorá umožní zobraziť pravidelne aktualizované údaje o dochádzke na predmete WebTe2.

1.	Na stránku
https://github.com/apps4webte/curldata2021   
sú postupne pridávané reporty o účasti na prednáškach z predmetu WebTe2. Úlohou je, si tieto údaje stiahnuť k sebe na server do adresárovej štruktúry alebo do databázy. Stiahnutie, resp. importovanie do databázy, nerobte manuálne, ale použite na to vami vytvoreny skript. Ďalšou požadovanou funkcionalitou totiž je, aby sa pri každom zobrazení vašej stránky skontrolovalo, či sa na vašom serveri nachádzajú aktualizované údaje. T.j., či sa na github-e nezobrazil nejaký nový súbor, ktorý ste si ešte nestiahli. Ak áno, tak tieto údaje preberte k sebe na server.
2.	Oboznámte sa so štruktúrou poskytnutých súborov. Obsahujú informácie o tom, ktorí študenti boli na danej online prednáške, kedy sa na ňu prihlásili a kedy z nej odišli.
3.	Na stránke zobrazte tabuľku, kde v jednotlivých stĺpcoch budú zobrazené nasledovné údaje:
•	prvý stĺpec 				meno študenta
•	druhý až predpredposledný stĺpec 	počet minút strávených na jednotlivých 
prednáškach (v záhlaví týchto stĺpcov bude uvedené poradie a dátum prednášky)
•	predposledný stĺpec 			celkový počet účastí na prednáškach
•	posledný stĺpec			celkový počet minút strávených na 
prednáškach

4.	Tabuľka bude zotriediteľná podľa priezviska študenta a podľa posledných dvoch stĺpcov. V prípade zhodných údajov bude druhé kritérium zoradenia priezvisko alebo krstné meno.
5.	Pri kliknutí myšou na číslo uvedené pri jednotlivých prednáškach (druhý až predpredposledný stĺpec) sa zobrazí v modálnom okne detail účasti daného študenta na prednáške, t.j. kedy na prednášku prišiel a odišiel. V prípade, že počas prednášky študent miestnosť opustil a opätovne sa do nej vrátil, tak v detaile uveďte všetky jeho príchody a odchody (ktoré teoreticky môžu súvisieť s výpadkami Internetu). 
6.	V prípade, že študent podľa csv súboru neodišiel z miestnosti po skončení prednášky, tak zodpovedajúce číslo v druhom až predpredposlednom stĺpci bude farebne odlíšené. V takomto prípade uvažujte, že čas jeho odchodu je rovnaký ako posledný evidovaný odchod z miestnosti (kvôli určeniu počtu minút strávených na prednáške).
7.	Na stránke (alebo na podstránke) zobrazte graf, ktorý bude zobrazovať, koľko študentov sa zúčastnilo na ktorej prednáške. Na jeho vykreslenie je možné použiť ľubovoľnú JS knižnicu alebo aj GD knižnicu na strane serveru.
