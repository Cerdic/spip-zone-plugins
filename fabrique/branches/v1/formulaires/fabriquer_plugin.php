<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_fabriquer_plugin_identifier_dist(){
	return serialize(true);
}


function formulaires_fabriquer_plugin_charger_dist() {

	// v_spip = 3.0.0-beta2
	// v_futur = 3.0.*
	$v_spip = $GLOBALS['spip_version_branche'];
	$v_futur = explode('.', $v_spip);
	array_pop($v_futur);
	$v_futur = implode('.', $v_futur) . '.*';
	$contexte = array(
		'fabrique' => array(), // ne sert pas dans ce formulaire, mais un jour peut être
		'paquet' => array(
			'prefixe' => '',// 'Nouveau plugin',
			'version' => '1.0.0',
			'auteur' => $GLOBALS['visiteur_session']['nom'],
			'auteur_lien' => $GLOBALS['visiteur_session']['url_site'],
			'categorie' => '',
			'etat' => 'dev', // c'est du développement au debut, normalement.
			'compatibilite' => '[' . $v_spip . ';' . $v_futur . ']',
			'licence' => 'GNU/GPL',
			'schema' => '1.0.0',
			'fichiers' => array(),
			'exemples' => '', // inserer des exemples, de l'aide dans les fichiers.
		),
		'objets' => array(),
	);

	// On initialise la session si elle est vide
	if (is_null($fabrique = session_get(FABRIQUE_ID))){
		session_set(FABRIQUE_ID, $contexte);
		$fabrique = array();
	}
	
	// ça merge que la profondeur 1, c'est surtout la 2 qui interesse
	#$contexte = array_merge($contexte, $fabrique);
	foreach ($contexte as $c=>$v) {
		if (isset($fabrique[$c])) {
			$contexte[$c] = array_merge($contexte[$c], $fabrique[$c]);
		}
	}

	//on ajoute des informations d'image qui peuvent servir dans notre tableau de données.
	$contexte = fabrique_completer_contexte_images($contexte);

	//on ajoute des informations qui servent souvent dans notre tableau de données.
	$contexte = fabrique_completer_contexte($contexte);

	// et quelques autres informations de destination
	$destination = fabrique_destination();
	$contexte['destination'] = $destination;
	if (!$prefixe = $contexte['paquet']['prefixe']) {
		$prefixe = 'prefixe';
	} 
	$contexte['destination_plugin']        = $destination  . $prefixe . '/';
	$contexte['destination_ancien_plugin'] = $destination  . '.backup/' . $prefixe . '/';


	// on ouvre sur la tab d'un nouvel objet lorsqu'il est cree
	$tab = _request('open_tab', '');
	$contexte['open_tab'] = $tab;

	// on ouvre du coup le 2e accordion par defaut.
	// si l'objet a deja ete rempli une fois
	$accordion = _request('open_accordion', array());
	foreach ($contexte['objets'] as $c => $o) {
		if (isset($o['champs']) and count($o['champs'])) {
			$accordion[$c+1] = 1;
		}
	}
	$contexte['open_accordion'] = $accordion;

	// relou pour passer un message en plus depuis le traiter.
	$contexte['_message_info'] = _request('message_info');
	$contexte['_message_diff'] = _request('message_diff');
	$contexte['_message_diff_suppressions'] = _request('message_diff_suppressions');

	// es-t-on webmestre
	$contexte['webmestre'] = autoriser('webmestre');

	return $contexte;
}





function formulaires_fabriquer_plugin_verifier_dist(){

	// cas d'action qui n'ont pas a être testées ici.
	if (_request('f_action')) {
		return array(); // forcer aucune erreur
	}

	$erreurs = array();

	$paquet = _request('paquet');
	foreach (array('prefixe', 'version', 'categorie', 'etat', 'nom') as $obli) {
		if (!$paquet[$obli]) {
			$erreurs['paquet'][$obli] = _T("info_obligatoire");
		}
	}

	$objets = _request('objets');
	if (is_array($objets)) {
		foreach ($objets as $c => $o) {
			foreach (array('nom', 'table') as $obli) {
				if (!$o[$obli]) {
					$erreurs['objets'][$c][$obli] = _T("info_obligatoire");
				}
			}
			if (is_array($o['champs'])) {
				foreach ($o['champs'] as $n => $champ) {
					foreach (array('nom', 'champ', 'sql') as $obli) {
						if (!$champ[$obli]) {
							$erreurs['objets'][$c]['champs'][$n][$obli] = _T("info_obligatoire");
						}
					}
				}
			}
		}
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T("fabrique:erreurs");
	}

	return $erreurs;
}





