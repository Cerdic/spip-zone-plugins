<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/plugin');

function inc_plugonet_traiter($traitement, $files, $forcer_paquetxml=false, $simuler=false) {
	// Chargement des fonctions de validation XML et d'extraction des informations contenues 
	// dans la balise plugin
	$valider_xml = charger_fonction('valider', 'xml');
	$informer_xml = charger_fonction('infos_plugin', 'plugins', true);
	
	// Suivant le traitement les fichiers traites sont plugin.xml ou paquet.xml
	// On definit donc la DTD associee
	$dtd = ($traitement == 'validation_paquetxml') ? 'paquet' : 'plugin';
	$erreurs = array();
	$commandes = array();
	$t0 = time();
	foreach ($files  as $nom)  {
		if (lire_fichier($nom, $contenu)) {
			$erreurs[$nom]['lecture_' . $dtd . 'xml'] = false;
			// Validation formelle du fichier plugin.xml ou paquet.xml suivant le traitement
			$resultats = $valider_xml($contenu, false, false, $dtd . '.dtd');
			$erreurs[$nom]['validation_' . $dtd . 'xml'] = is_array($resultats) ? $resultats[1] : $resultats->err; //2.1 ou 2.2
			
			// Si le traitement en cours est la validation d'un paquet.xml, on a termine ici
			// Sinon on continue
			if ($traitement != 'validation_paquetxml') {
				// Recherche de toutes les balises plugin contenues dans le fichier plugin.xml et 
				// extraction de leurs infos
				$regexp = '#<plugin[^>]*>(.*)</plugin>#Uims';
				if ($nb_balises = preg_match_all($regexp, $contenu, $matches)) {
					$plugins = array();
					// Pour chacune des occurences de la balise on extrait les infos
					$erreurs[$nom]['information_pluginxml'] = false;
					foreach ($matches[0] as $_balise_plugin) {
						// Extraction des informations du plugin suivant le standard SPIP
						// -- si une balise est illisible on sort de la boucle et on retourne 
						//    l'erreur sans plus de traitement
						if (!$infos = $informer_xml($_balise_plugin)) {
							$erreurs[$nom]['information_pluginxml'] = true;
							break;
						}
						$plugins[] = $infos;
					}
				}
				else
					$erreurs[$nom]['information_pluginxml'] = true;
	
				if (($traitement == 'generation_paquetxml') 
				AND !$erreurs[$nom]['information_pluginxml']) {
					// Puisqu'on sait extraire les infos du plugin.xml, on construit le contenu
					// du fichier paquet.xml a partir de ces infos
					list($paquet_xml, $commandes[$nom], $prefixe, $descriptions) = plugin2paquet($plugins);
					// On valide le contenu obtenu avec la nouvelle DTD paquet
					$resultats = $valider_xml($paquet_xml, false, false, 'paquet.dtd');
					$erreurs[$nom]['validation_paquetxml'] = is_array($resultats) ? $resultats[1] : $resultats->err;
					
					// Si aucune erreur de validation de paquet.xml, on peut ecrire les fichiers de sortie :
					// -- paquet.xml dans le repertoire du plugin
					// -- les paquet-${prefixe}_${langue}.php pour chaque langue trouvee dans le 
					//    repertoire lang/ du plugin
					// -- le fichier des commandes svn
					if (!$erreurs[$nom]['validation_paquetxml'] OR $forcer_paquetxml ) {
						// Determination du repertoire en fonction du mode choisi
						if ($simuler) {
							$dirs = explode('/', dirname($nom));
							$dir = sous_repertoire(_DIR_TMP, "plugonet");
							foreach ($dirs as $_dir) {
								if ($_dir !== '..' AND $_dir !== 'plugins' AND $_dir !== 'auto') 
								  $dir = sous_repertoire($dir, rtrim($_dir,'_'));
							}
						}
						else
							$dir = dirname($nom);
						if ($modules = plugin2balise_description($descriptions, $prefixe, $dir))
							$commandes[$nom]['traduction'] = "svn add " . join(' ', $modules);
						if (ecrire_fichier($dir . '/paquet.xml', $paquet_xml))
							$commandes[$nom]['paquet'] = "svn add paquet.xml";
						$sh = plugin2balise_migration($commandes[$nom], $nom);
						ecrire_fichier($dir . "/paquet-migration.sh", $sh);
					}
				}
			}
		}
		else
			$erreurs[$nom]['lecture_' . $dtd . 'xml'] = true;
	}

	return array($erreurs, time()-$t0, $commandes);
}


// --------------------- CONSTRUCTION DES BALISES PAQUET ET SPIP -----------------
//

