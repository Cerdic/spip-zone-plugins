<?php
/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *
 **/


include_spip('lettres_fonctions');

/**
 * Dupliquer une lettre existante sur une autre
 */
function action_joindrearticle_lettre_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id_lettre = $securiser_action();

	$id_article = _request('id_article');

	if ($id_lettre = intval($id_lettre)
		AND $id_article = intval($id_article)
	  AND autoriser('joindrearticle', 'lettre',$id_lettre)) {

		$lettre = new lettre($id_lettre);
		if ($id_article>0)
			$lettre->enregistrer_article($id_article);
		else
			$lettre->supprimer_article(-$id_article);

	}

}


?>