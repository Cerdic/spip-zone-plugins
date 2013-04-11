<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
    return;

function action_supprimer_asso_fonctions_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// cette action peut etre appelee selon trois modes
	if (strpos($arg, '-')) { // mode d'appel 1 : directement depuis un squelette avec en argument <id_groupe>-<id_auteur>
		list($id_groupe, $id_auteur) = explode('-', $arg);
		sql_delete('spip_asso_fonctions', "id_groupe=".intval($id_groupe)." AND id_auteur=".intval($id_auteur));
	} else { // exclusion en lot de plusieurs membres
		$id_auteurs = association_recuperer_liste('id_auteurs', TRUE);
		$id_groupes = _request('id_groupes');
		if (is_array($id_groupes)) { // mode d'appel 3 : depuis la page des adherents et la suppression multiple. Les differents id_groupes sont alors dans un tableau id_groupes
			foreach($id_groupes as $id_groupe) {
				exclusion_membres($id_groupe, $id_auteurs);
			}
		} else { // mode d'appel 2 : depuis le traitement d'un formulaire et arg contient alors uniquement <id_groupe>
			exclusion_membres($arg, $id_auteurs);
		}

	}
}

function exclusion_membres ($id_groupe, $id_auteurs) {
	if (count($id_auteurs)) { // securite : ne pas executer la requete s'il n'y a pas de id_auteur
		sql_delete('spip_asso_fonctions', 'id_groupe='.intval($id_groupe).' AND '.sql_in('id_auteur', $id_auteurs) );
	}
}

?>