// Boucle sur chaque contenu des balises plugin et creation du contenu de paquet.xml
function plugin2paquet($plugins) {

	// Pour accelerer on simplifie le cas majoritaire ou le tableau ne contient qu'un seul element
	$balises_spip = '';
	$paquet = $commandes = array();
	if (count($plugins) == 1 ) {
		$paquet = $plugins[0];
		$paquet['compatibilite_paquet'] = ($paquet['compatibilite']) ? $paquet['compatibilite'] : '';
	}
	else {
		// Cas de plusieurs balises plugin (limite a 2)
		// -- On determine le bloc dont la borne min de compatibilite SPIP est la plus elevee
		//     et celui dont la borne min est la moins elevee
		// -- On construit l'intervalle de compatibilite maximal
		$cle_min_max = $cle_min_min = -1;
		$borne_min_max = _PLUGONET_VERSION_SPIP_MIN;
		$borne_min_min = _PLUGONET_VERSION_SPIP_MAX;
		$compatibilite_paquet = '';
		foreach ($plugins as $_cle => $_plugin) {
			if (!$_plugin['compatibilite'])
				$borne_min = _PLUGONET_VERSION_SPIP_MIN;
			$bornes_spip = intervalle2bornes($_plugin['compatibilite']);
			$borne_min = ($bornes_spip['min']['valeur']) ? $bornes_spip['min']['valeur'] : _PLUGONET_VERSION_SPIP_MIN;
			if (spip_version_compare($borne_min_max, $borne_min, '<=')) {
				$cle_min_max = $_cle;
				$borne_min_max = $borne_min;
			}
			if (spip_version_compare($borne_min_min, $borne_min, '>=')) {
				$cle_min_min = $_cle;
				$borne_min_min = $borne_min;
			}
			$compatibilite_paquet = (!$compatibilite_paquet) 
				? $_plugin['compatibilite']
				: fusionner_intervalles($compatibilite_paquet, $_plugin['compatibilite']);
		}

		// On initialise les informations non techniques du bloc de compatibilite la moins elevee avec celles
		// du bloc dont la borne min de compatibilite SPIP est la plus elevee.
		$paquet['prefix'] = $plugins[$cle_min_max]['prefix'];
		$paquet['categorie'] = $plugins[$cle_min_max]['categorie'];
		$paquet['logo'] = $plugins[$cle_min_max]['logo'];
		$paquet['version'] = $plugins[$cle_min_max]['version'];
		$paquet['etat'] = $plugins[$cle_min_max]['etat'];
		$paquet['schema'] = $plugins[$cle_min_max]['schema'];
		$paquet['meta'] = $plugins[$cle_min_max]['meta'];
		$paquet['documentation'] = $plugins[$cle_min_max]['documentation'];
		$paquet['nom'] = $plugins[$cle_min_max]['nom'];
		$paquet['auteur'] = $plugins[$cle_min_max]['auteur'];
		$paquet['licence'] = $plugins[$cle_min_max]['licence'];
		$paquet['slogan'] = $plugins[$cle_min_max]['slogan'];
		$paquet['description'] = $plugins[$cle_min_max]['description'];

		// On fusionne les informations necessaires
		// les traitements effectues sont les suivants :
		// -- nom, prefix, documentation, version, etat, version_base, description : *rien*, on conserve ces informations en l'etat
		// -- options, fonctions, install, path, pipeline, bouton, onglet : *rien*, meme si certaines pourraient etre fusionnees ces infos ne sont pas stockees
		// -- auteur, licence : *rien*, l'heuristique pour fusionner ces infos est trop compliquee aujourdhui car c'est du texte libre
		// -- categorie, logo : si la valeur du bloc selectionne est vide on essaye d'en trouver une non vide dans les autres blocs
		// -- compatible : on constuit l'intervalle global de compatibilite SPIP
		if (!$paquet['categorie'] AND $plugins[$cle_min_min]['categorie'])
			$paquet['categorie'] = $plugins[$cle_min_min]['categorie'];
		if (!$paquet['logo'] AND $plugins[$cle_min_min]['logo'])
			$paquet['logo'] = $plugins[$cle_min_min]['logo'];
		// On initialise la compatibilite avec la fusion des intervalles de compatibilite SPIP
		$paquet['compatibilite_paquet'] = $compatibilite_paquet;

		// -- necessite, utilise, lib, chemin, pipeline, bouton, onglet : on indexe chaque liste de dependances
		//    par l'intervalle de compatibilite sans regrouper les doublons pour l'instant
		$fusion = fusionner_balises_techniques($plugins, $cle_min_min, $cle_min_max);

		// Maintenant que les balises techniques sont fusionnees, il faut :
		// -- generer les balises spip qui ne contiennnent que les balises techniques d'un inetrvalle de compatibilite spip donne
		// -- completer la balise paquet avec les balises techniques communes
		foreach ($fusion as $_cle => $_fusion) {
			if ($_cle === 0) {
				// C'est la partie commune qui doit etre incluse dans la balise paquet
				$paquet = array_merge($paquet, $_fusion);
			}
			else {
				// Generation des balises spip
				// Il faut au prealable inclure l'intervalle de compatibilite spip
				$_fusion['compatibilite'] = $_cle;
				list($spip, $commandes_spip,) = plugin2balise($_fusion, 'spip');
				$balises_spip .= "\n\n$spip";
				$commandes['balise_spip'][$_plugin['compatibilite']] = $commandes_spip;
			}
		}
	}
	
	// -- On conclut avec la balise paquet
	list($paquet_xml, $commandes_paquet, $descriptions) = plugin2balise($paquet, 'paquet', $balises_spip);
	$commandes['balise_paquet'] = $commandes_paquet;

	return array(
			$paquet_xml, 
			$commandes, 
			$paquet['prefix'],
			$descriptions);
}

