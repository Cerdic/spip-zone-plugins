<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

class ChampExtra{
	var $table = ''; // type de table ('rubrique')
	var $champ = ''; // nom du champ ('ps')
	var $label = ''; // label du champ, code de lanque ('monplug:mon_label')
	var $type = 'textarea'; // type (input/textarea)
	var $sql = ''; // declaration sql (text NOT NULL DEFAULT '')
	
	// constructeur
	function ChampExtra($params=array()) {
		$this->definir($params);
	}
	
	// definir les champs
	function definir($params=array()) {
		foreach ($params as $cle=>$valeur) {
			if (isset($this->$cle)) {
				// si une fonction specifique existe pour ce type, l'utiliser
				if (method_exists('ChampExtra','set_'.$cle)) {
					$this->{'set_'.$cle}($valeur);
				} else {
					$this->$cle = $valeur;
				}
			}
		}
	}
	
	// declarations specifiques
	function set_type($val='textarea') {
		if (!in_array($val, array('textarea','input'))) {
			$val = 'textarea';
		}
		$this->type = $val;	
	}
	
	// transformer en tableau PHP les variable de la classe.
	function toArray(){
		$extra = array();
		foreach ($this as $cle=>$val) {
			$extra[$cle] = $val;
		}
		return $extra;
	}
}


function declarer_champs_extras($champs, $tables){
	// ajoutons les champs un par un
	foreach ($champs as $c){
		$table = table_objet_sql($c->table);
		if (isset($tables[$table]) and $c->champ and $c->sql) {
			$tables[$table]['field'][$c->champ] = $c->sql;
		}
	}	
	return $tables;
}



function creer_champs_extras($champs, $nom_meta_base_version, $version_cible) {
	$current_version = 0.0;

	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		// cas d'une installation
		if ($current_version==0.0){
			include_spip('base/create');
			// on recupere juste les differentes tables a mettre a jour
			$tables = array();
			foreach ($champs as $c){ 
				if ($table = table_objet_sql($c->table)) {
					$tables[$table] = $table;
				}
			}		
			// on met a jour les tables trouvees
			foreach($tables as $table) {
				maj_tables($table);
			}
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
	}	
}

/**
 * Supprime les champs extras (objets ChampExtra passes dans le tableau $champs)
 */
function vider_champs_extras($champs, $nom_meta_base_version) {
	// on efface chaque champ trouve
	foreach ($champs as $c){ 
		if ($table = table_objet_sql($c->table) and $c->champ and $c->sql) {
			sql_alter("TABLE $table DROP $c->champ");
		}
	}
	effacer_meta($nom_meta_base_version);	
}

/**
 * Log une information si l'on est en mode debug 
 * ( define('EXTRAS_DEBUG',true); )
 * Ou si le second parametre est true.
 */
function extras_log($contenu, $important=false) {
	if ($important
	OR (defined('EXTRAS_DEBUG') and EXTRAS_DEBUG)) {
		spip_log($contenu,'extras');
	}
}
?>
