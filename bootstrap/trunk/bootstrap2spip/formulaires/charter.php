<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Chargement des valeurs
 * @return array
 */
function formulaires_charter_charger_dist(){

	$valeurs = array(
		'text'=>'',
		'text_obli'=>'',
		'textarea'=>'',
		'textarea_pleine_largeur'=>'',
		'text_long_label'=>'',
		'radio'=>'non',
		'checkbox'=>array(1),
		'checkbox_long_label'=>array(1,2),
	);

	return $valeurs;
}

/**
 * Verifier la saisie
 * on simule des erreurs si on a clique sur annuler
 * @return array
 */
function formulaires_charter_verifier_dist(){
	$erreurs = array();
	if (_request('cancel')){
		$erreurs['message_erreur'] = ('Un long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur...');
		$erreurs['text'] = ('Erreur<br />'."<input type='checkbox' name='confirm' id='confirm' value='oui' /><label for='confirm'>Confirmez que vous &ecirc;tes sur</label>");
		$erreurs['text_obli'] = ('Erreur');
		$erreurs['textarea'] = ('Un long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur, long message d\'erreur...');
		$erreurs['textarea_pleine_largeur'] = ('Erreur');
		$erreurs['textarea_pleine_largeur_obli'] = ('Erreur');
		$erreurs['text_long_label'] = ('Erreur');
		$erreurs['radio'] = ('Erreur');
		$erreurs['checkbox'] = ('Erreur');
		$erreurs['checkbox_long_label'] = ('Erreur');
	}

	return $erreurs;
}

/**
 * Traitement de la saisie
 */
function formulaires_charter_traiter_dist(){
	return array('message_ok'=>('Bravo, c\'est une reussite !'));
}

?>