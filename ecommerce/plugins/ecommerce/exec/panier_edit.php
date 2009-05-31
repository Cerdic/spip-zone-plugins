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

include_spip("inc/presentation");
include_spip("inc/config");
include_spip('inc/ecommerce_outils');
include_spip("inc/ecommerce_panier");


function exec_panier_edit()
	{
	global $spip_lang_right;

	$new = _request('new');
	$retour = _request('retour');
	$id_session = _request('id_session');
	$supp_session = _request('supp_session');


	//
	// Affichage de la page
	//
	debut_page("&laquo; $titre &raquo;", "documents", "Boutique","");
	debut_gauche();
	debut_droite();

//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("EDIT Panier [Tout commence ici]")."</strong> ";
//	exit;
//
// FIN
//

	if ($retour)
		$retour = urldecode($retour);

	if ($id_session)
		{
//
// DEBUGGING MODE
//
//	echo "<p><strong>"._L("EDIT Panier [Mise a jour, modification]")."</strong> ";
//	exit;
//
// FIN
//

		afficher_paniers (_L("Contenu du panier"),
			array(
				"SELECT" => "id_session",
				"FROM" => "spip_ecommerce_paniers",
				"WHERE" => "id_session=".$id_session,
				"GROUP BY" => "id_session"
				)
			);	
	}

//
// Icones retour et suppression
//
	echo "<div style='text-align:$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "rien.gif",'right');
	$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('boutique')));
	if ($id_session && estceque_boutique_administrable($id_session)) 
		{
		echo "<div style='float:$spip_lang_left'>";
		$link = generer_url_ecrire('session_delete','supp_session='."$id_session&retour=".urlencode($retour));
		icone(_L("Supprimer cette session"), $link, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "supprimer.gif");
		echo "</div>";
		}
	echo "<div style='clear:both;'></div>";
	echo "</div>";
//
// FIN
//
	fin_page();
	}
?>
