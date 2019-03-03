<?php
/**
 * API de vérification : vérification de la validité de la date_fin 'une période
 *
 * @plugin     Objets restrictions périodes
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Objets_restrictions_periodes\Verifier
 */
 
/**
 * Teste si la date_fin est conforme aux restrictions imposés par la période.
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   valeurs_restriction -> les valeurs de la restriction
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_periodes_date_fin_dist($valeur, $options) {
	include_spip('inc/objets_restrictions_periodes');
	return periodes_verifier_date('date_fin', $valeur, $options);
}
