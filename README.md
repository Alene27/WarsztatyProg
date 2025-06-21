Instrukcja - NBP

Co to jest?
Aplikacja do sprawdzania średniego kursu waluty z wybranego okresu.

Jak uruchomić?
```
mvn spring-boot:run
```
Potem wejdź na: `http://localhost:8080`

Jak sprawdzić kurs?

 1. Wpisz walutę
Przykłady: USD, EUR, GBP, CHF

 2. Wybierz daty  
- Od: np. 2024-01-01
- Do: np. 2024-01-31

 3. Kliknij "Sprawdź"

 4. Zobacz wynik
Aplikacja pokaże średni kurs z tego okresu.

 Częste problemy

"Nie znaleziono danych"
- Sprawdź czy waluta jest poprawna (3 litery)
- Wybierz dni robocze (nie weekend)

"Błędna data" 
- Data od 2002-01-02 do dzisiaj
- Data "od" musi być wcześniejsza niż "do"

"Za dużo zapytań"
- Poczekaj chwilę i spróbuj ponownie

 Co zapisuje aplikacja?
Każde sprawdzenie zapisuje w bazie:
- Walutę
- Daty
- Obliczony kurs
- Czas zapytania

 Pliki projektu

```
src/main/java/pl/pjatk/jazs29866nbp/
├── JazS29866NbpApplication.java   # Główna klasa
├── NbpController.java             # Obsługa strony
├── NbpService.java               # Logika
├── NbpRepository.java            # Baza danych
├── ExchangeRateQuery.java        # Tabela w bazie
└── AppConfig.java                # Konfiguracja

src/main/resources/
├── templates/index.html          # Strona internetowa
└── application.properties       # Ustawienia bazy

pom.xml                          # Zależności Maven
```

 Konfiguracja bazy (application.properties)

```properties
spring.datasource.url=jdbc:mysql://szuflandia.pjwstk.edu.pl:3306/s29866
spring.datasource.username=s29866
spring.datasource.password=Ale.Kozl
spring.jpa.hibernate.ddl-auto=update
```

 API (dla programistów)

```
GET /api/average-rate?currency=USD&startDate=2024-01-01&endDate=2024-01-31
```

Zwraca JSON:
```json
{
  "currency": "USD",
  "startDate": "2024-01-01",
  "endDate": "2024-01-31", 
  "averageRate": 4.2537,
  "queryDate": "2024-12-20"
}
```