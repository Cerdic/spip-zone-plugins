<?php

function balise_NUAGE($p){
	$p->code = "nuage(0)";
	return $p;
}

function nuage($id_mot, $titre = '', $url = '', $poids = 0){
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
			$texte .= '<ul class="nuage_frequence">'."\n";
			foreach ($titre as $id => $t) {
				$score = $poids[$id]/$maxpop; # entre 0 et 1
				if($score > 0.05){
					$s = 0.02*ceil(75*$score);
					$s = 0.75 + $s;
					$l = "<span class=\"nuage_item_titre\" style=\"white-space:nowrap; font-size:".$s."em;\">$t<span class=\"nuage_frequence\"> (".$poids[$id]."/".$totalpop.")</span></span>";
					$l = "<li><a href='".$url[$id]."'>$l</a></li>";
					$texte .= "$l\n";
				}
			}
			$texte .= '</ul>'."\n";
			$nuage = array();
		}
	}
	return $texte;
}

?>
