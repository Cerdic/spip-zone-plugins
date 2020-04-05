<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2016                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Gestion du formulaire de date
 *
 * @package SPIP\Core\Formulaires
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Chargement du formulaire d'édition d'une date
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array|string $options
 *     Options. Si string, unserialize pour obtenir un tableau.
 *
 *     - date_nouvelle_edition : Permet de modifier en plus la date de rédaction antérieure
 * @return array
 *     Environnement du formulaire
 **/
function formulaires_dater_livre_charger_dist($objet, $id_objet, $retour = '', $options = array()) {

	$objet = objet_type($objet);
	if (!$objet or !intval($id_objet)) {
		return false;
	}

	if (!is_array($options)) {
		$options = unserialize($options);
	}

	$_id_objet = id_table_objet($objet);
	$table = table_objet($objet);
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table($table);

	if (!$desc) {
		return false;
	}

	$champ_date = $desc['date'] ? $desc['date'] : 'date';
	if (!isset($desc['field'][$champ_date])) {
		return false;
	}

	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'id' => $id_objet,
	);


	$select = "$champ_date as date ,date_nouvelle_edition";
	if (isset($desc['field']['statut'])) {
		$select .= ",statut";
	}


	$row = sql_fetsel($select, $desc['table'], "$_id_objet=" . intval($id_objet));
	$statut = isset($row['statut']) ? $row['statut'] : 'publie'; // pas de statut => publie

	$valeurs['editable'] = autoriser('dater', $objet, $id_objet, null, array('statut' => $statut));

	$possedeDateNouvelleEdition = false;

	if (isset($row['date_nouvelle_edition']) and
		$regs = recup_date($row['date_nouvelle_edition'], false)
	) {
		$annee_nouvelle_edition = $regs[0];
		$mois_nouvelle_edition = $regs[1];
		$jour_nouvelle_edition = $regs[2];
		$heure_nouvelle_edition = $regs[3];
		$minute_nouvelle_edition = $regs[4];
		$possedeDateNouvelleEdition = true;
		// attention : les vrai dates de l'annee 1 sont stockee avec +9000 => 9001
		// mais reviennent ici en annee 1 par recup_date
		// on verifie donc que le intval($row['date_nouvelle_edition']) qui ressort l'annee
		// est bien lui aussi <=1 : dans ce cas c'est une date sql 'nulle' ou presque, selon
		// le gestionnnaire sql utilise (0001-01-01 pour PG par exemple)
		if (intval($row['date_nouvelle_edition']) <= 1 and ($annee_nouvelle_edition <= 1) and ($mois_nouvelle_edition <= 1) and ($jour_nouvelle_edition <= 1)) {
			$possedeDateNouvelleEdition = false;
		}
	} else {
		$annee_nouvelle_edition = $mois_nouvelle_edition = $jour_nouvelle_edition = $heure_nouvelle_edition = $minute_nouvelle_edition = 0;
	}

	if ($regs = recup_date($row['date'], false)) {
		$annee = $regs[0];
		$mois = $regs[1];
		$jour = $regs[2];
		$heure = $regs[3];
		$minute = $regs[4];
	}

	// attention, si la variable s'appelle date ou date_nouvelle_edition, le compilo va
	// la normaliser, ce qu'on ne veut pas ici.
	$valeurs['afficher_date_nouvelle_edition'] = ($possedeDateNouvelleEdition ? $row['date_nouvelle_edition'] : '');
	$valeurs['date_nouvelle_edition_jour'] = dater_livre_formater_saisie_jour($jour_nouvelle_edition, $mois_nouvelle_edition, $annee_nouvelle_edition);
	$valeurs['date_nouvelle_edition_heure'] = "$heure_nouvelle_edition:$minute_nouvelle_edition";

	$valeurs['afficher_date'] = $row['date'];
	$valeurs['date_jour'] = dater_livre_formater_saisie_jour($jour, $mois, $annee);
	$valeurs['date_heure'] = "$heure:$minute";

	$valeurs['_editer_date_nouvelle_edition'] = true;

	// $valeurs['sans_nouvelle_edition'] = !$possedeDateNouvelleEdition;

	// if (isset($options['date_nouvelle_edition'])) {
	// 	$valeurs['_editer_date_nouvelle_edition'] = $options['date_nouvelle_edition'];
	// } else {
	// 	$valeurs['_editer_date_nouvelle_edition'] = ($objet == 'livre' and $possedeDateNouvelleEdition);
	// }
	$valeurs['_label_date'] = (($statut == 'publie') ? _T('texte_date_publication_objet') : _T('texte_date_creation_objet'));
	$valeurs['_saisie_en_cours'] = (_request('_saisie_en_cours') !== null or _request('date_jour') !== null);

	// cas ou l'on ne peut pas dater mais on peut modifier la date de redac anterieure
	// https://core.spip.net/issues/3494
	$valeurs['_editer_date'] = $valeurs['editable'];
	if ($valeurs['_editer_date_nouvelle_edition'] and !$valeurs['editable']) {
		$valeurs['editable'] = autoriser('modifier', $objet, $id_objet);
	}

	return $valeurs;
}

