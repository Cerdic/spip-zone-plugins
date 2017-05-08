<?php
/**
 * Plugin ARTicle-AUTeur-ROLE pour Spip 2.0-2.1
 * Licence GPL (c) 20012-02-02 - GilCot
 */

// Insertion dans le flux SPIP
//@: https://programmer.spip.net/affiche_milieu
function autartrole_affiche_milieu($flux)
{
//	if ($flux['args']['exec']=='articles' AND $id_article = $flux['args']['id_article'])
	if ($flux['args']['exec']=='articles' && autoriser('modifier', 'article', $id_article = $flux['args']['id_article']) )
	{ // page ?exec=articles
		$contexte = $_GET;
		$flux['data'] .= recuperer_fond('prive/boite/autartrole_article', $contexte, array('ajax'=>true));
	}

	if ($flux['args']['exec']=='auteur_infos' AND $id_auteur = $flux['args']['id_auteur'])
	{ // page ?exec=auteur_infos
		$contexte = $_GET;
		$flux['data'] .= recuperer_fond('prive/boite/autartrole_auteur', $contexte, array('ajax'=>true));
	}

	return $flux;
}


?>