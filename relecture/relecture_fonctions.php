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

	if ($id_relecture=intval($id)) {
		$from = 'spip_commentaires';
		$where = array("id_relecture=$id_relecture");
		$nb_commentaires = sql_countsel($from, $where);
		$where = array("id_relecture=$id_relecture", "statut<>" . sql_quote('ouvert'));
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
 * Filtre possible sur l'element d'article commente.
 * Le tableau de sortie est indexe par les valeurs de statut ouvert, accepte, refuse
 *
 * @param int $id
 * @param string $element
 * @return array
 */
function relecture_compter_commentaires($id, $element='') {
	$compteurs = array('ouvert' => 0, 'accepte' => 0, 'refuse' => 0, 'poubelle' => 0);

	if ($id_relecture = intval($id)) {
		$select = array('statut', 'count(*) AS compteur');
		$from = 'spip_commentaires';
		$where = array("id_relecture=$id_relecture");
		if ($element)
			$where[] = "element=" . sql_quote($element);
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


/**
 * Renvoyer la liste ordonnee des elements d'article non vides pouvant etre relus et commentes
 *
 * @param int $id
 * @return array
 */
function relecture_lister_elements($id) {
	$elements = array();

	if ($id_relecture = intval($id)) {
		$select = array('article_chapo AS chapo', 'article_descr AS descr', 'article_texte AS texte', 'article_ps AS ps');
		$from = 'spip_relectures';
		$where = array("id_relecture=$id_relecture");
		$champs = sql_fetsel($select, $from, $where);

		foreach ($champs as $_cle => $_valeur) {
			if (strlen(trim($_valeur)) > 0)
				$elements[] = $_cle;
		}
	}

    return $elements;
}


/**
 * Construire le titre d'une relecture a partir d'un appel a la balise #INFO_TITRE
 *
 * @param int $id
 * @param array $champs
 * @return string
 */
function generer_titre_relecture($id_objet, $champs) {

	$titre = _T('relecture:titre_relecture') . ' ' . $id_objet;
    return $titre;
}


/**
 * Extraire du texte fourni la partie correspondante determinee par les offsets de debut et fin
 * Si ceux sont nuls ou egaux la fonction renvoie une portion de texte autour du point d'insertion.
 *
 * @param string $texte
 * @param int $idebut
 * @param int $ifin
 * @return string
 */
function relecture_extraire_selection($texte, $idebut, $ifin) {
	$selection = '';

	if ($idebut < $ifin) {
		$selection = mb_substr($texte, $idebut, $ifin-$idebut+1, $GLOBALS['meta']['charset']);
	}
	else {
		$selection = mb_substr($texte, max($idebut-10, 0), min($idebut+10, strlen($texte)), $GLOBALS['meta']['charset']);
	}

    return $selection;
}

/**
 * Insérer les marqueurs HTML des tooltips des commentaires dans le texte de la relecture
 *
 * @param string $texte
 * @param string $element
 * @param int $id_relecture
 * @return string
 */
function relecture_inserer_reperes($texte, $element = '', $id_relecture = 0) {

	// récupérer les commentaires de la relectue pour cet élément
	$comments = sql_allfetsel('*', 'spip_commentaires', array("element=" . sql_quote($element), "id_relecture=$id_relecture"));
	
	$offset = 0;
	
	foreach ($comments as $comment) {
		$repere = unserialize($comment['repere']);
		$insert = '<span class="tooltip relecture ui-icon ui-icon-comment" data-comment-id="'. $comment['id_commentaire'].'" data-comment-date="'. $comment['date_ouverture'].'">'.$comment['texte'].'</span>';
		$texte = substr_replace($texte, $insert, $repere[1] + $offset, 0);
		$offset += strlen($insert);
	}
	
	return $texte;
}

?>
