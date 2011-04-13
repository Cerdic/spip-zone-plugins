<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// pour spip_version_compare, plugin_version_compatible et _FILE_PLUGIN_CONFIG
include_spip('inc/plugin'); 



// 3 fonctions du inc/plugin de SPIP2.1 absentes de SPIP 2.2
if (!function_exists('actualise_plugins_actifs')) {
  function actualise_plugins_actifs() {
    ecrire_plugin_actifs('',false, 'force');
  }
}

if (!function_exists('installe_un_plugin')) {
	function installe_un_plugin($plug,$infos,$dir = '_DIR_PLUGINS'){
	  $f = charger_fonction('installer', 'plugins');
	  $f = $f($plug, 'install', $dir);
	  return is_array($f) ? $f['install_test'][0] : $f;
	}
}
				
if (!function_exists('desinstalle_un_plugin')) {
	function desinstalle_un_plugin($plug,$infos,$dir = '_DIR_PLUGINS'){
	  $f = charger_fonction('installer', 'plugins');
	  $f = $f($plug, 'uninstall');
	  return is_array($f) ? $f['install_test'][0] : $f;
	}
}
				
// ---------------------- LIBS -------------------------------

// Faire la liste des librairies disponibles
// retourne un array ( nom de la lib => repertoire , ... )
function step_lister_librairies() {
	$libs = array();
	foreach (array_reverse(creer_chemin()) as $d) {
		if (is_dir($dir = $d.'lib/')
		AND $t = @opendir($dir)) {
			while (($f = readdir($t)) !== false) {
				if ($f[0] != '.'
				AND is_dir("$dir/$f"))
					$libs[$f] = $dir;
			}
		}
	}
	return $libs;
}



// retourne un cadre contenant la liste des librairies disponibles dans /lib
function step_html_liste_librairies_presentes(){
	// Lister les librairies disponibles
	$res = "";
	if ($libs = step_lister_librairies()) {
		ksort($libs);
		$res = '<ul>';
		foreach ($libs as $lib => $rep)
			$res .= "<li title='".joli_repertoire($rep)."'>$lib</li>\n";
		$res .= '</ul>';
	}
	return $res; 	
}




// ---------------------- ZONES de PLUGINS -------------------------------

// Met a jour les zones de plugins
function step_update(){


	// proposition d'action :
	// 1) lister tous les plugins locaux actifs et les afficher
	// 2) lister tous les plugins locaux inactifs (prefixe different des actifs)
	//   3) lister les plugins locaux actifs dont une maj existe en local (?)
	//   4) lister les plugins locaux inactifs plus anciens (?)
	// 5) lister les plugins distants : comparer a la fois l'etat (stable...) et la version
	//    et lister 1 prefix par etat (la version la plus recente)
	// 6) si ce plugin est deja present en local... on propose une mise a jour.
	
	// actualiser les plugins locaux
	step_actualiser_plugins_locaux();
	
	// actualiser les plugins distants
	$res = sql_select(array('id_zone','adresse'),'spip_zones_plugins');
	while ($r = sql_fetch($res)){
		step_actualiser_zone($r['adresse']);
	}
}



// Teste la validite d'une url d'une zone de paquets
function step_verifier_adresse_zone($url){
	return preg_match(',^https?://[^.]+\.[^.]+.*/.*[^/]$,', $url);
}





// Ajoute une zone de paquets 
// (xml d'une liste de plugin)
// a la liste des zones de SPIP
// et ajoute les plugins associes dans la table spip_plugins
function step_ajouter_zone($url){
	$url = trim($url);

	// mauvaise adresse
	if (!step_verifier_adresse_zone($url))
		return;
		
	// adresse deja presente
	if (sql_countsel('spip_zones_plugins', 'adresse='.sql_quote($url))){
		return;
	}

	include_spip('inc/distant');
	if (!$xml = recuperer_page($url)) {
		return false;
	}
	include_spip('inc/step_infos_plugin');
	// lire les donnees d'une zone de plugins
	$paquets = step_xml_parse_zone($xml);
	
	if (count($paquets)){
		// nom et description a definir dans le fichier xml de la source ?
		$id_zone = sql_insertq('spip_zones_plugins', array('nom'=>$url, 'adresse'=>$url, 'nombre_plugins'=>count($paquets)));
		
		// ajouter les plugins dans spip_plugins
		step_maj_liste_plugins($id_zone, $paquets);
	}
	
	return count($paquets);
}