// Construction d'une balise paquet ou spip
function plugin2balise($D, $balise, $balises_spip='') {

	// Constrution des balises englobantes
	if ($balise == 'paquet') {
		// Balise paquet
		// Extraction des attributs de la balise paquet
		$categorie = table_valeur($D, 'categorie');
		$etat = table_valeur($D, 'etat');
		$lien = table_valeur($D, 'documentation');
		$logo = table_valeur($D, 'logo');
		$meta = table_valeur($D, 'meta');
		$prefix = $D['prefix'];
		$version = $D['version'];
		$version_base = table_valeur($D, 'schema');
		$compatible =  bornes2intervalle(intervalle2bornes($D['compatibilite_paquet']));

		$attributs =
			($prefix ? "\n\tprefix=\"$prefix\"" : '') .
			($categorie ? "\n\tcategorie=\"$categorie\"" : '') .
			($version ? "\n\tversion=\"$version\"" : '') .
			($etat ? "\n\tetat=\"$etat\"" : '') .
			($compatible ? "\n\tcompatibilite=\"$compatible\"" : '') .
			($logo ? "\n\tlogo=\"$logo\"" : '') .
			($version_base ? "\n\tschema=\"$version_base\"" : '') .
			($meta ? "\n\tmeta=\"$meta\"" : '') .
			plugin2balise_lien($lien, 'documentation') ;
	
		// Constrution de toutes les autres balises incluses dans paquet uniquement
		$nom = plugin2balise_nom($D['nom']);
		list($commentaire, $descriptions) = plugin2balise_commentaire($D['nom'], $D['description'], $D['slogan'], $D['prefix']);
	
		$auteur = plugin2balise_copy(table_valeur($D, 'auteur/0'), 'auteur');
		$licence = plugin2balise_copy(table_valeur($D, 'licence/0'), 'licence');
		$traduire = is_array($D['traduire']) ? plugin2balise_traduire($D) :'';
	}
	else {
		// Balise spip
		$compatible =  bornes2intervalle(intervalle2bornes($D['compatibilite']));
		$attributs =
			($compatible ? " compatibilite=\"$compatible\"" : '');
		// raz des balises non utilisees
		$nom = $commentaire = $auteur = $licence = $traduire = '';
		$descriptions =array();
	}

	// Toutes les balises techniques sont autorisees dans paquet et spip
	$pipeline = is_array($D['pipeline']) ? plugin2balise_pipeline($D['pipeline']) :'';
	$chemin = is_array($D['chemin']) ? plugin2balise_chemin($D) :'';
	$necessite = (is_array($D['necessite']) OR is_array($D['lib'])) ? plugin2balise_necessite($D) :'';
	$utilise = is_array($D['utilise']) ? plugin2balise_utilise($D['utilise']) :'';
	$procure = is_array($D['procure']) ? plugin2balise_procure($D['procure']) :'';
	$bouton = is_array($D['menu']) ? plugin2balise_exec($D, 'menu') :'';
	$onglet = is_array($D['onglet']) ? plugin2balise_exec($D, 'onglet') :'';

	// On accumule dans un tableau les commandes de toutes les balises paquet et spip
	$commandes = array();
	$commandes = array_merge(
					plugin2balise_implicite($D, 'options', 'options'),
					plugin2balise_implicite($D, 'fonctions', 'fonctions'),
					plugin2balise_implicite($D, 'install', 'administrations'));
	
	$paquet = 
		"<$balise$attributs" . ($balise == 'paquet' ? "\n>" : ">") .
		"\t$nom$commentaire$auteur$licence$traduire$pipeline$procure$necessite$utilise$bouton$onglet$chemin$balises_spip\n" .
		"</$balise>\n";
	
	return array($paquet, $commandes, $descriptions);
}


// --------------------- ATTRIBUTS DE PAQUET ET BALISE NOM -----------------------
//
// - attribut documentation
// - balise nom
// - slogan extrait eventuellement de la description en commentaire (langue française)

// Eliminer les textes superflus dans les liens (raccourcis [XXX->http...])
// et normaliser l'esperluete pour eviter l'erreur d'entite indefinie
function plugin2balise_lien($url, $nom='lien', $sep="\n\t") {
	if (!preg_match(',https?://[^]\s]+,', $url, $r))
		return '';
	$url = str_replace('&', '&amp;', str_replace('&amp;', '&', $r[0]));
	return "$sep$nom=\"$url\"";
}

// Extrait la traduction francaise uniquement du nom pour creer la balise
// Pour l'instant on ne normalise pas le nom comme le fait SVP
// --> A voir plus tard
function plugin2balise_nom($texte) {
	$t = traiter_multi($texte);
	$res = ($t['fr']) ? "\n\t<nom>" . trim($t['fr']) . "</nom>" : '';

	return $res ? "\n$res" : '';
}

