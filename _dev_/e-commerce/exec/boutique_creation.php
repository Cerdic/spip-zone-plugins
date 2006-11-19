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


include_spip('inc/e-commerce_boutique');
include_spip("inc/presentation");
include_spip("inc/config");




function exec_boutique_creation()
	{
	global $spip_lang_right;

	$new = _request('new');
	$retour = _request('retour');
	$id_boutique = _request('id_boutique');
	$supp_boutique = _request('supp_boutique');


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

	if ($id_boutique)
		{
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [Mise a jour, modification]")."</strong> ";
//	exit;
//
// FIN
//
		}
	if ($supp_boutique)
		{
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [suppression]")."</strong> ";
//	exit;
//
// FIN
//
		}




	if ($new == 'oui')
		{
//
// DEBUGGING MODE
//
	echo "<p><strong>"._L("EDIT [Creation de la base]")."</strong> ";
//	exit;
//
// FIN
//
		include_spip('base/boutique');
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		}

//
// Icones retour et suppression
//
	echo "<div style='text-align:$spip_lang_right'>";
	icone(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "rien.gif",'right');
	$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('boutique')));
/*
	if ($id_boutique && estceque_boutique_administrable($id_boutique) && !$supp_boutique) 
		{
		echo "<div style='float:$spip_lang_left'>";
//		$link=generer_url_ecrire('boutique_edit', 'supp_boutique='."$id_boutique&retour="."$id_retour");
		$link = generer_url_ecrire('boutique_edit','supp_boutique='."$id_boutique&retour=".urlencode($retour));
//		$link=parametre_url('boutique_edit', urlencode(generer_url_ecrire('boutique_edit', 'supp_boutique='."$id_boutique".'retour='."$id_retour")));

		icone(_L("Supprimer cette boutique"), $link, "../"._DIR_PLUGIN_BOUTIQUE."/img_pack/euro.png", "supprimer.gif");
		echo "</div>";
		}
*/
	echo "<div style='clear:both;'></div>";
	echo "</div>";
//
// FIN
//
	fin_page();
	}
?>
