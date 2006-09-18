<?php
/*
 * Boucle xml
 * 
 *
 * Auteur :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('base/xml_temporaire');
function boucle_XML_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_xml";
	$boucle->select[] =  $boucle->id_table.".xpath";
	
	// on regarde dans les where si xml est specifie explicitement
	$_xml = '';
	foreach($boucle->where as $w){
		if ($w[0]=="'='" && $w[1]=="'xml.xml'")
		{
			$_xml=$w[2];
			break;
		}
	}
	if ($_xml==''){
		$champ = new Champ;
		$champ->nom_champ = 'xml';
		$_xml = calculer_liste(array($champ),array(), $boucles, $boucle->$id_boucle);
	}
	if ($_xml!='')
	$boucle->hash = "
	// CREER la table temporaire xml et la peupler avec le resultat du parser
	if (is_string(\$x=$_xml))
		xml_fill_table_temporaire_boucle(\$x);
";
	return calculer_boucle($id_boucle, $boucles); 
}

function extraire($attributs,$nom){
	return extraire_attribut("<fake $attributs>",$nom);
}
/*
function critere_attribut($idb, &$boucles, $crit) {
	global $table_des_tables, $tables_des_serveurs_sql,  $exceptions_des_jointures;
	$boucle = &$boucles[$idb];
	//if ($crit->not) $sens = $sens ? "" : " . ' DESC'";

	foreach ($crit->param as $tri) {

	  $fct = ""; // en cas de fonction SQL
	// tris specifies dynamiquement
	  if ($tri[0]->type != 'texte') {
	      $order = 
		calculer_liste($tri, array(), $boucles, $boucles[$idb]->id_parent);
				$r = $boucle->type_requete;
				$s = $boucles[$idb]->sql_serveur;
				if (!$s) $s = 'localhost';
				$t = $table_des_tables[$r];
				// pour les tables non Spip
				if (!$t) $t = $r; else $t = "spip_$t";
				$desc = $tables_des_serveurs_sql[$s][$t];
				if (is_array($desc['field'])){
					$liste_field = implode(',',array_map('spip_abstract_quote',array_keys($desc['field'])));
		      $order =
			"((\$x = preg_replace(\"/\\W/\",'',$order)) ? ( in_array(\$x,array($liste_field))  ? ('$boucle->id_table.' . \$x$sens):(\$x$sens) ) : '')";
				}
				else{
		      $order =
			"((\$x = preg_replace(\"/\\W/\",'',$order)) ? ('$boucle->id_table.' . \$x$sens) : '')";
				}
	  } else {
	      $par = array_shift($tri);
	      $par = $par->texte;
    // par multi champ
	      if (ereg("^multi[[:space:]]*(.*)$",$par, $m)) {
		  $texte = $boucle->id_table . '.' . trim($m[1]);
		  $boucle->select[] =  " \".creer_objet_multi('".$texte."', \$GLOBALS['spip_lang']).\"" ;
		  $order = "multi";
	// par num champ(, suite)
	      }	else if (ereg("^num[[:space:]]*(.*)$",$par, $m)) {
		  $texte = '0+' . $boucle->id_table . '.' . trim($m[1]);
		  $suite = calculer_liste($tri, array(), $boucles, $boucle->id_parent);
		  if ($suite !== "''")
		    $texte = "\" . ((\$x = $suite) ? ('$texte' . \$x) : '0')" . " . \"";
		  $as = 'num' .($boucle->order ? count($boucle->order) : "");
		  $boucle->select[] = $texte . " AS $as";
		  $order = "'$as'";
	      } else {
	      if (!ereg("^" . CHAMP_SQL_PLUS_FONC . '$', $par, $match)) 
		erreur_squelette(_T('zbug_info_erreur_squelette'), "{par $par} BOUCLE$idb");
	      else {
		if ($match[2]) { $par = substr($match[2],1,-1); $fct = $match[1]; }
	// par hasard
		if ($par == 'hasard') {
		// tester si cette version de MySQL accepte la commande RAND()
		// sinon faire un gloubi-boulga maison avec de la mayonnaise.
		  if (spip_abstract_select(array("RAND()")))
			$par = "RAND()";
		  else
			$par = "MOD(".$boucle->id_table.'.'.$boucle->primary
			  ." * UNIX_TIMESTAMP(),32767) & UNIX_TIMESTAMP()";
		  $boucle->select[]= $par . " AS alea";
		  $order = "'alea'";
		}
	// par date_thread
	// (date la plus recente d'un message dans un fil de discussion)
		else if ($par == 'date_thread') {
			if ($boucle->type_requete == 'forums') {
			  $t = 'forum';
			} else {
			  $t = critere_par_jointure($boucle, array('spip_forum','id_thread'));
			  $t = substr($t, 1, strpos($t,'.')-1);
			}
			$boucle->select[] = "MAX($t" . ".".
				$GLOBALS['table_date']['forums']
				.") AS date_thread";
			$boucle->group[] = $t . ".id_thread";
			$order = "'date_thread'";
			$boucle->plat = true;
		}
	// par titre_mot ou type_mot voire d'autres
		else if (isset($exceptions_des_jointures[$par])) {
			$order = critere_par_jointure($boucle, $exceptions_des_jointures[$par]);
			 }
		else if ($par == 'date'
		AND isset($GLOBALS['table_date'][$boucle->type_requete])) {
			$m = $GLOBALS['table_date'][$boucle->type_requete];
			$order = "'".$boucle->id_table ."." . $m . "'";
		}
		// par champ. Verifier qu'ils sont presents.
		else {
		  $r = $boucle->type_requete;
		  $s = $boucles[$idb]->sql_serveur;
		  if (!$s) $s = 'localhost';
		  $t = $table_des_tables[$r];
		  // pour les tables non Spip
		  if (!$t) $t = $r; else $t = "spip_$t";
		  $desc = $tables_des_serveurs_sql[$s][$t];
		  if ($desc['field'][$par])
		    $par = $boucle->id_table.".".$par;
		  // sinon tant pis, ca doit etre un champ synthetise (cf points)
		  $order = "'$par'";
		}
	      }
	      }
	  }
	  if ($order)
	    $boucle->order[] = ($fct ? "'$fct(' . $order . ')'" : $order) .
	      (($order[0]=="'") ? $sens : "");
	}
}*/
?>