// Extrait la traduction francaise uniquement
// -- on renvoie aussi le tableau des noms, descriptions et slogans par langue pour eviter de le 
//    recalculer ensuite
function plugin2balise_commentaire($nom, $description, $slogan, $prefixe) {
	$descriptions = extraire_descriptions($nom, $description, $slogan, $prefixe);
	if ($slogan = $descriptions['fr'][strtolower($prefixe) . '_slogan'])
		$res = "\t<!-- ". $slogan . " -->";

	return array($res ? "\n$res" : '', $descriptions);
}


// --------------------- BALISES COPYRIGHT (CREDITS) ------------------------
//
// - auteur
// - licence
// - copyright

// - elimination des multi (exclue dans la nouvelle version)
// - transformation en attribut des balises A
// - interpretation des balises BR et LI et de la virgule et du espace+tiret comme separateurs
function plugin2balise_copy($texte, $balise) {
	include_spip('inc/lien');

	// On extrait le multi si besoin et on selectionne la traduction francaise
	$t = traiter_multi($texte);

	$res = $resa = $resl = $resc = '';
	foreach(preg_split('@(<br */?>)|<li>|,|\s-|\n_*\s*|&amp;| & | et @', $t['fr']) as $v) {
		// On detecte d'abord si le bloc texte en cours contient un eventuel copyright
		// -- cela generera une balise copyright et non auteur
		$copy = '';
		if (preg_match('/(?:\&#169;|©|copyright|\(c\)|&copy;)[\s:]*([\d-]+)/i', $v, $r)) {
			$copy = trim($r[1]);
			$v = str_replace($r[0], '', $v);
			$resc .= "\n\t<copyright>$copy</copyright>";
		}
		
		// On detecte ensuite un lien eventuel d'un auteur
		// -- soit sous la forme d'une href d'une ancre
		// -- soit sous la forme d'un raccourci SPIP
		// Dans les deux cas on garde preferentiellement le contenu de l'ancre ou du raccourci
		// si il existe
		$href = $mail = '';
		if (preg_match('@<a[^>]*href=(\W)(.*?)\1[^>]*>(.*?)</a>@', $v, $r)) {
			$href = " lien=\"" . $r[2] ."\"";
			$v = str_replace($r[0], $r[3], $v);
		} 
		elseif (preg_match(_RACCOURCI_LIEN,$v, $r)) {
			if (preg_match('/([^\w\d._-]*)(([\w\d._-]+)@([\w\d.-]+))/', $r[4], $m))
				$mail = " mail=\"" . $r[4] ."\"";
			else
				$href = " lien=\"" . $r[4] ."\"";
			$v = ($r[1]) ? $r[1] : str_replace($r[0], '', $v);
		} 
		else 
			$href = '';
		
		// On detecte ensuite un mail eventuel
		if (!$mail AND preg_match('/([^\w\d._-]*)(([\w\d._-]+)@([\w\d.-]+))/', $v, $r)) {
			$mail = " mail=\"$r[2]\"";
			$v = str_replace($r[2], '', $v);
			if (!$v) {
				// On considere alors que la premiere partie du mail peut faire office de nom d'auteur
				if (preg_match('/(([\w\d_-]+)[.]([\w\d_-]+))@/', $r[2], $s))
					$v = ucfirst($s[2]) . ' ' . ucfirst($s[3]);
				else
					$v = ucfirst($r[3]);
			}
		}

		// On detecte aussi si le bloc texte en cours contient une eventuelle licence
		// -- cela generera une balise licence et non auteur
		//    cette heuristique n'est pas deterministe car la phrase de licence n'est pas connue
		$licnom = $licurl ='';
		if (preg_match('/\b((gnu|free|creative\s+common|cc)*[\/|\s|-]*(apache|lgpl|agpl|gpl|fdl|mit|bsd|art|attribution|by)(\s+licence|\-sharealike|-nc-nd|-nc-sa|-sa|-nc|-nd)*\s*v*(\d*[\.\d+]*))\b/i', $v, $r)) {
			if ($licence = balise2licence($r[2], $r[3], $r[4], $r[5])) {
				$licnom = $licence['nom'];
				$licurl = $licence['url'];
				$licurl = " lien=\"$licurl\"";
				$resl .= "\n\t<licence$licurl>$licnom</licence>";
			}
		}

		// On finalise la balise auteur ou licence si on a pas trouve de licence prioritaire
		$v = trim(textebrut($v));
		if ((strlen($v) > 2) AND !$licnom)
			$resa .= "\n\t<$balise$href$mail>$v</$balise>";
	}
	$res = ($resa ? "\n$resa" : '') . ($resc ? "\n$resc" : '') . ($resl ? "\n$resl" : '');

	return $res ? $res : '';
}


