<?php
/**
 * Plugin Archivage

 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Appel au pipeline affiche_milieu de l'espace prive.
 *
 * Pour les pages (exec) rubrique, mot et auteur :
 * Affichage de la liste des articles dont le statut est "archive". 
 * Utilisation du squelette spip prive/objets/liste/articles
 *
 *
 * @param $flux
 * @return mixed
 */
function archstatut_affiche_milieu($flux){
	
	$contexte = array('statut'=> 'archive',
					'titre' => _T('archstatut:info_tous_articles_archives')
				);
	
	if ($flux["args"]["exec"] == "rubrique") {
		$contexte = $contexte + array('id_rubrique'=> $flux["args"]["id_rubrique"] );
		$flux['data'] .= recuperer_fond('prive/objets/liste/articles', $contexte );
		}
	if ($flux["args"]["exec"] == "mot") {
		$contexte = $contexte + array('id_mot'=> $flux["args"]["id_mot"] );
		$flux['data'] .= recuperer_fond('prive/objets/liste/articles', $contexte );
		}
	if ($flux["args"]["exec"] == "auteur") {
		$contexte = $contexte + array('id_auteur'=> $flux["args"]["id_auteur"] );
		$flux['data'] .= recuperer_fond('prive/objets/liste/articles', $contexte );
		}

	return $flux;
}

?>