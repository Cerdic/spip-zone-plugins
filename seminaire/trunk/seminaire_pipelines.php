<?php
/**
 * Plugin Séminaires
 * Licence GNU/GPL
 * 
 * @package SPIP\Seminaires\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Insertion dans le pipeline post_insertion (SPIP)
 * 
 * A la création d'un évènement on associe le mot clé du formulaire
 * 
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux
 * 	Le même contexte sans modification, on a juste lié le mot clé récupéré dans le post
 */
function seminaire_post_insertion($flux) {
	if ($flux['args']['table'] == 'spip_evenements') {
		if(($id_mot = _request('id_mot')) && ($id_evenement = $flux['args']['id_objet'])){
			include_spip('action/editer_liens');
			objet_associer(array('mot'=>$id_mot),array('evenement'=>$id_evenement), $qualif = null);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * 
 * On ajoute le mot clé du type d'évènement dans l'environnement
 * 
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux
 * 	Le contexte modifié en cas d'erreur
 */
function seminaire_formulaire_charger($flux){
	if(isset($flux['args']['form']) && $flux['args']['form'] == 'editer_evenement'){
		if(isset($flux['data']['id_parent']) && $id_parent = $flux['data']['id_parent']){
			$seminaire = sql_getfetsel('seminaire','spip_articles','id_article='.intval($id_parent));
			$flux['data']['seminaire'] = $seminaire;
		}
		$flux['data']['id_mot_type'] = sql_getfetsel('lien.id_mot','spip_mots_liens as lien LEFT JOIN spip_mots as mots ON lien.id_mot=mots.id_mot LEFT JOIN spip_groupes_mots as groupe ON mots.id_groupe=groupe.id_groupe','lien.objet='.sql_quote('evenement').' AND groupe.id_groupe='.intval(lire_config('seminaire/groupe_mot_type')));
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier
 * 
 * On vérifie que le mot clé, obligatoire, a bien été sélectionné
 * 
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux
 * 	Le contexte modifié en cas d'erreur
 */
function seminaire_formulaire_verifier($flux){
	if(isset($flux['args']['form']) && $flux['args']['form'] == 'editer_evenement'){
		if(!_request('id_mot'))
			$flux['data']['id_mot'] = _T('seminaire:mot_obligatoire');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter
 * 
 * On lie le mot sur les évènements "séminaire"
 * 
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux
 * 	Le même contexte du pipeline après que le mot soit lié
 */
function seminaire_formulaire_traiter($flux){
	if(isset($flux['args']['form']) && $flux['args']['form'] == 'editer_evenement'){
		if(($id_mot = _request('id_mot')) && ($id_evenement = $flux['data']['id_evenement'])){
			$id_mot_origine = sql_getfetsel('lien.id_mot','spip_mots_liens as lien LEFT JOIN spip_mots as mots ON lien.id_mot=mots.id_mot LEFT JOIN spip_groupes_mots as groupe ON mots.id_groupe=groupe.id_groupe','lien.objet='.sql_quote('evenement').' AND groupe.id_groupe='.intval(lire_config('seminaire/groupe_mot_type')));
			if($id_mot_origine != $id_mot){
				include_spip('action/editer_liens');
				if($id_mot_origine)
					objet_dissocier(array('mot'=>$id_mot_origine),array('evenement'=>$id_evenement));
				objet_associer(array('mot'=>$id_mot),array('evenement'=>$id_evenement), $qualif = null);
			}
		}
	}
	return $flux;
}
?>