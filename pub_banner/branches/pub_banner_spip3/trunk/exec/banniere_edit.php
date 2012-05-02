<?php
/**
 * @name 		Bannieres edit
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_banniere_edit_dist() {
	global $connect_statut, $connect_id_auteur, $spip_lang_right;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:integer_edit');
	$rubrique = 'pubban';
	$sous_rubrique = "integer_pub";
	$id_banniere = _request('id_banniere') ? _request('id_banniere') : 'new';

	$retour = _request('retour') ? _request('retour') : (
		($id_banniere == 'new') ? generer_url_ecrire("bannieres_tous") : generer_url_ecrire("banniere_voir","id_banniere=$id_banniere")
	);
	$contexte = array(
		'id_banniere' => $id_banniere,
		'titre' => ($id_banniere == 'new') ? '' : pubban_recuperer_banniere($id_banniere, 'titre'),
		'icone_retour' => icone_inline(_T('icone_retour'), $retour, find_in_path("img/stock_left.png"), "rien.gif", $GLOBALS['spip_lang_left']),
		'redirect' => $retour
	);
	$milieu = recuperer_fond("prive/banniere", $contexte);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('', true),
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br />"._T('pubban:intro_integer_edit')),
  		_T("pubban:intro_integer_edit_texte"),  fin_cadre_relief(true), creer_colonne_droite('', true), debut_droite('', true),
		pipeline('affiche_milieu', array( 'args' => array( 'exec' => 'banniere_edit', 'id_banniere' => $id_banniere ), 'data' => $milieu ) ),
		fin_gauche(), fin_page();
}
?>