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
			// on crée une variable pour l'alias du site qu'on pourra par exemple ajouter en id à la ligne correspondante au site.
			// Il faudra trouver une astuce pour créer une ancre dans le tableau des plugins utilisés.
			$alias_site = $alias[$v] ;
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
			. " style='background-image: url(${url}ecrire/index.php?exec=mutualisation&amp;renouvelle_alea=yo)' id='$alias_site'>
			<td style='text-align:right;'><img src='${url}favicon.ico' style='float:left;'>$v$erreur$version_installee</td>
			<td><a href='${url}'>".typo($nom_site)."</a></td>
			<td><a href='${url}ecrire/'>ecrire</a></td>
			<td><div id='IMG$nsite' class='taille loading'></div></td>
			<td><div id='local$nsite' class='taille loading'></div></td>
			<td><div id='cache$nsite' class='taille loading'></div></td>
			<td style='text-align:right;'><a href='${url}ecrire/index.php?exec=statistiques_visites'>${stats}</a></td>
			<td>$adminplugin<a href='${url}ecrire/index.php?exec=admin_plugin'>${cntplugins}</a> <small>${plugins}</small></td>
			<td><a href='${url}ecrire/index.php?exec=config_fonctions#configurer-compresseur'>$compression</a></td>
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
	$site = array();
		foreach ($lsplugs as $plugin => $c){
			$plnum[count($c)] .= "<tr><td>".count($c)."</td><td>$plugin</td>"
				."<td>".$versionplug[$plugin]."</td><td>".join(', ', $c).'</td></tr>';
		}
		krsort($plnum);
		$page .= join('', $plnum);
		$page .= "</tbody></table>\n";


		$inutile = array();
		$extract = array();
		$list = array();
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
					AND !$lsplugs[strtolower(trim($r[1]))])
						$inutile[] = trim($r[1]);
			} else { // Si pas de paquet.xml, on cherche plugin.xml
				$result = glob($url . 'plugin.xml', GLOB_NOSORT);		
				$result = $result[0] ;
				// là, on reprend l'ancien code. On cherche la valeur de la balise prefix
				if (preg_match(',<prefix>([^<]+),ims', file_get_contents($result), $r)
					AND !$lsplugs[strtolower(trim($r[1]))])
						$inutile[] = trim($r[1]);
			}
		}

		if ($inutile) {
			$page .= "<p>"._L('Plugins inutilis&#233;s :')." ".join(', ', $inutile)."</p>";
		}
	}

	$page .= '<div style="text-align:center;"><img src="'
		. find_in_path('mutualisation/mutualiser.png')
		. '" alt="" /></div>';

	$page = minipres($titre, $page);
	
	$page = str_replace('</head>', '
		<style type="text/css">
		a {color:#5a3463;}
		table {border-collapse: collapse; border: 1px solid #999; width: 100%;}
		tr {vertical-align:top;border: 1px solid #999;}
		.tr0 {background-color:#ddded5}
		thead tr {font-weight:bold;background-color:#333;color:#fff;}
		thead tr input {font-weight:normal;font-size:0.9em;}
		thead tr .unite {font-weight:normal;font-size:0.9em;}
		td {text-align:left;border-left: 1px solid #ccc;}
		td em {color:#aaa;}
		#minipres{width:auto;}
		.upgrade {text-align: center; padding:0 .5em; display:inline;}
		.upgrade div {display:inline;}
		.upgrade input { border: 2px solid red;color:red; background-color:#fff; font-weight:bold;}
		.erreur {color:red;font-weight:bold;}
		.taille {text-align: right;}
		.loading {background: url(../mutualisation/images/loading.gif) left center no-repeat}
		</style>
		<script src="../prive/javascript/jquery.js" type="text/javascript"></script>
		<script src="../mutualisation/mutualisation_tailles.js" type="text/javascript"></script>
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
?>