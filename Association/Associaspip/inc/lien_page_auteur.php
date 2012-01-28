<?php
/***************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Ajout d'un lien vers la page de membre sur la page d'auteur
**/
function association_affiche_gauche($flux) {
	if ($flux['args']['exec']=='auteur_infos') {
		$id_auteur = $flux['args']['id_auteur'];
		$flux['data'] .= recuperer_fond('prive/boite/lien_page_auteur', array ('id_auteur' => $id_auteur));
	}
	return $flux;
}

?>