function formulaires_fabriquer_plugin_traiter_dist(){
	include_spip('fabrique_fonctions');
	include_spip('formulaires/fabriquer_plugin_actions');

	$paquet = _request('paquet');
	$objets = _request('objets');
	if (!is_array($objets)) {
		$objets = array();
	}

	$data = array(
		'fabrique' => array('version' => FABRIQUE_VERSION),
		'paquet' => $paquet,
		'objets' => $objets,
	);

	// gerer les cas d'envoi d'images.
	// (avant les éventuels retours sur l'ajout / suppression d'objet !)
	fabrique_recuperer_et_stocker_les_images($data);


	// Gerer les actions de la fabrique,
	// avec des input.submit name=f_action[type]
	// exemple : ajouter / supprimer un objet, supprimer des logos,
	// deplacer des champs, tabs ...
	if ($f_action = _request('f_action')) {
		return fabrique_action_modification_formulaire($f_action, $data);
	}


	// calcul automatique de certains champs lorsqu'ils ne sont pas remplis
	foreach ($data['objets'] as $c => $o) {
		if ($o['table'] and !$o['cle_primaire']) {
			$data['objets'][$c]['cle_primaire'] = id_table_objet($o['table']);
		}
		if ($o['table'] and !$o['cle_primaire_sql']) {
			$data['objets'][$c]['cle_primaire_sql'] = "bigint(21) NOT NULL";
		}
		if ($o['table'] and !$o['table_type']) {
			$data['objets'][$c]['table_type'] = objet_type($data['objets'][$c]['cle_primaire']);
		}
	}

	// sinon c'est creation du plugin
	session_set(FABRIQUE_ID, $data);

	// creation du repertoire de stockage du plugin cree dans
	// plugins/fabrique/$prefixe (si possible), sinon dans
	// tmp/cache/fabrique/$prefixe/
	$destination = fabrique_destination();
	$prefixe = $paquet['prefixe']; // il nous sert souvent ce prefixe !

	// $_d... pour eviter des rtrim '/' en dessous
	$_destination_plugin        = $destination . $prefixe;
	$_destination_backup        = $destination . '.backup';
	$_destination_ancien_plugin = $_destination_backup . '/' . $prefixe;

	$destination_plugin         = $_destination_plugin . '/';
	$destination_backup         = $_destination_backup  . '/';
	$destination_ancien_plugin  = $_destination_ancien_plugin . '/';

	// liste des variables disponibles pour l'execution de scripts
	// (en plus de $data)
	$contexte_scripts = compact(
		'prefixe',
		'destination',
		'destination_plugin',
		'destination_backup',
		'destination_ancien_plugin');

	// executer des actions si demandees
	fabrique_executer_script('pre_copie', $data, $contexte_scripts);

	// on efface l'eventuelle sauvegarde et on colle l'eventuel plugin deja genere a la place
	supprimer_repertoire($_destination_ancien_plugin);
	sous_repertoire_complet($_destination_backup);
	// si la sauvegarde est encore présente, c'est que la suppression s'est mal passée
	// il vaut mieux quitter en indiquant une erreur
	if (is_dir($_destination_ancien_plugin)) {
		fabrique_remettre_contexte($data);
		return array(
			'editable'=>'oui',
			'message_erreur' => _T('fabrique:erreur_suppression_sauvegarde', array('dir'=>$_destination_ancien_plugin))
		);
	}
	
	if (is_dir($_destination_plugin)) {
		// try ne fonctionne pas sur rename()
		if (!@rename($_destination_plugin, $_destination_ancien_plugin)) {
			// une erreur sur le rename signifie qu'on a pas les droits suffisants sur $_destination_plugin
			fabrique_remettre_contexte($data);
			return array(
				'editable'=>'oui',
				'message_erreur' => _T('fabrique:erreur_copie_sauvegarde', array('dir'=>$_destination_plugin))
			);
		}
	}
	if (is_dir($_destination_plugin)) {
		supprimer_repertoire($_destination_plugin);
	}
	sous_repertoire_complet($_destination_plugin);

	// inutiles maintenant
	unset($_destination_plugin, $_destination_ancien_plugin, $_destination_backup);

	// sauver le descriptif de creation.
	// ce qui permet de pouvoir restaurer toutes les donnees de construction
	// afin de relancer la procédure avec des versions ulterieures du plugin

	// on nettoie les noms de fichiers images locaux avant
	$images = session_get(FABRIQUE_ID_IMAGES);
	foreach ($images['paquet']['logo'] as $taille => $im) {
		if (is_array($im)) { // evitons une fatale erreur (mais ca ne devrait pas arriver !)
			unset($images['paquet']['logo'][$taille]['fichier']);
		}
	}
	foreach ($images['objets'] as $c => $objet) {
		if (isset($objet['logo']) and is_array($objet['logo'])) { 
			foreach ($objet['logo'] as $taille => $im) {
				if (is_array($im)) { // evitons une fatale erreur (mais ca ne devrait pas arriver !)
					unset($images['objets'][$c]['logo'][$taille]['fichier']);
				}
			}
		}
	}
	// on nettoie les saisies checkbox de vue_liens qui peuvent ne rien envoyer
	// on nettoie une eventuelle table ayant servi a pre-remplir l'objet
	foreach ($data['objets'] as $c => $o) {
		if (!isset($o['vue_liens']) OR !is_array($o['vue_liens'])) {
			$o['vue_liens'] = array();
		}
		unset($data['objets'][$c]['renseigner_avec_table']);
	}


	// cas particulier data (+images) pour l'export.
	fabriquer_fichier("fabrique_prefixe.php", array_merge($data, array('data'=>array_merge($data, array('images' => $images))))); 
	fabrique_sauvegarde_tournante_export($destination_plugin . "fabrique_$prefixe.php", $destination_backup);

	// pour tous les autres, on ajoute des informations dans notre tableau de données
	$data = fabrique_completer_contexte_images($data); // liste des fichiers images presents (pour paquet.xml)
	$data = fabrique_completer_contexte($data); // raccourcis

	// definir les fichiers indispensables au paquet.xml / plugin :
	// si des fichiers sont indispensables aux objets
	// a cause des options d'un objet, on les ajoute a ceux deja demandes
	$fichiers = fabrique_fichiers_paquets($data);

	// creer le paquet.xml
	// en declarant tous les fichiers necessaires
	$fichiers_coches = isset($data['paquet']['fichiers']) ? $data['paquet']['fichiers'] : array();
	$data['paquet']['fichiers'] = $fichiers;
	fabriquer_fichier("paquet.xml", $data);
	// on remet ce qu'avait saisi l'utilisateur
	$data['paquet']['fichiers'] = $fichiers_coches;

	// creer la langue du paquet.
	fabriquer_fichier("lang/paquet-prefixe_fr.php", $data);

	// creer la langue du plugin
	fabriquer_fichier("lang/prefixe_fr.php", $data);

	// creer le formulaire de configuration
	if ($paquet['formulaire_config']) {
		// creer le squelette d'appel du formulaire
		fabriquer_fichier("prive/squelettes/contenu/configurer_prefixe.html", $data);

		// creer le formulaire de config (simple, type CFG avec un champ d'exemple)
		fabriquer_fichier("formulaires/configurer_prefixe.html", $data);
	}

	// creer le fichier d'administrations
	if ($paquet['administrations']) {
		fabriquer_fichier("prefixe_administrations.php", $data);
	}


	// creer le fichier d'options, de fonctions, autorisations et de pipelines
	foreach($fichiers as $fichier) {
		fabriquer_fichier("prefixe_$fichier.php", $data);
	}


	// gerer plus specifiquement les objets
	if (count($data['objets'])) {

		// creer le fichier de declaration de tables SQL
		fabriquer_fichier("base/prefixe.php", $data);

		// pour chaque objet
		foreach ($data['objets'] as $c => $objet) {

			// des raccourcis de plus pour les squelettes specifiques aux objets
			$data['objet']       = $objet;
			$data['id_objet']    = $objet['id_objet'];
			$data['type']        = $objet['type'];
			$data['table']       = $objet['table'];
			$data['mobjet']      = $objet['mobjet']; // m = majuscule
			$data['lobjet']      = $objet['objet'];  // l = lower, minuscule, car on ne peut pas utiliser 'objet'
			$data['mtype']       = $objet['mtype'];  // m = majuscule
			$data['mid_objet']   = $objet['mid_objet']; // m = majuscule

			// creer les langues
			fabriquer_fichier("lang/objet_fr.php", $data);

			// créer le formulaire d'edition
			fabriquer_fichier("formulaires/editer_objet.html", $data);
			fabriquer_fichier("formulaires/editer_objet.php", $data);

			// créer la vue du contenu d'un objet
			fabriquer_fichier("prive/objets/contenu/objet.html", $data);

			// créer la liste d'un objet
			fabriquer_fichier("prive/objets/liste/objets.html", $data);

			// créer les listes de liaison
			if (option_presente($objet, 'vue_liens')) {
				fabriquer_fichier("prive/objets/liste/objets_lies.html", $data);
				fabriquer_fichier("prive/objets/liste/objets_lies_fonctions.php", $data); // pff
				fabriquer_fichier("prive/objets/liste/objets_associer.html", $data);
				fabriquer_fichier("prive/objets/liste/objets_associer_fonctions.php", $data); // pff
				// ce fichier est nécessaire
				fabriquer_fichier("prive/squelettes/contenu/objet_edit.html", $data);

				// la meme chose avec des roles s'il y en a
				if (option_presente($objet, 'roles')) {
					fabriquer_fichier("prive/objets/liste/objets_roles_lies.html", $data);
					fabriquer_fichier("prive/objets/liste/objets_roles_lies_fonctions.php", $data); // pff
					fabriquer_fichier("prive/objets/liste/objets_roles_associer.html", $data);
					fabriquer_fichier("prive/objets/liste/objets_roles_associer_fonctions.php", $data); // pff
				}
			}

			// si traductions demandees, creer le fichier de pre-chargement
			if (champ_present($objet, 'id_trad')) {
				fabriquer_fichier("inc/precharger_objet.php", $data);
			}

			// fichiers echafaudes par SPIP mais demandes explicitement
			if (isset($objet['echafaudages']) AND is_array($objet['echafaudages'])) {
				foreach ($objet['echafaudages'] as $fichier) {
					fabriquer_fichier($fichier, $data);
				}
			}

			// saisies demandees
			if (isset($objet['saisies']) and is_array($objet['saisies'])) {
				foreach ($objet['saisies'] as $saisie) {
					fabriquer_fichier('saisies/' . $saisie . '.html', $data);
					fabriquer_fichier('saisies-vues/' . $saisie . '.html', $data);
					// si parentee presente, saisie propose un mode recursif
					if (champ_present($objet, 'id_parent')) {
						fabriquer_fichier('saisies/_' . $saisie . '_recurs.html', $data);
					}
				}
			}

		}

		unset($data['objet'],  $data['id_objet'], $data['type'],  $data['table']);
		unset($data['mobjet'], $data['lobjet'],   $data['mtype'], $data['mid_objet']);

	}

	// creer les images
	// logo du plugin
	$images = session_get(FABRIQUE_ID_IMAGES);
	if ($i = $images['paquet']['logo'][0]['fichier']) {
		fabriquer_miniature($prefixe, $i, $prefixe, 128);
		fabriquer_miniature($prefixe, $i, $prefixe, 64);
		fabriquer_miniature($prefixe, $i, $prefixe, 32);
	}
	// logos des objets
	foreach ($images['objets'] as $c => $image) {
		$obj = $data['objets'][$c]['type'];
		$i_precedent = '';
		// on prend en priorite la taille desiree,
		// sinon la plus proche, avant,
		// sinon le logo de l'objet, sinon le logo du plugin.
		foreach (array(32, 24, 16, 12) as $taille) {
			if ((isset($image['logo'][$taille]) AND $i = $image['logo'][$taille]['fichier'])
			OR ($i = $i_precedent)
			OR (isset($image['logo'][0]['fichier']) and $i = $image['logo'][0]['fichier']) 
			OR (isset($images['paquet']['logo'][0]['fichier']) and $i = $images['paquet']['logo'][0]['fichier'])) { 
				$i_precedent = $i; // privilegier l'image juste plus grande que la precedente
				fabriquer_miniature($prefixe, $i, $obj, $taille);
				if ($data['objets'][$c]['logo_variantes'] && $taille >= 16) {
					fabriquer_miniature($prefixe, $i, $obj, $taille, 'new');
					fabriquer_miniature($prefixe, $i, $obj, $taille, 'add');
					fabriquer_miniature($prefixe, $i, $obj, $taille, 'del');
					fabriquer_miniature($prefixe, $i, $obj, $taille, 'edit');
				} elseif ($taille == 16) {
					// creer la variante 16+ qui sert a la barre outils_rapides
					fabriquer_miniature($prefixe, $i, $obj, $taille, 'new');
				}
			}
		}
	}

	// executer des actions si demandees
	fabrique_executer_script('post_creation', $data, $contexte_scripts);


	// supprimer tous les fichiers .ok des sous-répertoires de destination
	$fichiers =
		new RegexIterator(
		new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($destination . $prefixe)), '/[.]ok$/');
	foreach ($fichiers as $f) {
		supprimer_fichier((string)$f);
	}
	// activer un plugin peut creer un .ok a la racine de celui ci (avec skeleditor actif !).
	supprimer_fichier($destination_ancien_plugin . '.ok');


	// calcul du diff avec l'ancien plugin
	fabrique_generer_diff($destination_ancien_plugin, $destination_plugin, $prefixe);

	// ne pas prendre les champs postes
	// ils sont sauves en session php
	set_request('objets', null);
	set_request('paquet', null);

	// enlever les erreurs de plugin qui ont pu arriver
	// si l'on recree un plugin actif dans fabrique_auto.
	include_spip('inc/plugin');
	actualise_plugins_actifs();

	// indiquer qu'on peut creer le repertoire dans plugins.
	if (!is_writable(_DIR_PLUGINS . rtrim(FABRIQUE_DESTINATION_PLUGINS, '/'))) {
		set_request("message_info", _T('fabrique:repertoire_plugin_fabrique', array(
			'dir' => rtrim(FABRIQUE_DESTINATION_PLUGINS, '/'),
			'dir_cache' => rtrim(FABRIQUE_DESTINATION_CACHE, '/'))));
	}

	$res = array(
		'editable'=>'oui',
		'message_ok' => _T('fabrique:plugin_cree_succes_dans', array('dir'=>$destination_plugin))
	);
	return $res;
}


