{% extends 'layout.php' %}

{% block page_title %}Konfiguracja konta{% endblock %}
{% block content %}
	<p class="header">Konfiguracja konta</p>

  <div class="container">
      <form id="conf-update" style="width: 300px; display: inline-block;" action="/user/konfiguruj/" method="post">
	<p><input type="text" name="company" placeholder="Firma" value="{{user.company}}" style="width: 258px;"></p>
        <p><input type="text" name="nip" placeholder="NIP" value="{{user.nip}}" style="width: 258px;"></p>
        <p><input type="text" name="name" placeholder="Imię" value="{{user.name}}" readonly style="width: 258px;"></p>
        <p><input type="text" name="lname" placeholder="Nazwisko" value="{{user.lname}}" readonly style="width: 258px;"></p>
        <p><input type="email" name="email" placeholder="Email" value="{{user.email}}" readonly style="width: 258px;"></p>
        <p><input type="text" name="phone" placeholder="Telefon" value="{{user.phone}}" style="width: 258px;"></p>
        <p>
                <input type="text" name="addr_street" placeholder="Ulica" value="{{user.street}}" style="width: 115px;">
                <input type="text" name="addr_no" placeholder="Nr domu" value="{{user.no1}}" style="width: 55px;" maxlength="8">
                <input type="text" name="addr_no2" placeholder="Nr lokalu" value="{{user.no2}}" style="width: 55px;" maxlength="8">
        </p>
        <p><input type="text" name="city" placeholder="Miasto" value="{{user.city}}" style="width: 258px;"></p>
        <p>
            <input type="text" name="zip1" placeholder="XX" value="{{user.zip1}}" style="width: 40px;" maxlength="2">
            <input type="text" name="zip2" placeholder="XXX" value="{{user.zip2}}" style="width: 50px;" maxlength="3">
        </p>
        <p><input type="password" name="pass" placeholder="Hasło" value="" style="width: 258px;"></p>
        <div class="price-container" style="clear: both; margin-left: 40px;"> 
            <button type="button" class="dark" id="btn-submit" >Wyślij</button>
        </div>
      </form> 
      <div style="display: inline-block; vertical-align: top;">
          <p>
              <input type="text" value="" name="amount" placeholder="Kwota doładowania">
              <button type="button" class="dark" id="btn-prepay" >Doładuj</button>
          </p>
          <p>W Twojej skarbonce znajduje się: <span style="color: #10b86a; font-weight: bold;">{{user.prepay}}</span> zł</p>
      </div>
          
  </div>
    <form method="GET" id="sendingpayu" action="{{payULink}}">
            <input type="hidden" id="sessionId" name="sessionId" value="">
            <input type="hidden" id="oauth_token" name="oauth_token" value="">
            <input type="hidden" name="showLoginDialog" value="False">
    </form>
<script>
$j('#btn-submit').click(function(){
    $j('form#conf-update').submit();
})
</script>

{% endblock %}
