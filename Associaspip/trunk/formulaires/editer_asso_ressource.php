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

function formulaires_editer_asso_ressource_charger_dist($id_ressource=0) {
	$contexte = formulaires_editer_objet_charger('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');
	if (!$id_ressource) { // si c'est une nouvelle operation, on charge la date d'aujourd'hui ainsi qu'un id_compte et journal nuls
		$contexte['date_acquisition'] = date('Y-m-d');
		$contexte['ud'] = 'D';
	}
	association_chargeparam_operation('ressources', $id_activite, $contexte);
	association_chargeparam_destinations('ressources', &$contexte);

	if (!$contexte['statut']) { // par defaut utiliser les nouveaux statuts numeriques (on ne garde la compatibilite qu'en edition --si l'entree n'est pas convertie)
		$contexte['quantite'] = 1;
		$contexte['suspendu'] = '';
	} elseif (is_numeric($contexte['statut'])) { // et pour les statuts numerique convertir les nombres negatifs...
		$contexte['quantite'] = abs($contexte['statut']);
		$contexte['suspendu'] = ($contexte['statut']<0?'on':'');
	}
	if (!$contexte['ud']) { // les anciens enregistrements (ou ceux inseres autrement que via ce formulaire) n'ont pas d'unite de precise : on assigne la valeur par defaut (le jour)
		$contexte['ud'] = 'D';
	}

	// paufiner la presentation des valeurs
	if ($contexte['pu'])
		$contexte['pu'] = association_formater_nombre($contexte['pu']);
	if ($contexte['prix_acquisition'])
		$contexte['prix_acquisition'] = association_formater_nombre($contexte['prix_acquisition']);
	if ($contexte['prix_caution'])
		$contexte['prix_caution'] = association_formater_nombre($contexte['prix_caution']);
	if (is_numeric($contexte['statut']))
		$contexte['statut'] = association_formater_nombre($contexte['statut']);

	return $contexte;
}

function formulaires_editer_asso_ressource_verifier_dist($id_ressource=0) {
	$erreurs = array();

	if ($erreur = association_verifier_montant('pu') )
		$erreurs['pu'] = $erreur;
	if ($erreur = association_verifier_montant('prix_caution') )
		$erreurs['prix_caution'] = $erreur;
	if ($erreur = association_verifier_montant('prix_acquisition') )
		$erreurs['prix_acquisition'] = $erreur;
	if ($erreur = association_verifier_montant('quantite') )
		$erreurs['statut'] = $erreur;
	if ($erreur = association_verifier_destinations('prix_acquisition') )
		$erreurs['destinations'] = $erreur;
	if ($erreur = association_verifier_date('date_acquisition') )
		$erreurs['date_acquisition'] = $erreur;

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('asso:erreur_titre');
	}
	return $erreurs;
}

function formulaires_editer_asso_ressource_traiter($id_ressource=0) {
	return formulaires_editer_objet_traiter('asso_ressources', $id_ressource, '', '',  generer_url_ecrire('ressources'), '');
}

?>