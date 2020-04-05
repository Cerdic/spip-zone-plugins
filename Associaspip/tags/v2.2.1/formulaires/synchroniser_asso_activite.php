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

function formulaires_synchroniser_asso_activite_charger_dist($id_evenement=0) {
	sinon_interdire_acces(autoriser('gerer_activites', 'association'));
	if ( !_request('dir2cp') ) // pas de direction selectionnee :
		if ( sql_countsel('spip_evenements_participants', "id_evenement=$id_evenement") ) // on peut importer.
			$contexte['dir2cp'] = 'imp'; // preselectionner l'import.
		elseif( sql_countsel('spip_asso_activites', "id_evenement=$id_evenement") ) // on peut exporter.
			$contexte['dir2cp'] = 'exp'; // preselectionner l'import.
		else // on n'a pas de raison d'etre ici !
			return FALSE; // on echoue silencieusement.
		$contexte['dir2cp'] = 'imp'; // preselectionner l'import.
	$contexte['id_evenement'] = $id_evenement; // passer l'argument dans l'environnement
	$contexte['_hidden'] .= "<input type='hidden' name='id_evenement' value='$id_evenement' />"; // transmettre le parametre
	$contexte['_action'] = array('synchroniser_asso_activite', ''); // pour passer securiser action

	return $contexte;
}

function formulaires_synchroniser_asso_activite_verifier_dist($id_evenement=0) {
	$erreurs = array();

	if ( !in_array(_request('dir2cp'), array('imp','exp')) )
		$erreurs['dir2cp'] = _T('asso:erreur_direction_synchronisation');

	if ( count($erreurs) )
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	return $erreurs;
}

function formulaires_synchroniser_asso_activite_traiter_dist($id_evenement=0) {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_activite','action');

	$ret = $synchro(); // la fonction action retourne une liste dont le 1er element est le nombre d'insertion(s) reussies puis les auteurs inseres
	if ($ret[0]>1)
		$res['message_ok'] = _T("asso:membres_ajoutes", array('plusieurs'=>"$ret[0]/".(count($ret)-1),) );
	else
		$res['message_ok'] = _T("asso:membre_ajoute", array('un'=>"$ret[0]/".(count($ret)-1),) );

	return $res;
}

?>