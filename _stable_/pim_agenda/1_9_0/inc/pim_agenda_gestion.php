<?php
include_spip('inc/date');

function PIMAgenda_install(){
	PIMAgenda_verifier_base();
}

function PIMAgenda_uninstall(){
	include_spip('base/pim_agenda');
	include_spip('base/abstract_sql');

	// suppression du champ pim_agenda a la table spip_groupe_mots
	$query = "ALTER TABLE `spip_groupes_mots` DROP `pim_agenda`";
	spip_query($query);
	
}

function PIMAgenda_verifier_base(){
	$version_base = 0.10;
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['pim_agenda_base_version']) )
			|| (($current_version = $GLOBALS['meta']['pim_agenda_base_version'])!=$version_base)){
		include_spip('base/pim_agenda');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ajout du champ pim_agenda a la table spip_groupe_mots
			// si pas deja existant
			$desc = spip_abstract_showtable("spip_groupes_mots",'',true);
			if (!isset($desc['field']['pim_agenda'])){
				spip_query("ALTER TABLE spip_groupes_mots ADD `pim_agenda` VARCHAR(3) NOT NULL AFTER `syndic`");
			}
			ecrire_meta('pim_agenda_base_version',$current_version=$version_base);
		}
		
		ecrire_metas();
	}
	
	if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
		if (!isset($INDEX_elements_objet['spip_pim_agenda'])){
			$INDEX_elements_objet['spip_pim_agenda'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_objet_associes'])){
		$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
		if (!isset($INDEX_objet_associes['spip_pim_agenda']['spip_articles'])){
			$INDEX_objet_associes['spip_pim_agenda']['spip_articles'] = 1;
			ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_elements_associes'])){
		$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
		if (!isset($INDEX_elements_associes['spip_articles'])){
			$INDEX_elements_associes['spip_articles'] = array('titre'=>2,'descriptif'=>1);
			ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
			ecrire_metas();
		}
	}
}


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