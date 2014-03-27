{% extends 'layout.php' %}

{% block page_title %}Przedmioty niedozwolone w wysyłce:{% endblock %}
{% block content %}
	<h2>Przedmioty niedozwolone w wysyłce:</h2>
            <div class="textcontent" style="position: relative;">
                <img src="/public/img/niedozwolone.png" alt="Przedmioty niedozwolone w wysyłce" style="position: absolute; right: 350px; top: 40px;">
                <ol>        
                    <li>przesyłki o wartości deklarowanej powyżej 100 000,00 zł,</li>
                    <li>nośniki zawierające wartości sentymentalne, np. pamiętniki, czy inne dokumenty o wartości emocjonalnej oraz sentymentalnej,</li>
                    <li>ciecze,</li>
                    <li>rzeczy przewożone pod plombą celną,</li>
                    <li>mienie przesiedleńcze,</li>
                    <li>organy ludzkie, płyny ustrojowe oraz <br>produkty metabolizmu ludzkiego</li>
                    <li>Przedmioty o wyjątkowej wartości, np.: <br>
                    dzieła sztuki, antyki, kamienie 
                    szlachetne, złoto i srebro</li>
                    <li>Biżuteria i zegarki na rękę (oprócz<br> sztucznej biżuterii i niskiej klasy zegarków) o wartości przekraczającej 500 USD lub<br> równowartości tej kwoty w lokalnej walucie.</li>
                    <li>Broń palna</li>
                    <li>Futra</li>
                    <li>Kość słoniowa i wykonane z niej wyroby</li>
                    <li>Materiały pornograficzne</li>
                    <li>Napoje alkoholowe</li>
                    <li>Nasiona</li>
                    <li>Pieniądze, papiery wartościowe, takie jak: czeki, weksle, obligacje, bony skarbowe, akcje oraz inne dokumenty takie jak przedpłacone karty (pre-paid)</li>
                    <li>Rośliny</li>
                    <li>Skóry zwierząt (nieudomowionych)</li>
                    <li>Towary/materiały niebezpieczne (zgodnie z przepisami IATA oraz przepisami ADR)</li>
                    <li>Bagaż bez właściciela, czyli walizki, teczki, torby, plecaki, aktówki i inne tego typu przedmioty, niezależnie od ich zawartości (tego typu przedmioty mogą być wysyłane puste, niezamknięte i prawidłowo zapakowane, zgodnie ze wskazówkami firm kurierskich)</li>
                    <li>Towary nietrwałe</li>
                    <li>Tytoń i produkty tytoniowe</li>
                    <li>Żywe zwierzęta</li>
                </ol>
                <p>Informacje dodatkowe:</p>
                <p>Przesyłki zawierające sprzęt AGD (min. pralki, suszarki, kuchnie, kuchenki mikrofalowe, płyty ceramiczne) powinny być umieszczone na palecie (niezależnie od wagi przesyłki).</p>
                <p>Dodatkowe informacje znajdują się w regulaminach przewoźników:<br>
                    <a href="http://www.ups.com.pl/conditions/index.php"title="UPS">http://www.ups.com.pl/conditions/index.php</a></p>
        </div>
                    
{% endblock %}