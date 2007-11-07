<?php

/*! \file pipeline.php
 *  \brief Tous les appels pipeline sont centralisés ici
 *
 *  Les appels pipeline sont centralisé ici et renvoi en fonction des demandes vers la fonction necessaire.
 */    

/*! \brief surcharge de affiche_gauche
 *
 *  Pipeline gérant l'affichage gauche de l'espace privé
 *    
 *  \param $flux flux html de la partie gauche
 *  \return renvoi le flux html completé  
 */  
function doc2img_affiche_gauche($flux) {

    spip_log('pipeline affiche gauche','doc2img');

    spip_log('pipeline :'.$flux['args']['exec'],'doc2img');
	//determine la page demandée 
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
	//retourne l'affichage complet
	return $flux;
}


/*! \brief surcharge de post_edition
 *
 *  Pipeline gérant la conversion à la volée des documents
 *    
 *  \param $flux flux html de la partie gauche
 *  \return $flux renvoi le flux html complété  
 */  
function doc2img_post_edition($flux) {
    spip_log('document (tele)chargé','doc2img');

    $id_document = $flux['args']['id_objet'];
    spip_log('id_document'.$id_document,'doc2img');

    include_once('inc/convertir.php');
//	include_spip('inc/convertir');

    if (($flux['args']['operation'] == 'ajouter_document') 
            && (controler_document($id_document) == true)
            && (lire_config('doc2img/conversion_auto') == "on"))  {
	    spip_log('document '.$id_document.' en conversion automatique','doc2img');
	    convertir_document($id_document);
    }

	return $flux;
}

?>
