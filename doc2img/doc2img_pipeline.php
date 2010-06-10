<?php

/**
 * Plugin Doc2img
 * Fichier contenant les appels aux pipelines de SPIP
 */

/**
 * Insertion dans le pipeline affiche_gauche
 *
 * @param $flux flux html de la partie gauche
 * @return $flux le flux html completé
 */
function doc2img_affiche_gauche($flux) {
	switch ($flux['args']['exec']) {
		//la page articles est demandée
		case "articles" :
			//charge les fonctions necessaire
			include_once('inc/doc2img_espace_prive.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= affiche_liste_doc($id_article);
			break;
	}
	return $flux;
}

/**
 * Insertion dans le pipeline post-edition
 * Converti automatiquement les fichiers autorisés si possible
 *
 * @param $flux Le contexte du pipeline
 * @return $flux Le contexte du pipeline modifié
 */
function doc2img_post_edition($flux) {
    $id_document = $flux['args']['id_objet'];

    if (($flux['args']['operation'] == 'ajouter_document')
            && (can_doc2img($id_document) == true)
            && (is_doc2img($id_document) == false)
            && (lire_config('doc2img/conversion_auto') == "on"))  {
	    spip_log('document '.$id_document.' en conversion automatique','doc2img');
	    $convertir = charger_fonction('doc2img_convertir','inc');
	    $convertir($id_document);
    }
	return $flux;
}

?>
