{% extends 'layout.php' %}

{% block page_title %}Strona główna{% endblock %}
{% block content %}
	<h2>Logowanie</h2>
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
		<form id="rform" action="" class="bform" method="post" style="text-align: center;">
		<fieldset>
			<legend>Logowanie</legend>
			<h4>Zaloguj się do serwisu</h4>
			<p><input type="email" name="login" placeholder="Email" required></p>
			<p><input type="password" name="passw" placeholder="Hasło" required></p>
			<p>
        <button type="submit" id="login-btn" style="margin: 0;">Zaloguj mnie</button>
        <span class="ajax-loader"></span>
      </p>
			<h4>lub</h4>
			<p class="loginmore"><a href="#" onclick="shwPH(); return!1;">Przypomnij hasło</a> | <a href="/rejestracja">Załóż konto</a></p>
			<div id="loginmore">
				<h3>Utwórz nowe hasło</h3>
				<p>
					Wszelkie hasła w serwisie NadajTo są szyfrowane i nie ma możliwości ich odzyskania.<br>
					Aby uzyskać dostęp do swojego konta należy wygenerować nowe hasło korzystając z poniższego formularza.
				</p>
				<p>Pamiętam swój:
					<label><input type="radio" name="phsf" value="1" style="width: auto;" checked> Adres e-mail</label>
					<!--<label><input type="radio" name="phsf" value="2" style="width: auto;"> Login</label></p>-->
				<p><input type="text" name="resetpassw" placeholder="Wpisz e-mail"></p>
				<p><button type="submit" style="margin: 0;">Utwórz hasło</button> <button type="button" onclick="hidPH();" style="margin: 0 0 0 20px;">Anuluj</button></p>
			</div>
		</fieldset>
		</form>
<script type="text/javascript">
<!--
 function shwPH()
 {
	el = qwery('#loginmore')[0];
	el.style.opacity = 0;
	el.style.display = 'block';
	emile(el, 'opacity: 1;', { duration: 600 });
	return!1;
 }

 function hidPH()
 {
	el = qwery('#loginmore')[0];
	emile(el, 'opacity: 0;', { duration: 500, after: function() { qwery('#loginmore')[0].style.display = 'none'; }});
	return!1;
 }

 qwery('#loginmore')[0].style.display = 'none';
//-->
</script>
{% endblock %}