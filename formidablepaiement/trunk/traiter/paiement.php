<?php
/**
 * Traitement paiement apres la saisie d'un formulaire
 *
 * @plugin     Formulaires de paiement
 * @copyright  2014
 * @author     Cédric Morin
 * @licence    GNU/GPL
 * @package    SPIP\Formidablepaiement\traiter\paiement
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_paiement_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);


	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['paiement'] = true;
	return $retours;
}
