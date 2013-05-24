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

function formulaires_synchroniser_asso_groupe_charger_dist($id_groupe=0) {
	sinon_interdire_acces(autoriser(($id_groupe>=100)?'gerer_groupes':'gerer_autorisations', 'association'));
	$contexte['id_groupe'] = $id_groupe; // passer l'argument dans l'environnement
	$contexte['id_zone'] = sql_getfetsel('id_zone', 'spip_asso_groupes', 'id_groupe='.sql_quote($id_groupe)); // passer le parametre dans l'environnement
	if ( !$contexte['id_zone'] ) // pas de synchronisation possible...
		return FALSE; // on echoue silencieusement.
	if ( !_request('dir2cp') ) // pas de direction selectionnee :
		if( sql_countsel('spip_asso_fonctions', "id_groupe=$id_groupe") ) // on devrait pouvoir exporter.
			$contexte['dir2cp'] = 'exp'; // preselectionner l'import.
		elseif ( sql_countsel('spip_zones', "id_zone=$$contexte[id_zone]") ) // on devrait pouvoir importer.
			$contexte['dir2cp'] = 'imp'; // preselectionner l'import.
		else // on n'a pas de raison d'etre ici !
			return FALSE; // on echoue silencieusement.
	$contexte['_hidden'] .= "<input type='hidden' name='id_groupe' value='$id_groupe' />"; // transmettre le parametre
	$contexte['_action'] = array('synchroniser_asso_groupe', ''); // pour passer securiser action

	return $contexte;
}

function formulaires_synchroniser_asso_groupe_verifier_dist($id_groupe=0) {
	$erreurs = array();

	if ( !in_array(_request('dir2cp'), array('imp','exp')) )
		$erreurs['dir2cp'] = _T('asso:erreur_direction_synchronisation');

	if ( count($erreurs) )
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	return $erreurs;
}

function formulaires_synchroniser_asso_groupe_traiter_dist($id_groupe=0) {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_groupe','action');

	$ret = $synchro(); // la fonction action retourne une liste dont le 1er element est le nombre d'insertion(s) reussies puis les auteurs inseres
	if ($ret[0]>1)
		$res['message_ok'] = _T("asso:membres_ajoutes", array('plusieurs'=>"$ret[0]/".(count($ret)-1), ) );
	else
		$res['message_ok'] = _T("asso:membre_ajoute", array('un'=>"$ret[0]/".(count($ret)-1), ) );

	return $res;
}

?>