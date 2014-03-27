{% extends 'layout.php' %}

{% block page_title %}Strona główna{% endblock %}
{% block content %}
	<h2>Załóż darmowe konto</h2>
{% if ERRMSG %}
{% if ERRMSG == 'FAIL' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>Wystąpił błąd systemowy.</p></div>{% endif %}
{% if ERRMSG == 'LGNTKN' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>Wybrany login jest już zajęty.</p></div>{% endif %}
{% if ERRMSG == 'EMLFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>Adres e-mail nie jest poprawny.</p></div>{% endif %}
{% if ERRMSG == 'TELFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>&quot;Numer telefonu&quot; jest polem wymaganym.</p></div>{% endif %}
{% if ERRMSG == 'SURFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>&quot;Nazwisko&quot; jest polem wymaganym.</p></div>{% endif %}
{% if ERRMSG == 'NAMFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>&quot;Imię&quot; jest polem wymaganym.</p></div>{% endif %}
{% if ERRMSG == 'PSWFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>&quot;Hasło&quot; jest polem wymaganym.</p></div>{% endif %}
{% if ERRMSG == 'PS2FLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>&quot;Powtórz hasło&quot; jest polem wymaganym.</p></div>{% endif %}
{% if ERRMSG == 'PMHFLD' %}<div class="warning"><p><b>Rejestracja nie powiodła się!</b><br>Wpisne hasła nie były identyczne.</p></div>{% endif %}
{% endif %}
	<div class="regleft">
		<form id="rform" action="" class="bform" method="post" onsubmit="return chkTerms();">
		<fieldset>
			<legend>Rejestracja</legend>
			<h4>Rejestracja</h4>
			<p><label><span>Imię<var>*</var>:</span> <input type="text" name="imie" maxlength="40" onchange="propLogin()" onblur="propLogin()" required></label></p>
			<p><label><span>Nazwisko<var>*</var>:</span> <input type="text" name="nazwisko" maxlength="40" onchange="propLogin()" onblur="propLogin()" required></label></p>
			<p><label><span>Telefon<var>*</var>:</span> <input type="text" name="telefon" maxlength="40" required></label></p>
			<p><label><span>Adres e-mail<var>*</var>:</span> <input type="text" name="email" maxlength="80" required></label></p>
			<p><label><span>Hasło<var>*</var>:</span> <input type="password" name="passw" maxlength="40" required></label></p>
			<p><label><span>Powtórz hasło<var>*</var>:</span> <input type="password" name="passw2" maxlength="40" required></label></p>
			<!--<p class="prop" style="visibility: hidden;"><label><span>Login<var>*</var>:</span> <input type="text" name="login" maxlength="24" value="" autocomplete="off" required></label></p>-->
			<p style="text-align: center;"><label><input type="checkbox" name="akct" id="akct" value="1" style="width: auto; vertical-align: middle;"> oświadczam, że zapoznałem się i akceptuję <a href="/regulamin">regulamin</a></label></p>
			<p><button type="submit" id="register-btn">Zarejestruj się</button><span class="ajax-loader"></span></p>
		</fieldset>
		</form>
	</div>
	<div class="regright">
		<h4>Dlaczego warto?</h4>
		<p><strong>Skorzystaj z dodatkowych funkcjonalności:</strong></p>
		<ul class="nicelist">
			<li>indywidualna cena usług na podstawie<br>ilości wysyłanych paczek</li>
			<li>hurtowa nadawanie paczek</li>
			<li>tworzenie książki adresowej</li>
			<li>skarbonka (Prepaid)</li>
			<li>dostęp do statystyk wysyłkowych</li>
			<li>panel integracji z Allegro</li>
			<li>integracja z Quick.Cart</li>
			<li>faktury zbiorcze</li>
			<li style="background: none;"><em>...i wiele wiecej!</em></li>
		</ul>
	</div>
	<div id="retlog" style="display: none;"></div>
<script type="text/javascript">
<!--
 var regax = false;
 function propLogin()
 {
	if(regax) return!1;
	regax = new sack();
	regax.requestFile = 'ax_login.php';
	regax.method = 'GET';
	regax.setVar('name', qwery('#rform input[name="imie"]')[0].value);
	regax.setVar('surname', qwery('#rform input[name="nazwisko"]')[0].value);
	regax.element = 'retlog';
	regax.onCompletion = function() {
		if(qwery('#retlog')[0].innerHTML.length > 3)
		{
			qwery('#rform p.prop input')[0].value = qwery('#retlog')[0].innerHTML;
			qwery('#rform p.prop')[0].style.visibility = 'visible';
		}
		regax = false;
		return!1;
	};
	regax.runAJAX();
	return!1;
 }

 function chkTerms()
 {
	if(qwery('#akct')[0].checked) return true;
	alert('Proszę zapoznać się z Regulaminem i zaakceptować\njego postanowienia celem rejestracji w serwisie.');
	return!1;
 }
//-->
</script>
{% endblock %}