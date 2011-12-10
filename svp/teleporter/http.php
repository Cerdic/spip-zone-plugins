<?php
/**
 * Plugin S.P
 * Licence IV
 * (c) 2011 vers l'infini et au dela
 */

/**
 * Teleporter et deballer un composant
 * @param string $methode
 *   http|git|svn|...
 * @param string $source
 * @param string $dest
 * @param array $options
 *   non utilise ici
 * @return bool
 */
function teleporter_http_dist($methode,$source,$dest,$options=array()){

	$tmp = $options['dir_tmp'];
	# on ne se contente pas du basename qui peut etre un simple v1
	# exemple de l'url http://nodeload.github.com/kbjr/Git.php/zipball/v0.1.1-rc
	$fichier = $tmp . (basename($dest)."-".substr(md5($source),0,8)."-".basename($source));

	$res = teleporter_http_recuperer_source($source,$fichier);
	if (!is_array($res))
		return $res;

	list($fichier,$extension) = $res;
	if (!$deballe = charger_fonction("http_deballe_$extension","teleporter",true))
		return _T('svp:erreur_teleporter_format_archive_non_supporte',array('extension' => $extension));

	$old = "";
	if (is_dir($dest)){
		$dir = dirname($dest);
		$base = basename($dest);
		$old="$dir/.$base.bck";
		if (is_dir($old))
			supprimer_repertoire($old);
		rename($dest,$old);
	}

	if (!$target = $deballe($fichier, $dest, $tmp)){
		// retablir l'ancien sinon
		if ($old)
			rename($old,$dest);
		return _T('svp:erreur_teleporter_echec_deballage_archive',array('fichier' => $fichier));
	}

	return true;
}

