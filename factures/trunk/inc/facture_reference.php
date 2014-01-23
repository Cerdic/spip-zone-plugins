<?php
/**
 * Calcul d'une référence de facture
 * 
 * @plugin     Factures & devis
 * @copyright  2013
 * @author     Cyril Marion - Ateliers CYM
 * @licence    GNU/GPL
 * @package    SPIP\Factures\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Calcul un numéro de référence d'une facture
 *
 * Ici on ajoute +1 au dernier numéro de la précédente facture si on en trouve une,
 * sinon 'F-YYYY-1', soit F-2013-1
 *
 * @note
 *     Cette fonction est à surcharger selon ses choix de nommages.
 * 
 * @param array $valeurs
 *     Valeurs de chargement du formulaire CVT.
 *     Contient notamment les champs :
 * 
 *     - date_facture : date de la facture
 *     - id_organisation : organisation facturée
 *     - id_organisation_emettrice : organisation qui facture
 *
 * @return string
 *     Numéro de référence de la nouvelle facture
**/
function inc_facture_reference_dist($valeurs) {

	// composition n'est pas obligatoire ?
	// une composition peut indiquer un type de facture (devis, proforma, avoir)
	$trouver_table = charger_fonction('trouver_table', 'base');
	$desc = $trouver_table('spip_factures');
	$compo = isset($desc['field']['composition']) and $desc['field']['composition'];

	$where = array();
	if ($compo) {
		$where[] = 'composition = '. sql_quote('');
	}

	// par défaut, on prend la dernière, et on ajoute 1 !
	$precedent = sql_getfetsel('num_facture', 'spip_factures', $where, '', 'date_facture DESC', '0,1');

	if ($precedent) {
		if (preg_match('/([0-9]+)$/', $precedent, $matches)) {
			$num = $matches[1] + 1;
			// des 0 avant, tel que 055 -> 056 ou 099 -> 100
			str_pad($num, $matches[1], "0", STR_PAD_LEFT);
			return substr($precedent, 0, -strlen($matches[1])) . $num;
		} else {
			// on sait pas faire
			return $precedent . '-New';
		}
	} 

	return 'F-' . date('Y', time()) . '-1';
}


/**
 * Calcul un numéro de référence d'une facture, format F-YY-NJOUR-NN
 * tel que F-14-045-01
 *
 * On calcule la facture du jour, si une existe déjà on incrémente le NN
 *
 * @exemple
 *     ```
 *     // surcharge du calcul par défaut d'une référence de facture
 *     function inc_facture_reference($valeurs) {
 *         include_spip('inc/facture_reference');
 *         return inc_facture_reference_jours($valeurs);
 *     }
 *     ```
 * 
 * @param array $valeurs
 *     Valeurs de chargement du formulaire CVT.
 *     Contient notamment les champs :
 * 
 *     - date_facture : date de la facture
 *     - id_organisation : organisation facturée
 *     - id_organisation_emettrice : organisation qui facture
 *
 * @return string
 *     Numéro de référence de la nouvelle facture
**/
function inc_facture_reference_jours($valeurs) {
	$time = strtotime($valeurs['date_facture']);
	$NN = 1;
	$annee = date('y', $time);
	$jour  = str_pad(date('z', $time), 3, "0", STR_PAD_LEFT);
	do {
		$num_facture = "F-$annee-$jour-" . str_pad($NN, 2, "0", STR_PAD_LEFT);
		$NN++;
	} while (sql_getfetsel('num_facture', 'spip_factures', 'num_facture=' . sql_quote($num_facture)));

	return $num_facture;
}
