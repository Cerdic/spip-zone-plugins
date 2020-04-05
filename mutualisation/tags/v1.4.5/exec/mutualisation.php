<?php

header('charset:UTF-8');

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function exec_mutualisation_dist() {
	$ustart = memory_get_peak_usage(true);
	$timestart = microtime(true);
	$memory_limit = strtolower(ini_get('memory_limit'));

	$plnum = $lsplugs = $versionplug = array();
	$adminplugin = $compression = '';
	$cntplugins = 0;

	include_spip('inc/minipres');
	include_spip('inc/filtres');

	// pas admin ? passe ton chemin
	if (($GLOBALS['visiteur_session']['statut'] != '0minirezo') and ($_SERVER['REMOTE_ADDR'] != '127.0.0.1')) {
		die('pas admin !');
	}

	$lister_sites = charger_fonction('lister_sites', 'mutualisation');
	$sites = $lister_sites();

	$branche_nom = 'spip-' . $GLOBALS['spip_version_branche'];
	$version_spip = intval($GLOBALS['spip_version_branche']);

	$url_stats = 'ecrire/?exec=stats_visites';
	$url_compresseur = 'ecrire/?exec=configurer_avancees#formulaire_configurer_compresseur';
	$url_admin_plugin = 'ecrire/?exec=admin_plugin';
	$url_admin_vider = 'ecrire/?exec=admin_vider';

	if (!file_exists(_DIR_IMG . 'mutualiser.png')) {
		@copy(find_in_path('mutualiser.png'), _DIR_IMG . 'mutualiser.png');
	}

	$titre = _L(count($sites) . ' ' . 'sites mutualis&#233;s <em>(' . _T('version') . ' ' . $GLOBALS['spip_version_base'] . ')</em>');

	//$page .= "<div id='trace'></div>" ;
	$page = "<table class='sites'>
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
            <td>Plugins<br />
                <input type='button' name='pluginsupgrade' id='pluginsupgrade' value='Upgrader tout' onclick='plugins_upgrade();' /></td>
            <td>Compression</td>
            <td title=\"Configurations particulières\">Config</td>
            <td>Date</td>
        </tr>
    </thead>
    <tbody>";
	$page .= '<script type="text/javascript">
        //<![CDATA[
        tableau_sites = new Array();
        tableau_upgrade = new Array();
        //]]>
        </script>
        ';

	$nsite = 1;

	# aliases pour l'affichage court
	$alias = array();
	foreach ($sites as $v) {
		$redux = preg_replace(',^www\.|\..*$,', '', $v);
		if (!in_array($redux, $alias)) {
			$alias[$v] = $redux;
		} else {
			$alias[$v] = $v;
		}
	}

	// Recuperer la liste des plugins connus de l'instance SPIP en cours (celle qui est appelee par ecrire/?exec=mutualisation)
	include_spip('inc/plugin');
	$liste_plug = liste_plugin_files();
	$liste_plug_compat = liste_plugin_valides($liste_plug);
	$liste_plug_compat_base = $liste_plug_compat[1];
	// echo '<pre>';
	// var_dump($liste_plug_compat_base);
	// echo '</pre>';
	$liste_plug_compat = $liste_plug_compat[0];

	foreach ($sites as $v) {
		$nom_site = $stats = $plugins = $erreur = $version_installee = '';

		if (lire_fichier_securise(_DIR_RACINE . $GLOBALS['mutualisation_dir'] . '/' . $v . '/tmp/meta_cache.php',
				$meta) and is_array($meta = @unserialize($meta)) and $url = $meta['adresse_site']
		) {
			$url .= '/';
			$nom_site = sinon(importer_charset($meta['nom_site'], $meta['charset']), $v);
			$popularite_total = (isset($meta['popularite_total']) ? $meta['popularite_total'] : 0);
			$stats = intval($popularite_total);
			if ($cfg = @unserialize($meta['plugin'])) {
				$plugins = array_keys($cfg);
				ksort($plugins);
				foreach ($plugins as $key => $plugin) {
					if ((strtolower($plugin) == 'php') OR (trim(substr(strtolower($plugin), 0, 4)) == 'php:')) {
						unset($plugins[$key]);
					} else {
						$lsplugs[strtolower($plugin)][] = $alias[$v];
						$versionplug[strtolower($plugin)] = $cfg[$plugin]['version'];
						// Spip n'est pas un plugin… Mais en fait oui.
						// unset($lsplugs['spip']);
						// unset($versionplug['spip']);
					}
				}
				$cntplugins = count($plugins);
				$plugins = strtolower(implode(', ', $plugins));
			} else {
				$plugins = '-';
			}

			// S'il faut upgrader, creer un bouton qui permettra
			// de faire la mise a jour directement depuis le site maitre
			// Pour cela, on cree un bouton avec un secret, que mutualiser.php
			// va intercepter (pas terrible ?)
			$erreur = test_upgrade_site($meta);
			$adminplugin = adminplugin_site($meta, $liste_plug_compat, $liste_plug_compat_base);
			$version_installee = ' <em><small>' . $meta['version_installee'] . '</small></em>';
		} else {
			$url = 'http://' . $v . '/';
			$erreur = ' <em><small><span class="erreur">Erreur&nbsp;!</span></small></em>';
			$plugins = '-';
		}

		if (is_array($meta)) {
			$compression = '';
			if ($meta['auto_compress_css'] == 'oui') {
				$compression .= 'CSS';
			}
			if ($meta['auto_compress_js'] == 'oui') {
				$compression .= ($compression != '') ? '+JS' : 'JS';
			}
			if ($meta['auto_compress_http'] == 'oui') {
				$compression .= ($compression != '') ? '+HTTP' : 'HTTP';
			}
			if ($compression == '') {
				$compression = _L('Activer');
			}
			if (isset($GLOBALS['mutualisation_afficher_config'])) {
				$configs = explode(",", $GLOBALS['mutualisation_afficher_config']);
				$configsparticulieres = '';
				foreach ($configs as $config) {

					$configsparticulieres .= '<em><small>' . $config . ':</small></em> ' . lire_config_distante($config, $meta) . "<br />\n";
				}
			}
		}
		$page .= '<script type="text/javascript">
        //<![CDATA[
        tableau_sites.push(["../' . $GLOBALS['mutualisation_dir'] . '/' . $v . '"]);
        //]]>
        </script>
        ';

		$page .= "<tr class='tr" . $nsite % 2 . "' style='background-image: url(" . $url . "ecrire/index.php?exec=mutualisation&amp;renouvelle_alea=yo)' id='$alias[$v]'>\n
            <td class='text-right'><img src='" . $url . "favicon.ico' class='favicon' />$v$erreur$version_installee</td>\n
            <td><a href='" . $url . "'>" . typo($nom_site) . "</a></td>\n
            <td><a href='" . $url . "ecrire/'>ecrire</a><br />
                <a href='" . $url . "$url_admin_plugin'>plugins</a><br />
                <a href='" . $url . "$url_admin_vider'>cache</a></td>
            <td><div id='IMG$nsite' class='taille loading'></div></td>\n
            <td><div id='local$nsite' class='taille loading'></div></td>\n
            <td><div id='cache$nsite' class='taille loading'></div></td>\n
            <td class='text-right'><a href='" . $url . "$url_stats'>${stats}</a></td>\n
            <td>$adminplugin<div class='liste-plugins'><a href='" . $url . "$url_admin_plugin'>${cntplugins}</a> <small>${plugins}</small></div></td>\n
            <td><a href='" . $url . "$url_compresseur'>$compression</a></td>\n
            <td>$configsparticulieres</td>\n
            <td class='text-right'>" . date_creation_repertoire_site($v) . "</td>\n
            </tr>\n";
		++$nsite;
	}
	$page .= '</tbody></table>';

	// On liste ici tous les plugins-dist de la mutu.
	// Ça sera calculé une seule fois pour toute et réutilisé dans la fonction plus loin.
	$list_dist = array();
	// correspond à plugins-dist/nom_plugin/paquet.xml
	if ($les_paquets = glob(_DIR_PLUGINS_DIST . '*/paquet.xml', GLOB_NOSORT)) {
		foreach ($les_paquets as $value) {
			if (preg_match('/prefix="([^"]*)"/i', file_get_contents($value), $r)) {
				$list_dist[] = strtolower(trim($r[1]));
			}
		}
	}

	if ($lsplugs) {
		$nombre_plugins = count($lsplugs);
		$page .= "<br /><br /><table class='plugins'>
    <thead>\n
        <tr>
            <td class='nombre'>#</td>
            <td class='prefixe'>Plugins utilis&#233;s ($nombre_plugins) </td>
            <td class='dist'>Plugins-dist</td>
            <td class='version'>Version</td>
            <td class='liste'>Sites</td>
        </tr>\n
    </thead>
    <tbody>";
		foreach ($lsplugs as $plugin => $c) {
			$ligne = "<tr class='plugin $plugin'>\n<td class='nombre'>"
				. count($c)
				. "</td>\n<td class='prefixe'>$plugin</td>\n"
				. '<td class=\'dist\'>'
				. pluginDist($list_dist, $plugin)
				. "</td>\n<td class='version'>"
				. $versionplug[$plugin]
				. "</td>\n<td class='liste'>"
				. implode(' ', ancre_site($c))
				. '</td>'
				. "\n"
				. '</tr>'
				. "\n";
			if (isset($plnum[intval(count($c))])) {
				$plnum[intval(count($c))] .= $ligne;
			} else {
				$plnum[intval(count($c))] = $ligne;
			}
		}
		krsort($plnum);
		$page .= implode('', $plnum);
		$page .= "</tbody>\n</table>\n";

		$inutile = $extract = $list = array();
		// On crée une variable ici qui regardera les particularités des fichiers xml d'un plugin.
		// Si à l'avenir on change de terminologie de fichier xml, il suffira de l'ajouter dans un nouvel array()
		$cfg = array(
			array('fn' => 'paquet.xml', 'pre' => '/prefix="([^"]*)"/i', 'ver' => '/version="([^"]*)"/i'),
			array('fn' => 'plugin.xml', 'pre' => ',<prefix>([^<]+),ims', 'ver' => ',<version>([^<]+),ims')
		);

		$ustart_glob = memory_get_peak_usage(true);

		// Ici on est en SPIP 3.
		// En spip 3, avec SVP, on liste les plugins dans des sous-répertoires.
		// Ca peut aller jusqu'a 3 sous-répertoires.
		// On garde l'ancien principe d'un sous-répertoire pour ne pas casser la compat.

		// Utiliser la classe si elle existe (PHP 5.3+)
		$dir = _DIR_PLUGINS;
		if (class_exists('FilesystemIterator') and is_dir(_DIR_PLUGINS)) {
			$dir_it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
			$it = new RecursiveIteratorIterator($dir_it, RecursiveIteratorIterator::SELF_FIRST);

			foreach ($it as $path => $fo) {
				if (!$fo->isDir()) {
					continue;
				}

				$path .= '/';

				foreach ($cfg as $k => $v) {
					if (file_exists($path . $v['fn'])) {
						$res = processConfig($cfg[$k], $lsplugs, $path);
						if (false !== $res) {
							$inutile[] = $res;
						}
						break;
					}
				}
			}
		} else {
			// Pour php < 5.3
			// correspond à plugins/nom_plugin/fichier.xml
			if ($les_paquets = glob(_DIR_PLUGINS . '*/{paquet,plugin}.xml', GLOB_BRACE)) {
				foreach ($les_paquets as $value) {
					$list[] = $value;
				}
			}
			// correspond à plugins/auto/nom_plugin/fichier.xml
			if ($les_paquets = glob(_DIR_PLUGINS . '*/*/{paquet,plugin}.xml', GLOB_BRACE)) {
				foreach ($les_paquets as $value) {
					$list[] = $value;
				}
			}
			// correspond à plugins/auto/nom_plugin/x.y.z/fichier.xml
			if ($les_paquets = glob(_DIR_PLUGINS . '*/*/*/{paquet,plugin}.xml', GLOB_BRACE)) {
				foreach ($les_paquets as $value) {
					$list[] = $value;
				}
			}

			// Ici on va prendre les chemins d'extrusion uniquement, sans distinction du fichier xml
			foreach ($list as $value) {
				$extract[] = str_replace(array('plugin.xml', 'paquet.xml'), '', $value);
			}
			// On dédoublonne
			$extract = array_unique($extract);
			foreach ($extract as $url) {
				// Et on refait une recherche pour paquet.xml d'abord
				if ($result = glob($url . 'paquet.xml', GLOB_NOSORT)) {
					$result = $result[0];
					// dans paquet.xml on cherche la valeur de l'attribut prefix
					if (preg_match('/prefix="([^"]*)"/i', file_get_contents($result),
							$r) and !$lsplugs[strtolower(trim($r[1]))]
					) {
						preg_match('/version="([^"]*)"/i', file_get_contents($result), $n);
						$inutile[] = trim($r[1]) . ' (' . $n[1] . ')';
					}
				} else {
					// Si pas de paquet.xml, on cherche plugin.xml
					$result = glob($url . 'plugin.xml', GLOB_NOSORT);
					$result = $result[0];
					// là, on reprend l'ancien code. On cherche la valeur de la balise prefix
					if (preg_match(',<prefix>([^<]+),ims', file_get_contents($result),
							$r) and !$lsplugs[strtolower(trim($r[1]))]
					) {
						preg_match(',<version>([^<]+),ims', file_get_contents($result), $n);
						$inutile[] = trim($r[1]) . ' (' . $n[1] . ')';
					}
				}
			}
		}

		$uend_glob = memory_get_peak_usage(true);
		$inutile = array_map('mb_strtolower', $inutile);
		sort($inutile);

		if ($inutile) {
			$nombre_plugins_inutiles = count($inutile);
			$page .= "<div class='inutilises'>\n<p><strong>" . _L('Plugins inutilis&#233;s :') . '</strong> ' . implode(', ',
					$inutile) . '.<br />';
			$page .= '<em>Soit ' . $nombre_plugins_inutiles . _L(' plugins inutilis&#233;s') . ".</em></p>\n</div>";
		}
	}

	$page .= '<div class="logo_mutualisation"><img src="' . find_in_path('mutualisation/mutualiser.png') . '" alt="" /></div>';

	$page = minipres($titre, $page);

	$page = str_replace('</head>', '
        <link rel="stylesheet" type="text/css" href="../mutualisation/mutualisation.css" />
        <script src="../prive/javascript/jquery.js" type="text/javascript"></script>
        <script src="../mutualisation/mutualisation_tailles.js" type="text/javascript"></script>
        <script src="../mutualisation/mutualisation_upgrade.js" type="text/javascript"></script>
        <script src="../mutualisation/mutualisation_toolbar.js" type="text/javascript"></script>
        </head>
        ', $page);

	$uend = memory_get_peak_usage(true);
	$udiff = $uend - $ustart;
	$udiff_glob = $uend_glob - $ustart_glob;
	$timeend = microtime(true);
	$time = $timeend - $timestart;
	$page_load_time = number_format($time, 3);

	// On génère le contenu de notre toolbar.
	$debug_toolbar = "<div class='toolbar'>\n";

	$debug_toolbar .= "<div class='toolbar-block'>\n";
	$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-php_info'></i></div>\n";
	$debug_toolbar .= "<div class='toolbar-info'>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>SPIP</b> <span>" . $GLOBALS['spip_version_branche'] . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>PHP</b> <span>" . phpversion() . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Mémoire allouée</b> <span>" . $memory_limit . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Serveur</b> <span>" . $_SERVER['SERVER_SOFTWARE'] . "</span></div>\n";
	$debug_toolbar .= "</div></div>\n";

	$debug_toolbar .= "<div class='toolbar-block'>\n";
	$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-plugins'></i><span>" . ($nombre_plugins_inutiles + $nombre_plugins) . " plugins</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info'>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Utilisés</b> <span>" . $nombre_plugins . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Inutilisés</b> <span>" . $nombre_plugins_inutiles . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Total</b> <span>" . ($nombre_plugins_inutiles + $nombre_plugins) . "</span></div>\n";
	$debug_toolbar .= "</div></div>\n";

	$debug_toolbar .= "<div class='toolbar-block'>\n";
	$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-memory'></i> <span>" . memoryUsage($udiff) . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info'>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Mémoire :</b></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Au début</b> <span>" . memoryUsage($ustart) . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>À la fin</b> <span>" . memoryUsage($uend) . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Différence</b> <span>" . memoryUsage($udiff) . "</span></div>\n";
	$debug_toolbar .= "</div></div>\n";

	$debug_toolbar .= "<div class='toolbar-block'>\n";
	$debug_toolbar .= "<div class='toolbar-icon'><i class='icon-time'></i> <span>" . $page_load_time . " s</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info'>";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Début du script</b> <span>" . date('H:i:s',
			$timestart) . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Fin du script</b> <span>" . date('H:i:s',
			$timeend) . "</span></div>\n";
	$debug_toolbar .= "<div class='toolbar-info-element'><b>Temps d'exécution</b> <span>" . $page_load_time . " s</span></div>\n";
	$debug_toolbar .= "</div></div>\n";

	$debug_toolbar .= "</div>\n";

	$page = str_replace('</body>', $debug_toolbar . "\n </body>", $page);

	echo $page;
}

