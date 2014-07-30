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

// charger() : on crée le lock_file et on envoie l'alea dans le formulaire via le champ hidden
function doubleclick_formulaire_charger($flux) {
	$lock_file = doubleclick_cree_lock();

	if (isset($flux['data']['_hidden']) == false) $flux['data']['_hidden'] = '';
	$flux['data']['_hidden'] .= "\n".'<input type="hidden" name="doubleclick_lock" value="'.$lock_file.'">'."\n";
	
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
?>