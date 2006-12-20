<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("UPDATE [resultat -> $result]")."</strong> ";
//	exit;
//
// FIN
//	


include_spip('inc/ecommerce_boutique');
include_spip("inc/presentation");
include_spip("inc/config");




function exec_boutique_creation()
	{
	global $spip_lang_right;

	//
	// Affichage de la page
	//
	debut_page("&laquo; $titre &raquo;", "documents", "Boutique","");
	debut_gauche();
	debut_droite();

//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [Tout commence ici]")."</strong> ";
//	exit;
//
// FIN
//

	if ($retour)
		$retour = urldecode($retour);

//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [Creation de la base]")."</strong> ";
//	exit;
//
// FIN
//
		include_spip('base/ecommerce');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [Creation de la base terminee]")."</strong> ";
//	exit;
//
// FIN
//


//
// Icones retour 
//
	echo "<div style='text-align:$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "rien.gif",'right');
	$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('boutique')));
	echo "<div style='clear:both;'></div>";
	echo "</div>";
//
// FIN
//
	fin_page();
	}
?>
