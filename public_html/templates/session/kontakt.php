{% extends 'layout.php' %}

{% block page_title %}Strona główna{% endblock %}
{% block content %}
	<h2>Kontakt</h2>
{% if RETMSG is defined %}
{% if RETMSG == 0 %}<p class="msgok">Twoja wiadomość została wysłana. Dziękujemy!</p>{% endif %}
{% if RETMSG == 1 %}<div class="warning"><p><b>Nie wysłano wiadomości!</b><br>Wystąpił błąd podczas wysyłania. Prosimy spróbować ponownie.</p></div>{% endif %}
{% if RETMSG == 2 %}<div class="warning"><p><b>Nie wysłano wiadomości!</b><br>Wymagane pola nie zostały uzupełnione.</p></div>{% endif %}
{%endif%}
	<div class="mainleft">
		<h3>Dane firmy</h3>
		<p><b>NadajTo</b> prowadzone jest przez firmę:</p>
		<p>
			<b>NadajTo</b>
			<br>43-450 Ustroń
			<br>ul. Wiśniowa 8
			<br>NIP: 498-024-37-41
		</p>
		<p>
			Telefon: +48 883 448 818
			<br>E-mail: <a class="eaddr">biuro@nadajto,pl</a>
		</p>
	</div>
	<div class="mainright" id="kontakt">
		<h3>Napisz do nas</h3>
		<form name="cform" action="" class="myform" method="post">
		<fieldset>
			<legend>Formularz kontaktowy</legend>
			<p><input type="text" name="imienaz" placeholder="Twoja godność" required></p>
			<p><input type="text" name="email" placeholder="E-mail" required></p>
			<p><textarea name="tresc" placeholder="Treść wiadomości" required></textarea></p>
			<button type="submit">Wyślij wiadomość</button>
		</fieldset>
		</form>
	</div>
	<br style="clear: both;">
<script defer="defer" type="text/javascript">
<!--
 qwery('#ludzik')[0].src = 'img/pict_2.png';
//-->
</script>
{% endblock %}