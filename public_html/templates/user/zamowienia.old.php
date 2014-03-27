{% extends 'layout.php' %}

{% block page_title %}Szczegóły{% endblock %}
{% block content %}
	<p class="header">Zamówienia</p>

  <div class="container">
		<table class="cennik" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>Numer trackingowy</th>
					<th>Data złożenia zamówienia</th>
          <th>Cena</th>
          <th>Kurier</th>
          <th>Typ wysyłki</th>
          <th>Forma płatności</th>
					<th>Opcje</th>
				</tr>
			</thead>
			<tbody>
          {% for order in orders  %}
          <tr>
          	<td class="usluga">{{order.tracking}}</td>
					  <td>{{order.date}}</td>
            <td>{{order.amount}}</td>
            <td>{{order.courier}}</td>
            <td>{{order.delivery}}</td>
            <td>{% if order.payment == 1%}Prepaid{% elseif order.payment == 2%}Pobranie{% else %}Błąd{% endif %}</td>
            <td class="option"><a href="/user/zamowienia/dostawa/{{order.id}}" class="icon-search" title="Szczegóły"></a></td>
          </tr>
          {% else %}
          <tr>
            <td colspan="7">Brak zamówień</td>
          </tr>            
          {% endfor %}
			</tbody>
		</table>
        <a href="/user/faktury" class="back">Powrót</a>
  </div>


{% endblock %}