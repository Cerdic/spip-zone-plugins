<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// etre certain d'avoir la classe ChampExtra de connue
include_spip('inc/cextras');

function iextras_get_extras(){
	static $extras = null;
	if ($extras === null) {
		$extras = @unserialize($GLOBALS['meta']['iextras']);
		if (!is_array($extras)) $extras = array();
	}
	return $extras;
}


/* retourne l'extra ayant l'id demande */
function iextra_get_extra($extra_id){
		$extras = iextras_get_extras();
		foreach($extras as $extra) {
			if ($extra->get_id() == $extra_id) {
				return $extra;
			}
		}
		return false;
}

function iextras_set_extras($extras){
	ecrire_meta('iextras',serialize($extras));
	return $extras;
}

// tableau des extras, mais classes par table SQL
// et sous forme de tableau PHP pour pouvoir boucler dessus.
function iextras_get_extras_par_table($appliquer_typo = false){
	$extras = iextras_get_extras();
	if ($appliquer_typo) {
		$extras = _extras_typo($extras);
	}
	$tables = array();
	foreach($extras as $e) {
		if (!isset($tables[$e->table])) {
			$tables[$e->table] = array();
		}
		$tables[$e->table][] = $e->toArray();
	}

	return $tables;
}

// tableau des extras, tries par table SQL
function iextras_get_extras_tries_par_table(){
	$extras = iextras_get_extras();
	$tables = $extras_tries = array();
	foreach($extras as $e) {
		if (!isset($tables[$e->table])) {
			$tables[$e->table] = array();
		}
		$tables[$e->table][] = $e;
	}
	sort($tables);
	foreach ($tables as $table) {
		foreach ($table as $extra) {
			$extras_tries[] = $extra;
		}
	}
	return $extras_tries;
}

/**
 * Compter les saisies extras d'un objet
 *
 * @param 
 * @return 
**/
function compter_champs_extras($objet) {
	static $objets = array();
	if (isset($objets[$objet])) {
		return $objets[$objet];
	}
	
	include_spip('inc/saisies');
	if ($s = unserialize( $GLOBALS['meta'][ 'champs_extras_'.$objet ] )) {
		$s = saisies_lister_par_nom($s);
		return $objets[$objet] = count($s);
	}
	
	return $objets[$objet] = 0;
}


/**
 * Ajouter les saisies SQL et de recherche
 * sur les options de config d'une saisie (de champs extras)
 *
 * @param 
 * @return 
**/
function iextras_formulaire_verifier($flux) {
	if ($flux['args']['form'] == 'construire_formulaire' 
	AND strpos($flux['args']['args'][0], 'champs_extras_')===0) {
		
		// recherche de la saisie à configurer
		$name = "";
		foreach($flux['data'] as $nom=>$err) {
			if (strpos($nom, 'configurer_')===0) {
				$name = substr($nom, strlen('configurer_'));
			}
			break;
		}
		
		// pas de formulaire de configuration de saisie a afficher.
		// on sort sans rien faire.
		if (!$name) {
			return $flux;
		}
		
		// On ajoute un préfixe devant l'identifiant
		$identifiant = 'constructeur_formulaire_'.$flux['args']['args'][0];
		// On récupère le formulaire à son état actuel
		$formulaire_actuel = session_get($identifiant);
		$saisies_actuelles = saisies_lister_par_nom($formulaire_actuel);
		$type_saisie = $saisies_actuelles[$name]['saisie'];
		
		// on récupère les informations de la saisie
		// pour savoir si c'est un champs éditable (il a une ligne SQL)
		$saisies_disponibles = saisies_lister_disponibles();
		if (isset($saisies_disponibles[$type_saisie]['defaut']['options']['sql'])) {
			$sql = $saisies_disponibles[$type_saisie]['defaut']['options']['sql'];
			$flux['data'][$nom] = saisies_inserer($flux['data'][$nom], array(

					'saisie' => 'fieldset',
					'options' => array(
						'nom' => "saisie_modifiee_${name}[options][options_techniques]",
						'label' => _T('iextras:legend_options_techniques'),			
					),
					'saisies' => array(
						array(
							'saisie' => 'input',
							'options' => array(
								'nom' => "saisie_modifiee_${name}[options][sql]",
								'label' => _T('iextras:label_sql'),
								'obligatoire' => 'oui',
								'size' => 50,
								'defaut' => $sql
							)
						),
						array(
							'saisie' => 'oui_non',
							'options' => array(
								'nom' => "saisie_modifiee_${name}[options][rechercher]",
								'label' => _T('iextras:label_rechercher'),
								'explication' => _T('iextras:precisions_pour_rechercher'),
								'defaut' => ''
							)
						),
					
				)));	
		}
	}
	return $flux;
}
?>