// cherche un fichier dans la fabrique,
// le calcule et le copie dans le répertoire du futur plugin
function fabriquer_fichier($chemin, $data) {
	static $reps = array(); // repertoire de destination deja crees.
	static $chemins = array(); // fichiers sources deja utilises
	// retrouver la destination de copie des fichiers
	$destination = fabrique_destination();
	$destination .= $data['paquet']['prefixe'] . '/';

	// on retrouve le nom du fichier et la base du chemin de destination
	$dest = explode('/', $chemin);
	$nom = array_pop($dest);
	$chemin_dest = implode('/', $dest);

	// ne pas creer systematiquement les repertoires tout de meme.
	if (!isset($reps[$chemin_dest])) {
		sous_repertoire_complet($destination . $chemin_dest);
		$reps[$chemin_dest] = true;
	}

	// on modifie le nom de destination :
	// 'prefixe' => $prefixe.
	// 'objet'   => $objet.
	$nom = str_replace('prefixe', $data['paquet']['prefixe'], $nom);
	if (isset($data['objet'])) {
		$nom = str_replace('objets',  $data['objet']['lobjet'], $nom);
		$nom = str_replace('objet',   $data['objet']['type'], $nom);
	}

	// calcul du squelette et copie a destination du contenu.
	$contenu = recuperer_fond(FABRIQUE_SKEL_SOURCE . $chemin, $data);

	ecrire_fichier($destination . $chemin_dest . '/' . $nom, $contenu);
}



