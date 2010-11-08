<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Traitements des depots ---------------------------------

/**
 * Teste la validite d'une url d'un depot de paquets
 *
 * @param string $url
 * @return boolean
 */

// $url	=> url du fichier xml de description du depot
function svp_verifier_adresse_depot($url){
	include_spip('inc/distant');
	return (!$xml = recuperer_page($url)) ? false : true;
}


/**
 * Ajout du depot et de ses extensions dans la base de donnees
 *
 * @param string $url
 * @return boolean
 */

// $url	=> url du fichier xml de description du depot
function svp_ajouter_depot($url, &$erreur=''){
	// On considere que l'url a deja ete validee (correcte et nouveau depot)
	$url = trim($url);

	// lire les donnees d'un depot de paquets
	$infos = svp_xml_parse_depot($url);
	if (!$infos) {
		$erreur = _T('svp:message_nok_xml_non_conforme', array('fichier' => $url));
		return false;
	}
	
	// Ajout du depot dans la table spip_depots. Les compteurs de paquets et de plugins
	// sont mis a jour apres le traitement des paquets
	$champs = array('titre' => filtrer_entites($infos['depot']['titre']), 
					'descriptif' => filtrer_entites($infos['depot']['descriptif']),
					'type' => $infos['depot']['type'],
					'url_paquets'=> $url,
					'sha_paquets'=> sha1_file($url));
	$id_depot = sql_insertq('spip_depots', $champs);
		
	// Ajout des paquets dans spip_paquets et actualisation des plugins dans spip_plugins
	// On passe l'url en premier argument car l'id n'est pas encore connu
	$ok = svp_actualiser_paquets($id_depot, $infos['paquets'], $nb_paquets, $nb_plugins, $nb_autres);
	if (!$ok OR ($nb_paquets == 0)) {
		// Si une erreur s'est produite, on supprime le depot deja insere
		sql_delete('spip_depots','id_depot='.sql_quote($id_depot));
		if (!ok)
			$erreur = _T('svp:message_nok_xml_non_conforme', array('fichier' => $url));
		else
			$erreur = _T('svp:message_nok_aucun_paquet_ajoute', array('url' => $url));
		return false;
	}

	// On met à jour le nombre de paquets et de plugins du depot maintenant !
	sql_updateq('spip_depots',
				array('nbr_paquets'=> $nb_paquets, 'nbr_plugins'=> $nb_plugins, 'nbr_autres'=> $nb_autres),
				'id_depot=' . sql_quote($id_depot));
	
	return true;
}

/**
 * Suppression du depot et de ses extensions dans la base de donnees
 *
 * @param int $id
 * @return boolean
 */

// $id	=> id_depot de l'objet depot dans la table spip_depots ou url du depot
function svp_supprimer_depot($id){
	$id = trim($id);
	
	// Pas de depot a cette adresse ?
	if (!$id_depot = sql_getfetsel('id_depot', 'spip_depots', 'url_paquets=' . sql_quote($id) . ' OR id_depot='. sql_quote($id)) ){
		return false;
	}

	// On supprime les paquets heberges par le depot
	sql_delete('spip_paquets','id_depot='.sql_quote($id_depot));
	// On supprime ensuite :
	// - les liens des plugins avec le depot (table spip_depots_plugins)
	// - les plugins dont aucun paquet n'est encore heberge par un depot restant (table spip_plugins)
	svp_nettoyer_plugins($id_depot);
	// On supprime le depot lui-meme
	sql_delete('spip_depots','id_depot='.sql_quote($id_depot));
	return true;
}


/**
 * Actualisation des plugins du depot uniquement. Sert aussi pour une premiere insertion
 *
 * @param int $id
 * @return boolean
 */