// --------------------- BALISE DE TRADUCTION -----------------------
//
// - traduire : declaratif des modules de langue
// --> A RAJOUTER EVENTUELLEMENT :  TOUS LES MODULES EXISTANTS + LE MODULE PAQUET
function plugin2balise_traduire($D) {
	$res = '';
	foreach($D['traduire'] as $nom => $i) {
		$att = " module=\"" . $i['module'] . "\"" .
				" reference=\"" . $i['reference'] . "\"" .
				(empty($i['gestionnaire']) ? '' : (" gestionnaire=\"" . $i['gestionnaire'] . "\""));
		$res .= "\n\t<traduire$att />";
	}

	return $res ? "\n$res" : '';
}


// --------------------- BALISES TECHNIQUES (IMPLEMENTATION) -----------------------
//
// - pipeline
// - chemin
// - necessite (plugins)
// - lib (librairies)
// - utilise
// - procure
// - bouton
// - onglet

function plugin2balise_pipeline($D) {
	$res = '';
	foreach($D as $i) {
		$att = " nom=\"" . $i['nom'] . "\"" .
				(isset($i['action']) ? (" action=\"" . $i['action'] . "\"") : '') .
				(!empty($i['inclure']) ? (" inclure=\"" . $i['inclure'] . "\"") : '');
		$res .= "\n\t<pipeline$att />";
	}
	
	return $res ? "\n$res" : '';
}

function plugin2balise_chemin($D) {
	$res = '';
	$chemin_vide_trouve = false;
	foreach($D['chemin'] as $i) {
		$t = empty($i['type']) ? '' : (" type=\"" . $i['type'] . "\"");
		$p = $i['dir'];
		if (!$t AND (!$p OR $p==='.' OR $p==='./')) {
			if (!$p) $chemin_vide_trouve = true;
			continue;
		}
		$res .="\n\t<chemin path=\"$p\"$t />";
	}

	return $res ? "\n$res" . ($chemin_vide_trouve ? "\n\t<chemin path=\"\" />" : '') : '';
}

//Extraction des necessite des plugins et des librairies
function plugin2balise_necessite($D) {
	$nec = $lib = '';

	if ($D['necessite']) {
		foreach($D['necessite'] as $i) {
			$nom = isset($i['id']) ? $i['id'] : $i['nom'];
			$src = isset($i['src']) ? plugin2balise_lien($i['src'], 'lien', ' ') : '';
			$version = empty($i['version']) ? '' : (" compatibilite=\"" . bornes2intervalle(intervalle2bornes($i['version'])) . "\"");
			if (preg_match('/^lib:(.*)$/', $nom, $r))
				$lib .= "\n\t<lib nom=\"" . $r[1] . "\"$src />";
			else 
				$nec .="\n\t<necessite nom=\"$nom\"$version />";
		}
	}

	// Si on lit avec infos_plugin les librairies sont dans une branche 'lib'
	if ($D['lib']) {
		foreach($D['lib'] as $i) {
			$nom = isset($i['id']) ? $i['id'] : $i['nom'];
			$src = " lien=\"" . $i['lien'] . "\"";
			$lib .= "\n\t<lib nom=\"$nom\"$src />";
		}
	}

	$res = $nec . $lib;
	return $res ? "\n$res" : '';
}

function plugin2balise_utilise($D) {
	$res = '';
	foreach($D as $i) {
		$nom = isset($i['id']) ? $i['id'] : $i['nom'];
		$src = isset($i['src']) ? $i['src'] : '';
		$att = " nom=\"$nom\"" .
				(!empty($i['version']) ? (" compatibilite=\"" . bornes2intervalle(intervalle2bornes($i['version'])) . "\"") : '') .
				plugin2balise_lien($src);
		$res .="\n\t<utilise$att />";
	}

	return $res ? "\n$res" : '';
}

function plugin2balise_procure($D) {
	$res = '';
	foreach($D as $i) {
		$nom = isset($i['id']) ? $i['id'] : $i['nom'];
		$att = " nom=\"$nom\"" .
				(!empty($i['version']) ? (" version=\"" . $i['version']) . "\"" : '');
		$res .="\n\t<procure$att />";
	}

	return $res ? "\n$res" : '';
}

// Extraction des boutons et onglets
function plugin2balise_exec($D, $balise) {
	$res = '';
	foreach($D[$balise] as $nom => $i) {
		$res .= "\n\t<$balise" .
			" nom=\"" . $nom . "\"" .
			plugin2attribut('titre', @$i['titre']) .
			plugin2attribut('parent', @$i['parent']) .
			plugin2attribut('position', @$i['position']) .
			plugin2attribut('icone', @$i['icone']) .
			plugin2attribut('action', @$i['action']) .
			plugin2attribut('parametres', str_replace('&', '&amp;', str_replace('&amp;', '&', @$i['parametres']))) .
			' />';
	}

	return $res ? "\n$res" : '';
}


// --------------------- BALISES DISPARUES ET COMMANDES DE MIGRATION --------
//
// - fonctions, options et install : creation des commandes svn de substitution
// - slogan, description : creation des fichiers de langue ${prefixe}-paquet_${codelangue}.php
// - creation du fichier d'aide contenant les commandes svn de modification du plugin

