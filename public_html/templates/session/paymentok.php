{% extends 'layout.php' %}

{% block page_title %}Płatność ok{% endblock %}
{% block content %}
	<ul id="tabbed" >
		<li class="1"><a href="/" >Strona głowna</a></li>
	</ul>
	<div>
            <div class="container">
		<p class="payment-info">{{message}}</p>


            </div>
	 </div>

{% endblock %}