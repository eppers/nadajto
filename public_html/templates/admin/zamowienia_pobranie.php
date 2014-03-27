{% extends 'layout.php' %}

{% block page_title %}Szczegóły{% endblock %}
{% block content %}
	<p class="header">Zamówienia - pobranie - szczegóły</p>

  <div class="container">
    <table class="cennik" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
              <th>Numer zamówienia</th>
              <th>Numer konta</th>
              <th>Kwota</th>
              <th>Data zwrotu</th>
            </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{order.id}}</td>              
            <td>{{order.bank}}</td>
            <td>{{order.price}}</td>
            <td>{{order.date_repay}}</td>
          </tr>
 
			</tbody>
		</table>
        <a href="/admin/zamowienia/pobranie" class="back">Pobranie</a>
  </div>


{% endblock %}