<?php

/**
 * Formater le message informatif concernant les nombres de commentaires déposés et
 * pris en compte pour une relecture donnee
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

?>
