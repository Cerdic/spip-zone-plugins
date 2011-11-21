<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


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
		if ($poids>=0)
			$nuage['poids'][$id_mot] = $poids;
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
	$out = "";
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
	if ($tri=='titre') {
		$titres = array_combine(array_keys($nuage),array_map('strtolower',array_keys($nuage)));
		array_multisort($titres,SORT_ASC,$nuage);
		return $nuage;
	}
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

function nuage_insert_head_css($flux) {
	$css = "<link rel='stylesheet' href='".find_in_path("css/nuage.css")."' type='text/css' />\n";
	$flux .= "\n".$css;
	return $flux;
}
?>
