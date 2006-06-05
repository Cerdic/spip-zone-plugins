<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

function exec_articles_edit() {
	global $id_auteur;

	if(_request('new')=='oui') {

		if (!$id_rubrique = intval(_request('id_rubrique'))) {
			$row = spip_abstract_fetsel('id_rubrique', 'spip_rubriques',
										array(array('=', 'id_parent', '0')),
										'', array('0+titre','titre'), '1');
			$id_rubrique = $row['id_rubrique'];
		}

		// creer un article avec un statut "nouveau"
		$id_article= spip_abstract_insert('spip_articles',
						"(statut,titre,id_rubrique)",
						"('nouveau', 'nouvel article', $id_rubrique)");
		spip_abstract_insert('spip_auteurs_articles',
							 "(id_auteur,id_article)",
							 "('$id_auteur','$id_article')");

		// et partir pour l'editer
		redirige_par_entete("index.php?exec=articles&id_article=$id_article");
	} else {
		include_ecrire('exec/articles_edit.php');
		return exec_articles_edit_dist();
	}

}

?>
