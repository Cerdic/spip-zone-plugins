<?php
/**
 * @name 		Puce activation
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Pages exec
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_pubban_puce_activation_dist() {
	if(!_request('id')) return;
	if(!_request('type')) return;
	if(_request('type') == 'emplacement') exec_pubban_puce_activation_emplacement_args(_request('id'));
	elseif(_request('type') == 'pub') exec_pubban_puce_activation_pub_args(_request('id'));
}

function exec_pubban_puce_activation_emplacement_args($id) {
	$div = '';
	$empl = pubban_recuperer_banniere($id);
	$img_a = "<img src='".$GLOBALS['pubban_btns']['actif']."' alt='"._T('pubban:btn_active')."'";
	$img_ina = "<img src='".$GLOBALS['pubban_btns']['inactif']."' alt='"._T('pubban:btn_desactive')."'";
	$end_img = " />";
	$img_id = " id='imgstatutemplacement$id'";

	$div .= "<span class='puce_emplacement_fixe'>"
		.($empl['statut'] == '1inactif' ? $img_a.$img_id.$end_img : $img_ina.$img_id.$end_img )
		."</span>"
		."<span class='puce_emplacement_popup' id='statutdecalemplacement$id' style='margin-left: -11px;'>"
		."<a href='".(generer_action_auteur("activer_banniere", 'activer-'.$id, generer_url_ecrire('pubban_banniere_tous','id_empl='.$id)))."&activer=oui' onclick='return confirm(confirm_changer_statut);' title='"._T('pubban:btn_active')."'>".$img_a.$end_img."</a>"
		."<a href='".(generer_action_auteur("activer_banniere", 'desactiver-'.$id, generer_url_ecrire('pubban_banniere_tous','id_empl='.$id)))."&activer=non' onclick='return confirm(confirm_changer_statut);' title='"._T('pubban:btn_desactive')."'>".$img_ina.$end_img."</a>"
		. "</span>";
	
	ajax_retour($div);
}

function exec_pubban_puce_activation_pub_args($id) {
	$div = '';
	$pub = pubban_recuperer_pub($id);
	$img_a = "<img src='".$GLOBALS['pubban_btns']['actif']."' alt='"._T('pubban:btn_active')."'";
	$img_ina = "<img src='".$GLOBALS['pubban_btns']['inactif']."' alt='"._T('pubban:btn_desactive')."'";
	$end_img = " />";
	$img_id = " id='imgstatutpub$id'";

	$div .= "<span class='puce_pub_fixe'>"
		.($pub['statut'] == '1inactif' ? $img_a.$img_id.$end_img : $img_ina.$img_id.$end_img )
		."</span>"
		."<span class='puce_pub_popup' id='statutdecalpub$id' style='margin-left: -11px;'>"
		."<a href='".(generer_action_auteur("activer_publicite", 'activer-'.$id, generer_url_ecrire('pubban_publicite_tous','id_pub='.$id)))."&activer=oui' onclick='return confirm(confirm_changer_statut);' title='"._T('pubban:btn_active')."'>".$img_a.$end_img."</a>"
		."<a href='".(generer_action_auteur("activer_publicite", 'desactiver-'.$id, generer_url_ecrire('pubban_publicite_tous','id_pub='.$id)))."&activer=non' onclick='return confirm(confirm_changer_statut);' title='"._T('pubban:btn_desactive')."'>".$img_ina.$end_img."</a>"
		. "</span>";
	
	ajax_retour($div);
}
?>