// actualise une zone de plugins
// $id = adresse zone OU id_zone dans la table spip_zones_plugins
function step_actualiser_zone($id){
	$id = trim($id);
	
	// pas de zone a cette adresse ?
	if (!$z = sql_fetsel(array('id_zone', 'adresse'), 'spip_zones_plugins', 'adresse=' . sql_quote($id) . ' OR id_zone='. sql_quote($id)) ){
		return;
	}

	
	include_spip('inc/distant');
	if (!$xml = recuperer_page($z['adresse'])) {
		return false;
	}
	include_spip('inc/step_infos_plugin');
	// lire les donnees d'une zone de plugins
	$paquets = step_xml_parse_zone($xml);

	if (count($paquets)){
		// nom et description a definir dans le fichier xml de la source ?
		sql_updateq('spip_zones_plugins', array(
				'nom'=> $z['adresse'],
				'adresse'=> $z['adresse'],
				'nombre_plugins'=>count($paquets)), 
				'id_zone=' . sql_quote($z['id_zone'])
		);
		
		// actualiser les plugins dans spip_plugins
		step_maj_liste_plugins($z['id_zone'], $paquets);
	}
}



// actualise la liste des plugins locaux presents et installes
function step_actualiser_plugins_locaux(){
	
	if ($plugins = step_liste_plugin_files()) {
		// connaitre la liste des plugins actuellement actifs
		$actifs = step_liste_plugin_actifs();
		$recents = step_liste_plugin_recents();

		// recuperer les plugins.xml
		// et mettre a jour la bdd...
		// si le plugin n'est pas activable,
		// on ne l'affiche pas non plus
		sql_delete('spip_plugins','id_zone=' . sql_quote(0));

		foreach($plugins as $constante=>$liste) {
			foreach ($liste as $p) {
				step_actualiser_plugin_local($constante, $p, $actifs, $recents);
			}		
		}
		sql_delete('spip_resultats');
	}
}


function step_actualiser_plugin_local($constante, $p, $actifs, $recents) {
	include_spip('inc/step_infos_plugin');

	if ($insert = step_get_infos_plugin($constante, $p, $actifs, $recents)) {
		// recuperer les champs sql utiles
		if ($insert = step_selectionner_champs_sql_plugin($insert)) {
				$insert['id_zone'] = 0;
				$insert['present'] = 'oui';
				$insert['constante'] = $constante; 
				$insert['dossier'] = $p . '/'; 
				$prefix = strtoupper($insert['prefixe']);
				// flag sur plugin actif et installe
				if (is_array($actifs[$prefix])
				and ($actifs[$prefix]['dir'] == $p)) {
					$insert['actif'] = 'oui';
					if (step_plugin_est_installe($p))
						$insert['installe'] = 'oui';
				}
				// flag sur plugin utilise recemment
				if (isset($recents[$p]) and $recents[$p]) {
					$insert['recent'] = 1; // si on met la valeur, il sera difficile d'appliquer {recent?}
				}
				// flag s'il existe une version plus stable ou plus recente
				// (seulement si le plugin n'est pas actif)
				// et on rend obsolete, inversement, les plugins que l'on perime					
				if ($res = sql_select(array('id_plugin','version','etatnum'),'spip_plugins',array(
					'id_zone=' . sql_quote(0),
					'prefixe=' . sql_quote($insert['prefixe'])))
				AND sql_count($res)) {
					$invalides = array();
					while ($r = sql_fetch($res)) {
						// nota ; idem aux plugs des zones... il faudrait
						// par defaut ne juger que le numero de version, et non l'etat
						// en attandant, si (v3 stable et v4 test) on affiche les 2
						
						// si version <= moi et etat <= moi, on invalide ce plugin
						if (spip_version_compare($r['version'],$insert['version'],'<=') and ($r['etatnum'] <= $insert['etatnum'])) {
							$invalides[] = $r['id_plugin'];
						}
						if ($insert['actif'] != 'oui') {
							// s'il existe un plugin en tout point mieux, je m'invalide
							if ((spip_version_compare($r['version'],$insert['version'],'>') and ($r['etatnum'] >= $insert['etatnum']))
							or  (spip_version_compare($r['version'],$insert['version'],'>=') and ($r['etatnum'] > $insert['etatnum']))
							) {
								$insert['obsolete'] = 'oui';
							}
						}
					}
					if ($invalides) {
						sql_updateq('spip_plugins',array('obsolete'=>'oui'), sql_in('id_plugin', $invalides));
					}
				}
				// on recherche d'eventuelle mises a jour existantes
				if ($res = sql_select(array('id_plugin','version','superieur'),'spip_plugins',array(
					'actif=' . sql_quote('non'),
					'id_zone>' . sql_quote(0),
					'prefixe=' . sql_quote($insert['prefixe']),
					'etatnum>=' . sql_quote($insert['etatnum'])))
				AND sql_count($res)) {
					$superieurs = array();
					while ($r = sql_fetch($res)) {
						// si version superieure et etat identique ou meilleur,
						// c'est que c'est une mise a jour possible !
						if (spip_version_compare($r['version'],$insert['version'],'>')) {
							if (!$insert['maj_version'] or spip_version_compare($r['version'], $insert['maj_version'],'>')) {
								$insert['maj_version'] = $r['version'];
							}
						}
						if ($r['superieur'] != 'oui') {
							$superieurs[] = $r['id_plugin'];
						}
					}
					if ($superieurs) {
						sql_updateq('spip_plugins',array('superieur'=>'oui'), sql_in('id_plugin', $superieurs));
					}
				}			
				
				sql_insertq('spip_plugins', $insert);	
		}
	}
}					

