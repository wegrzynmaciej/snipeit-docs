# SnipeIT Docs

Aplikacja do generowania protokołów przekazania sprzętu na podstawie danych wyciągniętych z systemu do inwentaryzacji sprzętu [SnipeIT](https://snipeitapp.com/)

## Działanie

1. Wybranie typu protokołu - zdawczy lub odbiorczy i wpisanie numerów inwentarzowych
2. Utworzenie requesta do SnipeIT przez API
    1. Pobranie danych sprzętu
    2. Pobranie danych użytkownika na podstawie ID sprzętu
3. Wygenerowanie obiektu dokumentu na podstawie parametrów i dodanie danych sprzętu
4. Sformatowanie treści HTML
5. Konwersja na plik PDF, zapisanie w lokalizacji sieciowej i otwarcie do wydruku

### TODO
* Frontpage - formularz
* Generacja wielu protokołów dla wielu osób na raz
* Autowykrywanie typu protokołu (na podstawie daty checkin/checkout?)