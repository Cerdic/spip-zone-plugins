<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_mutualisation_dist() {
	global $auteur_session;

	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin
	if ( ($auteur_session['statut'] != '0minirezo') and ( $_SERVER["REMOTE_ADDR"]!='127.0.0.1'))
		die('pas admin !');

	// Dans quel site sommes-nous ?
	$notre_spip = basename(dirname(_DIR_TMP));

	// Si ce n'est pas un site maitre, le dire
	if (defined('_SITES_ADMIN_MUTUALISATION')
	AND !in_array($notre_spip, explode(',',_SITES_ADMIN_MUTUALISATION))) {
		die (_L("Pour acceder a cette page d'admin, veuillez inscrire @site@ dans la constante _SITES_ADMIN_MUTUALISATION", array('site' => $notre_spip)));
	}

	$sites = array();
	foreach(preg_files('../sites/', '.*/config/connect.php') as $s) {
		$sites[] = preg_replace(',^\.\./sites/(.*)/config/connect.php,', '\1', $s);
	}
	sort($sites);

	$titre = _L(count($sites).' '.'sites mutualis&#233;s');

	$page = "<table>
	<thead>
		<tr>
			<td>Site</td>
			<td>Nom</td>
			<td>ecrire</td>
			<td>Stats</td>
		</tr>
	</thead>
	<tbody>";

	$nsite = 1;
	foreach ($sites as $v) {
		if (lire_fichier(_DIR_RACINE.'sites/'.$v.'/tmp/meta_cache.txt', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon($meta['nom_site'], $v);
			$erreur = '';
		}
		else {
			$url = 'http://'.$v.'/';
			$erreur = ' (erreur!)';
		}
		$page .= "<tr class='tr". $nsite % 2 ."'>
			<td>$v$erreur</td>
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

	echo $page;
}

?>
