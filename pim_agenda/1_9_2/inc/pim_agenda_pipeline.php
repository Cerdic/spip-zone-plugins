<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/date');

function PIMAgenda_affiche_droite($flux){
	$exec = $flux['args']['exec'];
	if ($exec=='auteur_infos'){
		$id_auteur = intval($flux['args']['id_auteur']);
		global $spip_lang_right;
		if (!isset($GLOBALS['meta']['pim_agenda_auteurs_actifs'])){
			ecrire_meta('pim_agenda_auteurs_actifs',serialize(array()));
			ecrire_metas();
		}
		if (($id=_request('pim_agenda_active'))!=NULL){
			$auteurs_agenda_actif = unserialize($GLOBALS['meta']['pim_agenda_auteurs_actifs']);
			$auteurs_agenda_actif = array_merge($auteurs_agenda_actif,array($id));
			ecrire_meta('pim_agenda_auteurs_actifs',serialize($auteurs_agenda_actif));
			ecrire_metas();
		}
		if (($id=_request('pim_agenda_desactive'))!=NULL){
			$auteurs_agenda_actif = unserialize($GLOBALS['meta']['pim_agenda_auteurs_actifs']);
			$auteurs_agenda_actif = array_diff($auteurs_agenda_actif,array($id));
			ecrire_meta('pim_agenda_auteurs_actifs',serialize($auteurs_agenda_actif));
			ecrire_metas();
		}
		$auteurs_agenda_actif = unserialize($GLOBALS['meta']['pim_agenda_auteurs_actifs']);

		$out = "";
		$out .= debut_cadre_relief('',true);
		$out .= generer_url_post_ecrire('auteur_infos', "id_auteur=$id_auteur");
		if (!in_array($id_auteur,$auteurs_agenda_actif)){
			$out .= "<input type='hidden' name='pim_agenda_active' value='$id_auteur' />\n";
			$out .= "<div>"._T("pimagenda:info_activer_agenda")."\n";
		}
		else{
			$out .= "<input type='hidden' name='pim_agenda_desactive' value='$id_auteur' />\n";
			$out .= "<div>"._T("pimagenda:info_desactiver_agenda")."\n";
		}
		$out .= "<div align='$spip_lang_right'><input type='submit' name='Modifier' value='"._T('bouton_modifier')."' class='fondo'></div>\n";
		$out .= "</div></form>";
		$out .= fin_cadre_relief(true);
		$flux['data'].= $out;
	}
	if ($exec=='auteurs') {
	}
	return $flux;
}
function PIMAgenda_affiche_milieu($flux){
	switch($flux['args']['exec']) {
		case 'auteurs_edit':
		case 'auteur_infos':
			include_spip('inc/pim_agenda');
			$id_auteur = $flux['args']['id_auteur'];
			$nouv_groupe = _request('nouv_groupe');
			$supp_groupe = _request('supp_groupe');
			// le formulaire qu'on ajoute
			global $connect_statut;
			$flux['data'] .= PIMAgenda_formulaire_groupes('auteurs', $id_auteur, $nouv_groupe, $supp_groupe, $connect_statut == '0minirezo', generer_url_ecrire($flux['args']['exec'],"id_auteur=$id_auteur"));
			break;
		default:
			break;
	}

	return $flux;
}
function PIMAgenda_affiche_gauche($flux){
	switch($flux['args']['exec']) {
		case 'auteurs':
			include_spip('public/assembler');
			$flux['data'] .= "<br />" . debut_cadre_trait_couleur('',true);
			if (autoriser('modifier','groupe')) {
				$res = icone_horizontale(_T('pimagenda:creer_groupe'), generer_url_ecrire("auteurs_groupe_edit","new=oui"), _DIR_PLUGIN_PIMAGENDA."img_pack/groupes-24.gif", "creer.gif",false);
				$flux['data'] .= $res;
			}
			if (($id_groupe=_request('id_groupe')) && autoriser('modifier','groupe',$id_groupe)) {
				$res = icone_horizontale(_T('pimagenda:modifier_groupe'), generer_url_ecrire("auteurs_groupe_edit","id_groupe=$id_groupe&retour=".urlencode(self())), _DIR_PLUGIN_PIMAGENDA."img_pack/groupes-24.gif", "edit.gif",false);
				$flux['data'] .= $res;
			}
			$fond = recuperer_fond('fonds/liste_groupe',array('id_groupe'=>_request('id_groupe')));
			$flux['data'] .= $fond ;
			$flux['data'] .= fin_cadre(true);
			break;
		default:
			break;
	}

	return $flux;
}

function PIMAgenda_rendu_boite($titre,$descriptif,$lieu,$type='ics'){
	$texte = "<span class='calendrier-verdana10'><span  style='font-weight: bold;'>";
	$texte .= wordwrap($sum=typo($titre),15)."</span>";
	/*$texte .= "<span class='survol'>";
	$texte .= "<strong>$sum</strong><br />";
	$texte .= $lieu ? propre($lieu).'<br />':'';
	$texte .= propre($descriptif);
	$texte .= "</span>";*/
	if ($type=='ics'){	
		$texte .= (strlen($lieu.$descriptif)?"<hr/>":"").$lieu.(strlen($lieu)?"<br/>":"");
		$texte .= $descriptif;
	}
	$texte .= "</span>";

	return $texte;
}
function PIMAgenda_rendu_evenement($flux) {
	global $couleur_claire;
	$evenement = $flux['args']['evenement'];
	$url = $evenement['URL']; 
	$texte = PIMAgenda_rendu_boite($evenement['SUMMARY'],$evenement['DESCRIPTION'],$evenement['LOCATION'],$flux['args']['type']);
	if (is_string($url)) {
		if (_DIR_RESTREINT && preg_match(',agenda_tip,',$url)) {
			$id = substr(md5($url),0,8);
			$texte = "<a class='jTip' href='".quote_amp(parametre_url($url,'edit',urlencode(self())))."' id='$id' >$texte</a>"; 
		}
		else {
			$texte = http_href(quote_amp($url), $texte, '', '', '', '');
		}
	}
	else if (is_array($url))
		$texte = ajax_action_auteur(
			$url['action'], $url['id'], $url['script'], 
			isset($url['args'])?$url['args']:'', 
			array($texte,""),
			isset($url['args_ajax'])?$url['args_ajax']:'', 
			isset($url['fct_ajax'])?$url['fct_ajax']:'');
	
	$flux['data'] = $texte;
	return $flux;
}

?>