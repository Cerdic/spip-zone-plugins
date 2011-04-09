<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/langonet_generer_fichier');

function inc_plugonet_generer($files, $write)
{
  $nb_files = count($files);
  $sep = ($nb_files > 1) ? ";" : "\n";
  $all = $res = array();
  $total = $ko = $ko = 0;
  $valider_xml = charger_fonction('valider', 'xml');
  // est-on en 2.2 ? 
  $infos_xml = charger_fonction('infos_plugin', 'plugins', true) ?
  'plugin2paquet_infos' : charger_fonction('get_infos', 'plugins');
  spip_log("Plugonet: fonction de lecture: $infos_xml");

  foreach($files as $nom)  {
    $old = (basename($nom) == 'plugin.xml');
    if (lire_fichier ($nom, $text))
      $erreurs = $valider_xml($text, false, false, $old ? 'plugin.dtd' : 'paquet.dtd');
    $erreurs = is_array($erreurs) ? $erreurs[1] : $erreurs->err; //2.1 ou 2.2
    foreach ($erreurs as $k => $v) {
      $msg = preg_replace('@<br[^>]*>|:|,@', ' ', $v[0]);
      if ($nb_files > 1) $msg = preg_replace(',\s*[(][^)]*[)],', '', $msg);
      $erreurs[$k] = trim(str_replace("\n", ' ', textebrut($msg)));
      $msg = preg_replace(',<b>[^>]*</b>,', '* ', $msg);
      @++$all[trim(str_replace("\n", '', textebrut($msg)))];
    }
    $msg2 = $nom;
    if ($n = count($erreurs)) {
      $total+=$n;
      $ko++;
      $msg2 .= ' ' . $n . " erreur(s)" . $sep . join($sep, $erreurs);
    }
    $dir = dirname($nom);
    if ($old) {
      if (!$infos = $infos_xml(basename($dir), true, dirname($dir) .'/'))
	$msg2 .= " plugin.xml illisible";
      else {
	// Extraction des balises nom, slogan et description()
	// -- le nom n'est plus traduit si il est en multi
	// -- slogan et description sont transformes en items de langue dans un module a part
	// --> Bonne solution ??? Meme module que celui existant ?
	// Le slogan est vu comme la premiere phrase de la description
	// (Heuristique reprise de SVP)
	$nomplug = preg_replace('@<!--[^-]*-->@', '', $infos['nom']);
	if (strpos($nomplug, '>')) {
		$slogan = $nomplug;
		$nomplug = preg_replace('@</?multi>@', '', $nomplug);
		$nomplug = preg_replace('/[[][^]]*[]]/', '', $nomplug);
		$nomplug = preg_replace('/^\s*(\w+).*$/', '\1', $nomplug);
	} else {
		$slogan = $infos['description'];
	}
	$xml = plugin2paquet($infos, $dir, $nomplug);
	$e = $valider_xml($xml, false, false, 'paquet.dtd');
	$e = is_array($e) ? $e[1] : $e->err; //2.1 ou 2.2
	if ($e)  {
	  $msg2 .=" erreurs en nouveau format: " . count($e) . ". "; 
	  if ($nb_files ==1)  
	    $msg2.= join("\n", array_map('array_shift', $e)) . ".";
	}  else {
	  $msg2 .= " Correct en nouveau format.";
	  if ($write) {
	    if ($modules = plugin2paquet_description($infos['description'], $slogan, $infos['prefix'], $dir))
	      $xml = "\n<!-- svn add " . join(' ', $modules) . " -->" . $xml;
	    if (ecrire_fichier($dir . '/paquet.xml', $xml) OR $echecs)
	      $xml = "\n<!-- svn add paquet.xml -->" . $xml;
	  }
	  $res[$nom]= $xml;
	  $ok++;
	}
      }
    }
    spip_log("Plugonet: $nom : $msg2");
  }
  if ($nb_files > 1)
    $msg2 = "\n---- Statistiques des $total erreurs des $ko fichiers fautifs sur $nb_files ($ok bien reecrits) ----\n";
  asort($all);
  foreach ($all as $k => $v) $all[$k] = sprintf("%4d %s", $v, $k);

  return array($msg2, $all, $res);
}

