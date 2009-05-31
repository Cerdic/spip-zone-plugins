<?php
function presenteAuteursPubli($texte){
	$auteurs=explode(";",$texte);
	$auteurs=preg_replace('/\s*(.*)\s*,\s*(.*)\s*/us', '<span class="publi_prenom">\\2</span> <span class="publi_nom">\\1</span>', $auteurs);
	return implode(', ',$auteurs);
}

function backendAuteursPubli($texte){
	$auteurs=explode(";",$texte);
	$auteurs=preg_replace('/\s*(.*)\s*,\s*(.*)\s*/us', '<dc:creator>\\1, \\2</dc:creator>', $auteurs);
	return implode(' ',$auteurs);
}
//****** extraction du publisher dans les tags
function publiHAL_extraction_publisher($texte){
	if(preg_match('@\<a\s*rel=\'publisher\'\s*\>((?U).*)\</a\>@uis', $texte, $matches)) return $matches[1];
	return '';	
}

//#SOUSTITRE|rubCode_CodeRequis ??
function balise_REFEDITEUR($p){
	$_tags = champ_sql('tags', $p);
	$p->code = "publiHAL_extraction_publisher($_tags)";
	$p->interdire_scripts = false;
	return $p;
}

function balise_LISTE_IDS($p1, $src = NULL){
	// récupération par #ENV
	$p=balise_ENV_dist($p1, $src);
//	$_nom="1";
//	if ($p->param && !$p->param[0][0]){
//			$_nom =  $p->param[0][1][0]->texte;
//		}
	$p->code = "split(',',$p->code)";
	$p->interdire_scripts = false;
	return $p;
}

function publiHAL_extraction_tags($texte){
	/**
	 * <a rel="tag">Cognitive science/Computer science</a>
	 * <a rel="tag">Computer Science/Artificial Intelligence</a>
	 * <a rel="tag">Computer Science/Operations Research</a>
	 * <a rel="publisher">Workshop on Reasoning with Uncertainty in Robotics, 18th International Joint Conference on Artificial Intelligence, Acapulco (Mexico) (2003) x</a>
	 * <a rel="typedoc">COMM_ACT</a>
	 * <a rel="coverage">Bayesian programming;Obstacle avoidance;Command fusion</a>
	 */
	$tags=array('tag'=>array(),'publisher'=>array(),'typedoc'=>array(),'coverage'=>array(),'directory'=>array(),'embed'=>array());
	if(preg_match_all('@<a([^>]*)[[:space:]]+rel=([\'"])(\w+)\1([^>]*)>((?U).*)</a>@uis',$texte,$matches,PREG_SET_ORDER)){
		foreach ($matches as $match) {
			//var_dump($match);
			if(array_key_exists($match[3], $tags)){
				//echo $match[3]."=>".$match[5].'<br>';
				$tags[$match[3]][]=array('val'=>$match[5],'attr'=>$match[1].$match[4]);
			}
		}
	}
	return $tags;
}
?>