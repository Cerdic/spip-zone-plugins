<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline editer_contenu_objet
 *
 * Affiche les boutons supplémentaires de :
 * - changement de la valeur de podcast
 * - changement de la valeur de explicit
 *
 * @param array $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline complété
 */
function podcast_editer_contenu_objet($flux){
	$type_form = $flux['args']['type'];
	$id_document = $flux['args']['id'];
	if(in_array($type_form,array('document'))){
		if(preg_match(",<li [^>]*class=[\"']editer_credits.*>(.*)<\/li>,Uims",$flux['data'],$regs)){
			$ajouts .= recuperer_fond('inclure/formulaire_document_saisies',array('id_document'=>$id_document));
			$flux['data'] = preg_replace(",($regs[1]),Uims","\\1".$ajouts,$flux['data']);
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition
 */
function podcast_pre_edition($flux){
	if(($flux['args']['type'] == 'document') && ($flux['args']['action'] == 'modifier') && _request('podcast')){
		$flux['data']['podcast'] = _request('podcast');
		$flux['data']['explicit'] = _request('explicit');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post-edition
 *
 * Intervient à chaque modification d'un objet de SPIP
 * notamment lors de l'ajout d'un document
 *
 * - On met la valeur de podcast en fonction de la conf (oui/non)
 * - On met la valeur de explicit en fonction de la conf (oui/non)
 *
 * @return $flux Le contexte de pipeline complété
 * @param array $flux Le contexte du pipeline
 */
function podcast_post_edition($flux){
	/**
	 * On n'intervient que sur les actions d'ajout de document
	 */
	if(in_array($flux['args']['operation'], array('ajouter_document'))){
		$id_document = $flux['args']['id_objet'];
		$infos_doc = sql_fetsel('fichier,mode,distant','spip_documents','id_document='.intval($id_document));

		$valeur_podcast = lire_config('podcast/podcast_auto'); // oui ou non
		$valeur_explicit = lire_config('podcast/explicit_defaut'); // yes / clean / no

		/**
		 * On intervient uniquement lorsque :
		 * - les valeurs sont différentes de celles par défaut
		 * - que l'on ne soit pas en mode vignette
		 */
		if(($infos_doc['mode'] != 'vignette') &&
			((isset($valeur_podcast) && ($valeur_podcast != 'oui'))
			OR (isset($valeur_explicit) && ($valeur_explicit != 'clean')))){

			/**
			 * Mise à jour du document
			 */
			include_spip('inc/modifier');
			if(isset($valeur_explicit) && ($valeur_explicit != 'clean')){
				$infos['explicit'] = $valeur_explicit;
			}
			if(isset($valeur_podcast) && ($valeur_podcast != 'oui')){
				$infos['podcast'] = $valeur_podcast;
			}
			revision_document($id_document, $infos);

			/**
			 * On invalide le cache de cet élément si nécessaire
			 */
			include_spip('inc/invalideur');
			suivre_invalideur("id='id_document/$id'");
		}
	}
	return $flux;
}

?>