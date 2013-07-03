<?php
header( 'charset:UTF-8' );

if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_mutualisation_dist() {
	global $auteur_session;
	$ustart = memory_get_peak_usage(true);
	$timestart=microtime(true);
	$memory_limit = strtolower(ini_get('memory_limit'));
	
	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin
	if ( ($auteur_session['statut'] != '0minirezo') and ( $_SERVER["REMOTE_ADDR"]!='127.0.0.1'))
		die('pas admin !');

	$lister_sites = charger_fonction('lister_sites','mutualisation');
	$sites = $lister_sites();

	$branche_nom = "spip-" . $GLOBALS['spip_version_branche'] ;
	$version_spip = intval($GLOBALS['spip_version_branche']) ;


	$url_stats = "ecrire/?exec=stats_visites";
	$url_compresseur = "ecrire/?exec=configurer_avancees#formulaire_configurer_compresseur";
	$url_admin_plugin = "ecrire/?exec=admin_plugin";

	if (!file_exists(_DIR_IMG.'mutualiser.png'))
		@copy(find_in_path('mutualiser.png'), _DIR_IMG.'mutualiser.png');

	$titre .= _L(count($sites).' '.'sites mutualis&#233;s <em>(' . _T('version') . ' ' . $GLOBALS['spip_version_base'].')</em>');

	$page .= '<script type="text/javascript">
	//<![CDATA[
	var tableau_sites = new Array();
	//]]>
	</script>';

	//$page .= "<div id='trace'></div>" ;
	$page .= "<table style='clear:both;'>
	<thead>
		<tr>
			<td>Site</td>
			<td>Nom</td>
			<td>Admin</td>
			<td id='IMG'>IMG<span class='unite'>&nbsp;(Mo)</span><br />
				<input type='button' name='IMGcalculer' id='IMGcalculer' value='Calculer' onclick='rechercher_tailles(\"IMG\");' /></td>
			<td id='local'>local<span class='unite'>&nbsp;(Mo)</span><br />
				<input type='button' name='localcalculer' id='localcalculer' value='Calculer' onclick='rechercher_tailles(\"local\");' /></td>
			<td id='cache'>cache<span class='unite'>&nbsp;(Mo)</span><br />
				<input type='button' name='cachecalculer' id='cachecalculer' value='Calculer' onclick='rechercher_tailles(\"cache\");' /></td>
			<td title='Popularit&eacute; totale du site'>Stats</td>
			<td>Plugins</td>
			<td>Compression</td>
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
	$liste_plug_compat_base = $liste_plug_compat[2];
	$liste_plug_compat = $liste_plug_compat[0];

	foreach ($sites as $v) {
		$nom_site=$stats=$plugins=$erreur=$version_installee="";

		if (lire_fichier_securise(_DIR_RACINE.$GLOBALS['mutualisation_dir'].'/'.$v.'/tmp/meta_cache.php', $meta)
		AND is_array($meta = @unserialize($meta))
		AND $url = $meta['adresse_site']) {
			$url .= '/';
			$nom_site = sinon(importer_charset($meta['nom_site'], $meta['charset']), $v);
			$stats = intval($meta['popularite_total']);
			if ($cfg = @unserialize($meta['plugin'])) {
				$plugins = array_keys($cfg);
				ksort($plugins);
				foreach ($plugins as $plugin) {
					$lsplugs[strtolower($plugin)][] = $alias[$v];
					$versionplug[strtolower($plugin)] = $cfg[$plugin]['version'];
					// Spip n'est pas un plugin… Mais en fait oui.
					// unset($lsplugs['spip']);
					// unset($versionplug['spip']);
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
			$adminplugin = adminplugin_site($meta, $liste_plug_compat, $liste_plug_compat_base);
			$version_installee = ' <em><small>'.$meta['version_installee'].'</small></em>';
		}
		else {
			$url = 'http://'.$v.'/';
			$erreur = ' <em><small><span class="erreur">Erreur&nbsp;!</span></small></em>';
			$plugins = '-';
		}

		$compression = '';
		if ($meta['auto_compress_css']=='oui')
			$compression .= 'CSS';
		if ($meta['auto_compress_js']=='oui')
			$compression .= ($compression!='') ? '+JS':'JS';
		if ($meta['auto_compress_http']=='oui') 
			$compression .= ($compression!='') ? '+HTTP':'HTTP';
		if ($compression=='')
			$compression = _L('Activer');
		$page .= '<script type="text/javascript">
		//<![CDATA[
		tableau_sites.push(["../../'.$GLOBALS['mutualisation_dir'].'/'.$v.'"]);
		//]]>
		</script>';

		$page .= "<tr class='tr". $nsite % 2 ."'"
			. " style='background-image: url(${url}ecrire/index.php?exec=mutualisation&amp;renouvelle_alea=yo)' id='$alias[$v]'>
			<td style='text-align:right;'><img src='${url}favicon.ico' style='float:left;' />$v$erreur$version_installee</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire</a></td>
			<td><div id='IMG$nsite' class='taille loading'></div></td>
			<td><div id='local$nsite' class='taille loading'></div></td>
			<td><div id='cache$nsite' class='taille loading'></div></td>
			<td style='text-align:right;'><a href='${url}$url_stats'>${stats}</a></td>
			<td>$adminplugin<a href='${url}$url_admin_plugin'>${cntplugins}</a> <small>${plugins}</small></td>
			<td><a href='${url}$url_compresseur'>$compression</a></td>
			<td style='text-align:right;'>".date_creation_repertoire_site($v)."</td>
			</tr>\n";
		$nsite++;
	}
	$page .= "</tbody></table>";


	if ($lsplugs) {
		$nombre_plugins = count($lsplugs) ;
		$page .= "<br /><br /><table style='clear:both;'>
	<thead>
		<tr>
			<td>#</td>
			<td>Plugins utilis&#233;s ($nombre_plugins) </td>
			<td>Version</td>
			<td>Sites</td>
		</tr>
	</thead>
	<tbody>";
		foreach ($lsplugs as $plugin => $c){
			$plnum[count($c)] .= "<tr><td>".count($c)."</td><td>$plugin</td>"
				."<td>".$versionplug[$plugin]."</td><td>".join(', ', ancre_site($c)).'</td></tr>';
		}
		krsort($plnum);
		$page .= join('', $plnum);
		$page .= "</tbody></table>\n";


		$inutile = array();
		$extract = array();
		$list = array();
		
		$ustart_glob = memory_get_peak_usage(true);
		// Ici on est en SPIP 3.
		// En spip 3, avec SVP, on liste les plugins dans des sous-répertoires.
		// Ca peut aller jusqu'a 3 sous-répertoires.
		// On garde l'ancien principe d'un sous-répertoire pour ne pas casser la compat.

		// correspond à plugins/nom_plugin/fichier.xml
		if (glob(_DIR_PLUGINS . '*/{paquet,plugin}.xml',GLOB_BRACE)) {
			foreach (glob(_DIR_PLUGINS . '*/{paquet,plugin}.xml',GLOB_BRACE) as $value) {
				$list[] = $value;
			}
		}
		// correspond à plugins/auto/nom_plugin/fichier.xml
		if (glob(_DIR_PLUGINS . '*/*/{paquet,plugin}.xml',GLOB_BRACE)) {
			foreach (glob(_DIR_PLUGINS . '*/*/{paquet,plugin}.xml',GLOB_BRACE) as $value) {
				$list[] = $value;
			}
		}
		// correspond à plugins/auto/nom_plugin/x.y.z/fichier.xml
		if (glob(_DIR_PLUGINS . '*/*/*/{paquet,plugin}.xml',GLOB_BRACE)) {
			foreach (glob(_DIR_PLUGINS . '*/*/*/{paquet,plugin}.xml',GLOB_BRACE) as $value) {
				$list[] = $value;
			}
		}

		// Ici on va prendre les chemins d'extrusion uniquement, sans distinction du fichier xml
		foreach ($list as $value) {
			$extract[] = str_replace(array('plugin.xml','paquet.xml'), '', $value);
		}
		// On dédoublonne
		$extract = array_unique($extract);
		foreach ($extract as $url) {
			// Et on refait une recherche pour paquet.xml d'abord
			if(glob($url . 'paquet.xml', GLOB_NOSORT)) {
				$result = glob($url . 'paquet.xml', GLOB_NOSORT);		
				$result = $result[0] ;
				// dans paquet.xml on cherche la valeur de l'attribut prefix
				if (preg_match('/prefix="([^"]*)"/i', file_get_contents($result), $r) 
					AND !$lsplugs[strtolower(trim($r[1]))]){
						preg_match('/version="([^"]*)"/i', file_get_contents($result), $n);
						$inutile[] = trim($r[1]) . ' (' . $n[1] . ')';
				}

			} else { // Si pas de paquet.xml, on cherche plugin.xml
				$result = glob($url . 'plugin.xml', GLOB_NOSORT);		
				$result = $result[0] ;
				// là, on reprend l'ancien code. On cherche la valeur de la balise prefix
				if (preg_match(',<prefix>([^<]+),ims', file_get_contents($result), $r)
					AND !$lsplugs[strtolower(trim($r[1]))]){
						preg_match(',<version>([^<]+),ims', file_get_contents($result), $n);
						$inutile[] = trim($r[1]) . ' (' . $n[1] . ')';
				}
			}
		}
		$uend_glob = memory_get_peak_usage(true);
			

		$inutile = array_map('mb_strtolower', $inutile);
		sort($inutile);


		if ($inutile) {
			$nombre_plugins_inutiles =count($inutile) ;
			$page .= "<p><strong>"._L('Plugins inutilis&#233;s :')."</strong> ".join(', ', $inutile).".<br />";
			$page .= "<em>Soit " . $nombre_plugins_inutiles . _L(' plugins inutilis&#233;s') . ".</em></p>";
		}
	}

	$page .= '<div style="text-align:center;"><img src="'
		. find_in_path('mutualisation/mutualiser.png')
		. '" alt="" /></div>';

	$page = minipres($titre, $page);
	
	$page = str_replace('</head>', '
		<link rel="stylesheet" type="text/css" href="../mutualisation/mutualisation.css" />
		<script src="../prive/javascript/jquery.js" type="text/javascript"></script>
		<script src="../mutualisation/mutualisation_tailles.js" type="text/javascript"></script>
		<script src="../mutualisation/mutualisation_toolbar.js" type="text/javascript"></script>
		</head>
		', $page);

	$uend = memory_get_peak_usage(true);
	$udiff = $uend - $ustart;
	$udiff_glob = $uend_glob - $ustart_glob ;
	$timeend=microtime(true);
	$time=$timeend-$timestart;
	$page_load_time = number_format($time, 3);

	if (isset($_GET['toolbar']) AND $_GET['toolbar'] == 1) {
		$debug_toolbar = "<div class='toolbar'>\n";

		$debug_toolbar .= "<div class='toolbar-block'>\n";
		$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-php_info'></i></div>\n" ;
		$debug_toolbar .= "<div class='toolbar-info'>\n" ;
		$debug_toolbar .= "<div class='toolbar-info-element'><b>SPIP</b> <span>" . $GLOBALS['spip_version_branche'] . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>PHP</b> <span>" . phpversion() . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Mémoire allouée</b> <span>" . $memory_limit . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Serveur</b> <span>" . $_SERVER["SERVER_SOFTWARE"] . "</span></div>\n";
		$debug_toolbar .= "</div></div>\n" ;

		$debug_toolbar .= "<div class='toolbar-block'>\n";
		$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-plugins'></i><span>". ($nombre_plugins_inutiles + $nombre_plugins) ." plugins</span></div>\n" ;
		$debug_toolbar .= "<div class='toolbar-info'>\n" ;
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Utilisés</b> <span>" . $nombre_plugins . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Inutilisés</b> <span>" . $nombre_plugins_inutiles . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Total</b> <span>" . ($nombre_plugins_inutiles + $nombre_plugins) . "</span></div>\n";
		$debug_toolbar .= "</div></div>\n" ;

		$debug_toolbar .= "<div class='toolbar-block'>\n";
		$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-memory'></i> <span>". memoryUsage($udiff) . "</span></div>\n" ;
		$debug_toolbar .= "<div class='toolbar-info'>\n" ;
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Mémoire :</b></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Au début</b> <span>" . memoryUsage($ustart) . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>À la fin</b> <span>" . memoryUsage($uend) . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Différence</b> <span>" . memoryUsage($udiff) . "</span></div>\n";
		$debug_toolbar .= "</div></div>\n" ;

		$debug_toolbar .= "<div class='toolbar-block'>\n";
		$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-time'></i> <span>". $page_load_time . " s</span></div>\n" ;
		$debug_toolbar .= "<div class='toolbar-info'>" ;
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Début du script</b> <span>" . date("H:i:s", $timestart) . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Fin du script</b> <span>" . date("H:i:s", $timeend) . "</span></div>\n";
		$debug_toolbar .= "<div class='toolbar-info-element'><b>Temps d'exécution</b> <span>" . $page_load_time . " s</span></div>\n";
		$debug_toolbar .= "</div></div>\n" ;

		$debug_toolbar .= "</div>\n" ;

		$page = str_replace('</body>', $debug_toolbar . "\n </body>", $page);

	}
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

function adminplugin_site($meta, $liste_plug_compat, $liste_plug_compat_base) {
	if ($cfg = @unserialize($meta['plugin'])) {
		$plugins = array_keys($cfg);
		ksort($plugins);
		foreach ($plugins as $plugin) {
			$vplugin_base = $meta[strtolower($plugin).'_base_version'];
			$nouvelle_version_plugin_base = $liste_plug_compat_base[$liste_plug_compat[$plugin]['dir_type']][$liste_plug_compat[$plugin]['dir']]['version_base'];
			if ($cfg[$plugin]['version'] != $liste_plug_compat[$plugin]['version']
			AND !is_null($liste_plug_compat[$plugin]['version'])
			AND ($vplugin_base != $nouvelle_version_plugin_base) ) {
				$secret = $meta['version_installee'].'-'.$meta['popularite_total'];
				$secret = md5($secret);
				$vplugin = $vplugin_base . '&rarr;' . $nouvelle_version_plugin_base;
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

// faire une ancre vers le tableau des sites en haut de page
function ancre_site($c) {
	foreach ($c as $key => $value) {
		$c[$key] = "<a href='#$value'>" . $value . "</a>";
	}
	return $c;
}

function memoryUsage($bytes) {
        $bytes = (int) $bytes;

        if ($bytes > 1024*1024) {
            return round($bytes/1024/1024, 2).' MB';
        } elseif ($bytes > 1024) {
            return round($bytes/1024, 2).' KB';
        }

        return $bytes . ' B';
}

?>