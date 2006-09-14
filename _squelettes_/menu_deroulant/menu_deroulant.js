/*
 * Javascript pour le menu deroulant sur MSIE
 *
 * adapte de http://www.htmldog.com/articles/suckerfish/dropdowns/example/
 * et passe en jquery
 *
 */
$(document).ready(function(){
	$('#nav li').hover(
		function(){$(this).addClass('sfhover')},
		function(){$(this).removeClass('sfhover')}
	);
});