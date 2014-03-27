<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="pl">
<title>NadajTo.pl</title>
<meta name="description" content="{METADESC}">
<meta name="language" content="pl">
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
                        <h2><!--Wysyłaj taniej, szybciej i <span>wygodniej</span>--></h2>
                        <div id="top-container">
                            <ul id="mainmenu">
                                    <li><a href="/cennik">Cennik</a></li>
                                    <li><a href="/pomoc">Pomoc</a></li>
                                    <li><a href="/kontakt">Kontakt</a></li>
                                    {% if session.user.id_customer %}<li><a href="/wyloguj">Wyloguj</a></li>{% endif %}
                            </ul>
                            <p id="loginpass" {% if session.user.id_customer %}class="logged"{% endif %}>
                                    {% if session.user.id_customer is empty %}<a href="/rejestracja" class="ico_reg">Załóż konto</a> <a href="/logowanie" class="ico_log">Logowanie</a>{% endif %}
                                    {% if session.user.id_customer %}Zalogowany(a): <a href="{% if session.admin==1 %}/admin/{%else%}/user/faktury{% endif %}" style="text-align: left;">{{ session.user.login }}</a>{% endif %}
                            </p>
                        </div>
                        <span id="symbols">Przygotuj przesyłkę, Nadaj To, Natychmiastowa realizacja zlecenia</span>
                        <img id="ludzik" alt="" src="/public/img/blank.gif">
                </div>

                <div id="site">
                    {% block content %} {% endblock %}
                </div>

                <ul id="footmenu">
                        <li><a href="/elementy_niestandardowe">Elementy niestandardowe</a></li>
                        <li><a href="/jak_przygotowac">Jak zapakować przesyłkę</a></li>
                        <li><a href="/przedmioty_niedozwolone">Przedmioty niedozwolone w wysyłce</a></li>
                        <li><a href="/reklamacje">Reklamacje</a></li>
                        <li><a href="/regulamin">Regulamin</a></li>
                </ul>
                <p class="footer{% if page == 'start'%} startpg{% endif %}">Wykonanie oraz obsługa techniczna: <a href="http://nanei.pl/" rel="external"><img alt="Nanei" src="/public/img/nanei.gif"></a></p>





        


        {% if page == 'start'%}
                <div id="footer_start">
                        <h3><strong>Więcej</strong> informacji</h3>
                        <div class="likefp clearfix">
                                <img alt="" src="/public/img/nanei_white.png">
                                <h4>Kim jesteśmy?</h4>
                                <p>Serwis NadajTo.pl jest prowadzony przez agencję interaktywną NANEI. Jako laureaci wielu międzynarodowych konkursów informatycznych, tworzymy serwisy internetowe, które cechuje funkcjonalność, estetyka oraz intuicyjność. Ta zaawansowana platforma wysyłkowa pozwoli Ci w prosty sposób dokonać zlecenia kurierskiego.</p>
                        </div>
                        <div class="warr24h clearfix">
                                <div class="lttext">
                                        <h4>Bezpieczeństwo transakcji</h4>
                                        <p>PayU to niekwestionowany lider w obsłudze transakcji internetowych. Za jego pośrednictwem dokonywane są transakcje w najpopularniejszych serwisach aukcyjnych na terenie Polski. Błyskawiczne przelewy oraz potwierdzenia transferu Twojej gotówki w kilka sekund, to gwarancja sprawnego dokonywania zleceń kurierskich.</p>
                                </div>
                            <img alt="" src="/public/img/payu.png">
                        </div>
                        
                        <div class="likefp clearfix">
                            <img alt="" src="/public/img/ups.png">
                                <h4>UPS</h4>
                                <p>Największa na świecie firma kurierska. Dobierając naszych partnerów, priorytetem było bezpieczeństwo oraz szybkość realizowanych usług. UPS posiada najlepszą opinię wśród klientów w rankingach przewoźników kurierskich..</p>
                        </div>
                        
                        <div class="warr24h clearfix">
                                <div class="lttext">
                                        <h4>Skarbonka</h4>
                                        <p>Innowacyjna funkcjonalność dostępna dla zalogowanych użytkowników. Dzięki wpłacie dowolnej sumy na Twoje konto w systemie NadajTo, unikniesz każdorazowej zapłaty za przesyłkę podczas składania zamówienia. Kwota za dokonywane zlecenia będzie automatycznie pobierana z konta skarbonki. Dodatkowo otrzymujesz rabaty cenowe za doładowania.</p>
                                </div>
                                <img alt="" src="/public/img/piggy_bank.png">
                        </div>
                        
                        <div class="likefp clearfix">
                                <img alt="" src="/public/img/logo_os_czarny_kolor_300.png">
                                <h4>Ogólnopolski zasięg</h4>
                                <p>Nasz serwis jest zintegrowany z największą platformą sklepową na terenie kraju. Od teraz, każdy użytkownik sklepu QUICK.CART firmy Open Solution, będzie miał możliwość generowania oszczędności przy korzystaniu z naszej  oferty. Nadawanie przesyłek odbywa się bezpośrednio z panelu administracyjnego Twojego sklepu.</p>
                        </div>
                        <div class="warr24h clearfix">
                                <div class="lttext">
                                        <h4>Nadawanie paczek jest proste</h4>
                                        <p>Uzupełnij dane w 3 prostych
                                            i szybkich krokach, aby móc zlecić przesyłkę. Nie musisz być
                                            zarejestrowany w naszym portalu, aby dokonywać zleceń. Zarejestrowani
                                            użytkownicy mają dostęp do dodatkowych funkcjonalności.</p>
                                </div>
                            <img alt="" src="/public/img/ftinfo2.jpg">
                        </div>
                        
                </div>
        

        {% endif %}





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