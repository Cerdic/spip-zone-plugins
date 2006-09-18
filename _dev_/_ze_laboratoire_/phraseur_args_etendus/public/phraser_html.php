<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

define('BALISE_BOUCLE', '<BOUCLE');
define('BALISE_FIN_BOUCLE', '</BOUCLE');
define('BALISE_PRE_BOUCLE', '<B');
define('BALISE_POST_BOUCLE', '</B');
define('BALISE_ALT_BOUCLE', '<//B');

define('TYPE_RECURSIF', 'boucle');
define('SPEC_BOUCLE','\s*\(\s*([^\s)]+)(\s*[^)]*)\)');
define('NOM_DE_BOUCLE', "[-_][-_.a-zA-Z0-9]*+|[0-9]++");

define('NOM_DE_CHAMP', "#(?:(" . NOM_DE_BOUCLE . "):)?+((?:[A-F]*[G-Z_][A-Z_0-9]*+)|[A-Z_]++)(\*{0,2}+)");
define('CHAMP_ETENDU', '\[([^][]*)\(\s*+%##(\d++)@\s*+\)([^][]*)\]');

define('BALISE_INCLURE','<INCLU[DR]E[[:space:]]*(\(([^)]*)\))?((?:\{[^{}]+\})+)?\s*'.'>');

define('SQL_ARGS', '(\([^)]*\))');
define('CHAMP_SQL_PLUS_FONC', '`?([A-Za-z_][A-Za-z_0-9]*)' . SQL_ARGS . '?`?');

function public_phraser_html($texte, $id_parent, &$boucles, $nom, $ligne=1) {
	$res = array();
	analyse($texte,$res,$nom);
	//var_dump($texte,$res);
	//var_dump(make_obj($texte,$res,$boucles));
	return make_obj($texte,$res,$boucles);
}

function analyse(&$text,&$res,$nom) {
	match_fields($text,$res);
	match_idiomes($text,$res);
	match_simple_fields($text,$res);
	match_inclure($text,$res);
	match_ext_field($text,$res);
	match_loops($text,$res,$nom,'');
}

function match_ext_field(&$text,&$res) {
	while(preg_match("!".CHAMP_ETENDU."!sS",$text,$reg)) {
	//1: avant
	//2: token champ
	//3: args,filtres
	//4: apres
		$i = count($res);
		$pos = strpos($text,$reg[0]);
		$text = substr_replace($text,"%##".$reg[2]."@",$pos,strlen($reg[0]));
		$field = &$res[$reg[2]][1]; 
		$field->avant = $reg[1];
		$field->apres = $reg[3];
	}
	$before = $text;
	if(strpos($text,"%##")!==false && $before!=match_simple_fields($text,$res)) match_ext_field($text,$res);
	else return $text;
}

