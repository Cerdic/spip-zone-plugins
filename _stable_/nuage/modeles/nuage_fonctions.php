<?php

function balise_NUAGE($p){
	$p->code = "nuage(0)";
	return $p;
}

function nuage($id_mot, $titre = '', $url = '', $poids = 0){
	
	include find_in_path('nuage_options.php');
	
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
		$totalpop = array_sum($poids);
		if($maxpop>0){
			foreach ($titre as $id => $t) {
				$score = $poids[$id]/$maxpop; # entre 0 et 1
				if($score > 0.05){
					$s = ceil(30*$score);
					$s = 10 + $s;
					if ($nuage_mode == 'non-accessible') $l = "<span style='white-space:nowrap; font-size:".$s."px;'>$t</span>";
					else $l = "<span style='white-space:nowrap; font-size:".$s."px;'>$t<span class=\"nuage_frequence\"> (".$poids[$id]."/".$totalpop.")</span></span>";
					$l = "<a href='".$url[$id]."'>$l</a>";
					$texte .= "$l &nbsp; \n";
				}
			}
			$nuage = array();
		}
	}
	return $texte;
}

?>