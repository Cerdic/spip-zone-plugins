<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Balises propres a Boussole ---------------------------------

/**
 * La balise #BOUSSOLE_INFOS renvoie :
 * - soit le tableau des infos contenues dans la meta boussole_infos_<alias> si l'alias est fourni
 * - soit la liste de tous les tableaux d'infos des meta boussole_infos_xxxx sinon
 *
 * @param string $p	alias de la boussole ou vide
 * @return array	tableau des informations demandees (une boussole ou toutes les boussoles)
 */
function balise_BOUSSOLE_INFOS($p) {
	
	$alias = interprete_argument_balise(1,$p);
	$alias = isset($alias) ? str_replace('\'', '"', $alias) : '""';

	$p->code = 'calcul_boussole_infos('.$alias.')';

	return $p;
}

function calcul_boussole_infos($alias) {

	$infos = array();
	
	$where = array();
	$group_by = array();
	if ($alias)
		$where[] = 'aka_boussole=' . sql_quote($alias);
	else
		$group_by[] = 'aka_boussole';

	$akas_boussole = sql_allfetsel('aka_boussole', 'spip_boussoles', $where, $group_by);
	if ($akas_boussole) {
		foreach (array_map('reset', $akas_boussole) as $_aka_boussole) {
			$meta = sql_fetsel('valeur, maj', 'spip_meta', 'nom=' . sql_quote('boussole_infos_' . $_aka_boussole));
			if ($meta) {
				if ($alias)
					$infos = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
				else
					$infos[] = array_merge(unserialize($meta['valeur']), array('maj' => $meta['maj']));
			}
		}
	}

	return $infos;
}


// ----------------------- Filtres propres a Boussole ---------------------------------

/**
 * Renvoie la traduction d'un champ d'une boussole, d'un groupe ou d'un site
 *
 * @param string $aka_boussole	alias de la boussole
 * @param string $champ			champ a traduire
 * @param string $alias			alias du groupe ou du site
 * @return string				champ traduit
 */
function boussole_traduire($aka_boussole, $champ, $alias='') {
	if ($champ == 'non')
		return false;
	$champs_boussole = array('nom_boussole', 'slogan_boussole','descriptif_boussole', 'titre_actualite');
	$champs_groupe_site = array('nom_groupe', 'nom_site', 'slogan_site', 'nom_slogan_site', 'descriptif_site');

	$traduction = '';
	if ($aka_boussole) {
		if (in_array($champ, $champs_boussole))
			$traduction = _T('boussole:' . $champ . '_' . $aka_boussole);
		elseif (in_array($champ, $champs_groupe_site))
			if ($champ != 'nom_slogan_site')
				$traduction = _T('boussole:' . $champ . '_' . $aka_boussole . '_' . $alias);
			else
				$traduction = _T('boussole:nom_site_' . $aka_boussole . '_' . $alias) . ' - ' .
							  _T('boussole:slogan_site_' . $aka_boussole . '_' . $alias);
	}

	return $traduction;
}

?>