function test_upgrade_site($meta) {
	if ($GLOBALS['spip_version_base'] != str_replace(',', '.', $meta['version_installee'])) {
		$secret = $meta['version_installee'] . '-' . (isset($meta['popularite_total']) ? $meta['popularite_total'] : '0');
		$secret = md5($secret);
		$adresse_site = isset($meta['adresse_site']) ? $meta["adresse_site"] : '';

		return <<<EOF
<form action='$adresse_site/ecrire/index.php?exec=mutualisation' method='post' class='upgrade' target='_blank'>
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

/**
 * @param array $meta
 * @param array $liste_plug_compat
 * @param array $liste_plug_compat_base
 *
 * @return string
 */
function adminplugin_site($meta, $liste_plug_compat, $liste_plug_compat_base) {
	if ($cfg = @unserialize($meta['plugin'])) {
		$plugins = array_keys($cfg);
		ksort($plugins);
		$repertoires_plugins = array('_DIR_PLUGINS', '_DIR_PLUGINS_DIST', '_DIR_RESTREINT');
		$placeholder = '';
		foreach ($plugins as $plugin) {
			$vplugin_base = $nouvelle_version_plugin_base = $info_plugin = '';
			$vplugin_base = (isset($meta[strtolower($plugin) . '_base_version'])) ? trim($meta[strtolower($plugin) . '_base_version']) : '0.0.0';
			foreach ($repertoires_plugins as $repertoire) {
				if (isset($liste_plug_compat[$repertoire][strtolower($plugin)])) {
					$info_plugin = $liste_plug_compat[$repertoire][strtolower($plugin)];
				}
			}
			$nouvelle_version_plugin_base = (isset($info_plugin['schema'])) ? trim($info_plugin['schema']) : '0.0.0';

			if ((isset($cfg[$plugin]['version']) and isset($info_plugin['version'])) and
				(
					($cfg[$plugin]['version'] != $info_plugin['version'])
					or
					(spip_version_compare($vplugin_base, $nouvelle_version_plugin_base, '<'))
				)
			) {
				$vplugin = $vplugin_base . ' / ' . $cfg[$plugin]['version'] . ' &rarr; ' . $nouvelle_version_plugin_base . ' / ' . $info_plugin['version'];
				$placeholder .= "$plugin $vplugin <br/>";
			}
		}
		if (!empty($placeholder)) {
			return upgrade_placeholder($meta, $placeholder);
		}
		if (defined('_MUTUALISATION_UPGRADE_FORCE')) {
			return upgrade_placeholder($meta);
		}
	}

	return '';
}

function upgrade_placeholder($meta, $buttontxt = 'Upgrade plugins (forcé)') {
	static $id = 0;
	$id++;
	$secret = $meta['version_installee'] . '-' . $meta['secret_du_site'];
	$secret = md5($secret);
	$adresse_site = isset($meta['adresse_site']) ? $meta["adresse_site"] : '';
	$upgrade = '<script type="text/javascript">
	//<![CDATA[
	tableau_upgrade.push(["' . $adresse_site . '/ecrire/?exec=mutualisation&secret=' . $secret . '&upgradeplugins=oui&ajax=oui"]);
	//]]>
	</script>
	';

	return <<<EOF
$upgrade
<form action='$adresse_site/ecrire/?exec=mutualisation' method='get' class='upgrade' target='_blank'>
<div id='upgrade$id' class='taille'>
<input type='hidden' name='secret' value='$secret' />
<input type='hidden' name='exec' value='mutualisation' />
<input type='hidden' name='upgradeplugins' value='oui' />
<button type='submit' value='Upgrade plugins'>$buttontxt</button>
</div>
</form>
EOF;
}

function date_creation_repertoire_site($v) {
	return (date('d/M/y', @filectime('../' . $GLOBALS['mutualisation_dir'] . '/' . $v . '/config/connect.php')));
}

/**
 * lister les sites qui ont des sites/xx/config/connect.php
 * avec 'connect.php' ne changeant pas de nom.
 */
function mutualisation_lister_sites_dist() {
	$sites = array();
	foreach (glob('../' . $GLOBALS['mutualisation_dir'] . '/*/config/connect.php') as $s) {
		$sites[] = preg_replace(',^\.\./' . $GLOBALS['mutualisation_dir'] . '/(.*)/config/connect.php,', '\1', $s);
	}
	sort($sites);

	return $sites;
}

/**
 * autre exemple pour ceux qui mettent tous leurs fichiers de connexion
 * dans /config/connect/xx.php
 * fonction a mettre dans mes_options.php ou dans
 * mutualisations/lister_sites.php.
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
		$c[$key] = "<a href='#$value' class='$value'>" . $value . ',</a> ';
	}

	return $c;
}

function memoryUsage($bytes) {
	$bytes = (int) $bytes;

	if ($bytes > 1024 * 1024) {
		return round($bytes / 1024 / 1024, 2) . ' MB';
	} elseif ($bytes > 1024) {
		return round($bytes / 1024, 2) . ' KB';
	}

	return $bytes . ' B';
}

function pluginDist($array, $plugin) {
	$p = '-';
	if (in_array($plugin, $array)) {
		$p = 'Oui';
	}

	return $p;
}

/**
 * Petite fonction qui va automatiser la recherche de paquet.xml ou plugin.xml
 * quelque soit la profondeur dans l'arborescence.
 */
function processConfig(&$cfg, &$lsplugs, $path) {
	// echo "<!-- Process: " . $path . $cfg['fn'] . "--> \n";
	$data = file_get_contents($path . $cfg['fn']);

	if (1 === preg_match($cfg['pre'], $data, $r) and !isset($lsplugs[strtolower(trim($r[1]))])) {
		preg_match($cfg['ver'], $data, $n);

		return trim($r[1]) . ' (' . $n[1] . ')';
	}

	return false;
}

function lire_config_distante($cfg = '', $meta) {
	$def = null;
	$unserialize = true;
	// lire le stockage sous la forme valeur
	// ou casier/valeur

	// traiter en priorite le cas simple et frequent
	// de lecture direct $meta['truc'], si $cfg ne contient pas "/"
	if ($cfg and strpbrk($cfg, '/') === false) {
		$r = isset($meta[$cfg]) ?
			((!$unserialize
				// ne pas essayer de deserialiser autre chose qu'une chaine
				or !is_string($meta[$cfg])
				// ne pas essayer de deserialiser si ce n'est visiblement pas une chaine serializee
				or strpos($meta[$cfg], ':') === false
				or ($t = @unserialize($meta[$cfg])) === false) ? $meta[$cfg] : $t)
			: $def;

		return $r;
	} else {
		$cfg = explode('/', $cfg);
		$r = @unserialize($meta[$cfg[0]]);
		$r = $r[$cfg[1]];

		return $r;
	}
}
