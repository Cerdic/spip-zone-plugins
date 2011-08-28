<?php

function nuage_pop($id_mot, $titre = '', $url = '', $poids = -1, $expose = array()){
	static $nuage;
	$texte = '';
	if($titre and $url){
		$nuage['titre'][$id_mot] = $titre;
		$nuage['url'][$id_mot] = $url;
	}
	elseif($poids>=0){
		$nuage['poids'][$id_mot] += $poids;
	}
	else {
		$titre = $nuage['titre'];
		$url = $nuage['url'];
		$poids = $nuage['poids'];
		$max = empty($poids)?0:max($poids);
		if($max>0){
			foreach ($titre as $id => $t) {
				$score = $poids[$id]/$max; # entre 0 et 1
				$score = pow($score,1.5); # lissage
				$s = ceil(15*$score);
				$t = '<font size="'.$s.'">'.$t.'</font>';
				$l = $t.'<span class="frequence"> ('.($poids[$id]?$poids[$id]:"0")."/".$max.")</span>";
				$class = in_array($id, $expose) ? ' class="on"': '';
				$texte .= '<li><a rel="tag" href="'.$url[$id].'"'.$class.'>';
				$texte .= $l.'</a></li>'."\n";
			}
			$texte = $texte ? '<ul class="nuage">'."\n".$texte."</ul>\n":"";
			$nuage = array();
		}
	}
	return $texte;
}

?>
