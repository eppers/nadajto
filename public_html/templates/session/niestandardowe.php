{% extends 'layout.php' %}

{% block page_title %}Elementy niestandardowe{% endblock %}
{% block content %}
	<h2>Elementy niestandardowe</h2>
        	<div class="textcontent">
                    <p>Dodatkowa opłata za element niestandardowy, która zostanie doliczona do podstawowej ceny przesyłki ma miejsce, gdy:</p>
                    <ol>
                        <li>przedmioty opakowane są w kontenery wysyłkowe wykonane z metalu lub drewna,</li>

                        <li>przedmioty o kształcie cylindrycznym, takie jak: beczki, bębny, wiadra lub opony, 

                        które nie są całkowicie zamknięte w opakowaniu zewnętrznym z tektury falistej,</li>

                        <li>przedmioty o kształcie okrągłym, cylindrycznym, owalnym, o nieregularnych 

                        kształtach, z wystającymi elementami lub z lepką powierzchnią, uniemożliwiającą 

                        swobodne zsuwanie się (np. folie, gumy, itp.);</li>

                        <li>przedmioty, których zawartość stanowią luźne, ciężkie elementy lub płyny (w tym 

                        chemikalia)</li>

                        <li>paczki krajowe, których wymiary przekraczają: 
                            <ul>
                                <li>długość: 120 cm</li>

                                <li>lub szerokość: 80 cm</li>

                                <li>lub wysokość 80 cm</li>

                                <li>lub suma długości i obwodu przekracza 300 cm</li>
                            </ul>
                        </li>

                        <li>paczki, których waga rzeczywista jest większa niż 32 kg,</li>

                        <li>przesyłki, w których średnia waga paczki w przesyłce przekracza 32 kg i waga każdej 

                        paczki nie jest określona w dokumencie źródłowym lub w automatycznym systemie 

                        wysyłkowym UPS.</li>
                    </ol>
                    
                    
                    ¸ <h3>Wzór na ustalenie sumy długości i obwodu:</h3>

                    <p>1x długość+ (2*wysokość + 2*szerokość)<br>
                        Długość jest liczona jako najdłuższy bok paczki.
                    </p>
                    <img src="/public/img/niestandardowy.png" alt="niestandardowe elementy">
                    <h3>Duża paczka - warunki do spełnienia</h3>
                    <p>
                        Przesyłka UPS jest uznawana za dużą paczkę, jeśli suma jej długości oraz obwodu [(2 x 
                        szerokość) + (2 x wysokość)] przekracza 130 cali (330 cm) i jest mniejsza lub równa 165 cali (419 cm).
                    </p>
                    <p>Kwota dopłaty: <a href="http://nadajto.pl/cennik" title="Cennik">cennik</a></p>
                </div>
{% endblock %}