// reduit une image dont l'adresse est donnée,
// et la place dans prive/themes/spip/images du futur plugin
function fabriquer_miniature($prefixe, $src, $nom, $taille=128, $variante='') {
	// retrouver la destination de copie des fichiers
	$destination = fabrique_destination();
	$destination .= $prefixe . '/';
	// creer une fois l'arborescence de destination
	static $chemin = false;
	if (!$chemin) {
		$chemin = "prive/themes/spip/images";
		sous_repertoire_complet($destination . $chemin);
	}

	include_spip('inc/filtres_images');
	// passer en png
	$img = filtrer('image_format', $src, 'png');
	// reduire et graver
	$img = filtrer('image_passe_partout', $img, $taille, $taille);
	if ($variante and $img_variante = find_in_path("prive/themes/spip/images/$variante-$taille.png")) {
		$img = filtrer('image_masque', $img, $img_variante, 'mode=normal');
	}
	$img = filtrer('image_graver', $img);
	$src_img = extraire_attribut($img, 'src');
	// pas de ?date pour recuperer le contenu
	$src_img = explode('?', $src_img);
	$src_img = array_shift($src_img);
	$contenu = spip_file_get_contents($src_img);
	if ($contenu) {
		$nom = $nom . ($variante ? "-$variante" : ''). "-$taille.png";
		ecrire_fichier($destination . "$chemin/$nom", $contenu);
	}
}