function plugin2paquet($D, $dir, $nom)
{
	// Extraction des attributs de la balise paquet
	$categorie = $D['categorie'];
	$etat = $D['etat'];
	$lien = $D['lien'];
	$logo = $D['icon'];
	$meta = $D['meta'];
	$prefix = $D['prefix'];
	$version = $D['version'];
	$version_base = $D['version_base'];

	$compatible = '';
	// Si le tableau provient de infos_plugin la compatibilite SPIP est directement accessible
	if (isset($D['compatible']))
		$compatible =  $D['compatible'];
	// Si le tableau provient de get_infos la compatibilite SPIP est incluse dans les necessite
	else {
		foreach($D['necessite'] as $k => $i) {
			$id = isset($i['id']) ? $i['id'] : $i['nom'];
			if ($id AND strtoupper($id) == 'SPIP') {
				$compatible = $i['version'];
				unset($D['necessite'][$k]);
				break;
			}
		}
	}
	
	// Constrution de la balise paquet et de ses attributs
	$paquet_att =
		plugin2paquet_lien($lien, 'documentation') .
		($categorie ? "\n\tcategorie='$categorie'" : '') .
		($compatible ? "\n\tcompatible='$compatible'" : '') .
		($etat ? "\n\tetat='$etat'" : '') .
		($logo ? "\n\tlogo='$logo'" : '') .
		($meta ? "\n\tmeta='$meta'" : '') .
		($prefix ? "\n\tprefix='$prefix'" : '') .
		($version ? "\n\tversion='$version'" : '') .
		($version_base ? "\n\tversion_base='$version_base'" : '');

	$nom = plugin2paquet_texte('nom', $nom, $dir);
	$licence = plugin2paquet_texte('licence', $D['licence'], $dir);
	$auteur = plugin2paquet_texte('auteur', $D['auteur'], $dir);
	
	$chemin = is_array($D['path']) ? plugin2paquet_chemin($D) :'';
	$pipeline = is_array($D['pipeline']) ? plugin2paquet_pipeline($D['pipeline']) :'';
	$necessite = is_array($D['necessite']) ? plugin2paquet_necessite($D) :'';
	$utilise = is_array($D['utilise']) ? plugin2paquet_utilise($D['utilise']) :'';
	$bouton = is_array($D['bouton']) ? plugin2paquet_exec($D, 'bouton') :'';
	$onglet = is_array($D['onglet']) ? plugin2paquet_exec($D, 'onglet') :'';
	
	$renommer = plugin2paquet_implicite($D, 'options', 'options')
	. plugin2paquet_implicite($D, 'fonctions', 'fonctions')
	. plugin2paquet_implicite($D, 'install', 'actions');
	
	return "$renommer<paquet$paquet_att\n>\t$nom$licence$auteur$pipeline$necessite$utilise$bouton$onglet$chemin\n</paquet>\n";
}

// Eliminer les textes superflus dans les liens (raccourcis [XXX->http...])
// et normaliser l'esperluete pour eviter l'erreur d'entite indefinie
function plugin2paquet_lien($url, $nom='lien')
{
	if (!preg_match(',https?://[^]\s]+,', $url, $r)) return '';
	$url = str_replace('&', '&amp;', str_replace('&amp;', '&', $r[0]));
	return "\n\t$nom='$url'";
}

function plugin2paquet_pipeline($D)
{
  $res = '';
  foreach($D as $i) {
    $att = " nom='" . $i['nom'] . "'" .
      (!empty($i['action']) ? (" action='" . $i['action'] . "'") : '') .
      (!empty($i['inclure']) ? (" inclure='" . $i['inclure'] . "'") : '');
    $res .= "\n\t<pipeline$att />";
  }
  return $res ? "\n$res" : '';
}

function plugin2paquet_exec($D, $balise)
{
  $res = '';
  foreach($D[$balise] as $nom => $i) {
    $att = " nom='" . $nom . "'" .
    	" titre='" . $i['titre'] . "'" .
      (empty($i['parent']) ? '' : (" parent='" . $i['parent'] . "'")) .
      (empty($i['icone']) ? '' : (" icone='" . $i['icone'] . "'")) .
      (empty($i['url']) ? '' : (" action='" . $i['url'] . "'")) .
      (empty($i['args']) ? '' :
       (" args='" . str_replace('&', '&amp;', str_replace('&amp;', '&', $i['args'])) . "'"));

    $res .= "\n\t<$balise$att />";
  }
  return $res ? "\n$res" : '';
}

function plugin2paquet_chemin($D)
{
  $res = '';
  foreach($D['path'] as $i) {
    $t = empty($i['type']) ? '' : (" type='" . $i['type'] . "'");
    $p = $i['dir'];
    if (!$t AND (!$p OR $p==='.' OR $p==='./')) continue;
    $res .="\n\t<chemin path='$p'$t />";
  }
  return $res;
}

function plugin2paquet_necessite($D)
{
  $nec = $lib = '';
  foreach($D['necessite'] as $i) {
    $nom = isset($i['id']) ? $i['id'] : $i['nom'];
    $src = plugin2paquet_lien($i['src']);
    $version = empty($i['version']) ? '' : (" version='" . $i['version'] . "'");
    if (preg_match('/^lib:(.*)$/', $nom, $r))
      $lib .= "\n\t<lib nom='$r[1]'$src$version />";
    else $nec .="\n\t<necessite nom='$nom'$version />";
  }
  return $nec . $lib;
}