function match_loops(&$text,&$res,$nom,$id_parent) {
	while(preg_match("!".BALISE_BOUCLE."(".NOM_DE_BOUCLE.")!S",$text,$reg)) {
			$i = count($res);
			$name = $reg[1];
			if(preg_match(
			"!(?:".BALISE_PRE_BOUCLE.$name."\s*+>(.*?))?".BALISE_BOUCLE.$name.SPEC_BOUCLE."((?:\s*+\{.*?\}\s*+)*)>(.*?)".
			BALISE_FIN_BOUCLE.$name."\s*+>(?:(.*?)".BALISE_POST_BOUCLE.$name."\s*+>)?(?:(.*?)".BALISE_ALT_BOUCLE.$name."\s*+>)?!sS",$text,$reg)) {
			//1 : avant
			//2 : type
			//3 : joint
			//4 : criteri
			//5 : body
			//6 : apres
			//7 : altern 			 
				$res[] = ''; //just to put an element
				$text = str_replace($reg[0],"%##".$i."@",$text);
				$field = new Boucle;
				$field->id_parent = $id_parent;
				$field->id_boucle = $name;
				//jointures
				$jointures = trim($reg[3]);
				if ($jointures) {
					$field->jointures = preg_split("/\s+/",$jointures);
					$field->jointures_explicites = $jointures;
				}
				$type = $reg[2];
				if ($p = strpos($type, ':'))
				  {
				    $field->sql_serveur = substr($type,0,$p);
				    $soustype = strtolower(substr($type,$p+1));
				  }
				else
				  $soustype = strtolower($type);
		
				if ($soustype == 'sites') $soustype = 'syndication' ; # alias
				//
				// analyser les criteres et distinguer la boucle recursive
				//
				if (substr($soustype, 0, 6) == TYPE_RECURSIF) {
					$field->type_requete = TYPE_RECURSIF;
					$field->param[0] = substr($type, strlen(TYPE_RECURSIF));
				} else {
					$field->type_requete = $soustype;
				}
				
				// envoyer la boucle au debugueur
				if ($GLOBALS['var_mode']== 'debug') {
				  boucle_debug ($nom, $id_parent, $name, 
						$type . $jointures,
						$reg[4],
						$reg[1],
						$reg[5],
						$reg[6],
						$reg[7]);
				}

				//pre
				if($reg[1]) {
					$pos = strpos($reg[0],$reg[1]);
					$len = strlen($reg[1]);
					$reg[0] = substr_replace(
						$reg[0],
						$field->avant = match_simple_fields(match_loops($reg[1],$res,$nom,$id_parent),$res),
						$pos,$len
					);
				}
				//criterion
				if(isset($reg[4]) && $reg[4]) {
					$field->param = match_criteres($reg[4],$res);
				}
				//body
				if(isset($reg[5]) && $reg[5]) {
					$pos = strpos($reg[0],$reg[5]);
					$len = strlen($reg[5]);
					$reg[0] = substr_replace(
						$reg[0],
						$field->milieu = match_simple_fields(match_loops($reg[5],$res,$nom,$name),$res),
						$pos,$len
					);					
				}
				//post
				if(isset($reg[6]) && $reg[6]) {
					$pos = strpos($reg[0],$reg[6]);
					$len = strlen($reg[6]);
					$reg[0] = substr_replace(
						$reg[0],
						$field->apres = match_simple_fields(match_loops($reg[6],$res,$nom,$id_parent),$res),
						$pos,$len
					);					
				}
				//cond
				if(isset($reg[7]) && $reg[7]) {
					$pos = strpos($reg[0],$reg[7]);
					$len = strlen($reg[7]);
					$reg[0] = substr_replace(
						$reg[0],
						$field->altern = match_simple_fields(match_loops($reg[7],$res,$nom,$id_parent),$res),
						$pos,$len
					);					
				}
				$boucles[] = $i;
				$res[$i] = array($reg[0],$field);
			} else {
				echo "errore ciclo $name<br>";
				return;
			}
	}
	//If I match ext field now I would be able to have boucle nested in ext field
	//match_simple_fields($text,$res);
	return $text;
}

function match_fields(&$text,&$res) {
		$i = count($res);
		$orig = $text;
		while(preg_match("@".NOM_DE_CHAMP."@sS",$text,$reg)) {
			//replace #TAG{.....} with a token in $text
			$text = substr_replace($text,"%##".$i++."@",strpos($text,$reg[0]),strlen($reg[0]));
			//var_dump($text);
			$field = new Champ;
			$field->nom_boucle = &$reg[1];
			$field->nom_champ = &$reg[2];
			$field->etoile = &$reg[3];
			$res[] = array($reg[0],$field);
	}
}

function match_simple_fields(&$text,&$res) {
	while(preg_match("@%##(\d++)\@\{([^][{}]++)\}@s",$text,$reg)) {
		//%##0@{abc,%##1@{abc}|filter}
		//become
		//%##0@{abc,%##1@|filter}
		//where %##1@ has abc in param
		$text = substr_replace($text,"%##".$reg[1]."@",strpos($text,$reg[0]),strlen($reg[0]));
		if(strpos($reg[2],"%##")!==false) 
			$res[$reg[1]][1]->param[] = &match_filters($reg[2],$res);
		else
			$res[$reg[1]][1]->param[] =  &$reg[2];
	}
	$before = $text;
	if(strpos($text,"%##")!==false && $before!=match_filters($text,$res)) match_simple_fields($text,$res);
	else return $text;
}

function match_filters(&$text,&$res) {
	//match filters in tags enclosed by ()  - filters do not contain any ()[]
	//%##\d++@ then a number of |text{...} or |text} or |text) or |text
	while(preg_match("@%##(\d++)\@((?:\s*+\|{1,2}+[^][(){}]++(?:(?:\{[^][{}]*+\})|(?=[})]|$)))++)@s",$text,$reg)) {
		//1: num token
		//2: filters
		$res[$reg[1]][1]->fonctions = trim($reg[2]);
		//do not add a new resource; just store the fonctions in the field
		//this will delete the filter from the parent token
		$text = substr_replace($text,"%##".$reg[1]."@",strpos($text,$reg[0]),strlen($reg[0]));
	}
	return $text;
}

