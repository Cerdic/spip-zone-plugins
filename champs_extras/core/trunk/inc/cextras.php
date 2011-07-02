<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

class ChampExtra{
	var $table       = ''; // table SQL (spip_rubriques)
	var $champ       = ''; // nom du champ SQL (ps)
	var $sql         = ''; // définition SQL du champ SQL (text NOT NULL DEFAULT '')

	var $saisie      = ''; // type de saisie (input)

	var $saisie_parametres = array();
		/*
		    // peut indiquer tout parametre d'une #SAISIE, tel que :
			label       = ''; // label du champ. Code de langue (monplug:mon_label)
			obligatoire = false; // champ obligatoire ? 'oui'/true 
			explication => '', // message d'explication !
			attention => '',   // message d'attention !
			class => '',       // classes CSS sur l'element
			li_class => '',    // classes CSS sur l'element parent LI
			datas => '',       // donnees pour les listes d'éléments
		                       // liste de valeurs 
		                       // champ texte : "cle1,val1\ncle2,val2" 
		                       // ou tableau : array("cle1"=>"val1","cle2"=>"val2") 
		*/


	var $verifier         = false;   // Fonction de vérification du plugin API verifier
	var $verifier_options = array(); // Fonction de vérification du plugin API verifier
	
	var $rechercher       = false;   // ce champ entre-t-il dans le moteur de recherche ?
	var $traitements      = '';      // _TRAITEMENT_RACCOURCIS ,  
	                                 // _TRAITEMENT_TYPO ou autre declaration pour la $table_des_traitements

	var $_id = ''; // identifiant de ce champ extra

	// calcules a la volee
	var $_type = ''; // rubrique
	var $_objet = ''; // rubriques




	// constructeur
	function ChampExtra($params=array()) {
		$this->definir($params);

		// ne pas definir les objets à la creation
		// car au moment de l'appel de declarer_table_objet_sql
		// il peut y avoir une reentrance, via table_objet, surnoms,
		// dans declarer_table_objet_sql, renvoyant alors 0, puis aucun surnom,
		// et ne calculant finalement pas un table_objet correct.
		// On definit donc plus tard, au besoin.
		# $this->definir_raccourcis();

	}


	// definir les champs
	function definir($params=array()) {
		foreach ($params as $cle=>$valeur) {
			if (isset($this->$cle)) {
				$this->$cle = $valeur;
			}
		}
		
		$this->make_id();
	}

	// definir les raccourcis _objet _type
	function definir_raccourcis($params=array()) {			
		$this->_objet     = table_objet($this->table); // articles
		$this->_type      = objet_type($this->_objet); // article
	}
	
	// creer l'id du champ extra :
	function make_id(){
		// creer un hash
		$hash = $this->champ . $this->table . $this->sql;
		$this->_id = substr(md5($hash),0,6);
	}

	// determiner un identifiant
	function get_id(){
		if (!$this->_id) $this->make_id();
		return $this->_id;
	}

	// transformer en tableau PHP les variable publiques de la classe.
	function toArray(){
		$extra = array();
		foreach ($this as $cle=>$val) {
			if ($cle[0] !== '_') {
				$extra[$cle] = $val;
			}
		}
		$extra['extra_id'] = $this->get_id();
		return $extra;
	}

	// affichage si on fait un echo...
	function __toString() {
		return "<pre>" . print_r($this, true) . "</pre>";
	}
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