/**
 * Formate la date
 *
 * @param string|int $jour
 *     Numéro du jour
 * @param string|int $mois
 *     Numéro du mois
 * @param string|int $annee
 *     Année
 * @param string $sep
 *     Séparateur
 * @return string
 *     Date formatée tel que `02/10/2012`
 **/
function dater_livre_formater_saisie_jour($jour, $mois, $annee, $sep = "/") {
	$annee = str_pad($annee, 4, '0', STR_PAD_LEFT);
	if (intval($jour)) {
		$jour = str_pad($jour, 2, '0', STR_PAD_LEFT);
		$mois = str_pad($mois, 2, '0', STR_PAD_LEFT);

		return "$jour$sep$mois$sep$annee";
	}
	if (intval($mois)) {
		$mois = str_pad($mois, 2, '0', STR_PAD_LEFT);

		return "$mois$sep$annee";
	}

	return $annee;
}

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui
 * ne représentent pas l'objet edité
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array|string $options
 *     Options.
 * @return string
 *     Hash du formulaire
 **/
function formulaires_dater_livre_identifier_dist($objet, $id_objet, $retour = '', $options = array()) {
	return serialize(array($objet, $id_objet));
}

/**
 * Vérifications avant traitements du formulaire d'édition d'une date
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array|string $options
 *     Options.
 * @return Array
 *     Tableau des erreurs
 */
