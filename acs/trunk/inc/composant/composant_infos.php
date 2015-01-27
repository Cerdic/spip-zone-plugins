<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

/**
 * Retourne les composants et les pages du squelette qui utilisent l'instance $nic du composant $c
 * @param string $c : classe de composant
 * @param int $nic : numero d'instance
 */
function composant_infos($c, $nic) {
  include_spip('inc/composant/composants_liste');
  include_spip('inc/composant/composants_variables');
  include_spip('inc/composant/classComposantPrive');
  include_spip('inc/filtres');
  
  $r ='<br />';

  // On calcule la liste de toutes les instances de composants actifs
  $choixComposants = array();
  foreach(composants_liste() as $class=>$composant) {
  	foreach($composant['instances'] as $instance=>$params) {
  		if ($params['on'] == 'oui')
  			array_push($choixComposants, $class.($instance != 0 ? '-'.$instance :''));
  	}
  }

  if (count($choixComposants) == 0)
    ajax_retour($r.'<div class="alert">'._T('acs:config_not_found').'</div>');

  // On établit la liste de toutes les variables ACS ayant pour valeur le nom du composant $c.$nic
  $ca = array();
  foreach($GLOBALS['meta'] as $k => $v) {
    if ((substr($k, 0, 3) == 'acs') && ($v == $c.($nic ? '-'.$nic: ''))) {
      if (in_array($v, $choixComposants)) array_push($ca, substr($k, 3));
    }
  }

  // On retourne la liste de tous les composants qui contiennent ce composant
  if (count($ca)) {
    $lv = liste_variables();
    if (is_array($lv)) {
      $r .= '<span class="onlinehelp">'._T('acs:used_in').'</span><br />';
      foreach ($ca as $var) {
        if (isset($lv[$var]['c'])) {
        	$pc = $lv[$var]['c'];
        	$pnic = $lv[$var]['nic'];
        	if (isset($GLOBALS['meta']['acs'.ucfirst($pc).$pnic.'Nom']))
        		$pnom = $GLOBALS['meta']['acs'.ucfirst($pc).$pnic.'Nom'];
        	else
        	  $pnom = ucfirst($pc).(isset($pnic) ? $pnic : '');
          $r .= '&nbsp;&nbsp;&nbsp;<a class="nompage" href="?exec=acs&onglet=composants&composant='.$pc.($pnic ? '&nic='.$pnic : '').'" title="acs'.$var.'">'.$pnom.'</a><br />';
        }
      }
    }
  }

  // On cherche toutes les pages qui contiennent ce composant
  $l = liste_pages_composant($c, $nic);
  if ($l)
  	$r.= '<hr />'.$l;

	$traductions = cGetTraductions($c,'composants/'.$c.'/lang',';.*[.]php$;iS');
  $r .= '<hr /><table width="100%"><tr><td colspan="3" class="onlinehelp centre">'.ucfirst(_T('spip:afficher_trad')).'</td></tr>';
  $r .= (count($traductions[0]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:public').' </td><td>&nbsp;</td><td>'.liens_traductions($c, $traductions[0]).'</td></tr>' : '');
  $r .= (count($traductions[1]) ? '<tr><td style="width:10%; vertical-align: top;" align="'.$GLOBALS['spip_lang_right'].'"> '._T('acs:ecrire').' </td><td>&nbsp;</td><td>'.liens_traductions($c, $traductions[1], 'ecrire').'</td></tr>' : '');
  $r .= '</table>';
  
  // On crée une instance :
  $composant = new AdminComposant($c);
		$r .= '<hr /><div>'.
			_T('acs:require',
				array(
					'class' => $composant->nom,
					'version' => $composant->version)
			).' :<br />';
		// On récupère les dépendances dans un tableau ordonné 
		// avec les  plugins requis d'abord, puis les composants :
		foreach($composant->necessite['plugin'] as $nec) {
			$npr = $nec['nom'];
			$vr = $nec['compatibilite'];
			// Compatibilite avec anciennes versions
			if (isset($nec['id'])) {
				$npr = $nec['id'];
				$vr = $nec['version'];
			}
			$get_version = $npr.'_version';
			if (is_callable($get_version)) // la fonction existe pour spip et pour acs
				$current_version = $get_version();
			elseif ($f = chercher_filtre('info_plugin')) { // pour les plugins sans fonction plugin_version()
				if (is_callable($f))
					$current_version = $f($npr,'version');
			}
			if (!$current_version)
				$current_version = '?';
			$version = substr($vr, 1, -1);
			$version = explode(';',$version);
			$min_version = $version[0];
			$max_version = $version[1];
			if (version_compare($min_version, $current_version, '>')) {
				$class = 'alert';
			}
			else {
				$class = '';
			}
			$necessite .= '<li><span class="'.$class.'">'.
				_T('plugin_necessite_plugin',
				 array(
				 		'plugin' => '<b>'.$npr.'</b>',
						'version' => $min_version
				)).'</span>  (<b>'.$current_version.'</b>)</li>';
		}
		foreach($composant->necessite['composant'] as $nec) {
			$nset = $rec['set'];
			$ncr = $nec['nom'];
			$vr = $nec['compatibilite'];
			$cr = new AdminComposant($ncr);
			$current_version = $cr->version;
			if (!$current_version)
				$current_version = '?';
			$version = substr($vr, 1, -1);
			$version = explode(';',$version);
			$min_version = $version[0];
			$max_version = $version[1];
			if (version_compare($min_version, $current_version, '>')) {
				$class = 'alert';
			}
			else {
				$class = '';
			}
			$necessite .= '<li><span class="'.$class.'">'.
					_T('acs:composant').' '.$ncr.' '.$min_version.
					'</span>  (<b>'.$current_version.'</b>)</li>';
		}
		if ($necessite) {
			$r .= '<ul style="list-style-type: disc;list-style-position: inside;">'.$necessite.'</ul>';
		}
		$r .= '</div>';

  return $r;
}

function liens_traductions($c, $langs, $cadre='') {
  foreach($langs as $lang) {
    $url = '?exec=composant_get_trad&c='.$c.'&trcmp='.$lang.'&cadre='.$cadre;
    $r .= ' <a href="#cTrad" title="'.traduire_nom_langue($lang).'" onclick="AjaxSqueeze(\''.$url.'\',\'cTrad\');" ><img src="'._DIR_PLUGIN_ACS.'lang/flags/'.$lang.'.gif" alt="'.$lang.'" /></a> ';
  }
  return $r;
}

/**
 * Retourne toutes les pages des squelettes, composants, et noisettes qui 
 * contiennent l'instance $nic du composant $c
 */
function liste_pages_composant($c, $nic) {
	$dirs = array(
		array('', _T('acs:page'), _T('acs:pages')),
		array('modeles', _T('acs:modele'), _T('acs:modeles')),
		array('formulaires', _T('acs:formulaire'), _T('acs:formulaires'))
	);
  foreach (composants_liste() as $class=>$composant) {
  	$dirs[] = array('composants/'.$class, _T('acs:composant'), _T('acs:composants'));
  }
	foreach($dirs as $dir) {
    $p = cGetPages($c, $nic, $dir[0]);
    if (count($p['composant']) > 0) {
      $r = '<span class="onlinehelp">'.(count($p['composant']) > 1 ? $dir[2] : $dir[1]).'</span> ';  
      foreach($p['composant'] as $page) {
        $r .= show_override($p['chemin'], $page).' ';
      }
      $r .= '<br />';
    }
    if (count($p['variables']) > 0) {
      $len = strlen('acs'.$c.$nic);
      $r .= '<i>';
      foreach($p['variables'] as $page=>$var) {
        $r .= show_override($p['chemin'], $page).' (';
        foreach($var as $v) {
          $r .= '<a title="'.$v.($GLOBALS['meta'][$v] ? ' = '.htmlentities($GLOBALS['meta'][$v]) : '').'">'.substr($v,$len).'</a> ';
        }
        $r = rtrim($r);
        $r .= ')<br />';
      }
      $r .= '</i>';
    }
	}
  return $r;
}

function show_override($chemin, $page) {
	if ($chemin)
		$chemin .= '/';
  if (isset($GLOBALS['meta']['acsSqueletteOverACS']) && is_readable('../'.$GLOBALS['meta']['acsSqueletteOverACS'].'/'.$page.'.html'))
    $r = '<u>'.$page.'</u>';
  else
    $r = $page;
  $r = '&nbsp;&nbsp;&nbsp;<a class="nompage" href="?exec=acs&onglet=pages&pg='.$chemin.$page.'" title="'.$chemin.$page.'">'.$r.'</a>';
  return $r;
}

/**
 * Composant - Méthode cGetPages: retourne un tableau des squelettes qui utilisent le composant ($c,$nic)
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 */

function cGetPages($c, $nic, $chemin='') {
  $pages = array();
  $pages['composant'] = array();
  $pages['variables'] = array();

  if (!$c) return $pages;

  if ($chemin == '')
    $dir = _DIR_PLUGIN_ACS.'sets/'.$GLOBALS['meta']['acsSet'];
  else
    $dir = find_in_path($chemin);

  if (@is_dir($dir) AND @is_readable($dir) AND $d = @opendir($dir)) {
    $vars= cGetVars($c, $nic);
    if ($nic)
    	$cpreg = '/\{fond=composants\/'.$c.'\/[^\}]*\}.*\{nic='.$nic.'\}/';
   	else
   		$cpreg = '/\{fond=composants\/'.$c.'\/[^\}]*\}.^[\{nic='.$nic.'\}]*/';
    while (($f = readdir($d)) !== false && ($nbfiles<1000)) {
      if ($f[0] != '.' # ignorer . .. .svn etc
      AND $f != 'CVS'
      AND $f != 'remove.txt'
      AND @is_readable($p = "$dir/$f")) {
        if (is_file($p)) {
          if (preg_match(";.*[.]html$;iS", $f)) {
            $fic = @file_get_contents($p);
            if (preg_match($cpreg, $fic, $matches))
              $pages['composant'][] = substr($f, 0, -5);
            foreach($vars as $var) {
              if (strpos($fic, $var))
                $pages['variables'][substr($f, 0, -5)][] = $var;
            }
          }
        }
      }
      $nbfiles++;
    }
    $pages['chemin'] = $chemin;
  }
  return $pages;
}

/**
 * Composant - Méthode cGetTraductions: retourne un tableau des traductions d'un composant
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 * @param string $c : classe de composant
 */
function cGetTraductions($c) {
  $r[0] = cGetFiles($c, 'composants/'.$c.'/lang', $ext='php', strlen($c)+1);
  $r[1] = cGetFiles($c, 'composants/'.$c.'/ecrire/lang', $ext='php', strlen($c)+strlen('ecrire')+2);
  return $r;
}

/**
 * Composant - Méthode cGetFiles: retourne un tableau de fichiers d'un composant
 * qui vérifient l'expression régulière $expr (par défaut: les fichiers html)
 * Non inclus comme méthode d'objet composant pour permettre usage sans création d'objet composant
 */
function cGetFiles($c, $chemin='', $ext='html', $skip=0) {
  $files=array();
  if (!$c) return $files;
  $dir = find_in_path($chemin);
  if (@is_dir($dir) AND @is_readable($dir) AND $d = @opendir($dir)) {
    while (($f = readdir($d)) !== false && ($nbfiles<1000)) {
      if ($f[0] != '.' # ignorer . .. .svn etc
      AND $f != 'CVS'
      AND $f != 'remove.txt'
      AND @is_readable($p = "$dir/$f")) {
        if (is_file($p)) {
          if (preg_match(';.*[.]'.$ext.'$;iS', $f)) {
            $files[] = substr($f, $skip, -(strlen($ext)+1));
          }
        }
      }
      $nbfiles++;
    }
  }
  sort($files);
  return $files;
}

/**
 * Fonction cGetVars: retourne un tableau des variables du composant
 * sans création d'objet composant
 */
function cGetVars($composant, $nic) {
  $r = array();
  // Lit les paramètres de configuration du composant
  include_once('inc/xml.php');
  $configfile = find_in_path('composants/'.$composant.'/ecrire/composant.xml');
  $config = spip_xml_load($configfile);
  $c = $config['composant'][0];
  if (is_array($c['param'])) {
    foreach($c['param'] as $param) {
      if (is_array($param['nom']) && $param['nom'][0] == 'optionnel' && (($param['valeur'][0] == 'oui') || ($param['valeur'][0] == 'true')))
        array_push($r, 'acs'.ucfirst($composant).'Use');
    }
  }

  if (is_array($c['variable'])) {
    foreach($c['variable'] as $k=>$var) {
      foreach($var as $varname=>$value) {
        if ($varname=='nom')
          array_push($r, 'acs'.ucfirst($composant).$nic.$value[0]);
      }
    }
  }
  return $r;
}
?>