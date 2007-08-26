<?php

	// remonter l'arborescence jusqu'a trouver la racine du site
	$profondeur=0;
	while (!file_exists('ecrire/inc_version.php')
	AND $profondeur++ < 4)
		chdir('..');
	require 'ecrire/inc_version.php';
	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin (ce script est un vilain trou de securite)
	if ( ($auteur_session['statut'] != '0minirezo') and ( $_SERVER["REMOTE_ADDR"]!='127.0.0.1'))
		die('pas admin !');

	$sites = array();
	foreach(preg_files('sites', '.*/config/connect.php') as $s) {
		$sites[] = preg_replace(',^sites/(.*)/config/connect.php,', '\1', $s);
	}
	sort($sites);

	$titre = _L(count($sites).' '.'sites mutualis&#233;s');

	$page = "<table>";
	foreach ($sites as $v) {
		if (lire_fichier('sites/'.$v.'/tmp/meta_cache.txt', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon($meta['nom_site'], $v);
		}
		else
			$url = 'http://'.$v.'/';
		$page .= "<tr>
			<td>$v</td>
			<td><a href='${url}'>$nom_site</a></td>
			<td><a href='${url}ecrire/'>ecrire/</a></td>
			<td><a href='${url}ecrire/index.php?exec=statistiques_visites'>stats</a></td>
			</tr>\n";
	}
	$page .= "</table>";

	$page = minipres($titre, $page);

	echo str_replace('<head>', '<head>
		<base href="'.url_absolue(str_repeat('../', $profondeur)).'" />', $page);

?>
