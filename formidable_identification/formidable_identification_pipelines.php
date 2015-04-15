<?php
/**
 * Utilisations de pipelines 
 *
 * @plugin     Formidable Inscription
 * @copyright  2015
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Formidable_inscription\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Vérification des valeurs saisies :
 * - Si non anonymisé, on indique qu'il manque un email
 * - Si email non valide, on l'indique
 * - Si email déjà utilisé, on l'indique
 *
 * @param array $flux
 * @return array
 */
function formidable_identification_formulaire_verifier($flux){
	// gerer le retour paiement avec demande de confirmation
	if ($flux['args']['form']=='formidable'
		AND $id = $flux['args']['args'][0]){
		if(_request('formidable_identification') == 'on' && !isset($GLOBALS['visiteur_session']['id_auteur'])){
			if(!_request('formulaire_identification_email') OR !email_valide(_request('formulaire_identification_email'))){
				$flux['data']['formulaire_identification_email'] = _T('form_email_non_valide');
			}
			if(_request('formulaire_identification_email') && sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote(_request('formulaire_identification_email')))){
				$flux['data']['formulaire_identification_email'] = _T('formidable_identification:erreur_email_utilise');
			}
		}
		if(!isset($flux['data']['message_erreur']) && count($flux['data']) > 0){
			$flux['data']['message_erreur'] = _T('formidable_identification:erreur_verifier_formulaire');
		}
	}
	return $flux;
}


function formidable_identification_formulaire_charger($flux){
	// gerer le retour paiement avec demande de confirmation
	if ($flux['args']['form']=='formidable'
			AND $id = $flux['args']['args'][0]){
		if(_request('validation_inscription') == 'ok' && isset($GLOBALS['visiteur_session']['id_auteur'])){
			$flux['data']['editable'] = false;
			$flux['data']['message_ok'] = _T('formidable_identification:message_ok_inscription_validee');
			sql_updateq('spip_formulaires_reponses',array('statut' => 'prop'),'id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
		}
	}
	return $flux;
}