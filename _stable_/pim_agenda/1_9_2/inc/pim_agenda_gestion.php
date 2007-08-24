<?php
include_spip('inc/date');

function PIMAgenda_affiche_droite_auteurs_edit($flux){
	$exec = $flux['args']['exec'];
	if ($exec=='auteurs_edit'){
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
		$out .= generer_url_post_ecrire('auteurs_edit', "id_auteur=$id_auteur");
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
	return $flux;
}


?>