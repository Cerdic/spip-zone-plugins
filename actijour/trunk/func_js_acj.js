/* 
+--------------------------------------------+
| ACTIVITE DU JOUR
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| D. Chiche . pour la maj 2.0
| Script certifie KOAK2.0 strict, mais si !
+--------------------------------------------+
| javascript ... popup pour bargraph actijour
+--------------------------------------------+
*/
function actijourpop(id)
	{
	document.getElementById(id).target = 'graph_article';
	window.open('', 'graph_article','width=530,height=450,menubar=no,scrollbars=yes,resizable=yes');
	if(neo.window.focus){neo.window.focus();}
	}