// supprime une zone de plugins (et sa liste de plugins)
// $id = adresse zone OU id_zone dans la table spip_zones_plugins
// ainsi que les entrees associes dans la table spip_plugins
function step_supprimer_zone($id){
	$id = trim($id);
	
	// pas de zone a cette adresse ?
	if (!$id_zone = sql_getfetsel('id_zone', 'spip_zones_plugins', 'adresse=' . sql_quote($id) . ' OR id_zone='. sql_quote($id)) ){
		return;
	}

	sql_delete('spip_plugins','id_zone='.sql_quote($id_zone));
	sql_delete('spip_zones_plugins','id_zone='.sql_quote($id_zone));
	return true;
}



// met a jour la liste des plugins d'une zone donnee
function step_maj_liste_plugins($id_zone, $liste) {

	if ($id_zone and is_array($liste)) {
		sql_delete('spip_plugins','id_zone=' . sql_quote($id_zone));
		foreach ($liste as $file=>$p) {
			if ($insert = step_selectionner_champs_sql_plugin($p['plugin'])) {
				$insert['id_zone'] = $id_zone;
				$insert['paquet'] = $file;
				// on ajoute le plugin uniquement s'il est nouveau
				// ou de version superieure (etat et prefixe identiques)

				// le jour ou on aura des zones de plugins / etat, on pourra
				// rendre obsolete uniquement avec le numero de version
				// en attendant lorsque (v3 stable, et v4 test existent, on propose les 2)
				if ($res = sql_select(array('id_plugin','id_zone','version'),'spip_plugins',array(
					'etatnum<=' . sql_quote($insert['etatnum']),
					'prefixe=' . sql_quote($insert['prefixe'])))
				AND sql_count($res))
				{
					$add = false;
					while ($r = sql_fetch($res)) {
						// 2 possibilites :
						// - plus recent : on le met,
						//     + on met un flag sur les locaux... (maj_version)
						//     + on supprime les distants plus vieux
						// - plus ancien : on le met pas...
						if (spip_version_compare($insert['version'], $r['version'],'>')) {
							$add = true;
							if ($r['id_zone'] == 0) {
								sql_updateq('spip_plugins',array('maj_version'=>$insert['version']),'id_plugin='.sql_quote($r['id_plugin']));
								$insert['superieur'] = 'oui'; // dire que ce plugin est une mise a jour d'un plugin deja actif
							} else {
								// il y a un cas qui pose probleme :
								// - si on ne met pas les plugins aux versions identiques des zones distantes
								// - si on supprime le paquet local (avec ses fichiers)
								// - une recherche ne trouvera plus le paquet (vu qu'il ne sera pas dans la bdd)
								// cela oblige a actualiser les sources de plugins pour recalculer.
								// on laisse comme cela pour l'instant.
								sql_delete('spip_plugins','id_plugin='.sql_quote($r['id_plugin']));
							}
						}
					}
					if ($add) sql_insertq('spip_plugins',$insert);
				} else {
					sql_insertq('spip_plugins',$insert);
				}
			}
		}
	} 
}


