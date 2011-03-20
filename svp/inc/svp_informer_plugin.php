<?php

if (!defined("_ECRIRE_INC_VERSION")) return;



// Les archives xml sont deja applaties, pas la peine de se compliquer.
function svp_remplir_champs_sql($p) {

	if (!$p)
		return array();

	// On passe le prefixe en lettres majuscules comme ce qui est fait dans SPIP
	// Ainsi les valeurs dans la table spip_plugins coincideront avec celles de la meta plugin
	$p['prefix'] = strtoupper($p['prefix']);

	// calcul du tableau de dependances
	$dependances = array();
	$v_spip = '';
	if (is_array($p['necessite'])) {
		foreach ($p['necessite'] as $c=>$n) {
			$p['necessite'][$c]['id'] = strtoupper($n['id']);
			if ($n['id'] == 'SPIP') {
				$v_spip = $n['version'];
			}
		}
		$dependances['necessite'] = $p['necessite'];
	}
	
	if (is_array($p['utilise'])) {
		foreach ($p['utilise'] as $c=>$n) {
			$p['utilise'][$c]['id'] = strtoupper($n['id']);
		}
		$dependances['utilise'] = $p['utilise'];
	}

	// Etat numerique (pour simplifier la recherche de maj)
	$num = array('stable'=>4, 'test'=>3, 'dev'=>2, 'experimental'=>1);
	$etatnum = isset($num[$p['etat']]) ? $num[$p['etat']] : 0;
	
	// On passe en utf-8 avec le bon charset les champs pouvant contenir des entites html
	$p['description'] = unicode2charset(html2unicode($p['description']));
	$p['slogan'] = unicode2charset(html2unicode($p['slogan']));
	$p['nom'] = unicode2charset(html2unicode($p['nom']));
	$p['auteur'] = unicode2charset(html2unicode($p['auteur']));
	$p['licence'] = unicode2charset(html2unicode($p['licence']));

	// Nom, slogan et branche
	if ($p['prefix'] == _SVP_PREFIXE_PLUGIN_THEME) {
		// Traitement specifique des themes qui aujourd'hui sont consideres comme des paquets
		// d'un plugin unique de prefixe "theme"
		$nom = _SVP_NOM_PLUGIN_THEME;
		$slogan = _SVP_SLOGAN_PLUGIN_THEME;
	}
	else {
		// Calcul *temporaire* de la nouvelles balise slogan si celle-ci n'est
		// pas renseignee et de la balise nom. Ceci devrait etre temporaire jusqu'a la nouvelle ere
		// glaciaire des plugins
		// - Slogan	:	si vide alors on prend la premiere phrase de la description limitee a 255
		$slogan = (!$p['slogan']) ? svp_remplir_slogan($p['description']) : $p['slogan'];
		// - Nom :	on repere dans le nom du plugin un chiffre en fin de nom
		//			et on l'ampute de ce numero pour le normaliser
		//			et on passe tout en unicode avec le charset du site
		$nom = svp_normaliser_nom($p['nom']);
	}
	
	return array(
		'plugin' => array(
			'prefixe' => $p['prefix'],
			'nom' => $nom,
			'slogan' => $slogan,
			'categorie' => $p['categorie'],
			'tags' => $p['tags']),
		'paquet' => array(
			'logo' => $p['icon'],
			'description' => $p['description'],
			'auteur' => $p['auteur'],
			'version' => $p['version'],
			'version_base' => $p['version_base'],
			'version_spip' => $v_spip,
			'etat' => $p['etat'],
			'etatnum' => $etatnum,
			'licence' => $p['licence'],
			'lien' => $p['lien'],
			'dependances' => serialize($dependances))
	);
}

function svp_remplir_slogan($description) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$descriptions = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $description, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;

	// On boucle sur chaque multi ou sur la chaine elle-meme en extrayant le slogan
	// dans les differentes langues
	$slogan = '';
	foreach ($descriptions as $_lang => $_descr) {
		$_descr = trim($_descr);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',^(.+)[.!?\r\n\f],Um', $_descr, $matches);
		$slogan .= (($multi) ? '[' . $_lang . ']' : '') . 
					(($nbr_matches > 0) ? trim($matches[1]) : couper($_descr, 150, ''));
	}

	if ($slogan)
		// On renvoie un nouveau slogan multi ou pas
		$slogan = (($multi) ? '<multi>' : '') . $slogan . (($multi) ? '</multi>' : '');

	return $slogan;
}

function svp_normaliser_nom($nom) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$noms = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $nom, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;
	
	$nouveau_nom = '';
	foreach ($noms as $_lang => $_nom) {
		$_nom = trim($_nom);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',(.+)(\s+[\d._]*)$,Um', $_nom, $matches);
		$nouveau_nom .= (($multi) ? '[' . $_lang . ']' : '') . 
						(($nbr_matches > 0) ? trim($matches[1]) : $_nom);
	}
	
	if ($nouveau_nom)
		// On renvoie un nouveau nom multi ou pas sans la valeur de la branche 
		$nouveau_nom = (($multi) ? '<multi>' : '') . $nouveau_nom . (($multi) ? '</multi>' : '');
		
	return $nouveau_nom;
}

?>
