// SPIP-Listes paladin@quesaco.org 2007-10-15

/*
	Ajout nav rapide dans la barre des gadgets
*/

$("#gadget-navigation").ready(function(){
	$("#gadget-navigation").wrap("<div id='gadget-navigation-wrap'></div>");
	$("#gadget-navigation").after("<div id='gadget-navigation-spiplistes' style='clear:left;'></div>");
	$("#gadget-navigation-spiplistes").load('./?exec=spiplistes_menu_navigation\x26var_ajaxcharset=utf8');
});

