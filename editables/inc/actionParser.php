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

	// la liste d'action qui en resulte
	var $actions= array();
	// l'action en cours d'interpretation
	var $action;

	// si on trouve des erreurs en court de route (valeurs obligatoires
	// absentes principalement)
	var $errors= array();
	
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
		return "<xmp>".var_export($this->actions, 1)."</xmp>";
	}

	function startElement($parser, $name, $attrs) {
		//echo "START $name ".var_export($attrs, 1)."\n";
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
			// init d'une nouvelle action
			$this->action= array('type' => $name, 'aFaire' => false);
			break;

		case 'key':
		case 'field':
			if($st!='insert' && $st!='update') {
				die("key/field inattendu");
			}
			array_unshift($this->state, $name);
			$colonne= $attrs['name'];
			if($attrs['value']) {
				// attribut value => on prend la valeur sans chercher plus loin
				$this->action[$name][$colonne]= $attrs['value'];
			} elseif($vf=$attrs['valueFrom']) {
				// attribut valueFrom => on cherche si on a une telle
				// valeur dans le post, mais on teste également les conditions
				if($v=$this->valeurs[$vf]) {
					$this->action[$name][$colonne]= $v;
					$this->action['aFaire']= true;
				} elseif($attrs['cond']=='!') {
					$this->errors[]="pas de valeur pour $vf";
				}
			} else {
				// sinon on espere qu'un get va fournir une valeur
				$this->action['aTrouver']= $colonne;
			}
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
		//echo "END $name\n";
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
			if($this->action['aFaire']) {
				$this->actions[]= $this->action;
			}
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
		//echo "TEXT $text\n";
	}
}

?>
