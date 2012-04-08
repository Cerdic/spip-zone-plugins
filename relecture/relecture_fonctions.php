<?php

/**
 * Formater le message informatif concernant les nombres de commentaires déposés et
 * pris en compte pour une relecture donnee.
 *
 * @param int $id
 * @return string
 */
function relecture_informer_commentaires($id) {
	$texte = '';

	if (intval($id)>0) {
		$from = 'spip_commentaires';
		$where = array("id_relecture=$id");
		$nb_commentaires = sql_countsel($from, $where);
		$where = array("id_relecture=$id", "statut<>" . sql_quote('ouvert'));
		$nb_commentaires_fermes = sql_countsel($from, $where);

		if ($nb_commentaires == 0)
			$texte = _T('relecture:info_aucun_commentaire');
		else {
			$texte = singulier_ou_pluriel(
				$nb_commentaires,
				'relecture:info_1_commentaire',
				'relecture:info_nb_commentaires');
			if ($nb_commentaires_fermes == 0)
				$texte .= ', ' . _T('relecture:info_aucun_commentaire_ferme');
			else
				$texte .= ', ' . singulier_ou_pluriel(
					$nb_commentaires,
					'relecture:info_1_commentaire_ferme',
					'relecture:info_nb_commentaires_fermes');
		}
	}

    return $texte;
}

/**
 * Renvoyer les compteurs de commentaires par statut pour une relecture donnee.
 * Le tableau de sortie est indexe par les valeurs de statut ouvert, accepte, refuse
 *
 * @param int $id
 * @return array
 */
function relecture_compter_commentaires($id) {
	$compteurs = array('ouvert' => 0, 'accepte' => 0, 'refuse' => 0,);

	if (intval($id)>0) {
		$select = array('statut', 'count(*) AS compteur');
		$from = 'spip_commentaires';
		$where = array("id_relecture=$id");
		$group_by = 'statut';
		if ($lignes = sql_select($select, $from, $where, $group_by)) {
		    // Classer et compter par statut
		    while ($ligne = sql_fetch($lignes)) {
				$compteurs[$ligne['statut']] = $ligne['compteur'];
		    }
		}
	}

    return $compteurs;
}

?>
