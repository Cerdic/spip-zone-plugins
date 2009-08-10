<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip("inc/meta");
include_spip("inc/session");
include_spip("inc/autoriser");
include_spip("base/abstract_sql");


function formulaires_ajouter_auteur_charger_dist($id_article, $_T=array(), $retour=''){
	$valeurs = array('id_article'=>$id_article,'editable'=>true);
	$label_ajouter_auteur = (isset($_T['label_ajouter_auteur']) && $_T['label_ajouter_auteur']) ? $_T['label_ajouter_auteur'] : _T('ajouter_auteur:ajouter_un_auteur');
	$valeurs['_label_ajouter_auteur'] = $label_ajouter_auteur;
	if (!autoriser('modifier','article', $id_article)){
		$valeurs['editable'] = false; 
	}
	return $valeurs;
	
}

function formulaires_ajouter_auteur_traiter_dist($id_article, $_T=array(), $retour=''){

	//on recupere les infos de l'article necessaires
	$art = sql_select("*","spip_articles","id_article = "._q($id_article));
	$artinfos = sql_fetch($art);
	
	$id_rub_orig = $artinfos["id_rubrique"];
	$id_secteur = $artinfos["id_secteur"];
	$titre = $artinfos["titre"];
	$lang = $artinfos["lang"];
			
	//recuperer les donnees qui nous interessent
	
	$ajouter_auteur = _request('ajouter_auteur');
	$ajouter_id_auteur = _request('ajouter_id_auteur');

	$valider = _request('valider');
	
	if ($ajouter_id_auteur){
		if ($ajouter_id_auteur = intval($ajouter_id_auteur)) {
			$res = sql_select("id_auteur","spip_auteurs_articles","id_article = $id_article AND id_auteur=$ajouter_id_auteur");
			if (!sql_fetch($res)){
				sql_insertq("spip_auteurs_articles",  array("id_auteur" => $ajouter_id_auteur, "id_article" => $id_article));
				$invalider = true;
				spip_log("ajouter auteur $ajouter_id_auteur a larticle $id_article","ajouter_auteur");
				if ($retour) {
					include_spip('inc/headers');
					$res = array('message_ok'=>_T('ajouter_auteur:auteur_ajoute'),
					'redirect'=>parametre_url($retour,'var_mode','calcul'));
				}else{
					$res = array('message_ok'=>_T('ajouter_auteur:auteur_ajoute'), 'editable'=>true);
				}
				return $res;
			}
		}
	}
}
?>