// complete la description du paquet des fichiers indispensables
// pour les objets demandes
function fabrique_fichiers_paquets($data) {
	$fichiers = array();
	if (isset($data['paquet']['fichiers']) AND is_array($data['paquet']['fichiers'])) {
		$fichiers = $data['paquet']['fichiers'];
	}
	
	// si tout est coche deja, on sort !
	if (count($fichiers) == 4) {
		return $fichiers;
	}

	if (!in_array('pipelines', $fichiers)) {
		if (fabrique_necessite_pipeline($data['objets'], 'optimiser_base_disparus')
		OR  fabrique_necessite_pipeline($data['objets'], 'affiche_enfants')
		OR  fabrique_necessite_pipeline($data['objets'], 'affiche_auteurs_interventions')
		OR  fabrique_necessite_pipeline($data['objets'], 'affiche_milieu')) {
			$fichiers[] = 'pipelines';
		}
	}

	if (!in_array('autorisations', $fichiers)) {
		if (fabrique_necessite_pipeline($data['objets'], 'autoriser')) {
			$fichiers[] = 'autorisations';
		}
	}

	return $fichiers;
}


/**
 * Remet les infos de contexte dans l'environnement
 * - parce qu'on en ajoute par rapport à ce qui est posté -
 * afin de réafficher correctement le formulaire si on a des erreurs
 * dans la partie traiter(), car dans ce cas, le formulaire ne repasse pas dans le charger().
 *
 * @param array $data
 * 		Les infos postées
**/
function fabrique_remettre_contexte($data) {
	// on reintroduit le contexte complet, parce que l'erreur ne repasse pas dans charger() dans ce cas.
	$data = fabrique_completer_contexte_images($data); // liste des fichiers images presents (pour paquet.xml)
	$data = fabrique_completer_contexte($data); // raccourcis
	foreach($data as $nom => $valeur) {
		set_request($nom, $valeur);
	}
}

