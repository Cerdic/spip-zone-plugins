<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Vérifie si un critère de type id_xxx inclusif est présent dans la liste des critères fournis.
 *
 * @param array $criteres
 *        Liste des critères à vérifier au format standard d'une boucle SPIP.
 * @param string $champ_id
 *        Champ id dont il faut vérifier l'existence en tant que critère explicite.
 *
 * @return bool
 *         `true` si un critère inclusif existe, `false` sinon.
 */
function critere_id_est_explicite($criteres, $champ_id) {

	// Initialisation du retour
	$est_explicite = false;

	// Initialisation statique des opérateurs possibles pour éviter de recréer le tableau sur un même hit.
	static $operateurs = array('=', '==', 'IN');

	// On boucle sur chaque critère et on cherche les critères :
	// - {id_xxx = valeur}
	// - {id_xxx == regexp}
	// - {id_xxx IN liste}
	// - {!id_xxx} sauf pour id_secteur
	// et on sort au premier trouvé.
	foreach($criteres as $_critere){
		if (!empty($_critere->param[0][0]->texte)
			and $_critere->param[0][0]->texte == $champ_id
			and $_critere->not != '!'
			and $_critere->exclus != '!'
			and (in_array($_critere->op, $operateurs)
				or (($champ_id != 'id_secteur') and ($_critere->op == $champ_id) and ($_critere->not != '!')))
		) {
			$est_explicite = true;
			break;
		}
	}

	return $est_explicite;
}
