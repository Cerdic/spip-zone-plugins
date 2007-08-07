<?php

function critere_frequence_dist($idb, &$boucles, $crit) {
	$not = $crit->not;

	if ($not)
		erreur_squelette(_T('zbug_info_erreur_squelette'), $crit->op);

	//analyser chaque criteres de frequence
	$boucle = &$boucles[$idb];
	$nom = $boucle->type_requete;
	$parent = $boucles[$idb]->id_parent;
	$criteres = array();
	while(list(,$p) = each($crit->param)) {
 		$param = calculer_liste($p, array(), $boucles, $parent);
		$type = preg_match(',^\(?\'(\w+)(\s*)?([!=<>]+)?(\s*)?,', $param, $regs) ? $regs[1] : 'articles';
		$op = $regs[3] ? $regs[3] : '>=';
		if($val = $regs[0] ? preg_replace(',' . preg_quote($regs[0]) . ',', '', $param) : 0) {
			$val = preg_replace(',\'$,', '', $val);
			$val = preg_replace(',^\'\s\.\s(.*)\)$,Um', '$1', $val);
		}
		//Trouver une jointure n:n (cad table spip_mots_articles)
		if(in_array($type = $nom.'_'.$type, $boucle->jointures))
			$criteres[] = array($op, $type, $val);
	}
	if(empty($criteres)) $criteres[0] = array('>=', 'articles', 0);
	
	//composer la requete pour chaque jointure
//	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$cpt = 0;
	foreach($criteres as $critere) {
		$frequence = 'frequence'.(++$cpt);
		$boucle->select[]= 'COUNT('.$frequence.'.'.$id.') AS '.$frequence;
		$boucle->from[$frequence] = "spip_'.$type_requete.'_".$type."s";
		$boucle->where[] = array("'='", "'".$id_table."'", "'freq.".$boucle->primary."'");
		$boucle->group[] = $id_table;
		$boucle->having[] = array("'".$op."'", "'frequence'",$op_val);
	}
}

function balise_NUAGE_dist($p) {
  $filtre = chercher_filtre('nuage');
	$p->interdire_scripts = false;
	if(function_exists('balise_ENV'))
		return balise_ENV($p, $filtre.'(0, "", "", -1, $Pile["vars"]["expose"])');
	else
		return balise_ENV_dist($p, $filtre.'(0, "", "", -1, $Pile["vars"]["expose"])');
	return $p;
}

function restituer($valeur, $cle = 'url') {
	return interdire_scripts(entites_html($valeur[$cle]));
}

function filtre_calculer_nuage_dist($titres, $urls, $poids, $expose) {
  $resultat = array();
  $max = empty($poids)?0:max($poids);
  if($max>0) {
    foreach ($titres as $id => $t) {
      $score = $poids[$id]/$max; # entre 0 et 1
      if($score > 0.05){
        $s = ($unite=floor($score += 0.900001)) . '.' . floor(10*($score - $unite));
        $resultat[$t] = array(
          'url'   => $urls[$id],
          'poids' => $poids[$id].'/'.$max,
          'style' => 'font-size: '.$s.'em;',
          'class' => in_array($id, $expose)
        );
      }
    }
  }
  return $resultat;
}

function filtre_nuage_dist($id_mot, $titre = '', $url = '', $poids = -1, $expose = array()){
	static $nuage = array();
	if($titre and $url){
		$nuage['titre'][$id_mot] = $titre;
		$nuage['url'][$id_mot] = $url;
	}
	elseif($poids>=0){
		$nuage['poids'][$id_mot] += $poids;
	}
	else {
		$calcul = chercher_filtre('calculer_nuage');
		$retour = $calcul($nuage['titre'], $nuage['url'], $nuage['poids'], $expose);
    $nuage = array();
	}
	return !empty($retour) ? $retour : '';
}

?>