// on complete avec des donnees qui servent souvent les informations de data.
// pour se simplifier (un peu) les squelettes, et éviter de multiples calculs
// (type, table, id_objet, objet...)
function fabrique_completer_contexte($data) {
	$data['prefixe']  = $data['paquet']['prefixe'];
	$data['mprefixe'] = strtoupper($data['paquet']['prefixe']); // m = majuscule
	$data['exemples'] = $data['paquet']['exemples'];
	if (!is_array($data['objets'])) $data['objets'] = array();
	$data['les_objets'] = $data['les_types'] = $data['les_id_objets'] = array();
	foreach( $data['objets'] as $c => $o ) {
		// quelques raccourcis
		if (isset($o['table']) and $o['table']) {
			$data['objets'][$c]['objets_surnoms'] = array();

			// si la table est different de spip_xxs
			// ou si elle possede des espaces spip_xx_yys
			// on indique qu'il faudra creer
			// des surnoms car ce n'est pas un nommage standard.
			// on s'appuie sur la table pour calculer l'objet (pluriel en general)
			$preparation_objet = preg_replace(',^spip_|^id_|s$,', '', $o['table']);
			// table avec espace
			if (false !== strpos($preparation_objet, '_')) {
				// on prend en surnom d'objet le type sans son espace
				$data['objets'][$c]['objets_surnoms'][] = str_replace('_', '', $preparation_objet); // xxyy = xx_yy
			}
			// table non standard sans s
			if (rtrim($o['table'], 's') == $o['table']) {
				// la table n'est pas standard.
				$data['objets'][$c]['objets_surnoms'][] = $o['table_type']; // xx
				$data['les_objets'][] = $data['objets'][$c]['objet'] = $preparation_objet;
			}
			// table standard avec s
			else {
				$data['les_objets'][] = $data['objets'][$c]['objet'] = table_objet($o['table']);
			}
			$data['les_types'][]     = $data['objets'][$c]['type']     = $o['table_type'];
			$data['les_id_objets'][] = $data['objets'][$c]['id_objet'] = $o['cle_primaire'];
			// l'objet en majuscule
			$data['objets'][$c]['mobjet']     = strtoupper($data['objets'][$c]['objet']);
			$data['objets'][$c]['mtype']      = strtoupper($data['objets'][$c]['type']);
			$data['objets'][$c]['mid_objet']  = strtoupper($data['objets'][$c]['id_objet']);
			// l'objet en minuscule
			$data['objets'][$c]['lobjet'] = $data['objets'][$c]['objet']; // etre coherent avec l'appel de squelettes specifiques a un objet
			// la table de liens
			if ($o['table_liens']) {
				$data['objets'][$c]['nom_table_liens'] = $o['table'] . '_liens';
			}
		}
		// mettre les majuscules sur les champs aussi dans mchamp
		if (isset($o['champs']) and is_array($o['champs'])) {
			foreach ($o['champs'] as $j => $champ) {
				if (isset($champ['champ'])) {
					 $data['objets'][$c]['champs'][$j]['mchamp'] = strtoupper($champ['champ']);
				}
			}
		}
	}
	// fabrique_lister_tables() apres avoir ajoute les infos en plus sur les objets
	// pour pouvoir les utiliser dedans.
	$data['les_tables']        = fabrique_lister_tables($data['objets']);
	$data['les_tables_objets'] = fabrique_lister_tables($data['objets'], 'objets');
	$data['les_tables_liens']  = fabrique_lister_tables($data['objets'], 'liens');
	return $data;
}



