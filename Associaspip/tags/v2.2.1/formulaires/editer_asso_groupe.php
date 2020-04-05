<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;
include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_groupe_charger_dist($id_groupe=0) {
	$contexte = formulaires_editer_objet_charger('asso_groupe', $id_groupe, '', '',  generer_url_ecrire('groupes'), ''); // cet appel va charger dans $contexte tous les champs de la table spip_asso_groupes associes a l'id_groupe passe en param
	if ($id_groupe>0 && $id_groupe<100) {
		$contexte['_autorisation'] = TRUE;
	}
	return $contexte;
}

function formulaires_editer_asso_groupe_traiter($id_groupe=0) {
	return formulaires_editer_objet_traiter('asso_groupe', $id_groupe, '', '', generer_url_ecrire('membres_groupe', "id=$id_groupe"), '');
}

?>