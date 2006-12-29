<?php

function article_nuage($id_mot, $titre = '', $url = '', $poids = -1){
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
				if($score > 0.05){
					$s = ($unite=floor($score += .500001)) . '.' . floor(10*($score - $unite));
					$l = $t.'<span class="frequence"> ('.$poids[$id]."/".$max.")</span>";
					$texte .= '<li><a rel="tag" href="'.$url[$id].'" style="font-size: '.$s.'em;">';
					$texte .= $l.'</a></li>'."\n";
				}
			}
			$texte = $texte ? '<ul class="nuage">'."\n".$texte."</ul>\n":"";
			$nuage = array();
		}
	}
	return $texte;
}

?>
