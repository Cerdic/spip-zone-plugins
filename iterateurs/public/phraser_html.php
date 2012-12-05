<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) return;

include(_DIR_RESTREINT . 'public/phraser_html.php');

define('CHAMP_SQL_PLUS_FONC', '`?([A-Z_\/][A-Z_\/0-9.]*)' . SQL_ARGS . '?`?');

// analyse des criteres de boucle, 

// http://doc.spip.org/@phraser_criteres
function phraser_criteres_iterateurs($params, &$result) {

	$err_ci = ''; // indiquera s'il y a eu une erreur
	$args = array();
	$type = $result->type_requete;
	$doublons = array();
	foreach($params as $v) {
		$var = $v[1][0];
		$param = ($var->type != 'texte') ? "" : $var->texte;
		if ((count($v) > 2) && (!preg_match(",[^A-Za-z]IN[^A-Za-z],i",$param)))
		  {
// plus d'un argument et pas le critere IN:
// detecter comme on peut si c'est le critere implicite LIMIT debut, fin

			if ($var->type != 'texte'
			OR preg_match("/^(n|(n-)?\d+)$/S", $param)) {
			  $op = ',';
			  $not = "";
			} else {
			  // Le debut du premier argument est l'operateur
			  preg_match("/^([!]?)([a-zA-Z][a-zA-Z0-9]*)[[:space:]]*(.*)$/ms", $param, $m);
			  $op = $m[2];
			  $not = $m[1];
			  // virer le premier argument,
			  // et mettre son reliquat eventuel
			  // Recopier pour ne pas alterer le texte source
			  // utile au debusqueur
			  if ($m[3]) {
			    // une maniere tres sale de supprimer les "' autour de {critere "xxx","yyy"}
			    if (preg_match(',^(["\'])(.*)\1$,', $m[3])) {
			    	$c = null;
			    	eval ('$c = '.$m[3].';');
			    	if (isset($c))
			    		$m[3] = $c;
			    }
			    $texte = new Texte;
			    $texte->texte = $m[3]; 
			    $v[1][0]= $texte;
			  } else array_shift($v[1]);
			}
			array_shift($v); // $v[O] est vide
			$crit = new Critere;
			$crit->op = $op;
			$crit->not = $not;
			$crit->exclus ="";
			$crit->param = $v;
			$args[] = $crit;
		  } else {
		  if ($var->type != 'texte') {
		    // cas 1 seul arg ne commencant pas par du texte brut: 
		    // erreur ou critere infixe "/"
		    if (($v[1][1]->type != 'texte') || (trim($v[1][1]->texte) !='/')) {
			$err_ci = array('zbug_critere_inconnu', 
					array('critere' => $var->nom_champ));
			erreur_squelette($err_ci, $result);
		    } else {
		      $crit = new Critere;
		      $crit->op = '/';
		      $crit->not = "";
		      $crit->exclus ="";
		      $crit->param = array(array($v[1][0]),array($v[1][2]));
		      $args[] = $crit;
		    }
		  } else {
	// traiter qq lexemes particuliers pour faciliter la suite
	// les separateurs
			if ($var->apres)
				$result->separateur[] = $param;
			elseif (($param == 'tout') OR ($param == 'tous'))
				$result->modificateur['tout'] = true;
			elseif ($param == 'plat') 
				$result->modificateur['plat'] = true;

	// Boucle hierarchie, analyser le critere id_article - id_rubrique
	// - id_syndic, afin, dans les cas autres que {id_rubrique}, de
	// forcer {tout} pour avoir la rubrique mere...

			elseif (!strcasecmp($type, 'hierarchie') AND
				($param == 'id_article' OR $param == 'id_syndic'))
				$result->modificateur['tout'] = true;
			elseif (!strcasecmp($type, 'hierarchie') AND ($param == 'id_rubrique'))
				{;}
			else {
			  // pas d'emplacement statique, faut un dynamique
			  /// mais il y a 2 cas qui ont les 2 !
			  if (($param == 'unique') || (preg_match(',^!?doublons *,', $param)))
			    {
			      // cette variable sera inseree dans le code
			      // et son nom sert d'indicateur des maintenant
			      $result->doublons = '$doublons_index';
			      if ($param == 'unique') $param = 'doublons';
			    }
			  elseif ($param == 'recherche')
			    // meme chose (a cause de #nom_de_boucle:URL_*)
			      $result->hash = ' ';
			  if (preg_match(',^ *([0-9-]+) *(/) *(.+) *$,', $param, $m)) {
			    $crit = phraser_critere_infixe($m[1], $m[3],$v, '/', '', '');
			  } elseif (preg_match(',^([!]?)(' . CHAMP_SQL_PLUS_FONC . 
					 ')[[:space:]]*(\??)(!?)(<=?|>=?|==?|\b(?:IN|LIKE)\b)(.*)$,is', $param, $m)) {
			    $a2 = trim($m[8]);
			    if ($a2 AND ($a2[0]=="'" OR $a2[0]=='"') AND ($a2[0]==substr($a2,-1)))
			      $a2 = substr($a2,1,-1);
			    $crit = phraser_critere_infixe($m[2], $a2, $v,
							   (($m[2] == 'lang_select') ? $m[2] : $m[7]),
							   $m[6], $m[5]);
					$crit->exclus = $m[1];
			  } elseif (preg_match("/^([!]?)\s*(" .
					       CHAMP_SQL_PLUS_FONC .
					       ")\s*(\??)(.*)$/is", $param, $m)) {
		  // contient aussi les comparaisons implicites !
			    // Comme ci-dessus: 
			    // le premier arg contient l'operateur
			    array_shift($v);
			    if ($m[6]) {
			      $v[0][0] = new Texte;
			      $v[0][0]->texte = $m[6];
			    } else {
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
			 	$err_ci = array('zbug_critere_inconnu', 
					array('critere' => $param));
				erreur_squelette($err_ci, $result);
			  }
			  if ((!preg_match(',^!?doublons *,', $param)) || $crit->not)
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
	// Si erreur, laisser la chaine dans ce champ pour le HTTP 503
	if (!$err_ci) $result->criteres = $args;
}

function public_phraser_html($texte, $id_parent, &$boucles, $descr, $ligne=1) {

	$all_res = array();

	while (($pos_boucle = strpos($texte, BALISE_BOUCLE)) !== false) {

		$err_b = ''; // indiquera s'il y a eu une erreur
		$result = new Boucle;
		$result->id_parent = $id_parent;
		$result->descr = $descr;
# attention: reperer la premiere des 2 balises: pre_boucle ou boucle

		if (!preg_match(",".BALISE_PRE_BOUCLE . '[0-9_],', $texte, $r)
			OR ($n = strpos($texte, $r[0]))===false
			OR ($n > $pos_boucle) ) {
		  $debut = substr($texte, 0, $pos_boucle);
		  $milieu = substr($texte, $pos_boucle);
		  $k = strpos($milieu, '(');
		  $id_boucle = trim(substr($milieu,
					   strlen(BALISE_BOUCLE),
					   $k - strlen(BALISE_BOUCLE)));
		  $milieu = substr($milieu, $k);

		} else {
		  $debut = substr($texte, 0, $n);
		  $milieu = substr($texte, $n);
		  $k = strpos($milieu, '>');
		  $id_boucle = substr($milieu,
				       strlen(BALISE_PRE_BOUCLE),
				       $k - strlen(BALISE_PRE_BOUCLE));

		  if (!preg_match(",".BALISE_BOUCLE . $id_boucle . "[[:space:]]*\(,", $milieu, $r)) {
			$err_b = array('zbug_erreur_boucle_syntaxe', array('id' => $id_boucle));
			erreur_squelette($err_b, $result);
		  }
		  $pos_boucle = $n;
		  $n = strpos($milieu, $r[0]);
		  $result->avant = substr($milieu, $k+1, $n-$k-1);
		  $milieu = substr($milieu, $n+strlen($id_boucle)+strlen(BALISE_BOUCLE));
		}
		$result->id_boucle = $id_boucle;

		preg_match(SPEC_BOUCLE, $milieu, $match);
		$result->type_requete = $match[0];
                $milieu = substr($milieu, strlen($match[0]));
		$type = $match[1];
		$jointures = trim($match[2]);
		$table_optionnelle = ($match[3]);
		if ($jointures) {
			// on affecte pas ici les jointures explicites, mais dans la compilation
			// ou elles seront completees des jointures declarees
			$result->jointures_explicites = $jointures;
		}
		
		if ($table_optionnelle){
			$result->table_optionnelle = $type;
		}
		
		// 1ere passe sur les criteres, vu comme des arguments sans fct
		// Resultat mis dans result->param
		phraser_args($milieu,"/>","",$all_res,$result);

		// En 2e passe result->criteres contiendra un tableau
		// pour l'instant on met le source (chaine) :
		// si elle reste ici au final, c'est qu'elle contient une erreur
		$result->criteres =  substr($milieu,0,@strpos($milieu,$result->apres));
		$milieu = $result->apres;
		$result->apres = "";

		//
		// Recuperer la fin :
		//
		if ($milieu[0] === '/') {
			$suite = substr($milieu,2);
			$milieu = '';
		} else {
			$milieu = substr($milieu,1);
			$s = BALISE_FIN_BOUCLE . $id_boucle . ">";
			$p = strpos($milieu, $s);
			if ($p === false) {
				$err_b = array('zbug_erreur_boucle_fermant',
					array('id' => $id_boucle));
				erreur_squelette($err_b, $result);
			}

			$suite = substr($milieu, $p + strlen($s));
			$milieu = substr($milieu, 0, $p);
		}

		$result->milieu = $milieu;

		//
		// 1. Recuperer la partie conditionnelle apres
		//
		$s = BALISE_POST_BOUCLE . $id_boucle . ">";
		$p = strpos($suite, $s);
		if ($p !== false) {
			$result->apres = substr($suite, 0, $p);
			$suite = substr($suite, $p + strlen($s));
		}

		//
		// 2. Recuperer la partie alternative
		//
		$s = BALISE_ALT_BOUCLE . $id_boucle . ">";
		$p = strpos($suite, $s);
		if ($p !== false) {
			$result->altern = substr($suite, 0, $p);
			$suite = substr($suite, $p + strlen($s));
		}
		$result->ligne = $ligne + substr_count($debut, "\n");
		$m = substr_count($milieu, "\n");
		$b = substr_count($result->avant, "\n");
		$a = substr_count($result->apres, "\n");

		if ($p = strpos($type, ':')) {
			$result->sql_serveur = substr($type,0,$p);
			$type = substr($type,$p+1);
		}
		$soustype = strtolower($type);
		if ($soustype == 'sites') $soustype = 'syndication' ; # alias

		if (!isset($GLOBALS["table_des_tables"][$soustype]))
			$soustype = $type;

		$result->type_requete = $soustype;
		// Lancer la 2e passe sur les criteres si la 1ere etait bonne
		if (!is_array($result->param))
			$err_b = true;
		else {
			phraser_criteres_iterateurs($result->param, $result);
			if (strncasecmp($soustype, TYPE_RECURSIF, strlen(TYPE_RECURSIF)) == 0) {
				$result->type_requete = TYPE_RECURSIF;
				$args = $result->param;
				array_unshift($args,
					substr($type, strlen(TYPE_RECURSIF)));
				$result->param = $args;
			}
		}

		$result->avant = public_phraser_html_dist($result->avant, $id_parent,$boucles, $descr, $result->ligne);
		$result->apres = public_phraser_html_dist($result->apres, $id_parent,$boucles, $descr, $result->ligne+$b+$m);
		$result->altern = public_phraser_html_dist($result->altern,$id_parent,$boucles, $descr, $result->ligne+$a+$m+$b);
		$result->milieu = public_phraser_html_dist($milieu, $id_boucle,$boucles, $descr, $result->ligne+$b);

		// Prevenir le generateur de code que le squelette est faux
		if ($err_b) $result->type_requete = false;

		// Verifier qu'il n'y a pas double definition
		// apres analyse des sous-parties (pas avant).
		
		if (isset($boucles[$id_boucle])) {
			$err_b_d = array('zbug_erreur_boucle_double',
				 	array('id'=>$id_boucle));
			erreur_squelette($err_b_d, $result);
		// Prevenir le generateur de code que le squelette est faux
			$boucles[$id_boucle]->type_requete = false;
		} else
			$boucles[$id_boucle] = $result;
		$all_res = phraser_champs_etendus($debut, $ligne, $all_res);
		$all_res[] = &$boucles[$id_boucle];
		$ligne += substr_count(substr($texte, 0, strpos($texte, $suite)), "\n");
		$texte = $suite;
	}

	return phraser_champs_etendus($texte, $ligne, $all_res);
}
?>
