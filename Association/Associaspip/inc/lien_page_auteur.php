<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

/**
 * Ajout d'un lien vers la page de membre sur la page d'auteur
**/
function association_affiche_gauche($flux) {
	if ($flux['args']['exec']=='auteur_infos') {
		$id_auteur = $flux['args']['id_auteur'];
		if (autoriser('voir_membres', 'association', $id_auteur)) {
			$flux['data'] .= recuperer_fond('prive/boite/lien_page_auteur', array ('id_auteur' => $id_auteur));
		}
	}
	return $flux;
}

?>