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
# Code tres tres tres lent !
#	foreach(preg_files('../'.$GLOBALS['mutualisation_dir'].'/', '.*/config/connect.php') as $s) {
#		$sites[] = preg_replace(',^\.\./'.$GLOBALS['mutualisation_dir'].'/(.*)/config/connect.php,', '\1', $s);
#	}
# Code rapide
	$dir = '../'.$GLOBALS['mutualisation_dir'].'/';	
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (filetype($dir . $file) == 'dir') {
					if (file_exists($dir . $file . '/config/connect.php')) $sites[] = $file;
				}
			}
			closedir($dh);
		}
	}	

	sort($sites);

	if (!file_exists(_DIR_IMG.'mutualiser.png'))
		@copy(find_in_path('mutualiser.png'), _DIR_IMG.'mutualiser.png');

	$titre .= _L(count($sites).' '.'sites mutualis&#233;s <em>('._T('version')
		. ' ' . $GLOBALS['spip_version_base'].')</em>');

	$page = '';


	$page .= "<table style='clear:both;'>
	<thead>
		<tr>
			<td>Site</td>
			<td>Nom</td>
			<td>Admin</td>
			<td title='Popularit&eacute; totale du site'>Stats</td>
			<td>Plugins</td>
			<td>Date</td>
		</tr>
	</thead>
	<tbody>";

	$nsite = 1;
	foreach ($sites as $v) {
		$nom_site=$stats=$plugins=$erreur=$version_installee="";

		if (lire_fichier_securise(_DIR_RACINE.$GLOBALS['mutualisation_dir'].'/'.$v.'/tmp/meta_cache.php', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon($meta['nom_site'], $v);
			$stats = intval($meta['popularite_total']);
			if ($plugins = @unserialize($meta['plugin'])) {
				ksort($plugins);
				$plugins = count($plugins)
				. ' <small>'
				. strtolower(join(', ', array_keys($plugins)))
				. '</small>';
			} else
				$plugins = '-';

			// S'il faut upgrader, creer un bouton qui permettra
			// de faire la mise a jour directement depuis le site maitre
			// Pour cela, on cree un bouton avec un secret, que mutualiser.php
			// va intercepter (pas terrible ?)
			$erreur = test_upgrade_site($meta);
			$version_installee = ' <em><small>'.$meta['version_installee'].'</small></em>';
		}
		else {
			$url = 'http://'.$v.'/';
			$erreur = ' <em><small><span class="erreur">Erreur&nbsp;!</span></small></em>';
			$plugins = '-';
		}
		$page .= "<tr class='tr". $nsite % 2 ."'"
			. " style='background-image: url(${url}spip.php?action=cron&amp;renouvelle_alea=yo);'>
			<td style='text-align:right;'>$v$erreur$version_installee</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire</a></td>
			<td style='text-align:right;'><a href='${url}ecrire/index.php?exec=statistiques_visites'>${stats}</a></td>
			<td><a href='${url}ecrire/index.php?exec=admin_plugin'>${plugins}</a></td>
			<td style='text-align:right;'>".date_creation_repertoire_site($v)."</td>
			</tr>\n";
		$nsite++;
	}
	$page .= "</tbody></table>";
	
	$page .= '<div style="text-align:center;"><img src="'
		. _DIR_IMG.'mutualiser.png'
		. '" alt="" /></div>';

	$page = minipres($titre, $page);
	
	$page = str_replace('</head>', '
		<style type="text/css">
		a {color:#5a3463;}
		table {border-collapse: collapse; border: 1px solid #999;}
		tr {vertical-align:top;border: 1px solid #999;}
		.tr0 {background-color:#ddded5}
		thead tr {font-weight:bold;background-color:#333;color:#fff;}
		td {text-align:left;border-left: 1px solid #ccc;}
		td em {color:#aaa;}
		#minipres{width:auto;}
		.upgrade {text-align: center; padding:0 .5em; display:inline;}
		.upgrade div {display:inline;}
		.upgrade input { border: 2px solid red;color:red; background-color:#fff; font-weight:bold;}
		.erreur {color:red;font-weight:bold;}
		</style>
		</head>
		', $page);
		


	echo $page;
}


function test_upgrade_site($meta) {
	if ($GLOBALS['spip_version_base']
	!= str_replace(',','.',$meta['version_installee'])) {
		$secret = $meta['version_installee'].'-'.$meta['alea_ephemere'];
		$secret = md5($secret);
		return <<<EOF
<form action='$meta[adresse_site]/ecrire/index.php?exec=mutualisation' method='post' class='upgrade' target='_blank'>
<div>
<input type='hidden' name='secret' value='$secret' />
<input type='hidden' name='exec' value='mutualisation' />
<input type='hidden' name='upgrade' value='oui' />
<input type='submit' value='Upgrade site' />
</div>
</form>
EOF;
	}
}

function date_creation_repertoire_site ($v) {
	return (date("d/M/y", @filectime('../'.$GLOBALS['mutualisation_dir'].'/'.$v."/config/connect.php"))) ;	
}
?>