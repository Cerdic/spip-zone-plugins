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
	foreach ($tweaks_pipelines['inc_'.$type] as $inc) include_spip('tweaks/'.$inc);
	$temp = '';
	foreach ($tweaks_pipelines['code_'.$type] as $code) $temp .= $code."\n";
	eval($temp);
tweak_log("  $type = $temp");
}

// passe le $flux dans le $pipeline ...
function tweak_pipeline($pipeline, $flux) {
	global $tweaks, $tweaks_pipelines;
	if (isset($tweaks_pipelines[$pipeline])) {
		foreach ($tweaks_pipelines[$pipeline]['inclure'] as $inc) include_spip('tweaks/'.$inc);
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

// est-ce que $pipe est un pipeline ?
function is_tweak_pipeline($pipe, &$set_pipe) {
	if ($ok=preg_match(',^\s*pipeline\s*:(.*)$,',$pipe,$t)) $set_pipe = trim($t[1]);
	return $ok;
}

// initialise : $tweaks_pipelines, $tweaks_css
function tweak_initialise_includes() {
  global $tweaks, $tweaks_pipelines, $tweaks_css;
  $tweaks_pipelines = $tweaks_css = array();
  foreach ($tweaks as $i=>$tweak) {
	// stockage de la liste des fonctions par pipeline, si le tweak est actif...
	if ($tweak['actif']) {
		$inc = $tweak['id']; $pipe2 = '';
		foreach ($tweak as $pipe=>$fonc) if (is_tweak_pipeline($pipe, $pipe2)) {
			$tweaks_pipelines[$pipe2]['inclure'][] = $inc;
			$tweaks_pipelines[$pipe2]['fonction'][] = $fonc;
		}
		$f = find_in_path('tweaks/'.$inc.'.css');
		if ($f) {
			include_spip('tweaks/filtres');
			$tweaks_css[] = '<link rel="stylesheet" href="'.direction_css($f).'" type="text/css" media="projection, screen" />';
		}
		if (isset($tweak['code'])) { $inc = $tweak['code']; $prefixe = 'code_'; }
			else $prefixe = 'inc_';
		if ($tweak['options']) $tweaks_pipelines[$prefixe.'options'][] = $inc;
		if ($tweak['fonctions']) $tweaks_pipelines[$prefixe.'fonctions'][] = $inc;
	}
  }
}

// remplace les valeurs marquees comme %%toto%% par la valeur reelle de $metas_vars['toto']
// attention : la description du tweak (trouvee dans lang/tweak_xx.php) doit 
// obligatoirement conporter la demande de valeur : %toto%
// %%toto/d%% oblige un nombre et %%toto/s%% oblige une chaine
// %%toto/valeurpardefaut%% renvoie valeurpardefaut si le meta n'existe pas encore
// syntaxe generale : %%toto/d/valeurpardefaut%% ou %%toto/s/valeurpardefaut%% 
// $code est le code inline livre par tweak_spip_config
function tweak_parse_code($code) {
	global $metas_vars;
	while(preg_match(',%%([a-zA-Z_][a-zA-Z0-9_]*)(/[ds])?(/[^%]+)?%%,', $code, $matches)) {
		$rempl = '""';	
		// si le meta est present on garde la valeur du meta, sinon la valeur par defaut si elle existe
		if (isset($metas_vars[$matches[1]])) $rempl = $metas_vars[$matches[1]];
			else { 
				$rempl = isset($matches[3])?substr($matches[3],1):'""';
				if($matches[2]=='/d') $rempl = 'intval('.$rempl.')';
					elseif($matches[2]=='/s') $rempl = 'strval('.$rempl.')';
				eval('$rempl='.$rempl.';');
				if($matches[2]!='/d') $rempl = '"'.str_replace('"','\"',$rempl).'"';
			}
		$code = str_replace($matches[0], $rempl, $code);
		// on conserve le resultat dans $metas_vars
		$metas_vars[$matches[1]] = $rempl;
//print_r($metas_vars);
//print_r($matches); echo "rempl=$rempl\ncode=$code\n\n";
	}
	return $code;
}

// parse la description et renseigne le nombre de variables
function tweak_parse_description($tweak, $tweak_input) {
	global $tweaks, $metas_vars; 
	$tweaks[$tweak]['nb_variables'] = 0;
	$tweaks[$tweak]['description'] = propre($tweaks[$tweak]['description']);
	$t = preg_split(',%([a-zA-Z_][a-zA-Z0-9_]*)%,', $tweaks[$tweak]['description'], -1, PREG_SPLIT_DELIM_CAPTURE);
	$descrip = '';
	for($i=0;$i<count($t);$i+=2) if (($var=trim($t[$i+1]))!='') {
		// si le meta est present on remplace
		if (isset($metas_vars[$var])) 
				$descrip .= $tweak_input(
					$tweaks[$tweak]['basic']+(++$tweaks[$tweak]['nb_variables']), 
					$var, 
					$metas_vars[$var],
					$t[$i],
					$tweaks[$tweak]['actif'], 
					'tweak_spip_admin');
			else $descrip .= $t[$i]."[$var?]";
	} else $descrip .= $t[$i];
	$tweaks[$tweak]['description'] = $descrip;
}

// decommenter pour debug...
function tweak_log($s) { 
//	spip_log($s);
}	

// lit les metas et initialise $tweaks_pipelines et les includes
function tweak_initialisation() {
	global $tweaks, $metas_vars;
tweak_log("tweak_initialisation");
	include_spip('inc/meta');
	lire_metas();
	$metas_tweaks = unserialize($GLOBALS['meta']['tweaks']);
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_vars']);
	// au cas ou un tweak a besoin d'input
	$tweak_input = charger_fonction('tweak_input', 'inc');
	// completer les variables manquantes et incorporer l'activite lue dans les metas
	foreach($temp = $tweaks as $i=>$tweak) {
		if (!isset($tweak['id'])) { $tweaks[$i]['id']='erreur'; $tweaks[$i]['nom'] = _T('tweak:erreur_id');	}
		if (!isset($tweak['categorie'])) $tweaks[$i]['categorie'] = _T('tweak:divers');
			else $tweaks[$i]['categorie'] = _T('tweak:'.$tweaks[$i]['categorie']);
		if (!isset($tweak['nom'])) $tweaks[$i]['nom'] = _T('tweak:'.$tweak['id'].':nom');
		$tweaks[$i]['auteur'] = propre($tweaks[$i]['auteur']);
		if (!isset($tweak['description'])) $tweaks[$i]['description'] = _T('tweak:'.$tweak['id'].':description');
		$tweaks[$i]['actif'] = isset($metas_tweaks[$tweaks[$i]['id']])?$metas_tweaks[$tweaks[$i]['id']]['actif']:0;
		// au cas ou des variables sont presentes dans le code
		$tweaks[$i]['basic'] = $i*10; $tweaks[$i]['nb_variables'] = 0;
		// cette ligne peut initialiser des variables dans $metas_vars
		if (isset($tweak['code'])) $tweaks[$i]['code'] = tweak_parse_code($tweaks[$i]['code']);
		// cette ligne peut utiliser des variables dans $metas_vars
		tweak_parse_description($i, $tweak_input);
	}
	ecrire_meta('tweaks', serialize($metas_tweaks));
	ecrire_meta('tweaks_vars', serialize($metas_vars));
	ecrire_metas();
	tweak_initialise_includes();
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