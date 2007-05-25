<?php

function balise_DEBUT_TEXTE_HEAD($p) {
	if(verifie_debut_fin_texte_head())
    $p->code = "'<!-- spip_debut_texte_head'.\$Pile[0]['cle_head'].'-->'";
  else {
    $p->code = "''";
  }
	return $p;
}

function balise_FIN_TEXTE_HEAD($p) {
  if(verifie_debut_fin_texte_head(true))
    $p->code = "'<!-- spip_fin_texte_head'.\$Pile[0]['cle_head'].'-->'";
  else {
    $p->code = "''";
  }    
	return $p;
}

function verifie_debut_fin_texte_head($fin = false) {
  static $debut = false;
  if(!$fin && !$debut) {
    $debut = true;
    return true;
  } 
  if($fin && $debut) {
    $debut = false;
    return true;
  }
  return false;   
}

function balise_INCLURE($p) {
	$champ = phraser_arguments_inclure($p, true);
	$_contexte = argumenter_inclure($champ, $p->descr, $p->boucles, $p->id_boucle, false);
  
	if (isset($_contexte['fond'])) {
		// Critere d'inclusion {env} (et {self} pour compatibilite ascendante)
		if (isset($_contexte['env'])
		|| isset($_contexte['self'])
		) {
			$flag_env = true;
			unset($_contexte['env']);
		}
		$l = 'array(' . join(",\n\t", $_contexte) .', "cle_head" => $Pile[0]["cle_head"])';
		if ($flag_env) {
			$l = "array_merge(\$Pile[0],$l)";
		}
		$p->code = "recuperer_fond('',".$l.",true, false)";
	} else {
		$n = interprete_argument_balise(1,$p);
		$p->code = '(($c = find_in_path(' . $n . ')) ? spip_file_get_contents($c) : "")';
	}

	$p->interdire_scripts = false; // la securite est assuree par recuperer_fond
	return $p;
}

function balise_MODELE($p) {
	$contexte = array();

	// recupere le premier argument, qui est obligatoirement le nom du modele
	if (!is_array($p->param))
		die("erreur de compilation #MODELE{nom du modele}");

	// Transforme l'ecriture du deuxieme param {truc=chose,machin=chouette} en
	// {truc=chose}{machin=chouette}... histoire de simplifier l'ecriture pour
	// le webmestre : #MODELE{emb}{autostart=true,truc=1,chose=chouette}
	if ($p->param[0]) {
		while (count($p->param[0])>2){
			$p->param[]=array(0=>NULL,1=>array_pop($p->param[0]));
		}
	}
	$modele = array_shift($p->param);
	$nom = strtolower($modele[1][0]->texte);
	if (!$nom)
		die("erreur de compilation #MODELE{nom du modele}");

	$champ = phraser_arguments_inclure($p, true); 

	// a priori true
	// si false, le compilo va bloquer sur des syntaxes avec un filtre sans argument qui suit la balise
	// si true, les arguments simples (sans truc=chose) vont degager
	$code_contexte = argumenter_inclure($champ, $p->descr, $p->boucles, $p->id_boucle, false);

	// Si le champ existe dans la pile, on le met dans le contexte
	// (a priori c'est du code mort ; il servait pour #LESAUTEURS dans
	// le cas spip_syndic_articles)
	#$code_contexte[] = "'$nom='.".champ_sql($nom, $p);

	// Reserver la cle primaire de la boucle courante
	if ($primary = $p->boucles[$p->id_boucle]->primary) {
		$id = champ_sql($primary, $p);
		$code_contexte[] = "'$primary='.".$id;
	}
  
  $code_contexte[] = '"cle_head" => $Pile[0]["cle_head"]';
  
	$p->code = "( ((\$recurs=(isset(\$Pile[0]['recurs'])?\$Pile[0]['recurs']:0))<5)?
	recuperer_fond('modeles/".$nom."',
		creer_contexte_de_modele(array(".join(',', $code_contexte).",'recurs='.(++\$recurs), \$GLOBALS['spip_lang']))):'')";
	$p->interdire_scripts = false; // securite assuree par le squelette

	return $p;
}


?>