// passe simplement un '[version;version]'
function step_verifier_plugin_compatible_version_spip($version){
	static $versions = false;
	if ($versions === false) { $versions = array(); }
	if (isset($versions[$version])) { return $versions[$version]; }
	$version_spip = $GLOBALS['spip_version_branche'].".".$GLOBALS['spip_version_code'];
	return $versions[$version] = plugin_version_compatible($version, $version_spip);
}



// Les archives xml sont deja applaties, pas la peine de se compliquer.
function step_selectionner_champs_sql_plugin($p) {

	// calcul du tableau de dependances
	// Et on ne propose pas de plugin ne fonctionnant pas
	// avec notre version de SPIP
	$dependances = array();
	if (is_array($p['necessite'])) {
		foreach ($p['necessite'] as $c=>$n) {
			if ($n['id'] == 'SPIP') {
				if (!step_verifier_plugin_compatible_version_spip($n['version'])) {
					return false;
				}
				break;
			}
			$p['necessite'][$c]['id'] = strtolower($n['id']);
		}
		$dependances['necessite'] = $p['necessite'];
	}
	
	if (is_array($p['utilise'])) {
		foreach ($p['utilise'] as $c=>$n) {
			$p['utilise'][$c]['id'] = strtolower($n['id']);
		}
		$dependances['utilise'] = $p['utilise'];
	}

	// etat numerique (pour simplifier la recherche de maj)
	$num = array('stable'=>4, 'test'=>3, 'dev'=>2, 'experimental'=>1);
	$etatnum = isset($num[$p['etat']]) ? $num[$p['etat']] : 0;
	return array(
		'nom' => $p['nom'],
		'prefixe' => $p['prefix'],
		'auteur' => $p['auteur'],
		'version' => $p['version'],
		'version_base' => $p['version_base'],
		'licence' => $p['licence'],
		'shortdesc' => $p['shortdesc'],
		'description' => $p['description'],
		'dependances' => serialize($dependances),
		'etat' => $p['etat'],
		'etatnum' => $etatnum,
		'actif' => 'non',
		'installe' => 'non',
		'logo' => $p['icon'],
		'categorie' => $p['categorie'] ? $p['categorie'] : ' ',
		'tags' => $p['tags'],
		'lien' => $p['lien'],
	);
}




// ------------------- A peu de chose pres dans le core ----------------


// retourne le chemin d'une image contenu dans images/
function step_chemin_image($icone){
	return find_in_path($icone, _NOM_IMG_PACK);
}



// -------------------- Autorisations ----------------------------

// teste si l'on peut ecrire un plugin dans un repertoire donne
function autoriser_plugins_telecharger($faire, $type, $id, $qui, $opt){
	
	if (
		!_AUTORISER_TELECHARGER_PLUGINS
		OR !step_verifier_droit_ecriture()
		OR !autoriser('configurer', $type, $id, $qui, $opt)
	) return false;
	
	return true;
}

// verifie que l'on peut ecrire dans le repertoire donne
function step_verifier_droit_ecriture($dir = _DIR_PLUGINS2){
	if ($dir)
		return is_dir($dir) AND is_writable($dir);
	else 
		return false;	
}


// ------------------------ Recherche des plugins locaux ------------