// verifie que la balise $nom declare une unique fichier $prefix_$nom:
// fonctions -> $prefix_fonctions
// options -> $prefix_options, 
// install -> $prefix_administrations
function plugin2balise_implicite($D, $balise, $nom) {
	if (!isset($D[$balise])) $files = array();
	else $files = is_array($D[$balise]) ? $D[$balise] : array($D[$balise]);
	$contenu = join(' ', array_map('trim', $files));
	$std = strtolower($D['prefix']) . "_$nom" . '.php';
	
	if (!$contenu OR $contenu == $std) 
		return array();
	if (!strpos($contenu, ' ')) 
		return array("svn mv $contenu $std");
	$k = array_search($std, $files);
	if (!$k)
		return array("cat $contenu > $std", "svn add $std");
	unset($files[$k]);
	$contenu = join(' ', array_map('trim', $files));
	return array("cat $contenu >> $std", "svn rm $contenu");
}

// Passer les lettres accentuees en entites XML
function plugin2balise_description($descriptions, $prefixe, $dir) {
	include_spip('inc/langonet_generer_fichier');

	$dirl = $dir . '/lang';
	if (!is_dir($dirl)) 
		mkdir($dirl);
	$dirl .= '/';
	$files = array();
	foreach($descriptions as $_lang => $_couples) {
		$module = "paquet-" . strtolower($prefixe);
		$producteur = "\n// Fichier produit par PlugOnet";
		$fichier_lang = ecrire_fichier_langue_php($dirl, $_lang, $module, $_couples, $producteur);
		if ($fichier_lang) 
			$files[]= substr($fichier_lang, strlen($dir)+1);
	}

	return $files;
}

function plugin2balise_migration($commandes, $plugin_xml) {

	$date = date("d-M-Y H:i:s");
	
	// En-tete du fichier
	$migration = "#!/bin/sh
### FICHIER DE DESCRIPTION DE LA MIGRATION VERS PAQUET.XML
### ------------------------------------------------------
### - Date : $date
### - Fichier d'origine : $plugin_xml
### - Contient les informations et les commandes SVN pour
###   rendre effective la migration utilisant les fichiers produits.
###   Pour se garder de toute erreur de manipulation
###   les commandes sont en commentaire.
### ------------------------------------------------------\n\n";

	// Le fichier paquet.xml (existe toujours)
	$migration .= 
"### Ajout du fichier paquet.xml au depot.
### On conserve le fichier plugin.xml tout le temps de la migration !\n";
	$migration .= "# " . $commandes['paquet'] . "\n\n";

	// Les fichiers de langue (existe toujours au moins en fr)
	$migration .= 
"### Ajout au depot des fichiers de langue donnant slogan et description du plugin. 
### Attention la liste des langues provient des traductions (avec multi)
### de la description dans plugin.xml et non des modules de langue du plugin !\n";
	$migration .= "# " . $commandes['traduction'] . "\n";

	// Les balises disparues et la standardisation des fichiers options, fonctions et administrations
	// -- Intro
	$migration .= "
### La disparition des balises options, fonctions et install au profit 
### d'un nommage standard d'un fichier unique exige parfois de renommer 
### les fichiers, voire de les fusionner.
### Si le code du plugin inclut explicitement ces fichiers 
### il vous faudra les renommer dans les appels de include_spip !\n";
	// -- Filtrage des commandes pour eliminer les commandes redondantes dues a la presence de balises spip
	// -- On ajoute ces commandes a celles de la balise paquet et on tri par ordre alphabetique pour eviter que les 
	//     cat soient apres les svn
	if (isset($commandes['balise_spip']) and $commandes['balise_spip']) {
		foreach ($commandes['balise_spip'] as $_compatible => $_commandes) {
			foreach ($_commandes as $_commande) {
				if (!in_array($_commande, $commandes['balise_paquet']))
					$commandes['balise_paquet'][] = $_commande;
			} 
		}
	}
	if (isset($commandes['balise_paquet']) and $commandes['balise_paquet']) {
		asort($commandes['balise_paquet']);
		// -- Ecriture des commandes filtrees et ordonnees
		foreach ($commandes['balise_paquet'] as $_commande) {
			$migration .= "# " . $_commande . "\n";
		}
	}

	return $migration;
}


// --------------------- FONCTIONS UTILITAIRES -----------------------------------
//
// - extraction des multi d'une balise
// - extraction des tableaux description et slogan a partir de la balise description
// - extraction des bornes d'un intervalle de compatibilite
// - formatage d'un attribut de balise

// Expanse les multi en un tableau de textes complets, un par langue
function traiter_multi($texte) {
	include_spip('inc/filtres');

	if (!preg_match_all(_EXTRAIRE_MULTI, $texte, $regs, PREG_SET_ORDER))
		return array('fr' => $texte);
	$trads = array();
	foreach ($regs as $reg) {
		foreach (extraire_trads($reg[1]) as $k => $v) {
			// Si le code de langue n'est pas precise dans le multi c'est donc fr
			$lang = ($k) ? $k : 'fr';
			$trads[$lang]= str_replace($reg[0], $v, isset($trads[$k]) ? $trads[$k] : $texte);
		}
	}
	return $trads;
}

