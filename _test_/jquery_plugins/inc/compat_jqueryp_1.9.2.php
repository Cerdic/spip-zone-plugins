<?php

define('_COMPAT_JQUERYP_192', true);

// http://doc.spip.org/@chargeur_charger_zip
function jqueryp_compat_chargeur_charger_zip($quoi = array())
{
	if (!$quoi) {
		return true;
	}
	if (is_scalar($quoi)) {
		$quoi = array('zip' => $quoi);
	}
	if (isset($quoi['depot']) || isset($quoi['nom'])) {
		$quoi['zip'] = $quoi['depot'] . $quoi['nom'] . '.zip';
	}
	foreach (array(	'remove' => 'spip',
					'arg' => 'lib',
					'plugin' => null,
					'cache_cache' => null,
					'rename' => array(),
					'edit' => array(),
					'root_extract' => false, # extraire a la racine de dest ?
					'tmp' => sous_repertoire(_DIR_CACHE, 'chargeur')
				)
				as $opt=>$def) {
		isset($quoi[$opt]) || ($quoi[$opt] = $def);
	}


	# destination finale des fichiers
	switch($quoi['arg']) {
		case 'lib':
			$quoi['dest'] = _DIR_RACINE . _DIR_LIB;
			break;
		case 'plugins':
			$quoi['dest'] = _DIR_PLUGINS_AUTO;
			break;
		default:
			$quoi['dest'] = '';
			break;
	}


	if (!@file_exists($fichier = $quoi['fichier']))
		return 0;

	include_spip('inc/pclzip');
	$zip = new PclZip($fichier);
	$list = $zip->listContent();

	// on cherche la plus longue racine commune a tous les fichiers
	foreach($list as $n) {
		$p = array();
		foreach(explode('/', $n['filename']) as $n => $x) {
			$sofar = join('/',$p);
			$paths[$n][$sofar]++;
			$p[] = $x;
		}
	}

	$total = $paths[0][''];
	$i = 0;
	while (isset($paths[$i])
	AND count($paths[$i]) <= 1
	AND array_values($paths[$i]) == array($total))
		$i++;

	$racine = $i
		? array_pop(array_keys($paths[$i-1])).'/'
		: '';

	$quoi['remove'] = $racine;

	if (!strlen($nom = basename($racine)))
		$nom = basename($fichier, '.zip');

	$dir_export = $quoi['root_extract']
		? $quoi['dest']
		: $quoi['dest'] . $nom.'/';

	$tmpname = $quoi['tmp'].$nom.'/';

	// On extrait, mais dans tmp/ si on ne veut pas vraiment le faire
	$ok = $zip->extract(
		PCLZIP_OPT_PATH,
			$quoi['extract']
				? $dir_export
				: $tmpname
		,
		PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER,
		PCLZIP_OPT_REMOVE_PATH, $quoi['remove']
	);
	if ($zip->error_code < 0) {
		spip_log('- Echec installation : decompression du zip impossible ' . $zip->error_code .
			' de : ' . $quoi['zip'] . ' // dans : ' . $quoi['dest'], 'jquery_plugins');
		return //$zip->error_code
			$zip->errorName(true);
	}

	spip_log('+ Installation : decompression de : ' . $quoi['zip'] . ' // dans : ' . $quoi['dest'], 'jquery_plugins');

	$size = $compressed_size = 0;
	$removex = ',^'.preg_quote($quoi['remove'], ',').',';
	foreach ($list as $a => $f) {
		$size += $f['size'];
		$compressed_size += $f['compressed_size'];
		$list[$a] = preg_replace($removex,'',$f['filename']);
	}

	// Indiquer par un fichier install.log
	// a la racine que c'est chargeur qui a installe ce plugin
	include_spip('inc/flock');
	ecrire_fichier($tmpname.'/install.log',
		"installation: charger_plugin\n"
		."date: ".gmdate('Y-m-d\TH:i:s\Z', time())."\n"
		."source: ".$quoi['zip']."\n"
	);

	return array(
		'files' => $list,
		'size' => $size,
		'compressed_size' => $compressed_size,
		'dirname' => $dir_export,
		'tmpname' => $tmpname
	);
}