// Retourne la liste de tous les plugins d'un repertoire donne
// ou d'un tableau de repertoires
// (modif de la fonction de spip 2.1)
function step_liste_plugin_files($dir_plugins = ""){
	static $plugin_files=array();

	if (!$dir_plugins) {
		$dir_plugins = array(
			'_DIR_PLUGINS',
			'_DIR_EXTENSIONS',
		);
		// plugins supp (1 seul dossier)
		if (defined('_DIR_PLUGINS_SUPPL')) {$dir_plugins[] = '_DIR_PLUGINS_SUPPL';}
	}
	
	// tableau
	if (is_array($dir_plugins)) {
		foreach ($dir_plugins as $dir) {
			$plugin_files[$dir] = step_liste_plugin_files($dir);
		}
		return $plugin_files;
	}
	
	// solo
	if (!isset($plugin_files[$dir_plugins])
	OR count($plugin_files[$dir_plugins]) == 0){
		$plugin_files[$dir_plugins] = array();
		foreach (preg_files(constant($dir_plugins), '/plugin[.]xml$') as $plugin) {
			$plugin_files[$dir_plugins][] = str_replace(constant($dir_plugins),'',dirname($plugin));
		}
		sort($plugin_files[$dir_plugins]);
	}

	return $plugin_files[$dir_plugins];
}



// ----------------------- Connaitre les plugins actifs, recents et desinstallables


// de inc/plugins
function step_liste_plugin_actifs(){
	$meta_plugin = isset($GLOBALS['meta']['plugin'])?$GLOBALS['meta']['plugin']:'';
	if (strlen($meta_plugin)>0){
		if (is_array($t=unserialize($meta_plugin)))
			return $t;
	}
	return array();
}


// liste des plugins utilises recemment
function step_liste_plugin_recents(){
	$meta_plugin = isset($GLOBALS['meta']['plugins_interessants'])?$GLOBALS['meta']['plugins_interessants']:'';
	if (strlen($meta_plugin)>0){
		if (is_array($t=unserialize($meta_plugin)))
			return $t;
	}
	return array();
}



function step_plugin_est_installe($plug_path){
	$plugin_installes = isset($GLOBALS['meta']['plugin_installes'])?unserialize($GLOBALS['meta']['plugin_installes']):array();
	if (!$plugin_installes) return false;
	return in_array($plug_path,$plugin_installes);
}


function step_selectionner_maj() {
	$ids = array();
	if ($res = sql_select('id_plugin','spip_plugins',array(
		'maj_version<>' .sql_quote(''),
		'id_zone='.sql_quote(0),
		'constante<>'.sql_quote('_DIR_EXTENSIONS'), // On ne permet pas de mettre à jour directement ce qui se trouve dans EXTENSIONS
		'obsolete='.sql_quote('non')))) {
			while ($r = sql_fetch($res)) {
				$ids[] = $r['id_plugin'];
			}
	}
	return $ids;
}



/* fonction (pas au point pour installer un plugin)
 *
 * @param $prefixe : prefixe du plugin ou tableau de prefixes
 *
 */
function step_install($prefixe, $redirect='') {
	if (!$prefixe) {
		return false;
	}
	
	if (!$redirect) {
		$redirect = generer_url_ecrire('step');
	}

	if (!is_array($prefixe)) {
		$prefixe = array($prefixe);
	}

	// mettre a jour la liste des plugins presents
	step_actualiser_plugins_locaux();
		
	// recuperer les ids des plugins souhaites
	$ids = sql_allfetsel('id_plugin', 'spip_plugins', array(
		sql_in('prefixe', $prefixe),
		'obsolete=' . sql_quote('non')
	));

	if (!$ids) {
		 return false;
	}
	
	$todo = array();
	foreach($ids as $i) {
		$todo[$i['id_plugin']] = 'on';
	}

	// que doit on faire en fonction des plugins demandes
	include_spip('inc/step_decideur');
	$decideur = new Decideur;
	#$decideur->log = true;
	$decideur->verifier_dependances($todo);

	// le decideur renvoie une todo completee des instructions necessaires
	$_todo = array();
	foreach ($decideur->todo as $info) {
		$_todo[$info['i']] = $info['todo'];
	}

	// todo : erreurs ?

	// on envoie ces instructions a l'actionneur
	// pour qu'il cree le fichier des actions a realiser
	include_spip('inc/step_actionneur');
	$actionneur = new Actionneur();
	$actionneur->ajouter_actions($_todo);
	$actionneur->sauver_actions();

	// on redirige vers l'action d'installation qui traitera
	// une par une les actions a faire.
	include_spip('inc/headers');
	$url = generer_action_auteur('step_install', '', $redirect);
	redirige_par_entete(str_replace('&amp;','&', $url));
	
}
 
?>