function extraire_descriptions($nom, $description, $slogan, $prefixe) {
	include_spip('inc/langonet_utils');
	include_spip('inc/texte');

	$langs = array();
	
	// Traitement de la balise nom
	$noms = traiter_multi($nom);
	if (count($noms) > 1) {
		foreach ($noms as $lang => $_descr) {
			if (!$lang)
				$lang = 'fr';
			$langs[$lang][strtolower($prefixe) . '_nom'] = entite2utf(trim($_descr));
		}
	}
	
	// Traitement de la balise slogan si elle existe
	if ($slogan) {
		foreach (traiter_multi($slogan) as $lang => $_descr) {
			if (!$lang)
				$lang = 'fr';
			$langs[$lang][strtolower($prefixe) . '_slogan'] = entite2utf(trim($_descr));
		}
	}
	
	// Traitement de la balise description.
	// Si pas de balise slogan passee en argument, on extrait un slogan de la description
	foreach (traiter_multi($description) as $lang => $_descr) {
		if (!$lang)
			$lang = 'fr';
		$texte = entite2utf(trim($_descr));
		$langs[$lang][strtolower($prefixe) . '_description'] = $texte;
		if (!$slogan)
			if (preg_match(',^\s*(.+)[.!?:\r\n\f],Um', $texte, $matches))
				$langs[$lang][strtolower($prefixe) . '_slogan'] = trim($matches[1]);
			else
				$langs[$lang][strtolower($prefixe) . '_slogan'] = trim(couper($texte, 150, ''));
	}
	
	return $langs;
}

function intervalle2bornes($intervalle='') {
	include_spip('inc/plugin');

	static $borne_vide = array('valeur' => '', 'incluse' => false);
	$bornes = array('min' => $borne_vide, 'max' => $borne_vide);

	if ($intervalle
	AND preg_match(_EXTRAIRE_INTERVALLE, $intervalle, $matches)) {
		if ($matches[1]) {
			$bornes['min']['valeur'] = trim($matches[1]);
			$bornes['min']['incluse'] = ($intervalle{0} == "[");
		}
		if ($matches[2]) {
			$bornes['max']['valeur'] = trim($matches[2]);
			$bornes['max']['incluse'] = (substr($intervalle,-1) == "]");
		}
	}
	
	return $bornes;
}


function bornes2intervalle($bornes, $dtd='paquet') {
	return ($bornes['min']['incluse'] ? '[' : ($dtd=='paquet' ? ']' : '('))
			. $bornes['min']['valeur'] . ';' . $bornes['max']['valeur']
			. ($bornes['max']['incluse'] ? ']' : ($dtd=='paquet' ? '[' : ')'));
}


function fusionner_intervalles($intervalle_a, $intervalle_b) {

	// On recupere les bornes de chaque intervalle
	$borne_a = intervalle2bornes($intervalle_a);
	$borne_b = intervalle2bornes($intervalle_b);

	// On initialise la borne min de chaque intervalle a 1.9.0 si vide
	if (!$borne_a['min']['valeur'])
		$borne_a['min']['valeur'] = _PLUGONET_VERSION_SPIP_MIN;
	if (!$borne_b['min']['valeur'])
		$borne_b['min']['valeur'] = _PLUGONET_VERSION_SPIP_MIN;

	// On calcul maintenant :
	// -- la borne min de l'intervalle fusionne = min(min_a, min_b)
	// -- suivant l'intervalle retenu la borne max est forcement dans l'autre intervalle = max(autre intervalle)
	//    On presuppose evidemment que les intervalles ne sont pas disjoints et coherents entre eux
	if (spip_version_compare($borne_a['min']['valeur'], $borne_b['min']['valeur'], '<=')) {
		$bornes_fusionnees['min'] = $borne_a['min'];
		$bornes_fusionnees['max'] = $borne_b['max'];
	}
	else {
		$bornes_fusionnees['min'] = $borne_b['min'];
		$bornes_fusionnees['max'] = $borne_a['max'];
	}

	return bornes2intervalle($bornes_fusionnees, 'plugin');
}


function plugin2attribut($nom, $val) {
	return empty($val) ? '' : (" $nom=\"" . str_replace("'","&#039;",$val) . "\"");
}


