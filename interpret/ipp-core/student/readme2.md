# Implementační dokumentace k 2. úloze do IPP 2023/2024

**Jméno a příjmení:** Ondřej Janečka

**Login:** xjanec33

## Úvod

Dokumentace popisuje způsob řešení druhé úlohy z projektu IPP 2024. Cílem úlohy bylo vytvořit interpret instrukcí uložených ve formátu XML, který je výstupem skriptu parse.py z první ůlohy.

## Návrh

Navrhovaná implementace projektu je založena na principu objektově orientovaného programování (OOP) a využívá návrhový vzor řetězec odpovědnosti (**Chain of Responsibility**). Tento návrhový vzor se týká vykonávání různých typů instrukcí v aplikaci. Řetězec odpovědnosti umožňuje, aby se každá instrukce zpracovávala nezávisle na ostatních, což poskytuje flexibilitu a snadnou rozšiřitelnost systému. Každý objekt v řetězci odpovědnosti má možnost zpracovat požadavek nebo předat odpovědnost dalšímu objektu v řetězci, až je požadavek úspěšně zpracován nebo dosáhne konce řetězce. Retězec je vytvořený na základě odhadované četnosti jednotlivých instrukcí v programu s cílem o co nejefektivnější zpracovaní.

## Struktura kódu



## Zpracování instrukcí



## Testování a ladění



## Závěr


