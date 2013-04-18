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

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_editer_asso_membre_charger_dist($id_auteur) {
	$contexte = formulaires_editer_objet_charger('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');
	list($annee, $mois, $jour) = explode('-',$contexte['date_validite']);
	if ($jour==0 OR $mois==0 OR $annee==0) // on verifie que la date de validite n'est pas nulle et si oui on la met a hier
		$contexte['date_validite'] = date('Y-m-d', mktime(0, 0, 0, date('m')  , date('d')-1, date('Y')));

	return $contexte;
}

function formulaires_editer_asso_membre_verifier_dist($id_auteur) {
	$erreurs = array();

	if ($erreur = association_verifier_date('date_validite'))
		$erreurs['date_validite'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_membre_traiter($id_auteur) {
	// traitement des appartenance a un groupe
	$action_membre_groupes = charger_fonction('editer_membre_groupes','action');
	$action_membre_groupes($id_auteur);
	// ajout a un groupe
	$action_ajouter_groupes = charger_fonction('ajouter_membre_groupes', 'action');
	$action_ajouter_groupes($id_auteur);
	// traitement des informations du membre
	return formulaires_editer_objet_traiter('asso_membres', $id_auteur, '', '',  generer_url_ecrire('adherents'), '');
}

?>