<?php

function balise_NUAGE_dist($p) {
	$p->interdire_scripts = false;
	if(function_exists('balise_ENV'))
		return balise_ENV($p, 'nuage(0, "", "", -1, $Pile["vars"]["expose"])');
	else
		return balise_ENV_dist($p, 'nuage(0, "", "", -1, $Pile["vars"]["expose"])');
	return $p;
}

function restituer($valeur, $cle = 'url') {
	return $valeur[$cle];
}

function nuage($id_mot, $titre = '', $url = '', $poids = -1, $expose = array()){
	static $nuage = array();
	$test = array();
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
		if($max>0) {
			foreach ($titre as $id => $t) {
				$score = $poids[$id]/$max; # entre 0 et 1
				if($score > 0.05){
					$s = ($unite=floor($score += 0.900001)) . '.' . floor(10*($score - $unite));
					$test[$t] = array(
						'url'   => $url[$id],
						'poids' => $poids[$id].'/'.$max,
						'style' => 'font-size: '.$s.'em;',
						'class' => in_array($id, $expose)
					);
				}
			}
			$nuage = array();
		}
	}
	return !empty($test) ? $test : '';
}

?>