function match_criteres(&$text,&$res) {
	$i = count($res);
	//match criteres in {}
	$crit = array();
	while(preg_match("@\{([^{}]++)\}@s",$text,$reg)) {
		$pos = strpos($text,$reg[0]);
		$text = substr_replace($text,"",$pos,strlen($reg[0]));
		$crit[] = $reg[1];
	}
	return $crit;
}

function match_inclure(&$text, &$res) {
	//<INCLU[DR]E[[:space:]]*(\(([^)]*)\))?((?:\{[^{}]\})+)?
	$i = count($res);
	while (preg_match("@".BALISE_INCLURE."@sS", $text, $reg)) {
	//1: argument with ()
	//2: argument without ()
	//3: sequence of {}
		$pos = strpos($text,$reg[0]);
		$text = substr_replace($text,"%##".$i++."@",$pos,strlen($reg[0]));
		$field = new Inclure;
		$field->texte = $reg[2];
		while($reg[3][0]=="{") {
			$pos = strpos($reg[3],"}");
			$param = substr($reg[3],1,$pos-1);
			$param = explode("=",$param);
			$field->param[] = $param;
			$reg[3] = substr($reg[3],$pos);
		}
		$res[] = array($reg[0],$field);
	}
	return $text;
}

function match_idiomes(&$text,&$res) {
	$i = count($res);
	// Reperer les balises de traduction <:toto:>
	while (preg_match("@<:(([a-z0-9_]+):)?([a-z0-9_]+)((\|[^:>]*)?:>)@iS", $text, $reg)) {
		$pos = strpos($text, $reg[0]);
		$text = substr_replace($text, "%##".$i++."@", $pos,strlen($reg[0]));
		$field = new Idiome;
		$field->nom_champ = strtolower($reg[3]);
		$field->module = $reg[2] ? $reg[2] : 'public/spip/ecrire';
		$res[] = array($reg[0],$field);
	}
	return $text;
}

function make_obj(&$text,&$res,&$boucles) {
	$all_res = array();
	if(strlen($text)==0) {
		$field = new Texte;
		$field->texte = '';
		return array($field);		
	}
	while(strlen($text)!=0) {
		if(strpos($text,"%##")!==false && preg_match("!%##(\d++)@!S",$text,$reg)) {
				$pos = strpos($text,$reg[0]);
				
				if($pos==0) {
					//token
					process_field($reg[1],$res,$all_res,$boucles);
					$text = substr($text,strlen($reg[0]));
				} else {			
					//text
					$field = new Texte;
					$field->texte = substr($text,0,$pos);
					$all_res[] = $field;
					$text = substr($text,$pos);
				}
		} else {
			//text
			$field = new Texte;
			$field->texte = $text;
			$all_res[] = $field;
			break;
		}
	} 
	return $all_res;
}

function process_field($index,&$res,&$all_res,&$boucles) {
	$lex = &$res[$index][1];
	switch ($lex->type) {
		case "champ":
			$field = $lex;
			$num = count($field->param);
			for($i=0;$i<$num;$i++) {
				$param = &$field->param[$i];
				$param = make_param($param,$res,$field,$boucles);
			}
			if($avant = &$field->avant) $field->avant = &make_obj($avant,$res,$boucles);
			if($apres = &$field->apres) $field->apres = &make_obj($apres,$res,$boucles);;
			if($field->fonctions) {
				make_filter($field->fonctions,$res,$field,$boucles);
			}
			$all_res[] = &$field;
			break;
		case "boucle" :
			$field = $lex;
			$boucles[$field->id_boucle] = &$field;
			if($field->type_requete != TYPE_RECURSIF) {
				make_criteres($field->param,$res,$field,$boucles);
			}
			$field->avant = &make_obj($field->avant,$res,$boucles);
			$field->milieu = &make_obj($field->milieu,$res,$boucles);
			$field->apres = &make_obj($field->apres,$res,$boucles);
			$field->altern = &make_obj($field->altern,$res,$boucles);
			$all_res[] = &$field;
			break;
		//}
		case "include" :
			$field = $lex;
			$num = count($field->param);
			//each argument in param as an array
			//[0] is the var
			//[1] is the val,
			for($i=0;$i<$num;$i++) {
				$param = &$field->param[$i];
				$var_name = array_shift($param); 
				$param = make_param(array_shift($param),$res,$field,$boucles,$var_name);
			}
			$all_res[] = &$field;
			break;
		case "idiome" :
		case "texte" :
			$all_res[] = $lex;
	}
}

