<?php
/**
 * @name 		Publicites edit
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_publicite_edit_dist() {
	global $connect_statut, $spip_lang_right;
	if ($connect_statut != "0minirezo" ) { include_spip('inc/minipres'); echo minipres(); exit; }
	include_spip('inc/presentation');
	$commencer_page = charger_fonction('commencer_page', 'inc');
 	$titre_page = _T('pubban:pub_edit');
	$rubrique = 'pubban';
	$sous_rubrique = "liste_pub";
	$id_publicite = _request('id_publicite') ? _request('id_publicite') : 'new';

	$retour = _request('retour') ? _request('retour') : (
		($id_publicite == 'new') ? generer_url_ecrire("publicites_tous") : generer_url_ecrire("publicite_voir","id_publicite=$id_publicite")
	);
	$contexte = array(
		'id_publicite' => $id_publicite,
		'titre' => ( $id_publicite == 'new' ) ? '' : pubban_recuperer_publicite($id_publicite, 'titre'),
		'icone_retour' => icone_inline(_T('icone_retour'), $retour, find_in_path("img/stock_left.png"), "rien.gif", $GLOBALS['spip_lang_left']),
		'redirect' => $retour
	);
	$milieu = recuperer_fond("prive/pub", $contexte);

	echo($commencer_page(_T('pubban:pubban')." - ".$titre_page, $rubrique, $sous_rubrique)), debut_gauche('', true),
		debut_cadre_relief(find_in_path("img/ico-pubban.png"), true, "", "<br />"._T('pubban:intro_pub_edit')),
  		_T("pubban:intro_pub_edit_texte"), fin_cadre_relief(true), creer_colonne_droite('', true), debut_droite('', true),
		pipeline('affiche_milieu', array( 'args' => array( 'exec' => 'publicite_edit', 'id_publicite' => $id_publicite ), 'data' => $milieu ) ),
		fin_gauche(), fin_page();
}
?>