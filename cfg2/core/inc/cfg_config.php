<?php
/*
 * Plugin CFG pour SPIP
 * (c) toggg, marcimat 2009, distribue sous licence GNU/GPL
 *
 * Definitions des fonctions lire_config, ecrire_config et effacer_config. 
 */



//
// en l'absence du nom de depot (gauche des ::) cette fonction prendra comme suit :
// metapack si / present, sinon meta
//
function inc_cfg_analyse_args_dist($args) {
	if (strpos($args, '::') !== false) {
		return explode('::', $args, 2);
	} elseif (strpos($args, '/') !== false) {
		return array('metapack', $args);
	}
	return array('meta', $args);
}


// charge le depot qui va bien en fonction de l'argument demande
// exemples : 
// - meta::description
// - metapack::prefixe_plugin
// - metapack::prefixe/casier/champ
// raccourcis :
// - description
// - prefixe_plugin/
// - prefixe/casier/champ
function cfg_charger_depot($args){
	static $analyse = false;
	if ($analyse === false) {
		$analyse = charger_fonction('cfg_analyse_args', 'inc');
	}
	
	list($depot, $args) = $analyse($args);

	$depot = new cfg_depot($depot);
	$depot->charger_args($args);
	return $depot;
}


// cette classe charge les fonctions de lecture et ecriture d'un depot (dans depots/)
//
// Ces depots ont une version qui evoluera en fonction si des changements d'api apparaissent

// version 2 (fonctions)
// - charger_args
// - lire, ecrire, effacer
class cfg_depot{
	
	var $nom;
	var $depot;
	
	//
	// Constructeur de la classe
	// 'depot' est le nom du fichier php stocke dans /depots/{depot}.php
	// qui contient une classe 'cfg_depot_{depot}'
	//
	// $params est un tableau de parametres passes a la classe cfg_depot_{depot} qui peut contenir :
	// 'champs' => array(
	//		'nom'=>array(
	//			'balise' => 'select|textarea|input', // nom de la balise
	//			'type' => 'checkbox|hidden|text...', // type d'un input 
	//			'tableau' => bool, // est-ce un champ tableau name="champ[]" ?
	//			'id' => y, // cle du tableau 'champs_id' (emplacement qui possede ce champ)
	//		),
	// 'champs_id' => array(
	//		cle => 'nom' // nom d'un champ de type id
	//		),
	//	'param' => array(
	//		'parametre_cfg' => 'valeur' // les parametres <!-- param=valeur --> passes dans les formulaires cfg
	//		),
	//	'val' => array(
	//		'nom' => 'valeur' // les valeurs des champs sont stockes dedans
	//		)
	//	);
	//
	//
	function cfg_depot($depot='metapack', $params=array()){
		if (!isset($params['param'])) {
			$params['param'] = array();
		}
		
		include_spip('depots/'.$depot);
		if (class_exists($class = 'cfg_depot_'.$depot)) {
			$this->depot = new $class($params);
		} else {
			die("CFG ne trouve pas le d&eacute;pot $depot");
		}
		
		$this->version = $this->depot->version;
		$this->nom = $depot;
	}
	
	// ajoute les parametres transmis dans l'objet du depot
	function add_params($params){
		foreach ($params as $o=>$v) {
			$this->depot->$o = $v;
		}	
	}
	
	function lire($params = array()){
		$this->add_params($params);
		return $this->depot->lire(); // array($ok, $val, $messages)
	}
		
	function ecrire($params = array()){
		$this->add_params($params);
		return $this->depot->ecrire(); // array($ok, $val, $messages)
	}
	
	function effacer($params = array()){
		$this->add_params($params);
		return $this->depot->effacer(); // array($ok, $val, $messages)
	}	
	
	function lire_config($unserialize=true){
		list($ok, $s) = $this->depot->lire($unserialize);
		if ($ok && ($nom = $this->nom_champ())) {
			return $s[$nom];
		} elseif ($ok) {
			return $s;	
		} 
	}
	
	function ecrire_config($valeur){
		if ($nom = $this->nom_champ()) {
			$this->depot->val = array($nom=>$valeur);
		} else {
			$this->depot->val = $valeur;
		}
		list($ok, $s) =  $this->depot->ecrire();
		return $ok;	
	}
	
	function effacer_config(){
		if ($nom = $this->nom_champ()){
			$this->depot->val[$nom] = false;
		} else {
			$this->depot->val = null;	
		}
		list($ok, $s) =  $this->depot->effacer();
		return $ok;	

	}	
	
	function nom_champ(){
		if (count($this->depot->champs)==1){
			foreach ($this->depot->champs as $nom=>$def){
				return $nom;	
			}
		}
		return false;			
	}
	
	// charge les arguments d'un lire/ecrire/effacer_config
	// dans le depot : lire_config($args = 'metapack::prefixe/casier/champ');
	function charger_args($args){
		if (method_exists($this->depot, 'charger_args')){
			return $this->depot->charger_args($args);	
		}
		return false;
	}
}




// lire_config() permet de recuperer une config depuis le php
// memes arguments que la balise (forcement)
// $cfg: la config, lire_config('montruc') est un tableau
// lire_config('montruc/sub') est l'element "sub" de cette config
// $def: un defaut optionnel

// $unserialize est mis par l'histoire, et affecte le depot 'meta'
if (!function_exists('lire_config')) {
	function lire_config($cfg='', $def=null, $unserialize=true) {
		$depot = cfg_charger_depot($cfg);
		$r = $depot->lire_config($unserialize);
		if (is_null($r)) return $def;
		return $r;
	}
}



//
// 
// ecrire_config($chemin, $valeur) 
// permet d'enregistrer une configuration
// 
//
if (!function_exists('ecrire_config')) {
	function ecrire_config($cfg='', $valeur=null){
		$depot = cfg_charger_depot($cfg);
		return $depot->ecrire_config($valeur);
	}
}


//
// effacer_config($chemin) 
// permet de supprimer une config 
//
if (!function_exists('effacer_config')) {
	function effacer_config($cfg=''){
		$depot = cfg_charger_depot($cfg);
		return $depot->effacer_config();	
	}
}



?>
