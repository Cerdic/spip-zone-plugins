<?php

class actionParser  {
	var $parser;
	// les valeurs récupérées par le contexte et à utiliser dans les actions
	var $valeurs;
	// les ordres sql qu'on en déduit
	var $sql= array();

	// on part d'un etat 'vide' et on empile des etats 'actions' puis
	// 'update' ou 'insert' puis 'key' ou 'field' puis eventuellement 'get' 
	var $state=array();

	function actionParser($valeurs) {
		$this->valeurs= $valeurs;
		$this->parser = xml_parser_create($GLOBALS['meta']['charset']);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "text");
	}

	function parse($data) {
		xml_parse($this->parser, $data);
	}

	function getSql() {
		return $this->sql;
	}

	function startElement($parser, $name, $attrs) {
		echo "START $name ".var_export($attrs, 1)."\n";
		$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions':
			if($st==null) {
				echo "c'est parti\n";
				$this->state= array('actions');
			} else {
				die("noeud racine invalide");
			}
			break;

		case 'update':
		case 'insert':
			if($st!='actions') {
				die("update/insert inattendu");
			}
			array_unshift($this->state, $name);
			echo "$name ...\n";
			break;

		case 'key':
		case 'field':
			if($st!='insert' && $st!='update') {
				die("key/field inattendu");
			}
			array_unshift($this->state, $name);
			echo "$name ...\n";
			break;

		case 'get':
			if($st!='key' && $st!='field' && $st!='where') {
				die("get inattendu");
			}
			array_unshift($this->state, $name);
			echo "GET ...\n";
			break;

		case 'where':
			if($st!='get') {
				die("where inattendu");
			}
			array_unshift($this->state, $name);
			echo "WHERE ...\n";
			break;

		case 'callback':
			echo("plus tard ...");
			break;

		default:
			die("noeud $name inattendu");
		}
	}

	function endElement($parser, $name) {
		echo "END $name\n";
		$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions' :
			echo "c'est fini\n";
			$this->state=null;
			break;

		case 'update':
		case 'insert':
			if($st!=$name) {
				die("fin update/insert inattendu");
			}
			array_shift($this->state);
			break;

		case 'key':
		case 'field':
			if($st!=$name) {
				die("fin key/field inattendu");
			}
			array_shift($this->state);
			break;

		case 'get':
			if($st!=$name) {
				die("fin get inattendu");
			}
			array_shift($this->state);
			break;

		case 'where':
			if($st!=$name) {
				die("fin where inattendu");
			}
			array_shift($this->state);
			break;

		case 'callback':
			echo("plus tard ...");
			break;

		default:
			die("Là .. y'a comme un os ...");
		}
	}

	function text($parser, $text) {
		echo "TEXT $text\n";
	}
}

?>