function make_criteres($args,&$res,&$field,&$boucles) {
	$criteres = array();
	foreach($args as $arg) {
		$c = array('');
		if(strpos($arg,",")!==false) {
			$params = preg_split("@(?<!\")(?:\s*+,\s*+)(?!\")@",$arg);
		} else $params = array($arg);
		$i = count($res);
		foreach($params as $param) {
			if(strpos($param,"%##")===false) {
				$temp = new Texte;
				//to manage the separator
				if (preg_match(",^([\"'])([^\\1]*)\\1$,",$param,$reg)) {
					$temp->texte = $reg[2];
					$temp->apres = $reg[1];
				} else
					$temp->texte = $param;
				$res[] = array($param,$temp);
				$c[] = make_obj($temp="%##".$i++."@",$res,$boucles);
			} else {
				$param = preg_replace("!\((%##\d++@)\)!","$1",$param);
				$c[] = make_obj($param,$res,$boucles);
			}				 
		}			
		$criteres[] = $c;
	}
	phraser_criteres($criteres,$field);
}

function make_param($args,$res,&$field,&$boucles,$filter='') {
	if(!$filter)	$arg = array('');
	else $arg = array($filter);
	//filter without arguments
	if($filter && strlen($args)==0) return $arg;
	$lexs = explode(',',$args);
	foreach($lexs as $lex) {
		//delete wrapping ' or "
		$lex = preg_replace(",^([\"'])([^\\1]*)\\1$,","$2",trim($lex));
		if($pos = strpos($lex,"%##")===false) { 
			$arg[] = &make_obj($lex,$res,$boucles);
		} else {
			$lex = preg_replace("!\((%##\d++@)\)!","$1",$lex);
			$arg[] = &make_obj($lex,$res,$boucles);
		} 
	}
	return $arg;
}

function make_filter($args,$res,&$field,&$boucles) {
	$filters = explode('|',$args);
	foreach($filters as $filter) {
		//verify false filters, that is token instead of filter
		if(strlen($filter)!=0 && strpos($filter,"%##")!==0) {
			$pos = strpos($filter,"{");
			if($pos===false) {
				$name = $filter;
				$filter_args = '';
			} else {
				$name = substr($filter,0,$pos);
				$filter_args = substr($filter,$pos+1,strlen($filter)-$pos-2);
			}
			$field->param[] = &make_param($filter_args,$res,$field,$boucles,$name);
		}
	}
}

