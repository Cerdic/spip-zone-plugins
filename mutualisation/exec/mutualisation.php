<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_mutualisation_dist() {
	global $auteur_session;

	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin
	if ( ($auteur_session['statut'] != '0minirezo') and ( $_SERVER["REMOTE_ADDR"]!='127.0.0.1'))
		die('pas admin !');

	$lister_sites = charger_fonction('lister_sites','mutualisation');
	$sites = $lister_sites();


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

	# aliases pour l'affichage court
	$alias = array();
	foreach ($sites as $v) {
		$redux = preg_replace(',^www\.|\..*$,', '', $v);
		if (!in_array($redux, $alias))
			$alias[$v] = $redux;
		else
			$alias[$v] = $v;
	}
	
	// Recuperer la liste des plugins connus de l'instance SPIP en cours (celle qui est appelee par ecrire/?exec=mutualisation)
	include_spip('inc/plugin');
	$liste_plug = liste_plugin_files();
	$liste_plug_compat = liste_plugin_valides($liste_plug);
	$liste_plug_compat = $liste_plug_compat[0];
	
	foreach ($sites as $v) {
		$nom_site=$stats=$plugins=$erreur=$version_installee="";

		if (lire_fichier_securise(_DIR_RACINE.$GLOBALS['mutualisation_dir'].'/'.$v.'/tmp/meta_cache.php', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon($meta['nom_site'], $v);
			$stats = intval($meta['popularite_total']);
			if ($cfg = @unserialize($meta['plugin'])) {
				$plugins = array_keys($cfg);
				ksort($plugins);
				foreach ($plugins as $plugin) {
					$lsplugs[strtolower($plugin)][] = $alias[$v];
					$versionplug[strtolower($plugin)] = $cfg[$plugin]['version'];
				}
				$cntplugins = count($plugins);
				$plugins = strtolower(join(', ', $plugins));
			} else
				$plugins = '-';

			// S'il faut upgrader, creer un bouton qui permettra
			// de faire la mise a jour directement depuis le site maitre
			// Pour cela, on cree un bouton avec un secret, que mutualiser.php
			// va intercepter (pas terrible ?)
			$erreur = test_upgrade_site($meta);
			$adminplugin = adminplugin_site($meta, $liste_plug_compat);
			$version_installee = ' <em><small>'.$meta['version_installee'].'</small></em>';
		}
		else {
			$url = 'http://'.$v.'/';
			$erreur = ' <em><small><span class="erreur">Erreur&nbsp;!</span></small></em>';
			$plugins = '-';
		}
		$page .= "<tr class='tr". $nsite % 2 ."'"
			. " style='background-image: url(${url}ecrire/index.php?exec=mutualisation&amp;renouvelle_alea=yo)'>
			<td style='text-align:right;'>$v$erreur$version_installee</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire</a></td>
			<td style='text-align:right;'><a href='${url}ecrire/index.php?exec=statistiques_visites'>${stats}</a></td>
			<td>$adminplugin<a href='${url}ecrire/index.php?exec=admin_plugin'>${cntplugins}</a> <small>${plugins}</small></td>
			<td style='text-align:right;'>".date_creation_repertoire_site($v)."</td>
			</tr>\n";
		$nsite++;
	}
	$page .= "</tbody></table>";

	if ($lsplugs) {
		$page .= "<br /><br /><table style='clear:both;'>
	<thead>
		<tr>
			<td>#</td>
			<td>Plugins utilis&#233;s</td>
			<td>Version</td>
			<td>Sites</td>
		</tr>
	</thead>
	<tbody>";
		foreach ($lsplugs as $plugin => $c)
			$plnum[count($c)] .= "<tr><td>".count($c)."</td><td>$plugin</td>"
				."<td>".$versionplug[$plugin]."</td><td>".join(', ', $c).'</td></tr>';
		krsort($plnum);
		$page .= join('', $plnum);
		$page .= "</tbody></table>\n";


		$inutile = array();
		foreach ( glob(_DIR_PLUGINS.'*/plugin.xml') as $pl) {
			if (preg_match(',<prefix>([^<]+),ims', file_get_contents($pl), $r)
			AND !$lsplugs[strtolower(trim($r[1]))])
				$inutile[] = trim($r[1]);
		}
		if ($inutile) {
			$page .= "<p>"._L('Plugins inutilis&#233;s :')." ".join(', ', $inutile)."</p>";
		}
	}

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
		$secret = $meta['version_installee'].'-'.$meta['popularite_total'];
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

function adminplugin_site($meta, $liste_plug_compat) {
	if ($cfg = @unserialize($meta['plugin'])) {
		$plugins = array_keys($cfg);
		ksort($plugins);
		foreach ($plugins as $plugin) {
			if ($cfg[$plugin]['version'] <> $liste_plug_compat[$plugin]['version']) {
				$secret = $meta['version_installee'].'-'.$meta['popularite_total'];
				$secret = md5($secret);
				$vplugin = $cfg[$plugin]['version'] . '&rarr;' . $liste_plug_compat[$plugin]['version'];
				return <<<EOF
<form action='$meta[adresse_site]/ecrire/index.php?exec=mutualisation' method='post' class='upgrade' target='_blank'>
<div>
<input type='hidden' name='secret' value='$secret' />
<input type='hidden' name='exec' value='mutualisation' />
<input type='hidden' name='upgradeplugins' value='oui' />
<input type='submit' value='Upgrade plugins ($plugin $vplugin)' />
</div>
</form>
EOF;
			}
		}
	} 
	return '';
}


function date_creation_repertoire_site ($v) {
	return (date("d/M/y", @filectime('../'.$GLOBALS['mutualisation_dir'].'/'.$v."/config/connect.php"))) ;	
}

// lister les sites qui ont des sites/xx/config/connect.php
// avec 'connect.php' ne changeant pas de nom
function mutualisation_lister_sites_dist() {
	$sites = array();
	foreach(glob('../'.$GLOBALS['mutualisation_dir'].'/*/config/connect.php') as $s) {
		$sites[] = preg_replace(',^\.\./'.$GLOBALS['mutualisation_dir'].'/(.*)/config/connect.php,', '\1', $s);
	}
	sort($sites);
	return $sites;
}

/* autre exemple pour ceux qui mettent tous leurs fichiers de connexion
 * dans /config/connect/xx.php
 * fonction a mettre dans mes_options.php ou dans mutualisations/lister_sites.php
 */
/*
function mutualisation_lister_sites() {
	$sites = array();
	if (is_dir(_DIR_CONNECT)) {
		if ($dh = @opendir(_DIR_CONNECT)) {
			while (($file = readdir($dh)) !== false) {
				if (substr($file,-4)=='.php') {
					$sites[] = substr($file,0,-4);
				}
			}
		}
	}
	sort($sites);
	return $sites;
}
*/
?>