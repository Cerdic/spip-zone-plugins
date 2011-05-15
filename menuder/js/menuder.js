/*
 * Javascript pour le menu deroulant sur MSIE
 *
 * adapte de http://www.htmldog.com/articles/suckerfish/dropdowns/example/
 * et passe en jquery pour sa partie javascript (necessaire sous MSIE)
 *
 */
if (jQuery.browser.msie) {
$(document).ready(function(){
	$('.menuder li').hover(
		function(){$(this).addClass('hover')},
		function(){$(this).removeClass('hover')}
	);
});
}