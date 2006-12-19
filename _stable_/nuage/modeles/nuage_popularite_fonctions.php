<?php

function balise_NUAGE_POP($p){
	$p->code = "nuage_pop(0)";
	return $p;
}

function nuage_pop($id_mot, $titre = '', $url = '', $poids = 0){
	static $nuage;
	$texte = '';
	if($titre and $url){
		$nuage['titre'][$id_mot] = $titre;
		$nuage['url'][$id_mot] = $url;
	}
	elseif($poids){
		$nuage['poids'][$id_mot] += $poids;
	}
	else {
		$titre = $nuage['titre'];
		$url = $nuage['url'];
		$poids = $nuage['poids'];
		$maxpop = empty($poids)?0:max($poids);
		$totalpop = count($poids);
		if($maxpop>0){
			foreach ($titre as $id => $t) {
				$score = $poids[$id]/$maxpop; # entre 0 et 1
				$score = pow($score,1.5); # lissage
				$s = ceil(15*$score);
				$l = "<font style='white-space:nowrap;' size='$s'>$t<span class=\"nuage_frequence\"> (".$poids[$id]."/".$totalpop.")</span></font>";
				$l = "<a href='".$url[$id]."'>$l</a>";
				$texte .= "$l\n";
			}
			$nuage = array();
		}
	}
	return $texte;
}

?>