// on complete avec le nom des fichiers d'image le tableau data.
function fabrique_completer_contexte_images($data) {

	// gestion des images
	// en dehors des donnees postees
	// pour ne pas surcharger le formulaire
	// et poster à chaque fois les images.
	$images = array(
		'paquet' => array(
			'logo' => array(
				0 => array(
					'fichier' => '',
					'extension' => '',
					'contenu' => '',
				)
			)
		),
		'objets' => array()
	);

	// On initialise la session image si elle est vide
	if (is_null($fabrique_images = session_get(FABRIQUE_ID_IMAGES)) OR !is_array($fabrique_images)){
		session_set(FABRIQUE_ID_IMAGES, $images);
		$fabrique_images = array();
	}

	// On merge avec ce que l'on a dans la session
	foreach ($images as $c=>$v) {
		if (isset($fabrique_images[$c])) {
			$images[$c] = array_merge($images[$c], $fabrique_images[$c]);
		}
	}

	// on ajoute la localisation de l'image dans le contexte si presente
	// logo de plugin
	if (isset($images['paquet']['logo'][0]['fichier']) and $f = $images['paquet']['logo'][0]['fichier']) {
		$data['paquet']['logo'][0]['fichier'] = $f;
	}
	// logo des objets
	foreach ($images['objets'] as $c=>$image) {
		foreach (array(0, 32, 24, 16, 12) as $taille) {
			if (isset($image['logo'][$taille]['fichier']) and $f = $image['logo'][$taille]['fichier']) {
				$data['objets'][$c]['logo'][$taille]['fichier'] = $f;
			}
		}
	}

	return $data;
}


