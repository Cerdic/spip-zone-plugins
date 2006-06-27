<?php

include_spip('base/abstract_sql');
include_spip('public/interfaces');
include_spip('base/serial');
include_spip('public/references');

class actionParser  {
	var $parser;

	// le insert/update en cours d'analyse
	var $currentAction= null;
	// le bloc where/set en cours
	var $currentBloc= null;

	// faut il ajouter le texte rencontre au le bloc courant
	var $appendText= false;

	// la liste des actions qu'on en deduit
	var $actions=array();
	// les valeurs de ref/id rencontres
	var $ids= array();

	// forcage d'une url de retour
	var $retour= null;

	// parametre supplementaires a mettre dans l'url de retour
	var $retourQs= array();

	// initialisation d'un parseur d'actions et des données de travail
	function actionParser() {
		$this->parser = xml_parser_create($GLOBALS['meta']['charset']);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

		xml_set_object($this->parser, $this);

		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser, "textElement");
	}

	// effectue le parsing et en déduit une structure "actions"
	function parse($data) {
		error_log("xml_parse");
		xml_parse($this->parser, $data);

		if(($c=xml_get_error_code($this->parser)) != XML_ERROR_NONE) {
			return xml_error_string($c);
		} else {
			return null;
		}
	}

	// récupère le tableau des actions a effectuer et en fait une série de
	// codes php correspondant
	function evaluate($actions) {
		$varCpt=0; // compteur pour les variables intermediaires
		$res= array();
		foreach($actions as $action) {
			if($action['type']=='insert') {

				if(count($action['set'])==0) { continue; }
				$r= '';
				if($action['id']) {
					$r.='$tmp_var_'.$action['id'].'= ';
				}
				$table= description_type_requete($action['table']);
				$table= $table['table'];
				$colonnes= array();
				$valeurs= array();
				foreach($action['set'] as $set) {
					$colonnes[]= $set['name'];
					$valeurs[]= "spip_abstract_quote(".$set['value'].")";
				}
				$colonnes= join(", ", $colonnes);
				$valeurs= join('.", ".', $valeurs);
				$r.= "spip_abstract_insert(\"$table\",\"($colonnes)\",\"(\".$valeurs.\")\")";
				$res[]=$r;

			} elseif($action['type']=='update') {

				if(count($action['set'])==0) { continue; }
				// pour eviter les catastrophes
				if(count($action['where'])==0) { continue; }
				$set= array();
				foreach($action['set'] as $s) {
					$set[]=$s['name']." = \".spip_abstract_quote(".$s['value'].").\"";
				}
				$where= array();
				foreach($action['where'] as $w) {
					$where[]=$w['name']." = \".spip_abstract_quote(".$w['value'].").\"";
				}
				$table= description_type_requete($action['table']);
				$table= $table['table'];
				$rq='UPDATE '.$table.' SET '.join(', ', $set)
					.' WHERE '.join(' AND ', $where);
				$res[]="spip_query(\"$rq\")";

			} elseif($action['type']=='delete') {

				// pour eviter les catastrophes
				if(count($action['where'])==0) { continue; }
				$where= array();
				foreach($action['where'] as $w) {
					$where[]=$w['name']." = \".spip_abstract_quote(".$w['value'].").\"";
				}
				$table= description_type_requete($action['table']);
				$table= $table['table'];
				$rq='DELETE FROM '.$table.' WHERE '.join(' AND ', $where);
				$res[]="spip_query(\"$rq\")";

			} else {
				die("action de type '".$action['type']."' inconnu");
			}
		}
		return $res;
	}

	function startElement($parser, $name, $attrs) {
		//error_log("START $name ".var_export($attrs, 1)."\n");
		//$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions':
			break;

		case 'insert':
		case 'update':
		case 'delete':
			$this->currentAction=array(
				'type' => $name,
				'table' => $attrs['type'],
				'where' => array(),  // que pour un update
				'set' => array(),    // ce qui sera à updater/insérer
			);
			if(array_key_exists('id', $attrs)) {
				$this->currentAction['id']= $attrs['id'];
			}
			break;

		case 'where':
		case 'set':
		case 'urlparam':
		case 'url':
			$this->currentBloc= array('type' => $name, 'name' => $attrs['name']);

			if(array_key_exists('value', $attrs)) {
				$this->currentBloc['value']= array("'".str_replace("'", "\'", $attrs['value'])."'");
			} elseif(array_key_exists('ref', $attrs)) {
				$this->currentBloc['value']= array('$tmp_var_'.$attrs['ref']);
			} else {
				$this->currentBloc['value']= array();
				$this->appendText= true;
			}
			break;
			
		case 'value':
			if(array_key_exists('value', $attrs)) {
				$this->currentBloc['value'][]= "'".str_replace("'", "\'", $attrs['value'])."'";
			} elseif(array_key_exists('ref', $attrs)) {
				$this->currentBloc['value'][]= '$tmp_var_'.$attrs['ref'];
			}
			break;

		default:
			die("noeud $name inattendu");
		}
	}

	function textElement($parser, $data) {
		if($this->appendText) {
			$this->currentBloc['value'][]= "'".str_replace("'", "\'", $data)."'";
		}
	}

	function endElement($parser, $name) {
		//echo "END $name\n";
		//$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions' :
			break;

		case 'insert':
		case 'update':
		case 'delete':
			$this->actions[]= $this->currentAction;
			$this->currentAction= null;
			$this->currentBloc= null;
			break;

		case 'set':
		case 'where':
			$this->currentBloc['value']= join('.', $this->currentBloc['value']);
			$this->currentAction[$name][]= $this->currentBloc;
			$this->currentBloc= null;
			break;

		case 'url':
			$this->retour= join('.', $this->currentBloc['value']);
			$this->currentBloc= null;
			break;

		case 'urlparam':
			$this->retourQs[$this->currentBloc['name']]=
				join('.', $this->currentBloc['value']);
			$this->currentBloc= null;
			break;

		case 'value':
			break;

		default:
			die("Là .. y'a comme un os ...");
		}
	}
}

class actionWorker {
	var $objects= array();
	var $input= array();
	var $current= null;

	function actionWorker($valeurs) {
		$input= $valeurs;
	}

	function addChange($ref, $name, $value) {
		$this->objects[$ref]->values[$name]= $value;
	}

	function addWhere($name, $value) {
		$this->current->where[$name]= $value;
	}
}

?>
