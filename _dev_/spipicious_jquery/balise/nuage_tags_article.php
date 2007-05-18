<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_NUAGE_TAGS_ARTICLE($p) {
	return calculer_balise_dynamique($p,'NUAGE_TAGS_ARTICLE', array('id_article', 'page'));
}

/*
* La fonction statique retourne fait les verifications sur les variables récupérées par collecte
* ici, $args[0] contient donc id_article.
*
* ensuite en renvoi les arguments en questions pour la balise dynamique.
* 
*/

function balise_NUAGE_TAGS_ARTICLE_stat($args, $filtres) {  
  
	// Pas d'id_article ? Erreur de squelette 
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#NUAGE_TAGS_ARTICLE',
					'motif' => 'ARTICLES')), '');

	return $args;
}

/* la fonction dynamique fait les calculs et renvois le squelette qu'il faut afficher.
_request() cherche dans les valeurs post.
*/

function balise_NUAGE_TAGS_ARTICLE_dyn($id_article,$page) {
  include_spip('spipicious_fonctions');
  $groupe_tags = spipicious_get_idgroup_tags(); 
  $id_groupe_tags = $groupe_tags['id_groupe'];   // pour recuper ds squelette le groupe - tags - 
  
  // retour sur le squelette 
  return array('public/nuage_tags_article', $GLOBALS['delais'],
				   array('self' => $url,
						 'id_article' => $id_article,
						 'id_groupe' => $id_groupe_tags,
             'fond' => $page					 
						 )
				   );
}


?>