function teleporter_http_recuperer_source($source,$dest_tmp){

	# securite : ici on repart toujours d'une source neuve
	if (file_exists($dest_tmp))
		spip_unlink($dest_tmp);

	$extension = "";

	# si on ne dispose pas encore du fichier
	# verifier que le zip en est bien un (sans se fier a son extension)
	#	en chargeant son entete car l'url initiale peut etre une simple
	# redirection et ne pas comporter d'extension .zip
	include_spip('inc/distant');
	$head = recuperer_page($source, false, true, 0);

	if (preg_match(",^Content-Type:\s*application/zip$,Uims",$head))
		$extension = "zip";
	elseif (preg_match(",^Content-Disposition:\s*attachment;\s*filename=(.*)$,Uims",$head,$m)){
		$f = $m[1];
		if (pathinfo($f, PATHINFO_EXTENSION)=="zip"){
			$extension = "zip";
		}
	}
	// au cas ou, si le content-type n'est pas la
	// mais que l'extension est explicite
	elseif(pathinfo($source, PATHINFO_EXTENSION)=="zip")
		$extension = "zip";

	# format de fichier inconnu
	if (!$extension) {
		spip_log("Type de fichier inconnu pour la source $source","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_type_fichier_inconnu',array('source' => $source));
	}

	$dest_tmp = preg_replace(";\.[\w]{2,3}$;i","",$dest_tmp).".$extension";

	include_spip('inc/distant');
	$dest_tmp = copie_locale($source,'force',$dest_tmp);
	if (!$dest_tmp
	  OR !file_exists($dest_tmp = _DIR_RACINE . $dest_tmp)) {
		spip_log("Chargement impossible de la source $source","teleport"._LOG_ERREUR);
		return _T('svp:erreur_teleporter_chargement_source_impossible',array('source' => $source));
	}

	return array($dest_tmp,$extension);
}


function teleporter_http_deballe_zip($zip, $dest, $tmp){
	$status = teleporter_http_charger_zip(
		array(
			'zip' => $zip, // normalement l'url source mais on l'a pas ici
			'fichier' => $zip,
			'dest' => $dest,
			'tmp' => $tmp,
			'extract' => true,
			'root_extract' => true, # extraire a la racine de dest
		)
	);
	// le fichier .zip est la et bien forme
	if (is_array($status)
	  AND is_dir($status['target'])) {
		return $status['target'];
	}
	// fichier absent
	else if ($status == -1) {
		spip_log("dezip de $zip impossible : fichier absent","teleport"._LOG_ERREUR);
		return false;
	}
	// fichier la mais pas bien dezippe
	else {
		spip_log("probleme lors du dezip de $zip","teleport"._LOG_ERREUR);
		return false;
	}
}

// http://doc.spip.org/@chargeur_charger_zip
function teleporter_http_charger_zip($quoi = array()){
	if (!$quoi)
		return false;

	foreach (array(	'remove' => 'spip',
					'rename' => array(),
					'edit' => array(),
					'root_extract' => false, # extraire a la racine de dest ?
					'tmp' => sous_repertoire(_DIR_CACHE, 'chargeur')
				)
				as $opt=>$def) {
		isset($quoi[$opt]) || ($quoi[$opt] = $def);
	}

	if (!@file_exists($fichier = $quoi['fichier']))
		return 0;

	include_spip('inc/pclzip');
	$zip = new PclZip($fichier);
	$list = $zip->listContent();

	// on cherche la plus longue racine commune a tous les fichiers
	// pour l'enlever au deballage
	$max_n = 999999;
	$paths = array();
	foreach($list as $n) {
		$p = array();
		foreach(explode('/', $n['filename']) as $n => $x) {
			if ($n>$max_n)
				continue;
			$sofar = join('/',$p);
			$paths[$n][$sofar]++;
			$p[] = $x;
		}
		$max_n = min($n,$max_n);
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

	// si pas de racine commune, reprendre le nom du fichier zip
	// en lui enlevant la racine h+md5 qui le prefixe eventuellement
	// cf action/charger_plugin L74
	if (!strlen($nom = basename($racine)))
		$nom = preg_replace(",^h[0-9a-f]{8}-,i","",basename($fichier, '.zip'));

	$dir_export = $quoi['root_extract']
		? $quoi['dest']
		: $quoi['dest'] . $nom;
	$dir_export = rtrim($dir_export,'/').'/';

	$tmpname = $quoi['tmp'].$nom.'/';

	// choisir la cible selon si on veut vraiment extraire ou pas
	$target = $quoi['extract'] ? $dir_export : $tmpname;

	// ici, il faut vider le rep cible si il existe deja, non ?
	if (is_dir($target))
		supprimer_repertoire($target);

	// et enfin on extrait
	$ok = $zip->extract(
		PCLZIP_OPT_PATH,
			$target
		,
		PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER,
		PCLZIP_OPT_REMOVE_PATH, $quoi['remove']
	);
	if ($zip->error_code < 0) {
		spip_log('charger_decompresser erreur zip ' . $zip->error_code .' pour paquet: ' . $quoi['zip'],"teleport"._LOG_ERREUR);
		return //$zip->error_code
			$zip->errorName(true);
	}

	spip_log('charger_decompresser OK pour paquet: ' . $quoi['zip'],"teleport");

	$size = $compressed_size = 0;
	$removex = ',^'.preg_quote($quoi['remove'], ',').',';
	foreach ($list as $a => $f) {
		$size += $f['size'];
		$compressed_size += $f['compressed_size'];
		$list[$a] = preg_replace($removex,'',$f['filename']);
	}

	// Indiquer par un fichier install.log
	// a la racine que c'est chargeur qui a installe ce plugin
	ecrire_fichier($target.'install.log',
		"installation: charger_plugin\n"
		."date: ".gmdate('Y-m-d\TH:i:s\Z', time())."\n"
		."source: ".$quoi['zip']."\n"
	);


	return array(
		'files' => $list,
		'size' => $size,
		'compressed_size' => $compressed_size,
		'dirname' => $dir_export,
		'tmpname' => $tmpname,
		'target' => $target,
	);
}