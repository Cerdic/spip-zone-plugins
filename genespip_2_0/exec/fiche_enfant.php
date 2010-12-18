<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');
include_spip('genespip_fonctions');

function exec_fiche_enfant(){
	global $connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:fiche_enfant'), "", "");
	$id_individu = $_GET['id_individu'].$_POST['id_individu'];

	echo debut_gauche('',true);
	include_spip('inc/boite_info');
	include_spip('inc/raccourcis_fiche');

	echo debut_droite('',true);

	echo debut_cadre_relief(  "", false, "", $titre = _T('genespip:fiche_enfant'));

	echo gros_titre(_T(genespip_nom_prenom($id_individu,3)), '', false);
	echo "<br /><fieldset><legend>"._T('genespip:liste_des_enfants')."</b></i></legend>";
	echo genespip_nom_prenom($id_individu,2);
	echo "</fieldset>";

	echo fin_cadre_relief();  

	echo fin_page();
}
?>
