<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/texte');
include_spip('inc/layer');
include_spip('inc/presentation');

function exec_lienscontenus_admin()
{
	global $connect_statut, $connect_toutes_rubriques;
	global $spip_lang_right;
	global $couleur_claire;
	global $tweaks;

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('tweak:titre'), "configuration", "lienscontenus");

	gros_titre(_T('lienscontenus:configuration'));
	echo barre_onglets("configuration", "lienscontenus");

	debut_gauche();
	debut_boite_info();
	echo propre(_T('lienscontenus:administration_aide'));
	fin_boite_info();

	echo pipeline('affiche_gauche', array('args' => array('exec' => 'lienscontenus_admin'), 'data' => ''));
	creer_colonne_droite();
	echo pipeline('affiche_droite', array('args' => array('exec' => 'lienscontenus_admin'), 'data' => ''));
	debut_droite();
	lire_metas();

	debut_cadre_trait_couleur('administration-24.gif','','',_T('lienscontenus:liste'));

	echo generer_url_post_ecrire('lienscontenus_admin', '', 'submitform');
	$valider = "\n<div style='margin-top:0.4em; text-align:$spip_lang_right'>"
		. "<input type='submit' name='Valider2' value='"._T('bouton_valider')."' class='fondo' /></div>";
	echo $valider;

	fin_cadre_trait_couleur();

	echo pipeline('affiche_milieu', array('args' => array('exec' => 'lienscontenus_admin'), 'data' => ''));
	echo '</form>';

	echo fin_gauche(), fin_page();
}
?>