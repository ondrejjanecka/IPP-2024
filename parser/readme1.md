# Implementační dokumentace k 1. úloze do IPP 2023/2024

**Jméno a příjmení:** Ondřej Janečka

**Login:** xjanec33

## Obsah

1. Úvod
2. Struktura kódu
3. Zpracování instrukcí
4. Testování a ladění
5. Závěr

## Úvod

Dokumentace popisuje způsob řešení první úlohy z projektu IPP 2024. Cílem úlohy bylo vytvořit parser skriptů v jazyce IPPcode24, který prování lexikální analýzu, syntaktickou analýzu a generuje odpovídající výstup ve formátu XML. Součástí úlohy byla i možnosti rozšíření, rozhodl jsem se pro implementaci rozšíření STATP, které sbírá statistiky zdrojového kódu IPPcode24.

## Struktura kódu

Kód je rozdělený do jednotlivých funkcí, které se následně volají v průběhu analýzy kódu. Především se jedná o rozdělení podle počtu a typu argumentů a kontroly těchto typů.

## Zpracování instrukcí

Instrukce jsou rozděleny do osmi kategorií podle dostupných operandů pro každou instrukci. Následně proběhne kontrola typů a formátu. Poté je instrukce převedena do XML formátu spolu s případnými argumenty.

## Testování a ladění

Pro ověření správnosti parseru byly využity testovací skripty, které pokrývají různé scénáře a situace. Parser byl laděn a testován na různých vstupech, aby byla zajištěna jeho robustnost a spolehlivost.

## Závěr

Implementace projektu je plně funkční včetně implementace rozšíření STATP pro výpis statistik zdrojového kódu IPPcode24.
