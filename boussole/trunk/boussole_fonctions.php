<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Balises propres a Boussole ---------------------------------

/**
 * Balise retournant les informations sur une boussole.
 *
 * La balise #BOUSSOLE_INFOS renvoie :
 *
 * - le tableau des infos contenues dans la meta boussole_infos_alias si l'alias est fourni,
 * - la liste de tous les tableaux d'infos des meta boussole_infos_xxxx sinon.
 *
 * @balise boussole_infos
 *
 * @param string $p
 * 		alias de la boussole ou vide
 * @return array
 * 		tableau des informations demandees (une boussole ou toutes les boussoles)
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
 * @api
 * @filtre boussole_traduire
 *
 * @param string $aka_boussole	alias de la boussole
 * @param string $champ			champ a traduire
 * @param string $alias			alias du groupe ou du site
 * @return string				champ traduit
 */
function boussole_traduire($aka_boussole, $champ, $alias='') {
	static	$champs_boussole = array('nom_boussole', 'slogan_boussole', 'descriptif_boussole');
	static	$champs_groupe = array('nom_groupe');
	static	$champs_site = array('nom_site', 'slogan_site', 'descriptif_site');

	$traduction = '';

	if ($champ == '')
		return $traduction;


	// Détermination de la traduction à rechercher dans les extras de boussole
	if ($aka_boussole) {
		if (in_array($champ, $champs_boussole)) {
			$type_objet = 'boussole';
			$aka_objet = $aka_boussole;
			$info = str_replace('boussole', 'objet', $champ);
		}
		elseif (in_array($champ, $champs_groupe)) {
			$type_objet = 'groupe';
			$aka_objet = $alias;
			$info = str_replace('groupe', 'objet', $champ);
		}
		elseif (in_array($champ, $champs_site)) {
			$type_objet = 'site';
			$aka_objet = $alias;
			$info = str_replace('site', 'objet', $champ);
		}
		elseif ($champ == 'nom_slogan_site') {
			$type_objet = 'site';
			$aka_objet = $alias;
			$info = array('nom_objet', 'slogan_objet');
		}
		else
			return $traduction;
	}

	// Accès à la table boussoles_extras où sont stockées les traductions
	$where = array(
		'aka_boussole=' . sql_quote($aka_boussole),
		'type_objet=' . sql_quote($type_objet),
		'aka_objet=' . sql_quote($aka_objet));
	$traductions = sql_fetsel($info, 'spip_boussoles_extras', $where);
	if (count($traductions) == 1)
		$traduction = extraire_multi($traductions[$info]);
	else if (count($traductions) == 2)
		$traduction = extraire_multi($traductions['nom_objet']) . '-' . extraire_multi($traductions['slogan_objet']);

	return $traduction;
}

?>
