{% extends 'layout.php' %}

{% block page_title %}Faktury{% endblock %}
{% block content %}
	<p class="header">Faktury</p>

  <div class="container">
		<table class="cennik" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th>Nazwa faktury</th>
					<th>Data wystawienia</th>
					<th>Opcje</th>
				</tr>
			</thead>
			<tbody>
          {% for invoice in invoices  %}
          <tr>
          	<td class="usluga">{{invoice.name}}</td>
					  <td>{{invoice.date}}</td>
            <td class="option"><a href="/user/faktury/pobierz/{{invoice.id}}" class="icon-print" title="Pobierz fakturę"></a><a href="/user/faktury/szczegoly/{{invoice.id}}" class="icon-search" title="Szczegóły"></a></td>
          </tr>
          {% else %}
          <tr>
            <td colspan="3">Brak faktur</td>
          </tr>            
          {% endfor %}
			</tbody>
		</table>
  </div>


{% endblock %}