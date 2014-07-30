<?php
/**
 * Utilisations de pipelines par Double Click
 *
 * @plugin     Double Click
 * @copyright  2014
 * @author     Camille Sauvage
 * @licence    GNU/GPL
 * @package    SPIP\Doubleclick\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

// on passe par une globale faute de mieux
$lock_file = '';

// charger() : on crée le lock_file et on value la globale
function doubleclick_formulaire_charger($flux) {
	global $lock_file;
	$lock_file = doubleclick_cree_lock();
	
	return $flux;
}

/* verifier() : on vérifie l'existence du lock_file
 * - soit on le trouve et on l'efface en silence
 * - soit on ne le trouve pas et on renvoie une erreur
 */
function doubleclick_formulaire_verifier($flux) {
	if (_request('doubleclick_lock')) {
		if (doubleclick_existe_lock(_request('doubleclick_lock')) == false) {
			$flux['data']['message_erreur'] = _T('doubleclick:erreur_doubleclick');
		} else {
			doubleclick_supprime_lock(_request('doubleclick_lock'));
		}
	}
	
	return $flux;
}

/* affichage_final() :
 * si on trouve un form et un lock_file valué, on l'insère dans le formulaire
 */
function doubleclick_affichage_final($flux) {
	global $lock_file;
	
	if ($lock_file != '') {
		// rechercher/remplacer les "<form *> en ajoutant des hidden
		$hidden = '<input type="hidden" name="doubleclick_lock" value="'.$lock_file.'">';
		$flux = preg_replace('/<form.*?>/', "$0\n$hidden\n", $flux);
	}
	return $flux;
}

?>