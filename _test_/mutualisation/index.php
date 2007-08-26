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

	$page = "<table>
	<thead>
		<tr>
			<td>Domaine</td>
			<td>Nom site</td>
			<td>ecrire</td>
			<td>Stats</td>
		</tr>
	</thead>
	<tbody>";

	$nsite = 1;
	foreach ($sites as $v) {
		if (lire_fichier('sites/'.$v.'/tmp/meta_cache.txt', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon($meta['nom_site'], $v);
		}
		else
			$url = 'http://'.$v.'/';
		$page .= "<tr class='tr". $nsite % 2 ."'>
			<td>$v</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire/</a></td>
			<td><a href='${url}ecrire/index.php?exec=statistiques_visites'>stats</a></td>
			</tr>\n";
		$nsite++;
	}
	$page .= "</tbody></table>";

	$page = minipres($titre, $page);
	
	$page = str_replace('</head>', '
		<style type="text/css">
		tr {vertical-align:top;}
		.tr0 {background-color:#ddded5}
		thead tr {font-weight:bold;background-color:#333;color:#fff;}
		td {text-align:left;}
		#minipres{width:50em;}
		</style>
		</head>
		', $page);

	echo str_replace('<head>', '<head>
		<base href="'.url_absolue(str_repeat('../', $profondeur)).'" />', $page);

?>