// charge un plugin jquery depuis un zip distant
// code pris que action_charger_plugin_dist 1.9.3
function jqueryp_compat_install_zip($zip){
	
	# si premiere lecture, destination temporaire des fichiers
	include_spip('inc/flock');
	$tmp = sous_repertoire(_DIR_CACHE, 'chargeur');

	# dispose-t-on du fichier ?
	$status = null;
	$fichier = $tmp.basename($zip);
	if (!@file_exists($fichier)) {
		include_spip('inc/distant');
		$contenu = recuperer_page($zip, false, false,
			8000000 /* taille max */);
		if (!$contenu
		OR !ecrire_fichier($fichier, $contenu)) {
			spip_log('- Echec installation : impossible de charger '.$zip, 'jquery_plugins');
			$status = -1;
		}
	}

	if ($status === null) {
		$status = jqueryp_compat_chargeur_charger_zip(
			array(
				'zip' => $zip,
				'arg' => 'lib',
				'fichier' => $fichier,
				'tmp' => $tmp,
				'extract' => true
			)
		);
		supprimer_fichier($fichier);
	}

	if (is_array($status))
		return true;
	else
		return false;
}



include_spip('public/balises');
if (!function_exists('balise_FOREACH_dist')){
	//#FOREACH
	//
	// http://doc.spip.org/@balise_FOREACH_dist
	function balise_FOREACH_dist($p) {
		$_tableau = interprete_argument_balise(1,$p);
		$_tableau = str_replace("'", "", strtoupper($_tableau));
		$_tableau = sinon($_tableau, 'ENV');
		$f = 'balise_'.$_tableau;
		$balise = function_exists($f) ? $f : (function_exists($g = $f.'_dist') ? $g : '');

		if($balise) {
			$_modele = interprete_argument_balise(2,$p);
			$_modele = str_replace("'", "", strtolower($_modele));
			$__modele = 'foreach_'.strtolower($_tableau);
			$_modele = (!$_modele AND find_in_path('modeles/'.$__modele.'.html')) ?
				$__modele : 
				($_modele ? $_modele : 'foreach');

			$p->param = @array_shift(@array_shift($p->param));
			$p = $balise($p);
			$filtre = chercher_filtre('foreach');
			$p->code = $filtre . "(unserialize(" . $p->code . "), '" . $_modele . "')";
		}
		//On a pas trouve la balise correspondant au tableau a traiter
		else {
			erreur_squelette(
				_L(/*zbug*/'erreur #FOREACH: la balise #'.$_tableau.' n\'existe pas'),
				$p->id_boucle
			);
			$p->code = "''";
		}
		return $p;
	}
}

include_spip('inc/filtres');
if (!function_exists('chercher_filtre')){
	// http://doc.spip.org/@chercher_filtre
	function chercher_filtre($fonc) {
			foreach (
			array('filtre_'.$fonc, 'filtre_'.$fonc.'_dist', $fonc) as $f)
				if (function_exists($f)
				OR (preg_match("/^(\w*)::(\w*)$/", $f, $regs)                            
					AND is_callable(array($regs[1], $regs[2]))
				)) {
					return $f;
				}
			return NULL;
	}
}
	
if (!function_exists('filtre_foreach_dist')){
	//[(#ENV*|unserialize|foreach)]
	// http://doc.spip.org/@filtre_foreach_dist
	function filtre_foreach_dist($balise_deserializee, $modele = 'foreach') {
		spip_log("FILTRE  - ");
		$texte = '';
		if(is_array($balise_deserializee))
			foreach($balise_deserializee as $k => $v)
				$texte .= recuperer_fond(
					'modeles/'.$modele,
					array_merge(array('cle' => $k), (is_array($v) ? $v : array('valeur' => $v)))
				);
		return $texte;
	}
}

?>