function plugin2paquet_utilise($D)
{
  $res = '';
  foreach($D as $i) {
        $nom = isset($i['id']) ? $i['id'] : $i['nom'];
	$att = " nom='$nom'" .
	  (!empty($i['version']) ? (" version='" . $i['version'] . "'") : '') .
      plugin2paquet_lien($i['src']);
    $res .="\n\t<utilise$att />";
  }
  return $res;
}

// Passer les lettres accentuees en entites XML
function plugin2paquet_description($description, $slogan, $plug, $dir)
{
	$files = $langs = array();
	foreach (plugin2paquet_traite_mult($description) as $lang => $_descr) {
	  if (!$lang) $lang = 'fr';
	  $langs[$lang]['description'] = trim(htmlentities($_descr));
	}

	foreach (plugin2paquet_traite_mult($slogan) as $lang => $slogan) {
	  if (!$lang) $lang = 'fr';
	  if (preg_match(',^\s*(.+)[.!?\r\n\f],Um', $slogan, $matches))
	    $langs[$lang]['slogan'] = $matches[1];
	  else $langs[$lang]['slogan'] = couper($slogan, 150, '');
	}
	$dirl = $dir . '/lang';
	if (!is_dir($dirl)) mkdir( $dirl);
	$dirl .= '/';
	foreach($langs as $lang => $couples) {
	  $module = strtolower($plug) . "-description";
	  $t = "\n// Fichier produit par plugin2paquet";
	  $t = ecrire_fichier_langue_php($dirl, $lang, $module, $couples, $t);
	  if ($t) $files[]= substr($t, strlen($dir)+1);
	}
	return $files;
}

// Expanse les multi en un tableau de textes complets, un par langue
function plugin2paquet_traite_mult($texte)
{
	if (!preg_match_all(_EXTRAIRE_MULTI, $texte, $regs, PREG_SET_ORDER))
		return array('fr' => $texte);
	$trads = array();
	foreach ($regs as $reg) {
		foreach(extraire_trads($reg[1]) as $k => $v) {
			$trads[$k]= str_replace($reg[0], $v, 
				isset($trads[$k]) ? $trads[$k] : $texte);
		}
	}
	return $trads;
}

// - elimination des multi (exclue dans la nouvelle version)
// - transformation en attribut des balises A
// - interpretation des balises BR et LI comme separateurs

function plugin2paquet_texte($name, $texte, $plug)
{
	$t = plugin2paquet_traite_mult($texte);
#	if (count($t) > 1) 
#	  echo "$plug: multi dans $name: ", str_replace("\n", " ", $texte), "\n";
	$res = '';
	foreach(preg_split('@(<br */?>)|<li>@', $t['fr']) as $v) {
	    if (preg_match('@<a[^>]*href=(\W)(.*?)\1[^>]*>(.*?)</a>@', $v, $r)) {
	      $href = " lien='" . $r[2] ."'";
	      $v = str_replace($r[0], $r[3], $v);
	    } elseif (preg_match(_RACCOURCI_LIEN,$v, $r)) {
	      $href = " lien='" . $r[4] ."'";
	      $v = str_replace($r[0], '', $v);
	    } else $href = '';
	    if (preg_match('/\W([\w\d.-]+@[\w\d.-]+)/', $v, $r)) {
	      $mail = " mail='$r[1]'";
	      $v = str_replace($r[0], $r[3], $v);
	    } else $mail = '';
	    if ($v = trim(textebrut($v)))
	      $res .= "\n\t<$name$href$mail>$v</$name>";
	}

	return $res;
}

// verifie que la balise $nom declare une unique fichier $prefix_$nom:
// fonctions -> $prefix_fonctions
// options -> $prefix_options, 
// install -> $prefix_actions
function plugin2paquet_implicite($D, $balise, $nom)
{
   $contenu = str_replace("\n", ' ', is_array($D[$balise]) 
	? join(" ", array_map('trim', $D[$balise])) : trim($D[$balise]));

  $std = $D['prefix'] . "_$nom" . '.php';
  if (!$contenu OR $contenu == $std) return '';
  if (!strpos($contenu, ' ')) return "<!-- svn mv $contenu $std -->\n";
  return "<!-- echo include($contenu) > $std; svn add $std -->\n";
}

function plugin2paquet_infos($plug, $bof, $dir)
{
  $f = charger_fonction('infos_plugin', 'plugins');
  return $f(file_get_contents("$dir$plug/plugin.xml"), $plug, $dir);
}

?>
