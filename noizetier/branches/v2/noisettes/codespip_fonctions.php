<?php

// Fonction reprise de http://zone.spip.org/trac/spip-zone/browser/_plugins_/simpletest/inc/tests.php
// En attendant une éventuelle intégration dans le core
// Préfixée pour éviter tout conflit
// Voir aussi http://zone.spip.org/trac/spip-zone/browser/_plugins_/couteau_suisse/cout_pipelines.php?rev=77088#L293

if (!defined('_DIR_CODE')) {
	include_spip('inc/flock');
	sous_repertoire(_DIR_CACHE, 'recuperer_code');
	define('_DIR_CODE',_DIR_CACHE . 'recuperer_code/');
}

/**
	 * recupere le resultat du calcul d'une compilation de code de squelette
	 * $coucou = $this->recuperer_code('[(#AUTORISER{ok}|oui)coucou]');
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * 
	 * @return string/array : page compilee et calculee
	 */
	function noizetier_recuperer_code($code, $contexte=array(), $options = array(), $connect=''){
		if (!is_array($contexte)) $contexte = unserialize ($contexte);
		$fond = _DIR_CODE . md5($code);
		if(!file_exists($fond . 'html') || (defined('_VAR_MODE') && _VAR_MODE=='recalcul') || _request('var_mode')=='recalcul')
			ecrire_fichier($fond . '.html', $code);
		$fond = str_replace('../', '', $fond); // pas de ../ si dans ecrire !
		return recuperer_fond($fond, $contexte, $options, $connect);
	}
