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
 * Ici on ajoute 1 a la précédente facture si on en trouve une,
 * sinon 'F-YYYY-1', soit F-2013-1
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
	// par défaut, on prend la dernière, et on ajoute 1 !
	$precedent = sql_getfetsel('num_facture', 'spip_factures',
		// facture… de type facture
		'composition = '. sql_quote(''),
		'', 'date_facture DESC', '0,1');
	if ($precedent) {
		if (preg_match('/([0-9]+)$/', $precedent, $matches)) {
			$num = $matches[1] + 1;
			// des 0 avant, tel que 055 -> 056 ou 099 -> 100
			while (strlen($matches[1]) > strlen($num)) {
				$num = '0' . $num;
			}
			return substr($precedent, 0, -strlen($matches[1])) . $num;
		} else {
			// on sait pas faire
			return $precedent . '-New';
		}
	} 

	return 'F-' . date('Y', time()) . '-1';
}