/**
 * Complete les informations d'un objet
 * en fonction de la table SQL qui a ete demande.
 *
 * On essaie d'extraire de la table le plus d'info possibles.
**/
function fabrique_renseigner_objet($objet) {
	$table = $objet['renseigner_avec_table'];
	if (!$table) {
		return $objet;
	}

	// 'spip_articles' ou
	// 'autreconnect:spip_articles'
	list($connect, $table) = array_pad(explode(':', $table), 2, null);
	if (!$table) {
		$table = $connect;
		$connect = '';
	}

	// extraire le prefixe si present, pour remettre spip_
	$desc = sql_showtable($table, false, $connect); // avant l'utiliation de la globale connexions.
	$connexion = $GLOBALS['connexions'][$connect ? $connect : 0];
	if ($prefixe = $connexion['prefixe']) {
		$table_spip = preg_replace("/^$prefixe/", 'spip', $table);
	} else {
		$table_spip = $table;
	}

	// prefixer systematiquement de spip_ la table
	if (substr($table_spip,0,5) !== 'spip_') {
		$table_spip = 'spip_' . $table_spip;
	}

	// definir la table et le nom.
	$objet['table'] = $table_spip;
	$objet['nom'] = ucfirst(str_replace('_', ' ', substr($table_spip, 5)));
	$objet['nom_singulier'] = rtrim($objet['nom'], 's');

	// analyser les champs
	$fields = $desc['field'];

	// id_rubrique
	if (isset($fields['id_rubrique'])) {
		if (!isset($objet['rubriques'])) {
			$objet['rubriques'] = array();
		}
		if (!in_array('id_rubrique', $objet['rubriques'])) {
			$objet['rubriques'][] = 'id_rubrique';
		}
		unset($fields['id_rubrique']);
	}

	// lang
	if (isset($fields['lang'])) {
		if (!isset($objet['langues'])) {
			$objet['langues'] = array();
		}
		if (!in_array('lang', $objet['langues'])) {
			$objet['langues'][] = 'lang';
		}
		unset($fields['lang'], $fields['langue_choisie']);
	}

	// id_trad
	if (isset($fields['id_trad'])) {
		if (!isset($objet['langues'])) {
			$objet['langues'] = array();
		}
		if (!in_array('id_trad', $objet['langues'])) {
			$objet['langues'][] = 'id_trad';
		}
		// il faut absolument lang pour id_trad !
		if (!in_array('lang', $objet['langues'])) {
			$objet['langues'][] = 'lang';
		}
		unset($fields['id_trad']);
	}


	// on enleve la cle primaire (elle sera automatiquement ajoutée)
	$pk = $desc['key']['PRIMARY KEY'];
	unset($fields[$pk]);
	$objet['cle_primaire'] = $pk;

	// on enleve 'maj' (il sera automatiquement ajouté)
	unset($fields['maj']);

	// 'statut'
	if (isset($fields['statut'])) {
		unset($fields['statut']);
		$objet['statut'] = 'on';
	}

	// champ titre
	foreach (array('titre', 'nom') as $titre) {
		if (isset($fields[$titre])) {
			$objet['champ_titre'] = $titre;
			break;
		}
	}

	// champ date
	foreach (array('date', 'date_publication') as $date) {
		if (isset($fields[$date])) {
			$objet['champ_date'] = $date;
			// ce champ sera automatiquement ajouté
			unset($fields[$date]);
			break;
		}
	}

	// pour tous les autres. Il faut les créer.
	$champs = isset($objet['champs']) ? $objet['champs'] : array();

	// on enleve tous les champs qui ne sont pas de cette table
	foreach ($champs as $c => $champ) {
		if (!isset($fields[$champ['champ']])) {
			unset($champs[$c]);
		}
	}
	$champs = array_values($champs);

	// on ajoute (ou modifie) tous les champs de cette table
	foreach ($fields as $colonne => $sql) {
		// on n'ajoute pas un champ qui existe deja
		// on le modifie simplement
		$present = false;
		foreach ($champs as $c => $champ) {
			if ($champ['champ'] == $colonne) {
				$present = true;
				break;
			}
		}
		if (!$present) {
			$champs[] = array();
			$c = count($champs)-1;
		}

		$champs[$c]['champ'] = $colonne;
		$champs[$c]['nom']   = ucfirst(str_replace('_', ' ', $colonne));
		$champs[$c]['sql']   = $sql;
	}
	$objet['champs'] = $champs;

	// On enleve les chaines de langue
	unset($objet['chaines']);

	return $objet;
}
?>
