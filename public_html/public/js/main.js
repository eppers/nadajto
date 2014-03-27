if(parent.frames.length!=0)parent.location.replace(location.href)
var misio=false;
/*@cc_on @*/
/*@if(@_jscript_version>=5 && @_jscript_version<=5.8)
misio=true;
@end @*/
var ms6=false;
/*@cc_on @*/
/*@if(@_jscript_version>=5 && @_jscript_version<=5.6)
ms6=true;
@end @*/

function $(a){return document.getElementById(a)}function isDefined(variable){return eval("(typeof("+variable+')!="undefined");')}function createCookie(a,b,c){if(c){var d=new Date;d.setTime(d.getTime()+c*24*60*60*1e3);var e="; expires="+d.toGMTString()}else e="";document.cookie=a+"="+b+e+"; path=/"}function readCookie(a){nameEQ=a+"=",ca=document.cookie.split(";");for(i=0;i<ca.length;i++){c=ca[i];while(c.charAt(0)==" ")c=c.substring(1,c.length);if(c.indexOf(nameEQ)==0)return c.substring(nameEQ.length,c.length)}return null}
function xchgm(){
var emailReplaceArray = [['0','@'],[',','.']];
var replTab = emailReplaceArray;
for(var i=0;i<replTab.length;i++){
var s=escape(replTab[i][0].replace(/\s/g,''));
replTab[i][0]=s.replace(/%u/g,'\\u').replace(/%/g,'\\x');}
var getEmail=function(s){
s=s.replace(/\s/g,'');
for(var i=0;i<replTab.length;i++){s=s.replace(new RegExp(replTab[i][0],'g'),replTab[i][1]);}return s;};
var as=document.getElementsByTagName('a');
var html=document.getElementsByTagName('html')[0];
var text=html.textContent!=undefined?'textContent':'innerText';
for(var i=0;i<as.length;i++){
if(!as[i].className.match(/(^|\s)eaddr($|\s)/)) continue;
var email=getEmail(as[i][text]);
as[i].setAttribute('href','mailto:'+email);
as[i][text]=email;
}emailReplaceArray = undefined;}

function startJS()
{
 xchgm();
 for(i=0; i<document.links.length; i++)
 {
	if(document.links[i].rel && document.links[i].rel.indexOf('external') >= 0)
	{
		document.links[i].target = '_blank';
	}
 };
 tab = qwery('#tabbed');
 if(tab.length > 0)
 {
	lst = tab[0].getElementsByTagName('a');
	for(i=0; i<lst.length; i++)
	{
		lst[i].onclick = function()
		{
			//setTab(this.parentNode.getAttribute('class'));
                        tabActive = qwery('#tabbed a.current')[0];
                        tabActive.setAttribute('class', '');
                        this.setAttribute('class', 'current');
                        tt = qwery('#start')[0];
                        tn = this.parentNode.getAttribute('class');
                        emile(tt, 'text-indent: -'+((tn-1) * 920)+'px;', {duration: 500 });
			return!1;
		}
		//lst[i].setAttribute('href', 'javascript:;');
	}
	//setTab(1);
 };
 return!1;
}

var tabax = false;
function setTab(t)
{
   if(tabax) return!1;
   qwery('#progress')[0].style.visibility = 'visible';
   tabax = new sack();
   if(t != 4)
   {
	  tabax.method = 'POST';
	  tabax.requestFile = 'loadpage?step='+t;
	  vrs = qwery('#start input');
	  for(i=0; i<vrs.length; i++)
	  {
		 it = vrs[i].getAttribute('type').toLowerCase();
		 if((it == 'checkbox' && vrs[i].checked) || (it == 'radio' && vrs[i].checked) || it == 'text')
		 {
			tabax.setVar(vrs[i].getAttribute('name'), vrs[i].value);
		 }
	  }
	}
	  else
    {
	  tabax.method = 'GET';
	  tabax.requestFile = 'track';
	  tabax.setVar('cno', qwery('#cno')[0].value);
    }
	tabax.setVar('step', t);
	tabax.element = 'subpage'+t;
	tabax.onCompletion = function() {
		qwery('#progress')[0].style.visibility = '';
		tt = qwery('#start')[0];
		tn = parseInt(this.element.replace('subpage', ''));
		emile(tt, 'text-indent: -'+((tn-1) * 920)+'px;', {duration: 500 });
		tabax = false;
		return!1;
	};
	tabs = qwery('#tabbed a');
	for(i=0; i<tabs.length; i++)
	{
		tabs[i].setAttribute('class', '');
	};
   if(t < 4)
   {
	  tab = qwery('#tabbed a')[t-1];
	  tab.setAttribute('class', 'current');
   }
   tabax.runAJAX();
   return!1;
}

ready(function() {
    startJS();
});

// add mouse down event
AttachEvent(window, 'mousedown', function(event) {
	tag = event.target.nodeName.toLowerCase();
	if(mySelectDown && tag != 'a' && tag != 'ul')
	{
		document.getElementById(mySelectDown).parentNode.getElementsByTagName('ul')[0].style.display='';
		mySelectDown = false;
	}
}, true);
