<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'cfg_boite_accordeon' => 'Configuration avancée du plugin Menu Accordéon',
	'cfg_titre_accordeon' => 'Menu jQuery Accordéon',
	
	// E
	'explication_identifiant'=>'.class ou #identifiant de votre menu, par défaut .accordeon',
	'explication_identifiant_ver'=>'.class ou #identifiant de votre menu, verrouillé. Modifiable par le squelette <code>accordeon/identifiant.html</code>.',
	'explication_options' => 'Vous pouvez mettre ici les options à passer au script d\'effet accordéon. <a href="http://jqueryui.com/demos/accordion/">Voir la documentation d\'accordéon</a>. Ne pas mettre les accolades. Saisir par exemple <br /><strong>collapsible: true, active: $(".accordeon > li.on > a,.accordeon > li:first-child > a").last()</strong><br /> pour ouvrir le menu de classe accordeon sur l\'élément courant ou sinon sur le premier du DOM',
	'explication_options_ver' => 'Les options passée au script d\'effet accordéon sont verrouillées. Le webmestre peut les changer en modifiant le squelette <code>accordeon/options.html</code>. <a href="http://jqueryui.com/demos/accordion/">Voir la documentation d\'accordéon</a>.',
	// L
	'label_identifiant'=>'Identifiant du menu',
	'label_options' => 'Animation',
	
	// T
	'titre_menu' => 'jQuery Accordéon',
);
?>