// $id	=> id_depot de l'objet depot dans la table spip_depots ou url du depot
function svp_actualiser_depot($id){
	$id = trim($id);
	
	// pas de depot a cette adresse ?
	if (!$depot = sql_fetsel('*', 'spip_depots', 'url_paquets=' . sql_quote($id) . ' OR id_depot='. sql_quote($id)) ){
		return false;
	}

	$sha = sha1_file($depot['url_paquets']);
	if ($depot['sha_paquets'] == $sha) {
		// Le fichier n'a pas change (meme sha1) alors on ne fait qu'actualiser la date 
		// de mise a jour du depot en mettant a jour *inutilement* le sha1
		spip_log('>> AVERTISSEMENT : aucune modification du fichier XML, actualisation non declenchee - id_depot = ' . $depot['id_depot'], 'svp');
		sql_replace('spip_depots', array_diff_key($depot, array('maj' => '')));
	}
	else {
		// Le fichier a bien change il faut actualiser tout le depot
		$infos = svp_xml_parse_depot($depot['url_paquets']);
		if (!$infos)
			return false;
	
		// On actualise les paquets dans spip_paquets uniquement car le depot n'est
		// mis a jour que par le formulaire d'edition d'un depot.
		// Lors de la mise a jour des paquets, les plugins aussi sont actualises
		$ok = svp_actualiser_paquets($depot['id_depot'], $infos['paquets'], 
									$nb_paquets, $nb_plugins, $nb_autres);
		if ($ok) {
			// On met à jour le nombre de paquets et de plugins du depot ainsi que le nouveau sha1
			// ce qui aura pour effet d'actualiser la date de mise a jour
			sql_updateq('spip_depots', 
						array('nbr_paquets'=> $nb_paquets, 'nbr_plugins'=> $nb_plugins, 'nbr_autres'=> $nb_autres, 'sha_paquets'=> $sha),
						'id_depot=' . sql_quote($depot['id_depot']));
		}
	}
	
	return true;
}


/**
 * Actualisation de la table des paquets pour le depot choisi
 *
 * @param int $id_depot
 * @param array $paquets
 * @param int &$nb_paquets
 * @param int &$nb_plugins
 * @param int &$nb_autres
 * @return boolean
 */