// Determiner la licence exacte avec un nom et un lien de doc standardise
function balise2licence($prefixe, $nom, $suffixe, $version) {
	global $licences_plugin;
	$licence = array();

	$prefixe = strtolower($prefixe);
	$nom = strtolower($nom);
	$suffixe = strtolower($suffixe);

	if (((trim($prefixe) == 'creative common') AND ($nom == 'attribution'))
	OR (($prefixe == 'cc') AND ($nom == 'by')))
		$nom = 'ccby';

	if (array_key_exists($nom, $licences_plugin)) {
		if (!$licences_plugin[$nom]['versions']) {
			// La licence n'est pas versionnee : on affecte donc directement le nom et l'url
			$licence['nom'] = $licences_plugin[$nom]['nom'];
			$licence['url'] = $licences_plugin[$nom]['url'];
		}
		else {
			// Si la version est pas bonne on prend la plus recente
			if (!$version OR !in_array($version, $licences_plugin[$nom]['versions'], true))
				$version = $licences_plugin[$nom]['versions'][0];
			if (is_array($licences_plugin[$nom]['nom']))
				$licence['nom'] = $licences_plugin[$nom]['nom'][$version];
			else
				$licence['nom'] = str_replace('@version@', $version, $licences_plugin[$nom]['nom']);
			$licence['url'] = str_replace('@version@', $version, $licences_plugin[$nom]['url']);

			if ($nom == 'ccby') {
				if ($suffixe == '-sharealike')
					$suffixe = '-sa';
				if (!$suffixe OR !in_array($suffixe, $licences_plugin[$nom]['suffixes'], true))
					$suffixe = '';
				$licence['nom'] = str_replace('@suffixe@', strtoupper($suffixe), $licence['nom']);
				$licence['url'] = str_replace('@suffixe@', $suffixe, $licence['url']);
			}
		}
	}

	return $licence;
}


function fusionner_balises_techniques($plugins, $cle_min_min, $cle_min_max) {
	global $balises_techniques_plugin;

	$fusion = array();
	if (!$plugins)
		return $fusion;

	foreach ($balises_techniques_plugin as $_btech) {
		// On initialise le tableau de sortie avec la balise la plus recente dans cette boucle uniquement.
		// De cette façon, le tableau ne contiendra que des balises techniques
		// On range cette balise arbitrairement dans le bloc des balises communes
		if (isset($plugins[$cle_min_max][$_btech]))
			$fusion[0][$_btech] = $plugins[$cle_min_max][$_btech];

		if (!isset($fusion[0][$_btech]) AND !isset($plugins[$cle_min_min][$_btech])) {
			// Aucun des tableaux ne contient cette balise technique : on la positionne dans les balises
			// communes comme un array vide
			$fusion[0][$_btech] = array();
		}
		else if (!isset($fusion[0][$_btech]) OR !$fusion[0][$_btech]) {
			if ($plugins[$cle_min_min][$_btech]) {
				// La balise technique est vide dans le tableau de fusion mais non vide dans la deuxieme balise plugin
				// On range cette balise dans le tableau fusion de sa compatibilite et on cree la cle commune vide
				$fusion[$plugins[$cle_min_min]['compatibilite']][$_btech] = $plugins[$cle_min_min][$_btech];
				$fusion[0][$_btech] = array();
			}
		}
		else if (!isset($plugins[$cle_min_min][$_btech]) OR !$plugins[$cle_min_min][$_btech]) {
			// La balise technique est non vide dans le tableau de fusion mais vide dans la deuxieme balise plugin
			// On deplace cette balise dans le tableau fusion de sa compatibilite et on cree la cle commune vide
			$balise = $fusion[0][$_btech];
			unset($fusion[0][$_btech]);
			$fusion[$plugins[$cle_min_max]['compatibilite']][$_btech] = $balise;
			$fusion[0][$_btech] = array();
		}
		else {
			// Les deux tableaux contiennent une balise technique non vide : il faut fusionner cette balise technique !
			// On parcourt le premier tableau (fusion) en verifiant une egalite avec le deuxieme tableau
			foreach ($fusion[0][$_btech] as $_cle0 => $_balise0) {
				$balise_commune = false;
				foreach ($plugins[$cle_min_min][$_btech] as $_cle1 => $_balise1) {
					if (balise_identique($_balise0, $_balise1)) {
						// On laisse cette balise dans le bloc commun (index 0) et on la supprime dans l'autre
						// tableau en cours de comparaison
						unset($plugins[$cle_min_min][$_btech][$_cle1]);
						$balise_commune = true;
						break;
					}
				}
				if (!$balise_commune) {
					$fusion[$plugins[$cle_min_max]['compatibilite']][$_btech][] = $_balise0;
					unset($fusion[0][$_btech][$_cle0]);
				}
				if (!isset($fusion[0][$_btech]))
					$fusion[0][$_btech] = array();
			}

			// On traite maintenant les balises restantes du deuxieme tableau
			if ($plugins[$cle_min_min][$_btech]) {
				foreach ($plugins[$cle_min_min][$_btech] as $_balise2) {
					$fusion[$plugins[$cle_min_min]['compatibilite']][$_btech][] = $_balise2;
				}
			}
		}
	}

	return $fusion;
}


function balise_identique($balise1, $balise2) {
	if (is_array($balise1)) {
		foreach ($balise1 as $_attribut1 => $_valeur1){
			if (!array_key_exists($_attribut1, $balise2))
				return false;
			else
				if ($_valeur1 != $balise2[$_attribut1])
					return false;
		}
		return true;
	}
	else
		return ($balise1 == $balise2);
}

?>
