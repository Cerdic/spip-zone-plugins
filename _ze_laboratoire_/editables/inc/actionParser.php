<?php

include_spip('base/abstract_sql');
include_spip('public/interfaces');
include_spip('base/serial');
include_spip('public/references');

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
	var $liste= array();
	// les actions en cours d'interpretation
	var $actions= array();

	// si on trouve des erreurs en court de route (valeurs obligatoires
	// absentes principalement)
	var $errors= array();

	// compteur pour les variables temporaires generees
	var $tmpVars= 0;

	function actionParser($valeurs) {
		$this->valeurs= $valeurs;
		$this->parser = xml_parser_create($GLOBALS['meta']['charset']);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);

		xml_set_object($this->parser, $this);

		xml_set_element_handler($this->parser, "startElement", "endElement");
	}

	function parse($data) {
		//xml_parse_into_struct($this->parser, $data, $values, $indexes);
		//echo "VALUES=".var_export($values, 1)."\n";
		//echo "INDEXES=".var_export($indexes, 1)."\n";
		xml_parse($this->parser, $data);
	}

	function getCode($liste=null) {
		if(!$liste) {
			$l= $this->liste;
		} else {
			$l= $liste;
		}
		echo "<xmp>getSql:".var_export($l, 1)."</xmp>";
		$res='';
		foreach($l as $action) {
			if($action['type']=='insert') {
				foreach($action['field'] as $k => &$v) {
					$res.=$this->computeValue($v);
				}
				$table= description_type_requete($action['table']);
				$table= $table['table'];
				$colonnes= '('.join(", ", array_keys($action['field'])).')';
				$valeurs= '('.join(", ", array_values($action['field'])).')';
				$appel= "spip_abstract_insert(\"$table\",\"$colonnes\",\"$valeurs\")";
				if($id=$action['id']) {
					$res.="\n && (\$var_tmp_$id= $appel)";
				} else {
					$res.="\n && $appel";
				}
			} else {
				$set= array();
				foreach($action['field'] as $k => $v) {
					$res.=$this->computeValue($v);
					$set[]="$k = $v";
				}
				$where= array();
				foreach($action['where'] as $k => $v) {
					$res.=$this->computeValue($v);
					$where[]="$k = $v";
				}
				$table= description_type_requete($action['table']);
				$table= $table['table'];
				$rq='UPDATE '.$table.' SET '.join(', ', $set)
					.' WHERE '.join(' AND ', $where);
				$res.="\n && spip_query(\"$rq\")";
			}
			if($sa=$action['sousActions']) {
				$res.=$this->getCode($sa);
			}
		}
		if(!$liste) {
			return "return true$res;";
		} else {
			return $res;
		}
		
	}

	function startElement($parser, $name, $attrs) {
		echo "START $name ".var_export($attrs, 1)."\n";
		//$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions':
			//if($st==null) {
			//	echo "c'est parti\n";
			//	$this->state= array('actions');
			//} else {
			//	die("noeud racine invalide");
			//}
			break;

		case 'update':
		case 'insert':
			//if($st!='actions' && $st!='insert' && $st!='update') {
			//	die("update/insert inattendu");
			//}
			array_unshift($this->actions,
						  array('type' => $name, 'table' => $attrs['type'],
								'aFaire' => 'oui',
								'id' => $attrs['id']));

			// le flag "a faire" permet de savoir si l'action en cours doit
			// etre faite ou pas. s'il n'y a aucun valueFrom, l'action est faite
			// systematiquement. Par contre, s'il y en a, il faut qu'au moins
			// un d'entre eux soit renseigne. Enfin, si l'un deux a l'attribut
			// obligatoire="1" et qu'il n'est pas renseigne, l'action n'est
			// pas faite, meme si d'autre valueFrom sont renseignes

			// aFaire vaut 'oui' par defaut, passe a 'peutetre' si on
			// rencontre un valueFrom non renseigne et a 'non' si un
			// obligatoire='1' n'est pas rempli.
			// un 'peutetre' revient à 'oui' si on rencontre un valueFrom
			// renseigne
			// a la fin, seul un "oui" valide l'action.
			break;

		case 'where':
		case 'field':
			//if($st!='insert' && $st!='update' && $st!='select') {
			//	die("where/field inattendu");
			//}
			//array_unshift($this->state, $name);

			// si aFaire est a non, c'est pas la peine d'insister
			if($this->actions[0]['aFaire']=='non') {
				break;
			}

			$colonne= $attrs['name'];
			if($attrs['value']) {
				// attribut value => on prend la valeur sans chercher plus loin
				$this->actions[0][$name][$colonne]= $attrs['value'];
			} elseif($r=$attrs['ref']) {
				$this->actions[0][$name][$colonne]=
					array('type' => 'ref', 'ref' => $r);
			} elseif($vf=$attrs['valueFrom']) {
				// attribut valueFrom => on cherche si on a une telle
				// valeur dans le post, mais on teste également les conditions
				if($v=$this->valeurs[$vf]) {
					$this->actions[0][$name][$colonne]= $v;
					$this->actions[0]['aFaire']= 'oui';
				} else {
					if($attrs['obligatoire']) {
						$this->errors[]="pas de valeur pour $vf";
						$this->actions[0]['aFaire']='non';
					} elseif($this->actions[0]['aFaire']!='oui') {
						$this->actions[0]['aFaire']='peutetre';
					}
				}
			} else {
				// sinon on espere qu'un get va fournir une valeur
				$this->actions[0]['aTrouver']= array($name,$colonne);
			}
			break;

		case 'select':
			//if($st!='field' && $st!='where') {
			//	die("select inattendu");
			//}
			//array_unshift($this->state, $name);
			array_unshift($this->actions,
						  array('type' => 'select',
								'table' => $attrs['type'],
								'colonne' => $attrs['name']));
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
		//$st= ($this->state==null)?null:$this->state[0];
		switch($name) {
		case 'actions' :
			//echo "c'est fini\n";
			//$this->state=null;
			break;

		case 'update':
		case 'insert':
			//if($st!=$name) {
			//	die("fin update/insert inattendu");
			//}
			$a= array_shift($this->actions);
			echo "FERME ".var_export($a, 1);
			if($a['aFaire']=='oui' && $a['field']!=array()) {
				unset($a['aFaire']);
				if(count($this->actions)) {
					$this->actions[0]['sousActions'][]= $a;
				} else {
					$this->liste[]= $a;
				}
			}
			break;

		case 'field':
			//if($st!=$name) {
			//	die("fin key/field inattendu");
			//}
			//array_shift($this->state);
			break;

		case 'select':
			//if($st!=$name) {
			//	die("fin select inattendu");
			//}
			//array_shift($this->state);
			$a= array_shift($this->actions);
			if($a['aFaire']=='oui') {
				unset($a['aFaire']);
				list($type, $colonne)= $this->actions[0]['aTrouver'];
				$this->actions[0][$type][$colonne]= $a;
				unset($this->actions[0]['aTrouver']);
			}
			break;

		case 'where':
			//if($st!=$name) {
			//	die("fin where inattendu");
			//}
			//array_shift($this->state);
			break;

		case 'callback':
			echo("plus tard ...");
			break;

		default:
			die("Là .. y'a comme un os ...");
		}
	}

	function computeValue(&$value) {
		$res='';
		if(is_array($value)) {
			if($value['type']=='ref') {
				$value= "\".\$tmp_var_".$value['ref'].".\"";
			} elseif($value['type']=='select') {
				$where=array();
				foreach($value['where'] as $k => $v) {
					$res.=$this->computeValue($v);
					$where[]="$k = $v";
				}
				$where= join(' AND ', $where);
				$this->tmpVars++;
				$var= '$tmp_var'.$this->tmpVars;
				$res.="\n && (($var=spip_abstract_fetsel(\""
					.$value['colonne']."\", \"".$value['table']
					."\", \"$where\")) && ($var= ${var}['"
					.$value['colonne']."']))";
				$value= "\".$var.\"";
			}
		} else {
			$value= "'".addslashes($value)."'";
		}
		return $res;
	}
}

?>
