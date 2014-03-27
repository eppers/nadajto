<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="pl">
<title>NadajTo.pl</title>
<meta name="description" content="{METADESC}">
<meta name="language" content="pl">
<meta name="author" content="www.airmedia.pl">
<meta name="robots" content="all">
<base href="{SITEROOT}">
<link rel="SHORTCUT ICON" href="{SITEROOT}favicon.ico">
<link rel="icon" type="image/png" href="{SITEROOT}favicon.ico">
<link href="/public/css/style.css" rel="stylesheet" type="text/css" media="all">
<!--[if lte IE 8]><link href="theme/msie.css" rel="stylesheet" type="text/css"><![endif]-->
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script type="text/javascript" src="/public/js/scripts.jquery.js"></script>
<script type="text/javascript" src="/public/js/ready.js"></script>
<script type="text/javascript" src="/public/js/tw-sack.js"></script>
<script type="text/javascript" src="/public/js/qwery.min.js"></script>
<script type="text/javascript" src="/public/js/mlbox.js"></script>
<script type="text/javascript" src="/public/js/emile.min.js"></script>
<script type="text/javascript" src="/public/js/advselect.js"></script>

</head>

<body>
    <div id="bg">
        <div id="centr">
            <div id="kontener">

                <div id="top">
                        <h1><a href="/"><img alt="NadajTo.pl" src="/public/img/logo.png"></a></h1>
                        <h2>Wysyłaj taniej, szybciej i <span>wygodniej</span></h2>
                        <div id="top-container">
                            <ul id="mainmenu">
                                <li><a href="/user/zamowienia">Zamówienia</a></li>
                                <li><a href="/user/faktury">Faktury</a></li>
                                <li><a href="/user/konfiguruj">Konfiguracja</a></li>
                                <li><a href="/kontakt">Kontakt</a></li>
                                {% if session.user.id_customer %}<li><a href="/wyloguj">Wyloguj</a></li>{% endif %}
                            </ul>
                            <p id="loginpass" {% if session.user.id_customer %}class="logged"{% endif %}>
                                    {% if session.user.id_customer is empty %}<a href="/rejestracja" class="ico_reg">Załóż konto</a> <a href="/logowanie" class="ico_log">Logowanie</a>{% endif %}
                                    {% if session.user.id_customer %}Zalogowany(a): <a href="/user/" style="text-align: left;">{{ session.user.login }}</a>{% endif %}
                            </p>
                        </div>
                        <span id="symbols">Przygotuj przesyłkę, Nadaj To, Natychmiastowa realizacja zlecenia</span>
                        <img id="ludzik" alt="" src="/public/img/blank.gif">
                </div>

                <div id="site" class="admin">
                    {% block content %} {% endblock %}
                </div>

                <ul id="footmenu">
                        <li><a href="jak_przygotowac">Jak przygotować przesyłkę</a></li>
                        <li><a href="dla_kogo">Dla kogo jest NadajTo?</a></li>
                        <li><a href="regulamin">Regulamin</a></li>
                        <li><a href="godziny_odbioru">Godziny odbioru paczek</a></li>
                        <li><a href="przesylki">Przesyłki pobraniowe</a></li>
                </ul>
                <p class="footer{% if page == 'start'%} startpg{% endif %}">Wykonanie oraz obsługa techniczna: <a href="http://nanei.pl/" rel="external"><img alt="Nanei" src="/public/img/nanei.gif"></a></p>

            </div>
        </div>
    </div>
<script type="text/javascript" src="js/tooltip.js"></script>
<script type="text/javascript">
<!--
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38728413-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
//-->
</script>
<script type="text/javascript" src="/public/js/main.js"></script>
</body><!-- T:{TIME}, Q:{SQLQ} -->
</html>