{% extends 'layout.php' %}

{% block page_title %}Strona główna{% endblock %}
{% block content %}
<h2>Cennik</h2>
{% if RETMSG is defined %}
{% if RETMSG == 0 %}<p class="msgok">Twoja wiadomość została wysłana. Dziękujemy!</p>{% endif %}
{% if RETMSG == 1 %}<div class="warning"><p><b>Nie wysłano wiadomości!</b><br>Wystąpił błąd podczas wysyłania. Prosimy spróbować ponownie.</p></div>{% endif %}
{% if RETMSG == 2 %}<div class="warning"><p><b>Nie wysłano wiadomości!</b><br>Wymagane pola nie zostały uzupełnione.</p></div>{% endif %}
{% endif %}
	<div id="cennik_left">
		<table class="cennik" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th class="usluga">Usługa</th>
					<th><img alt="UPS" src="res/logo/ups_min.png"><br>Cena netto</th>
					<th><img alt="UPS" src="res/logo/ups_min.png"><br>Cena brutto</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="usluga">Przesyłka kopertowa</td>
					<td>18,00 zł</td>
					<td>21,77 zł</td>
				</tr>
				<tr>
					<td class="usluga">Przesyłka do 30 kg</td>
					<td>18,00 zł</td>
					<td>21,77 zł</td>
				</tr>
                                <tr>
					<td class="usluga">Przesyłka od 31 kg do 54 kg</td>
					<td>23,53 zł</td>
					<td>28,94 zł</td>
				</tr>
                                <tr>
					<td class="usluga">Przesyłka od 55 kg do 59 kg</td>
					<td>26,62 zł</td>
					<td>32,74 zł</td>
				</tr>
                                <tr>
					<td class="usluga">Przesyłka od 60 kg do 64 kg</td>
					<td>30,02 zł</td>
					<td>36,92 zł</td>
				</tr>
                                <tr>
					<td class="usluga">Przesyłka od 65 kg do 69 kg</td>
					<td>33,25 zł</td>
					<td>40,90 zł</td>
				</tr>
                                <tr>
					<td class="usluga">Przesyłka 70 kg</td>
					<td>36,64 zł</td>
					<td>45,07 zł</td>
				</tr>                                
			</tbody>
		</table>

		

		<table class="cennik" cellpadding="0" cellspacing="0" border="0" style="margin-top: 30px;">
			<thead>
				<tr>
					<th class="usluga">Usługi dodatkowe <img alt="UPS" src="res/logo/ups_min.png" style="vertical-align: middle;"></td>
					<th style="height: 40px;">Cena netto</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="usluga">Dostawa na adres prywatny</td>
					<td>+ 2 zł</td>
				</tr>
				<tr>
					<td class="usluga">Ubezpieczenie do 323 zł</td>
					<td><b>darmowe</b></td>
				</tr>
				<tr>
					<td class="usluga">Ubezpieczenie od 323 do 50 000 zł</td>
					<td>+ 2 zł</td>
				</tr>
				<tr>
					<td class="usluga">Ubezpieczenie od 50 001 do 100 000 zł</td>
					<td>+ 19 zł + 0,2% wartości</td>
				</tr>
				<tr>
					<td class="usluga">Przesyłka pobraniowa standard - zwrot w ciągu 6 dni roboczych</td>
					<td>Za każdy rozpoczęty 1 tys. zł:<br>+ 4 zł</td>
				</tr>
				<tr>
					<td class="usluga">Zwrot potwierdzonych dok. (ROD)</td>
					<td>+ 7,59 zł</td>
				</tr>
				<tr>
					<td class="usluga">Dopłata za przesyłkę niestandardową</td>
					<td>+ 10 zł</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div id="cennik_right">
		<h2>Wysyłasz więcej?<br>Wysyłaj jeszcze taniej!</h2>
		<p>Wysyłasz więcej paczek? Pozostaw swoje dane, a&nbsp;skontaktujemy się z Tobą w celu przedstawienia oferty:</p>
		<form name="oform" action="cennik" class="myform" method="post">
		<fieldset>
			<legend>Pozostaw dane</legend>
			<p><input type="text" name="imienaz" placeholder="Imię i nazwisko" required></p>
			<p><input type="text" name="email" placeholder="E-mail" required></p>
			<p><input type="text" name="telefon" placeholder="Numer telefonu"></p>
			<button type="submit">Pozostaw dane</button>
		</fieldset>
		</form>
	</div>
{% endblock %}