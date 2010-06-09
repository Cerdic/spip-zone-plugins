<?php

/*! \file pipeline.php
 *  \brief Tous les appels pipeline sont centralis�s ici
 *
 *  Les appels pipeline sont centralis� ici et renvoi en fonction des demandes vers la fonction necessaire.
 */

/*! \brief surcharge de affiche_gauche
 *
 *  Pipeline g�rant l'affichage gauche de l'espace priv�
 *
 *  \param $flux flux html de la partie gauche
 *  \return renvoi le flux html complet�
 */
function doc2img_affiche_gauche($flux) {

    spip_log('pipeline affiche gauche','doc2img');

    spip_log('pipeline :'.$flux['args']['exec'],'doc2img');
	//determine la page demand�e
	switch ($flux['args']['exec']) {
		//la page articles est demand�e
		case "articles" :
			//charge les fonctions necessaire
			include_once('inc/doc2img_espace_prive.php');
			$id_article = $flux['args']['id_article'];
			//recupere le complement d'affichage
			$flux['data'] .= affiche_liste_doc($id_article);
			break;
	}
	//retourne l'affichage complet
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

	include_spip('inc/doc2img_convertir');

    if (($flux['args']['operation'] == 'ajouter_document')
            && (can_doc2img($id_document) == true)
            && (is_doc2img($id_document) == false)
            && (lire_config('doc2img/conversion_auto') == "on"))  {
	    spip_log('document '.$id_document.' en conversion automatique','doc2img');
	    convertir_document($id_document);
    }

	return $flux;
}

?>
