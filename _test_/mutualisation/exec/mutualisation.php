<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_mutualisation_dist() {
	global $auteur_session;

	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin
	if ( ($auteur_session['statut'] != '0minirezo') and ( $_SERVER["REMOTE_ADDR"]!='127.0.0.1'))
		die('pas admin !');

	$sites = array();
	foreach(preg_files('../sites/', '.*/config/connect.php') as $s) {
		$sites[] = preg_replace(',^\.\./sites/(.*)/config/connect.php,', '\1', $s);
	}
	sort($sites);

	$titre = _L(count($sites).' '.'sites mutualis&#233;s');

	$page = '';
	
	$page .= '<div style="text-align:right">'._T('version')
		. ' ' . $GLOBALS['spip_version'].'</div>';


	$page .= "<table>
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
			$stats = intval($meta['popularite_total']);
			if ($plugins = @unserialize($meta['plugin']))
				$plugins = strtolower(join(', ', array_keys($plugins)));
			else
				$plugins = '';

			// S'il faut upgrader, creer un bouton qui permettra
			// de faire la mise a jour directement depuis le site maitre
			// Pour cela, on cree un bouton avec un secret, que mutualiser.php
			// va intercepter (pas terrible ?)
			$erreur = test_upgrade_site($meta);
		}
		else {
			$url = 'http://'.$v.'/';
			$erreur = ' (erreur!)';
		}
		$page .= "<tr class='tr". $nsite % 2 ."'>
			<td>$v$erreur</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire/</a></td>
			<td><a href='${url}ecrire/index.php?exec=statistiques_visites'>${stats}</a></td>
			<td><a href='${url}ecrire/index.php?exec=admin_plugin'>plugins</a>: ${plugins}</td>
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


function test_upgrade_site($meta) {
	if ($GLOBALS['spip_version']
	!= str_replace(',','.',$meta['version_installee'])) {
		$secret = $meta['version_installee'].'-'.$meta['alea_ephemere'];
		$secret = md5($secret);
		return <<<EOF
<form action='$meta[adresse_site]/ecrire/index.php?exec=mutualisation' method='post'>
<input type='hidden' name='secret' value='$secret' />
<input type='hidden' name='exec' value='mutualisation' />
<input type='hidden' name='upgrade' value='oui' />
<input type='submit' value='upgrade site' />
</form>
EOF;
	}
}

?>