function phraser_criteres($params, &$result) {

	$args = array();
	$type = $result->type_requete;
	$doublons = array();
	foreach($params as $v) {
		$var = $v[1][0];
		$param = ($var->type != 'texte') ? "" : $var->texte;
		if ((count($v) > 2) && (!eregi("[^A-Za-z]IN[^A-Za-z]",$param)))
		  {
// plus d'un argument et pas le critere IN:
// detecter comme on peut si c'est le critere implicite LIMIT debut, fin

			if (($var->type != 'texte') ||
			    (strpos("0123456789-", $param[strlen($param)-1])
			     !== false)) {
			  $op = ',';
			  $not = "";
			} else {
			  preg_match("/^([!]?)([a-zA-Z][a-zA-Z0-9]*)[[:space:]]*(.*)$/ms", $param, $m);
			  $op = $m[2];
			  $not = $m[1];
			  if ($m[3]) $v[1][0]->texte = $m[3]; else array_shift($v[1]);
			}
			array_shift($v);
			$crit = new Critere;
			$crit->op = $op;
			$crit->not = $not;
			$crit->param = $v;
			$args[] = $crit;
		  } else {
		  if ($var->type != 'texte') {
		    // cas 1 seul arg ne commencant pas par du texte brut: 
		    // erreur ou critere infixe "/"
		    if (($v[1][1]->type != 'texte') || (trim($v[1][1]->texte) !='/')) {
					erreur_squelette('criteres',$var->nom_champ);
		    } else {
		      $crit = new Critere;
		      $crit->op = '/';
		      $crit->not = "";
		      $crit->param = array(array($v[1][0]),array($v[1][2]));
		      $args[] = $crit;
		    }
		  } else {
	// traiter qq lexemes particuliers pour faciliter la suite
	// les separateurs
			if ($var->apres)
				$result->separateur[] = $param;
			elseif (($param == 'tout') OR ($param == 'tous'))
				$result->tout = true;
			elseif ($param == 'plat') 
				$result->plat = true;

	// Boucle hierarchie, analyser le critere id_article - id_rubrique
	// - id_syndic, afin, dans les cas autres que {id_rubrique}, de
	// forcer {tout} pour avoir la rubrique mere...

			elseif (($type == 'hierarchie') &&
				($param == 'id_article' OR $param == 'id_syndic'))
				$result->tout = true;
			elseif (($type == 'hierarchie') && ($param == 'id_rubrique'))
				{;}
			else { 
			  // pas d'emplacement statique, faut un dynamique
			  /// mais il y a 2 cas qui ont les 2 !
			  if (($param == 'unique') || (ereg('^!?doublons *', $param)))
			    {
			      // cette variable sera inseree dans le code
			      // et son nom sert d'indicateur des maintenant
			      $result->doublons = '$doublons_index';
			      if ($param == 'unique') $param = 'doublons';
			    }
			  elseif ($param == 'recherche')
			    // meme chose (a cause de #nom_de_boucle:URL_*)
			      $result->hash = true;
			  if (ereg('^ *([0-9-]+) *(/) *(.+) *$', $param, $m)) {
			    $crit = phraser_critere_infixe($m[1], $m[3],$v, '/', '', '');
			  } elseif (ereg('^(' . CHAMP_SQL_PLUS_FONC . 
					 ')[[:space:]]*(\??)(!?)(<=?|>=?|==?|IN)(.*)$', $param, $m)) {
			    $a2 = trim($m[7]);
			    if (ereg("^'.*'$", $a2) OR ereg('^".*"$', $a2))
			      $a2 = substr($a2,1,-1);
			    $crit = phraser_critere_infixe($m[1], $a2, $v,
							   (($m[1] == 'lang_select') ? $m[1] : $m[6]),
							   $m[5], $m[4]);
			  } elseif (preg_match("/^([!]?)\s*(" .
					       CHAMP_SQL_PLUS_FONC .
					       ")\s*(\??)(.*)$/ism", $param, $m)) {
		  // contient aussi les comparaisons implicites !

			    array_shift($v);
			    if ($m[6])
			      $v[0][0]->texte = $m[6];
			    else {
			      array_shift($v[0]);
			      if (!$v[0]) array_shift($v);
			    }
			    $crit = new Critere;
			    $crit->op = $m[2];
			    $crit->param = $v;
			    $crit->not = $m[1];
			    $crit->cond = $m[5];
			  }
			  else {
			    erreur_squelette(_T('zbug_critere_inconnu',
						array('critere' => $param)));
			  }
			  if ((!ereg('^!?doublons *', $param)) || $crit->not)
			    $args[] = $crit;
			  else 
			    $doublons[] = $crit;
			}
		  }
		}
	}
	// les doublons non nies doivent etre le dernier critere
	// pour que la variable $doublon_index ait la bonne valeur
	// cf critere_doublon
	if ($doublons) $args= array_merge($args, $doublons);
	$result->criteres = $args;
}

// http://doc.spip.org/@phraser_critere_infixe
function phraser_critere_infixe($arg1, $arg2, $args, $op, $not, $cond)
{
	$args[0] = new Texte;
	$args[0]->texte = $arg1;
	$args[0] = array($args[0]);
	$args[1][0]->texte = $arg2;
	$crit = new Critere;
	$crit->op = $op;
	$crit->not = $not;
	$crit->cond = $cond;
	$crit->param = $args;
	return $crit;
}

// http://doc.spip.org/@phraser_arguments_inclure
function phraser_arguments_inclure($p,$rejet_filtres = false){
	$champ = new Inclure;
	// on assimile {var=val} a une liste de un argument sans fonction
	foreach ($p->param as $k => $v) {
		$var = $v[1][0];
		if ($var==NULL){
			if ($rejet_filtres)
				break; // on est arrive sur un filtre sans argument qui suit la balise
			else
				$champ->param[$k] = $v;
		}
		else {
			if ($var->type != 'texte')
				erreur_squelette(_T('zbug_parametres_inclus_incorrects'),
					 $match[0]);
			else {
				$champ->param[$k] = $v;
				ereg("^([^=]*)(=)?(.*)$", $var->texte,$m);
				if ($m[2]) {
					$champ->param[$k][0] = $m[1];
					$val = $m[3];
					if (ereg('^[\'"](.*)[\'"]$', $val, $m)) $val = $m[1];
					$champ->param[$k][1][0]->texte = $val;
				}
				else
					$champ->param[$k] = array($m[1]);
			}
		}
	}
	return $champ;
}

?>