function formulaires_dater_livre_verifier_dist($objet, $id_objet, $retour = '', $options = array()) {
	$erreurs = array();

	// ouvrir le formulaire en edition ?
	if (_request('_saisie_en_cours')) {
		$erreurs['message_erreur'] = '';

		return $erreurs;
	}

	foreach (array('date', 'date_nouvelle_edition') as $k) {
		if ($v = _request($k . "_jour") and !dater_livre_recuperer_date_saisie($v, $k)) {
			$erreurs[$k] = _T('format_date_incorrecte');
		} elseif ($v = _request($k . "_heure") and !dater_livre_recuperer_heure_saisie($v)) {
			$erreurs[$k] = _T('format_heure_incorrecte');
		}
	}

	if (!_request('date_jour')) {
		$erreurs['date'] = _T('info_obligatoire');
	}

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition d'une date
 *
 * @param string $objet
 *     Type d'objet
 * @param int $id_objet
 *     Identifiant de l'objet
 * @param string $retour
 *     URL de redirection après le traitement
 * @param array|string $options
 *     Options.
 * @return Array
 *     Retours des traitements
 */
function formulaires_dater_livre_traiter_dist($objet, $id_objet, $retour = '', $options = array()) {
	$res = array('editable' => ' ');

	if (_request('changer')) {
		$_id_objet = id_table_objet($objet);
		$table = table_objet($objet);
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table);

		if (!$desc) {
			return array('message_erreur' => _L('erreur'));
		} #impossible en principe

		$champ_date = $desc['date'] ? $desc['date'] : 'date';

		$set = array();

		$charger = charger_fonction("charger", "formulaires/dater_livre/");
		$v = $charger($objet, $id_objet, $retour, $options);

		if ($v['_editer_date']) {
			if (!$d = dater_livre_recuperer_date_saisie(_request('date_jour'))) {
				$d = array(date('Y'), date('m'), date('d'));
			}
			if (!$h = dater_livre_recuperer_heure_saisie(_request('date_heure'))) {
				$h = array(0, 0);
			}

			$set[$champ_date] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);
		}
		if (isset($desc['field']['date_nouvelle_edition']) and $v['_editer_date_nouvelle_edition']) {
			if (!_request('date_nouvelle_edition_jour') or _request('sans_nouvelle_edition')) {
				$set['date_nouvelle_edition'] = sql_format_date(0, 0, 0, 0, 0, 0);
			} else {
				if (!$d = dater_livre_recuperer_date_saisie(_request('date_nouvelle_edition_jour'), "date_nouvelle_edition")) {
					$d = array(date('Y'), date('m'), date('d'));
				}
				if (!$h = dater_livre_recuperer_heure_saisie(_request('date_nouvelle_edition_heure'))) {
					$h = array(0, 0);
				}
				$set['date_nouvelle_edition'] = sql_format_date($d[0], $d[1], $d[2], $h[0], $h[1]);
			}
		}
		if (count($set)) {
			$publie_avant = objet_test_si_publie($objet, $id_objet);
			include_spip('action/editer_objet');
			objet_modifier($objet, $id_objet, $set);
			$publie_apres = objet_test_si_publie($objet, $id_objet);
			if ($publie_avant !== $publie_apres) {
				// on refuse ajax pour forcer le rechargement de la page ici
				// on refera traiter une 2eme fois, mais c'est sans consequence
				refuser_traiter_formulaire_ajax();
			}
		}
	}

	if ($retour) {
		$res['redirect'] = $retour;
	}

	set_request('date_jour');
	set_request('date_nouvelle_edition_jour');
	set_request('date_heure');
	set_request('date_nouvelle_edition_heure');

	return $res;
}

/**
 * Récupérer annee,mois,jour sur la date saisie
 *
 * @param string $post
 * @param string $quoi
 * @return array
 */
function dater_livre_recuperer_date_saisie($post, $quoi = "date") {
	if (!preg_match('#^(?:(?:([0-9]{1,2})[/-])?([0-9]{1,2})[/-])?([0-9]{4}|[0-9]{1,2})#', $post, $regs)) {
		return '';
	}
	if ($quoi == "date_nouvelle_edition") {
		if ($regs[3] <> '' and $regs[3] < 1001) {
			$regs[3] += 9000;
		}

		return array($regs[3], $regs[2], $regs[1]);
	} else {
		$t = mktime(0, 0, 0, $regs[2], $regs[1], $regs[3]);
		// si la date n'est pas valide selon mktime, la refuser
		if (!$t) {
			return '';
		}

		return array(date('Y', $t), date('m', $t), date('d', $t));
	}

}

/**
 * Récupérer heures,minutes sur l'heure saisie
 *
 * @param string $post
 * @return array
 */
function dater_livre_recuperer_heure_saisie($post) {
	if (!preg_match('#([0-9]{1,2})(?:[h:](?:([0-9]{1,2}))?)?#', $post, $regs)) {
		// ici gros hack : on force l'heure à 1mn du matin. 
		// Ainsi, avec le critère {age>=0} le livre sera publié tôt le matin, pas à minuit
		// à améliorer
		return array(0, 01);
	}

	return array($regs[1], $regs[2]);
}