// $id_depot	=> Id du depot dans la table spip_depots
// $paquets		=> Tableau des paquets extrait du fichier xml
//				   L'index est le nom de l'archive (xxxx.zip) et le contenu est
//				   un tableau à deux entrées :
//					- ['plugin'] le tableau des infos du plugin
//					- ['file'] le nom de l'archive .zip
// &$nb_paquets	=> Nombre de paquets reellement inseres dans la base renvoye a l'appelant
// &$nb_plugins	=> Nombre de plugins parmi les paquets inseres
// &$nb_autres	=> Nombre de contributions non issues de plugin parmi les paquets inseres
function svp_actualiser_paquets($id_depot, $paquets, &$nb_paquets, &$nb_plugins, &$nb_autres) {

	$nb_paquets = 0;
	$nb_plugins = 0;
	$nb_autres = 0;
	
	if ((!$id_depot) OR (!is_array($paquets)))
		return false;
	
	// On commence par vider les paquets et les plugins du depot
	sql_delete('spip_paquets','id_depot=' . sql_quote($id_depot));
	svp_nettoyer_plugins($id_depot);

	// Ensuite on recree chaque paquet a partir du contenu du fichier xml
	// comme pour la premiere insertion
	// On ne fait pas cas de la compatibilite avec la version de SPIP installee
	// car la mise a jour doit permettre de collecter tous les paquets
	foreach ($paquets as $_archive => $_infos) {
		$insert_paquet = array();
		// On initialise les informations communes de tous les paquets :
		// l'id du depot et les infos de l'archive
		$insert_paquet['id_depot'] = $id_depot;
		$insert_paquet['nom_archive'] = $_archive;
		$insert_paquet['nbo_archive'] = $_infos['size'];
		$insert_paquet['maj_archive'] = date('Y-m-d H:i:s', $_infos['date']);
		$insert_paquet['src_archive'] = $_infos['source'];

		$collision = false;

		if ($champs = svp_remplir_champs_sql($_infos['plugin'])) {
			// On est en presence d'un PLUGIN
			// ------------------------------
			$insert_paquet = array_merge($insert_paquet, $champs['paquet']);
			$insert_plugin = $champs['plugin'];

			// On loge l'absence de categorie ou une categorie erronee et on positionne la categorie
			// par defaut "aucune"
			// Provisoire tant que la DTD n'est pas en fonction
			if (!$insert_plugin['categorie']) {
				spip_log("Categorie absente dans le paquet issu de <". $insert_paquet['src_archive'] . 
						"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
				$insert_plugin['categorie'] = 'aucune';
			}
			else {
				$svp_categories = unserialize($GLOBALS['meta']['svp_categories']);
				if (!in_array($insert_plugin['categorie'], $svp_categories)) {
					spip_log("Categorie &#107;" . $insert_plugin['categorie'] . "&#108; incorrecte dans le paquet issu de <". $insert_paquet['src_archive'] . 
							"> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
					$insert_plugin['categorie'] = 'aucune';
				}
			}
				
			// On evite les doublons de paquet
			// Pour determiner un doublon on verifie actuellement :
			// - le prefixe
			// - la version du paquet et de la base
			// - l'etat
			// - et on exclu les themes car leur prefixe est toujours = a "theme"
			$where = array('t1.id_plugin=t2.id_plugin',
					't1.version=' . sql_quote($insert_paquet['version']),
					't1.version_base=' . sql_quote($insert_paquet['version_base']),
					't1.etatnum=' . sql_quote($insert_paquet['etatnum']),
					't2.prefixe=' . sql_quote($insert_plugin['prefixe']));
			if (($insert_plugin['prefixe'] == _SVP_PREFIXE_PLUGIN_THEME)
			OR (!$id_paquet = sql_getfetsel('t1.id_paquet', 'spip_paquets AS t1, spip_plugins AS t2', $where))) {
				// On traite d'abord le plugin du paquet pour recuperer l'id_plugin
				// On rajoute le plugin dans la table spip_plugins si celui-ci n'y est pas encore ou on recuperer_page
				// l'id si il existe deja
				if (!$id_plugin = sql_getfetsel('id_plugin', 'spip_plugins',
					array('prefixe=' . sql_quote($insert_plugin['prefixe'])))) {
					$id_plugin = sql_insertq('spip_plugins', $insert_plugin);
				}

				// On traite maintenant le paquet connaissant l'id du plugin
				$insert_paquet['id_plugin'] = $id_plugin;
				sql_insertq('spip_paquets',$insert_paquet);
				$nb_paquets += 1;

				// On rajoute le plugin comme heberge par le depot si celui-ci n'est pas encore
				// enregistre comme tel
				if (!sql_countsel('spip_depots_plugins',
					array('id_plugin=' . sql_quote($id_plugin),
						'id_depot=' . sql_quote($id_depot)))) {
					sql_insertq('spip_depots_plugins', array('id_depot' => $id_depot, 'id_plugin' => $id_plugin));
					$nb_plugins += 1;
				}
			}
			else
				$collision = true;
		}
		else {
			// On est en presence d'une CONTRIBUTION NON PLUGIN
			// ------------------------------------------------
			$where = array(
					't1.id_depot=' . sql_quote($insert_paquet['id_depot']),
					't1.nom_archive=' . sql_quote($insert_paquet['nom_archive']));
			if (!$id_paquet = sql_getfetsel('t1.id_paquet', 'spip_paquets AS t1', $where)) {
				// Ce n'est pas un plugin, donc id_plugin=0 et toutes les infos plugin sont nulles 
				$insert_paquet['id_plugin'] = 0;
				sql_insertq('spip_paquets',$insert_paquet);
				$nb_paquets += 1;
				$nb_autres += 1;
			}
			else
				$collision = true;
		}
		// On loge le paquet ayant ete refuse dans un fichier a part afin de les verifier
		// apres coup
		if ($collision AND _SVP_LOG_PAQUETS) {
			spip_log("Collision avec le paquet <". $insert_paquet['nom_archive'] . 
					" / " . $insert_paquet['src_archive'] . "> du depot <" . $insert_paquet['id_depot'] . ">\n", 'svp_paquets');
		}
	}
	
	return true;
}

function svp_nettoyer_plugins($id_depot) {

	// On rapatrie la liste des plugins du depot
	$liens = sql_allfetsel('id_plugin', 'spip_depots_plugins', 'id_depot='.sql_quote($id_depot));
	$plugins_depot = array_map('reset', $liens);

	// On peut donc supprimer tous ces liens *plugins-depots* du depot
	sql_delete('spip_depots_plugins', 'id_depot='.sql_quote($id_depot));

	// Maintenant on calcule la liste des plugins du depot qui ne sont pas heberges 
	// par un autre depot => donc a supprimer
	// - Liste de tous les plugins encore lies a un autre depot
	$liens = sql_allfetsel('id_plugin', 'spip_depots_plugins');
	$autres_plugins = array_map('reset', $liens);
	// - L'intersection des deux tableaux renvoie les plugins a supprimer	
	$plugins_a_supprimer = array_diff($plugins_depot, $autres_plugins);

	// On supprimer les plugins identifies
	sql_delete('spip_plugins', sql_in('id_plugin', $plugins_a_supprimer));	
	
	return true;
}


// Les archives xml sont deja applaties, pas la peine de se compliquer.
function svp_remplir_champs_sql($p) {

	if (!$p)
		return array();

	// On passe le prefixe en lettres majuscules comme ce qui est fait dans SPIP
	// Ainsi les valeurs dans la table spip_plugins coincideront avec celles de la meta plugin
	$p['prefix'] = strtoupper($p['prefix']);

	// calcul du tableau de dependances
	$dependances = array();
	$v_spip = '';
	if (is_array($p['necessite'])) {
		foreach ($p['necessite'] as $c=>$n) {
			$p['necessite'][$c]['id'] = strtoupper($n['id']);
			if ($n['id'] == 'SPIP') {
				$v_spip = $n['version'];
			}
		}
		$dependances['necessite'] = $p['necessite'];
	}
	
	if (is_array($p['utilise'])) {
		foreach ($p['utilise'] as $c=>$n) {
			$p['utilise'][$c]['id'] = strtoupper($n['id']);
		}
		$dependances['utilise'] = $p['utilise'];
	}

	// Etat numerique (pour simplifier la recherche de maj)
	$num = array('stable'=>4, 'test'=>3, 'dev'=>2, 'experimental'=>1);
	$etatnum = isset($num[$p['etat']]) ? $num[$p['etat']] : 0;
	
	// On passe en utf-8 avec le bon charset les champs pouvant contenir des entites html
	$p['description'] = unicode2charset(html2unicode($p['description']));
	$p['slogan'] = unicode2charset(html2unicode($p['slogan']));
	$p['nom'] = unicode2charset(html2unicode($p['nom']));
	$p['auteur'] = unicode2charset(html2unicode($p['auteur']));
	$p['licence'] = unicode2charset(html2unicode($p['licence']));

	// Nom, slogan et branche
	if ($p['prefix'] == _SVP_PREFIXE_PLUGIN_THEME) {
		// Traitement specifique des themes qui aujourd'hui sont consideres comme des paquets
		// d'un plugin unique de prefixe "theme"
		$nom = _SVP_NOM_PLUGIN_THEME;
		$slogan = _SVP_SLOGAN_PLUGIN_THEME;
	}
	else {
		// Calcul *temporaire* de la nouvelles balise slogan si celle-ci n'est
		// pas renseignee et de la balise nom. Ceci devrait etre temporaire jusqu'a la nouvelle ere
		// glaciaire des plugins
		// - Slogan	:	si vide alors on prend la premiere phrase de la description limitee a 255
		$slogan = (!$p['slogan']) ? svp_remplir_slogan($p['description']) : $p['slogan'];
		// - Nom :	on repere dans le nom du plugin un chiffre en fin de nom
		//			et on l'ampute de ce numero pour le normaliser
		//			et on passe tout en unicode avec le charset du site
		$nom = svp_normaliser_nom($p['nom']);
	}
	
	return array(
		'plugin' => array(
			'prefixe' => $p['prefix'],
			'nom' => $nom,
			'slogan' => $slogan,
			'categorie' => $p['categorie'],
			'tags' => $p['tags']),
		'paquet' => array(
			'logo' => $p['icon'],
			'description' => $p['description'],
			'auteur' => $p['auteur'],
			'version' => $p['version'],
			'version_base' => $p['version_base'],
			'version_spip' => $v_spip,
			'etat' => $p['etat'],
			'etatnum' => $etatnum,
			'licence' => $p['licence'],
			'lien' => $p['lien'],
			'dependances' => serialize($dependances))
	);
}

function svp_remplir_slogan($description) {
	include_spip('inc/texte');

	// On extrait les multi
	$descriptions = extraire_trads($description);

	// On boucle sur chaque multi ou sur la chaine elle-meme en extrayant le slogan
	// dans les differentes langues
	$slogan = '';
	foreach ($descriptions as $_lang => $_descr) {
		if (!$_lang) {
			if ($_descr != '<multi>') {
				// Ce n'est pas un multi, c'est directement la chaine
				$multi = false;
				$position = strpos($_descr, '.');
				$slogan = ($position) ? substr($_descr, 0, $position) : couper($_descr, 150, '');
			}
			else {
				// C'est un multi
				$multi = true;
			}
		}
		if ($_lang) {
			// C'est un multi, on construit slogan en multi
			$position = strpos($_descr, '.');
			$slogan .= '[' . $_lang . ']' . (($position) ? substr($_descr, 0, $position) : couper($_descr, 150, ''));
		}
	}

	if ($multi)
		// On renvoie le slogan multi
		$slogan = '<multi>' . $slogan . '</multi>';
		
	return $slogan;
}

function svp_normaliser_nom($nom) {
	include_spip('inc/texte');

	// On extrait les traductions de l'eventuel multi
	// Si le nom n'est pas un multi alors le tableau renvoye est de la forme '' => 'nom'
	$noms = extraire_trads(str_replace(array('<multi>', '</multi>'), array(), $nom, $nbr_replace));
	$multi = ($nbr_replace > 0) ? true : false;
	
	$nouveau_nom = '';
	foreach ($noms as $_lang => $_nom) {
		$_nom = trim($_nom);
		if (!$_lang)
			$_lang = 'fr';
		$nbr_matches = preg_match(',(.+)(\s+[\d._]*)$,Um', $_nom, $matches);
		$nouveau_nom .= (($multi) ? '[' . $_lang . ']' : '') . 
						(($nbr_matches > 0) ? trim($matches[1]) : $_nom);
	}
	
	if ($nouveau_nom)
		// On renvoie un nouveau nom multi ou pas sans la valeur de la branche 
		$nouveau_nom = (($multi) ? '<multi>' : '') . $nouveau_nom . (($multi) ? '</multi>' : '');
		
	return $nouveau_nom;
}


// ----------------------- Analyses XML ---------------------------------

// parse un fichier de source dont l'url est donnee
// ce fichier est un fichier XML contenant <depot><...</depot>
// et <archives>...</archives>
function svp_xml_parse_depot($url){
	include_spip('inc/xml');
	include_spip('inc/distant');

	// On lit le fichier xml
	if (!$xml = recuperer_page($url)) {
		return false;
	}

	// On enleve la balise doctype qui provoque une erreur "balise non fermee" lors du parsage
	$xml = preg_replace('#<!DOCTYPE[^>]*>#','',$xml);

	// Deux cas d'erreur de fichier non conforme
	// - la syntaxe xml est incorrecte
	// - aucun plugin dans le depot
	// Si le bloc <depot> n'est pas renseigne on ne considere pas cela comme une erreur
	$arbre = array();
	if (!is_array($arbre = spip_xml_parse($xml)) OR !is_array($archives = $arbre['archives'][0])){
		return false;
	}

	// On extrait les informations du depot si elles existent (balise <depot>)
	$infos = array('depot' => array(), 'paquets' => array());
	if (is_array($depot = $arbre['depot'][0]))
		$infos['depot'] = svp_xml_aplatit_multiple(array('titre','descriptif','type'), $depot);
	if (!$infos['depot']['titre'])
		$infos['depot']['titre'] = _T('svp:titre_nouveau_depot');
	if (!$infos['depot']['type'])
		$infos['depot']['type'] = 'svn';

	// On extrait les informations de chaque plugin du depot (balise <archives>)
	foreach ($archives as $z=>$c){
		$c = $c[0];
		// si fichier zip, on ajoute le paquet dans la liste
		// - cas 1 : c'est un plugin donc on integre les infos du plugin
		// - cas 2 : c'est une archive non plugin, pas d'infos autres que celles de l'archive
		if ($url = $c['file'][0]) {
			if (is_array($c['plugin']))
				$plugin = svp_xml_parse_plugin($c['plugin'][0]);
			else
				$plugin = array();
			// On remplit les infos dans les deux cas
			$infos['paquets'][$url] = array(
				'plugin' => $plugin, 
				'file' => $url,
				'size' => $c['size'][0],
				'date' => $c['date'][0],
				'source' => $c['source'][0]
			);
		}
	}
	
	return $infos;
}


// aplatit plusieurs cles d'un arbre xml dans un tableau
// effectue un trim() au passage
function svp_xml_aplatit_multiple($array, $arbre){
	$a = array();
	// array('uri','archive'=>'zip',...)
	foreach ($array as $i=>$n){
		if (is_string($i)) $cle = $i;
		else $cle = $n;
		$a[$n] = trim(spip_xml_aplatit($arbre[$cle]));
	}
	return $a;	
}


// parse un plugin.xml genere par spip_xml_parse()
// en un tableau plus facilement utilisable
// cette fonction doit permettre de mapper des changements 
// de syntaxe entre plugin.xml et step
function svp_xml_parse_plugin($arbre){

	if (!is_array($arbre)) 
		return false;
	
	// on commence par les simples !
	$plug_arbre = svp_xml_aplatit_multiple(
				array('nom','icon','auteur','licence','version','version_base','etat','slogan','categorie','tags',
				'description','lien','options','fonctions','prefix','install'), 
				$arbre);
	$plug_arbre['prefix'] = strtolower($plug_arbre['prefix']);
	
	// on continue avec les plus complexes...	
	// 1) balises avec attributs
	foreach (array(
			'necessite'=>array('necessite', null),
			'utilise'=>array('utilise', null),
			'chemin'=>array('path', array('dir'=>'')))
				as $balise=>$p){
		$params = $res = array();
		// recherche de la balise et extraction des attributs
		if (spip_xml_match_nodes(",^$balise,",$arbre, $res)){
			foreach(array_keys($res) as $tag){
				list($tag,$att) = spip_xml_decompose_tag($tag);
				$params[] = $att;
			}
		} 
		// valeur par defaut
		else {
			if ($p[1]!==null)
				$params[] = $p[1];
		}
		$plug_arbre[$p[0]] = $params;		
	}

	return $plug_arbre;
}


// ----------------------- Recherches de plugins ---------------------------------

/**
 * Actualisation des plugins du depot uniquement. Sert aussi pour une premiere insertion
 *
 * @param string $phrase
 * @param string $categorie
 * @param string $etat
 * @param string $doublons
 * @param array $exclusions
 * @param string $version
 * @return array
 */

// $version		=> version SPIP affichee
// $doublons	=> indique si on accepte des doublons de plugins (meme prefixe, version differente)
// $exclusions	=> tableau d'id de plugin
function svp_rechercher_plugins($phrase, $categorie, $etat, $doublons=false, $exclusions=array(), $version='') {

	$plugins = array();
	
	// Selectionne les informations completes des paquets qui repondent aux criteres categorie, etat et exclusions
	// -- Preparation de la requete
	$from = array('spip_plugins AS t1', 'spip_paquets AS t2');
	$select = array('t1.nom AS nom', 't1.slogan AS slogan', 't1.prefixe AS prefixe', 
					't2.description AS description', 't2.version_spip AS version_spip',
					't2.auteur AS auteur', 't2.licence AS licence', 't2.etat AS etat',
					't2.logo AS logo', 't2.version AS version', 't2.id_paquet AS id_paquet');
	$where = array('t1.id_plugin=t2.id_plugin');
	if (($categorie) AND ($categorie != 'toute_categorie'))
		$where[] = 't1.categorie=' . sql_quote($categorie);
	if (($etat) AND ($etat != 'tout_etat'))
		$where[] = 't2.etat=' . sql_quote($etat);
	if ($exclusions)
		$where[] = sql_in('t2.id_plugin', $exclusions, 'NOT');

	// -- Controle des resultats avec la compatibilite SPIP et la phrase 
	if ($resultats = sql_select($select, $from, $where)) {
		// On normalise la phrase a chercher en une regexp utilisable
		$phrase = svp_normaliser_phrase($phrase);

		while ($paquets = sql_fetch($resultats)) {
			$prefixe = $paquets['prefixe'];
			$version = $paquets['version'];
			$nom = extraire_multi($paquets['nom']);
			$slogan = extraire_multi($paquets['slogan']);
			$description = extraire_multi($paquets['description']);
			if (svp_verifier_compatibilite_spip($paquets['version_spip'])
			AND svp_rechercher_phrase($phrase, $nom, $slogan, $description,	$score)) {
				// Le paquet remplit tous les criteres, on peut le selectionner
				// -- on utilise uniquement la langue du site et on ajoute le score
				$paquets['nom'] = $nom;
				$paquets['slogan'] = $slogan;
				$paquets['description'] = $description;
				$paquets['score'] = $score;
				if ($doublons)
					// ajout systematique du paquet
					$plugins[] = $paquets;
				else {
					// ajout 
					// - si pas encore trouve 
					// - ou si sa version est inferieure (on garde que la derniere version)
					if (!$plugins[$prefixe]
					OR ($plugins[$prefixe] AND spip_version_compare($plugins[$prefixe]['version'], $version, '<'))) {
						$plugins[$prefixe] = $paquets;
					}
				}
			}
		}
	}
	
	return $plugins;
}

/**
 * Recuperation des id des plugins a exclure car deja installes
 *
 * @return array
 */
function svp_lister_plugins_installes(){

	$ids = array();

	// On recupere la liste des plugins installes physiquement sur le site
	// Pour l'instant ce n'est pas possible avec les fonctions natives de SPIP
	// donc on se contente des plugins actifs
	// - liste des prefixes en lettres majuscules des plugins actifs
	include_spip('inc/plugin');
	$plugins = liste_plugin_actifs();

	// - liste des id de plugin correspondants
	//   Il se peut que certains plugins ne soient pas trouves dans la bdd car aucun zip n'est disponible
	//   (donc pas inclus dans archives.xml). C'est le cas des extensions du core
	$ids = sql_allfetsel('id_plugin', 'spip_plugins', sql_in('prefixe', array_keys($plugins)));
	$ids = array_map('reset', $ids);
	$ids = array_map('intval', $ids);

	return $ids;
}

function svp_rechercher_phrase($phrase, $nom, $slogan, $description, &$score){
	$trouve = false;
	$score = 0;

	if (!$phrase)
		$trouve = true;
	else {
		if (stripos($nom, $phrase) !== false)
			$score += 8;
		if (stripos($slogan, $phrase) !== false)
			$score += 4;
		if (stripos($description, $phrase) !== false)
			$score += 2;
		$trouve = ($score>0);
	}
	return $trouve;
}

function svp_verifier_compatibilite_spip($version){
	include_spip('inc/plugin');
	$version_spip = $GLOBALS['spip_version_branche'].".".$GLOBALS['spip_version_code'];
	return plugin_version_compatible($version, $version_spip);
}

function svp_normaliser_phrase($phrase){
	return $phrase;
}


?>
