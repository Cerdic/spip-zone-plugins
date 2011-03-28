<?php
function critere_popularite($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'pop.id_'.$type;
	$pop = 'lf.popularite';
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$pop_max=max(1 , 0 + $GLOBALS['meta']['popularite_max']);
	$boucle->select[]= 'CEIL(SUM('.$pop.'*100/'.$pop_max.')) AS popularite_relative';
	$boucle->from['pop']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'pop.".$boucle->primary."'");
	$boucle->from['lf']="spip_".$type."s";
	$boucle->where[]= array("'='", "'".$type_id."'", "'lf.id_".$type."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'popularite_relative'",$op_val);
	
}
function critere_frequence_branche($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'freqb.id_'.$type;
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$boucle->select[]= 'COUNT('.$type_id.') AS frequence';
	$boucle->from['freqb']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'freqb.".$boucle->primary."'");
	$boucle->from['lf']="spip_".$type."s";
	$boucle->where[]= array("'='", "'".$type_id."'", "'lf.id_".$type."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'frequence'",$op_val);
	
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);
	$c = "calcul_mysql_in('lf.id_rubrique', calcul_branche($arg), '')";
	$c = "($arg ? $c : 1)";			
	if ($not)
		$boucle->where[]= array("'NOT'", $c);
	else
		$boucle->where[]= $c;
}
function critere_popularite_branche($idb, &$boucles, $crit){
	$op='';
	$boucle = &$boucles[$idb];
	$params = $crit->param;
	$type = array_shift($params);
	$type = $type[0]->texte;
	if(preg_match(',^(\w+)([<>=])([0-9]+)$,',$type,$r)){
		$type=$r[1];
		$op=$r[2];
		$op_val=$r[3];
	}
	$type_id = 'pop.id_'.$type;
	$pop = 'lf.popularite';
	$type_requete = $boucle->type_requete;
	$id_table = $boucle->id_table . '.' . $boucle->primary;
	$pop_max=max(1 , 0 + $GLOBALS['meta']['popularite_max']);
	$boucle->select[]= 'CEIL(SUM('.$pop.'*100/'.$pop_max.')) AS popularite_relative';
	$boucle->from['pop']="spip_'.$type_requete.'_".$type."s";
	$boucle->where[]= array("'='", "'".$id_table."'", "'pop.".$boucle->primary."'");
	$boucle->from['lf']="spip_".$type."s";
	$boucle->where[]= array("'='", "'".$type_id."'", "'lf.id_".$type."'");
	$boucle->group[]=$id_table;
	if ($op)
		$boucle->having[]= array("'".$op."'", "'popularite_relative'",$op_val);
	
	$not = $crit->not;
	$boucle = &$boucles[$idb];
	$arg = calculer_argument_precedent($idb, 'id_rubrique', $boucles);
	$c = "calcul_mysql_in('lf.id_rubrique', calcul_branche($arg), '')";
	$c = "($arg ? $c : 1)";
	if ($not)
		$boucle->where[]= array("'NOT'", $c);
	else
		$boucle->where[]= $c;
}

function nuage_note($score,$scoremax=1,$max=10) {
	if ($scoremax == 0) $scoremax = 1;
	$score = pow(($score/$scoremax),1.5); # lissage
	return ceil($max*$score);
}

function balise_POPULARITE_RELATIVE_dist($p) {
	$p->code = '$Pile[$SP][\'popularite_relative\']';
	$p->interdire_scripts = false;
	return $p;
}

function balise_NUAGE_dist($p) {
	$filtre = chercher_filtre('nuage');
	$p->interdire_scripts = false;
	if(function_exists('balise_ENV'))
		return balise_ENV($p, $filtre.'(0, "", "", -1, $Pile["0"]["expose"])');
	else
		return balise_ENV_dist($p, $filtre.'(0, "", "", -1, $Pile["0"]["expose"])');
	return $p;
}

function filtre_calculer_nuage_dist($titres, $urls, $poids, $expose) {
	$filtre_find = chercher_filtre('find');
	$resultat = array();
	if(function_exists('lire_config'))
		$score_min = lire_config('nuage/score_min',0.05);
	else
		$score_min = 0.05;
	$max = empty($poids)?0:max($poids);
	if($max>0) {
		foreach ($titres as $id => $t) {
			$score = $poids[$id]/$max; # entre 0 et 1
			if($score > $score_min){
				$s = ($unite=floor($score += 0.900001)) . floor(10*($score - $unite));
				$s -= 9;
				$resultat[$t] = array(
					'url'   => $urls[$id],
					'poids' => $poids[$id].'/'.$max,
					'class' => $s,
					'expose' => $filtre_find($expose, $id)
				);
			}
		}
	}
	return $resultat;
}

function filtre_nuage_dist($id_mot, $titre = '', $url = '', $poids = -1, $expose = array()){
	static $nuage = array();
	if($titre and $url){
		$nuage['titre'][$id_mot] = supprimer_tags($titre);
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

function nuage_tri_poids($a,$b){
	return (intval($a['poids'])==intval($b['poids']))?0:intval($a['poids'])<intval($b['poids'])?1:-1;
}

function nuage_tri_hasard($a,$b){
	return (intval($a['hasard'])==intval($b['hasard']))?0:intval($a['hasard'])<intval($b['hasard'])?1:-1;
}

function nuage_affiche($nuage,$max_mots = -1){
	if (!is_array($nuage)) $nuage = unserialize($nuage);
	if (!is_array($nuage)) return "";
	$out .= "";
	foreach($nuage as $cle=>$vals){
		$a = "<a rel='tag' href='".$vals['url']."' class='nuage".$vals['class'].($vals['expose']?' on':'')."'>";
		$a = $a . $cle . "</a>";
		$out .= "<dt>$a</dt> ";
		$out .= "<dd class='frequence'>".$vals['poids']."</dd>";
		if ($max_mots>0) $max_mots--;
		if ($max_mots==0) break;
	}
	return "<dl class='nuage'>$out</dl>";   
}

function nuage_tri($nuage,$tri = 'poids'){
	if (!is_array($nuage)) $nuage = unserialize($nuage);
	if (!is_array($nuage)) return array();
	if ($tri == 'titre')
		return $nuage;
	if ($tri == 'hasard') {
		foreach($nuage as $cle=>$vals){
			$nuage[$cle]['hasard'] = rand();
		}
	}
	if (function_exists($f= "nuage_tri_$tri"))
		uasort($nuage,$f);
	return $nuage;
}

function nuage_extrait($nuage,$nombre){
	if (!is_array($nuage)) $nuage = unserialize($nuage);
	return array_splice($nuage,$nombre);   
}
?>
