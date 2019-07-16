<?php

/**
 * Gestion du formulaire de filtrage des rôles de documents
 *
 * @package SPIP\Formulaires
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Chargement du formulaire de filtrage des rôles de documents
 *
 * @return array
 */
function formulaires_filtrer_roles_documents_charger_dist($redirect='') {

  $roles = roles_documents_presents();
  $valeurs['roles'] = $roles;
  $valeurs['role'] = _request('role');

	return $valeurs;
}

/**
 * Traiter le post des informations de filtrage des rôles de documents
 * 
 * @return array
 */
function formulaires_filtrer_roles_documents_traiter_dist($redirect='') {

  $retours = array();
  if (!$redirect) {
    $redirect = self();
  }
  $redirect = parametre_url($redirect, 'role', _request('role'));
  $retours['redirect'] = $redirect;

	return $retours;
}