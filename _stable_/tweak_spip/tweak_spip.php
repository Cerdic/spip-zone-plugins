<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

include_spip('tweak_spip_config');

// ajoute un tweak à $tweaks;
function add_tweak($tableau) {
	global $tweaks;
	$tweaks[] = $tableau;
}

// $type ici est egal à 'options' ou 'fonctions'
function include_tweaks($type) {
	global $tweaks_pipelines;
	foreach ($tweaks_pipelines['inc_'.$type] as $inc) include_spip('inc/'.$inc);
	foreach ($tweaks_pipelines['code_'.$type] as $code) eval($code);
}

// passe le $flux dans le $pipeline ...
function tweak_pipeline($pipeline, $flux) {
	global $tweaks, $tweaks_pipelines;
	if (isset($tweaks_pipelines[$pipeline])) {
		foreach ($tweaks_pipelines[$pipeline]['inclure'] as $inc) include_spip('inc/'.$inc);
		foreach ($tweaks_pipelines[$pipeline]['fonction'] as $fonc) if (function_exists($fonc)) $flux = $fonc($flux);
	}
	return $flux;
}

// retourne les css utilises (en vue d'un insertion en head)
function tweak_insert_css() {
	global $tweaks_css;
	$head = "\n<!-- CSS TWEAKS -->\n";
	foreach ($tweaks_css as $css) $head .= $css;
	return $head;
}

// initialise : $tweaks_pipelines, $tweaks_css
function tweaks_initialise_includes() {
  global $tweaks, $tweak_exclude, $tweaks_pipelines, $tweaks_css;
  $tweaks_pipelines = $tweaks_css = array();
  foreach ($tweaks as $i=>$tweak) {
	// stockage de la liste des fonctions par pipeline, si le tweak est actif...
	if ($tweak['actif']) {
		$inc = $tweak['id'];
		foreach ($tweak as $pipe=>$fonc) if(!in_array($pipe, $tweak_exclude)) {
			$tweaks_pipelines[$pipe]['inclure'][] = $inc;
			$tweaks_pipelines[$pipe]['fonction'][] = $fonc;
		}
		$f = find_in_path('inc/'.$inc.'.css');
		if ($f) {
			include_spip('inc/filtres');
			$tweaks_css[] = '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />';
		}
		if (isset($tweak['code'])) { $inc = $tweak['code']; $prefixe = 'code_'; }
			else $prefixe = 'inc_';
		if ($tweak['options']) $tweaks_pipelines[$prefixe.'options'][] = $inc;
		if ($tweak['fonctions']) $tweaks_pipelines[$prefixe.'fonctions'][] = $inc;
	}
  }
}

// lit les metas et initialise $tweaks_pipelines et les includes
function tweak_initialisation() {
	global $tweaks;
	include_spip('inc/meta');
	lire_metas();
	$metas_tweaks = unserialize($GLOBALS['meta']['tweaks']);
	// completer les variables manquantes et incorporer l'activite lue dans les metas
	foreach($temp = $tweaks as $i=>$tweak) {
		if (!isset($tweak['id'])) { $tweaks[$i]['id']='erreur'; $tweaks[$i]['nom'] = _T('tweak:erreur_id');	}
		if (!isset($tweak['categorie'])) $tweaks[$i]['categorie'] = _T('tweak:divers');
			else $tweaks[$i]['categorie'] = _T('tweak:'.$tweaks[$i]['categorie']);
		if (!isset($tweak['nom'])) $tweaks[$i]['nom'] = _T('tweak:'.$tweak['id'].':nom');
		if (!isset($tweak['description'])) $tweaks[$i]['description'] = _T('tweak:'.$tweak['id'].':description');
		$tweaks[$i]['actif'] = isset($metas_tweaks[$tweaks[$i]['id']])?$metas_tweaks[$tweaks[$i]['id']]['actif']:0;
	}
	ecrire_meta('tweaks', serialize($metas_tweaks));
	ecrire_metas();
	tweaks_initialise_includes();
}

// evite les transformations typo dans les balises $balises
// par exemple pour <html>, <cadre>, <code>, <frame>, <script>, <acronym> et <cite>, $balises = 'html|code|cadre|frame|script|acronym|cite'
// $fonction est la fonction prevue pour transformer $texte
// $texte est le texte d'origine
// si $balises = '' alors la protection par defaut est : html|code|cadre|frame|script
function tweak_exclure_balises($balises, $fonction, $texte){
	$balises = strlen($balises)?',<('.$balises.')(\s[^>]*)?>(.*)</\1>,UimsS':'';
	if ($spip_version_code<1.92 && $balises=='') $balises = ',<(html|code|cadre|frame|script)>(.*)</\1>,UimsS';
	$texte = echappe_retour($fonction(echappe_html($texte, 'TWEAKS', true, $balises)), 'TWEAKS');
	return $texte;
}

?>