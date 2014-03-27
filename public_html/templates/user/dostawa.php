{% extends 'layout.php' %}

{% block page_title %}Dostawa{% endblock %}
{% block content %}
	<p class="header">Dostawa</p>

  <div class="container" id="delivery">
  
  {% if delivery %}
      <p class="legend">Data wysyłki: {{delivery.date}}</p>
<table class="cennik" cellpadding="0" cellspacing="0" border="0">
    <thead>
        <tr>
          <th></th>
          <th>Nadawca</th>
          <th>Odbiorca</th>
        </tr>
    </thead>
    <tbody>
        <tr>
          <td>Nazwa firmy</td>
          <td>{{delivery.from_cmp}}</td>
          <td>{{delivery.to_cmp}}</td>
        </tr> 
        <tr>               
          <td>NIP</td>
          <td>{{delivery.from_nip}}</td>
          <td>{{delivery.to_nip}}</td>
        </tr> 
        <tr>                
          <td>Imię</td>
          <td>{{delivery.from_name}}</td>
          <td>{{delivery.to_name}}</td>
        </tr> 
        <tr>      
          <td>Nazwisko</td>
          <td>{{delivery.from_lname}}</td>
          <td>{{delivery.to_lname}}</td>
        </tr> 
        <tr>      
          <td>Adres</td>
          <td>{{delivery.from_addr}}</td>
          <td>{{delivery.to_addr}}</td>
        </tr> 
        <tr>
          <td>Miasto</td>
          <td>{{delivery.from_city}}</td>
          <td>{{delivery.to_city}}</td>
        </tr> 
        <tr>
          <td>Kod pocztowy</td>
          <td>{{delivery.from_zip}}</td>
          <td>{{delivery.to_zip}}</td>
        </tr> 
        <tr>
          <td>Telefon</td>
          <td>{{delivery.from_phone}}</td>
          <td>{{delivery.to_phone}}</td>
        </tr> 
        <tr>
          <td>Email</td>
          <td>{{delivery.from_email}}</td>
          <td>{{delivery.to_email}}</td>
        </tr> 
			</tbody>  
		</table>                
  
  {% else %}
    Brak danych
  {% endif %}
    <a href="/user/faktury/szczegoly/{{delivery.invoice}}" onclick="window.history.back(); return false;" class="back">Powrót</a>
  </div>


{% endblock %}