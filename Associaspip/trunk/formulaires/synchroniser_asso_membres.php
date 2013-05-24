<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_synchroniser_asso_membres_charger_dist() {
	sinon_interdire_acces( autoriser('gerer_membres', 'association') );
	$contexte['_action'] = array('synchroniser_asso_membres',''); // pour passer securiser action
	$q = sql_select('statut, COUNT(statut) AS nbr', 'spip_auteurs', '', 'statut');
	while ( $rep = sql_fetch($q) )
		$contexte['nombre_statuts_'.$rep['statut']] = $rep['nbr'];

	return $contexte;
}

function formulaires_synchroniser_asso_membres_verifier_dist() {
	$erreurs = array();

	if ( !in_array(_request('dir2cp'), array('imp','exp')) )
		$erreurs['dir2cp'] = _T('asso:erreur_direction_synchronisation');

	if ( count($erreurs) )
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	return $erreurs;
}

function formulaires_synchroniser_asso_membres_traiter_dist() {
	$res = array();

	$synchro = charger_fonction('synchroniser_asso_membres','action');
	$nbr_ins = $synchro(); // la fonction action retourne le nombre d'insertion realisees
	if ($nbr_ins>1) {
		$res['message_ok'] = _T('asso:membres_ajoutes', array('plusieurs'=>$nbr_ins) );
	} else {
		$res['message_ok'] = _T('asso:membre_ajoute', array('un'=>$nbr_ins) );
	}

	return $res;
}

?>