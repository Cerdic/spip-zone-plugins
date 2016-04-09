<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2auteurs_importation_charger_dist() {
	$annee=date("Y");
	$valeurs = array(
		"fichier_csv"                => "",
		"maj_utilisateur"            => "",
		"type_maj"                   => "ajouter",
		"abs_redac"                  => "",
		"abs_admin"                  => "",
		"abs_poubelle"               => "supprimer",
		"abs_visiteur"               => "",
		"traitement_article_efface"  => "rien_faire",
		"transfere_article"          => "",
		"id_rubrique_parent_archive"    => "0",
		"nom_rubrique_archive"       => "archive_$annee",
		"id_rubrique_parent"            => "0",
	);

	return $valeurs;
}

function formulaires_csv2auteurs_importation_verifier_dist() {
	$erreurs = array();
	// seuls les webmestres ont le droit d'utiliser cet outil!
	if ($GLOBALS['visiteur_session']['webmestre'] != 'oui') {
		$erreurs['message_erreur'] = _T('csv2auteurs:non_autorise');
		return $erreurs;
	}

	//champs obligatoire 
	if (!($_FILES['fichier_csv']['name'])) {
		$erreurs['fichier_csv'] = _T('csv2auteurs:obligatoire');
	} else {
		//Transfert réussi
		if ($_FILES['fichier_csv']['error'] > 0) 
		$erreurs['fichier_csv'] = _T('csv2auteurs:transfert');
		//Taille max du fichier csv < 2Mo
		$maxsize=1000000;
		if ($_FILES['fichier_csv']['size'] > $maxsize) 
		$erreurs['fichier_csv'] =_T('csv2auteurs:taille');
		//Extension csv
		$extensions_valides = array( 'csv','txt' );
		$extension_upload = strtolower(  substr(  strrchr($_FILES['fichier_csv']['name'], '.')  ,1)  );
		if (!in_array($extension_upload,$extensions_valides)) 
		$erreurs['fichier_csv'] = _T('csv2auteurs:extension');
	}
	//Il y a des erreurs
	if (count($erreurs)) 
	$erreurs['message_erreur'] = _T('csv2auteurs:erreurs');

	return $erreurs;
}

function formulaires_csv2auteurs_importation_traiter_dist() {
	$retour = array();
	$maj_utilisateur = _request('maj_utilisateur');
	$abs_redacs = _request('abs_redac');
	$abs_admins = _request('abs_admin');
	$abs_visiteurs = _request('abs_visiteur');
	$abs_poubelle = _request('abs_poubelle');
	$suppression_article_efface = _request('suppression_article_efface');
	$traitement_article_efface = _request('traitement_article_efface');
	$nom_rubrique_archive = _request('nom_rubrique_archive');
	$type_maj=_request('type_maj');

	// recuperation de l'id de la rubrique parent des rubriques admins 
	// NB: le sélecteur de rubrique retourne un champ rubrique_parent[] avec une valeur du style "rubrique|17"
	if (is_array(_request('rubrique_parent'))) {
		$id_rubrique_parent_admin = _request('rubrique_parent');
		$id_rubrique_parent_admin = explode('|',$id_rubrique_parent_admin[0]);
		$id_rubrique_parent_admin = $id_rubrique_parent_admin[1];
	}
	else
	$id_rubrique_parent_admin = 0;

	//récupération de l'id de la rubrique parent archive
	if (is_array(_request('rubrique_parent_archive'))) {
		$id_rubrique_parent_archive = _request('rubrique_parent_archive');
		$id_rubrique_parent_archive = explode('|',$id_rubrique_parent_archive[0]);
		$id_rubrique_parent_archive = $id_rubrique_parent_archive[1];
	}
	else 
	$id_rubrique_parent_archive = 0;

	include_spip('action/editer_rubrique');
	if (test_plugin_actif("accesrestreint"))
	include_spip('action/editer_zone');
	include_spip('action/editer_auteur');

	if ($abs_redacs OR $abs_admins OR $abs_visiteurs OR $abs_poubelle == 'supprimer'){
		include_spip('action/editer_objet');
		include_spip('action/editer_liens');
		include_spip('action/editer_zone');
	}

	// récupération du fichier csv
	include_spip('inc/config');
	$separateur  = lire_config("csv2auteurs_separateur");
	$tmp_name    = $_FILES['fichier_csv']['tmp_name'];
	$destination = _DIR_TMP.basename($tmp_name);
	$resultat    = move_uploaded_file($tmp_name,$destination);
	if (!$resultat) 
	$retour['message_erreur'] = _T('csv2auteurs:transfert');
	else 
	$retour['message_ok'] = _T('csv2auteurs:chargement_fichier_CSV_OK', array('nom_fichier' => $_FILES['fichier_csv']['name']));


	// transformation du fichier csv en 4 array : 
	// $en_tete = ligne entete 
	// pour les 3 tableaux suivant, la cle est soit le login et s'il n'existe pas on prend le mail
	// $tableau_csv_visiteurs
	// $tableau_csv_redacs
	// $tableau_csv_admins
	$tableau_csv_visiteurs = $tableau_csv_redacs = $tableau_csv_admins = array();
	$tableau_csv_rubriques_admins = array();
	$fichiercsv = fopen($destination, "r");

	// correspondance statut spip / statut csv (+ admettre les status spip ou les status abrégés dans le CSV)
	$Tcorrespondances = array('administrateur'=>'0minirezo', 'redacteur'=>'1comite', 'rédacteur'=>'1comite', 'visiteur'=>'6forum', 'poubelle' => '5poubelle',
	'0minirezo'=>'0minirezo', '1comite'=>'1comite', '6forum'=>'6forum', '5poubelle' => '5poubelle',
	'admin'=>'0minirezo', 'redac'=>'1comite', 'visit'=>'6forum', 'poub' => '5poubelle');

	// tableau de tous les admins
	$result = sql_select(array('login'), 'spip_auteurs', array('statut = "0minirezo"'));
	while ($r = sql_fetch($result)) {
		$Tadmin_tous[] = $r['login'];
	}
	// tableau des admins restreints
	$Tadmin_restreint=array();
	$from = array( 
		"spip_auteurs AS auteurs",
		"spip_auteurs_liens AS liens"
	);
	$where = array(
		"auteurs.statut = '0minirezo'",
		"liens.objet = 'rubrique'",
		"liens.id_auteur = auteurs.id_auteur",
		'login!=""'
	);
	$result = sql_select(array('login'),$from, $where);
	while ($r = sql_fetch($result)) 
	$Tadmin_restreint[] = $r['login'];

	// tableau admins complets
	$Tadmin_complet = array_diff($Tadmin_tous, $Tadmin_restreint);


	// traiter le fichier CSV
	$i = 0;
	$num_statut = $num_login = -1;
	while (($data = fgetcsv($fichiercsv, "$separateur")) !== FALSE) {
		// petit hack car fgetcsv ne reconnait pas le ~ comme séparateur !!!
		$data           = implode("$separateur", $data);
		$data           = explode("$separateur", $data);
		$nombre_elements = count($data);

		// régler le problème des champs CSV avec des espaces qui trainent en début ou fin de chaine
		$data = array_map('trim', $data);

		// Récupération de la ligne d'entete
		if ($i == 0) {
			for ($j = 0; $j < $nombre_elements; $j++) {
				$en_tete[$j] = strtolower($data[$j]);    
				if ($en_tete[$j] == 'statut')
				$num_statut = $j;
				if ($en_tete[$j] == 'login')
				$num_login = $j;
			}
			if ($num_statut < 0 OR $num_login < 0) {
				$retour['message_erreur'] = _T('csv2auteurs:champ_manquant').' login:'.$num_login.' statut'.$num_statut;
				return  $retour;
			}
		} 
		else {
			// on ne veut pas les auteurs du CSV ayant un login égal à celui d'un admin complet
			if (in_array($data[$num_login], $Tadmin_complet)) {
				$retour['message_ok'] .= '<br />'._T('csv2auteurs:login_idem_admin_non_traite', array('login_refuse' => $data[$num_login]));
				continue;
			}
			for ($j = 0; $j < $nombre_elements; $j++) {
				if (($data[$num_login] AND !in_array($data[$num_login], $Tadmin_complet))) {
					// creation du tableau contenant l'ensemble des données à importer
					if ($Tcorrespondances[strtolower($data[$num_statut])] == '6forum')
					$tableau_csv_visiteurs[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "6forum" : $data[$j];

					elseif ($Tcorrespondances[strtolower($data[$num_statut])] == '1comite')
					$tableau_csv_redacs[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "1comite" : $data[$j];

					elseif ($Tcorrespondances[strtolower($data[$num_statut])] == '0minirezo') {
						$tableau_csv_admins[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "0minirezo" : $data[$j];
						// récup des rubriques pour les admins restreints
						if ($en_tete[$j] == 'ss_groupe' AND $data[$j]) {
							$Trub = explode('|', $data[$j]);
							foreach ($Trub as $rub) 
							if (!in_array($rub, $tableau_csv_rubriques_admins))
							$tableau_csv_rubriques_admins[] = $rub;
						}
					}
					// si pas de statut reconnu on passe en visiteur
					else {
						$tableau_csv_visiteurs[$data[$num_login]][$en_tete[$j]] = $en_tete[$j] == "statut" ? "6forum" : $data[$j];
						if ($en_tete[$j] == "login")
						$retour['message_ok'] .= '<br />'._T('csv2auteurs:statut_absent', array('login_auteur' => $data[$j]));
					}
				}
			}
		}
		$i++;
	}
	fclose($fichiercsv);
	unlink($destination);
	$retour['message_ok'] .= '<br />'._T('csv2auteurs:nbe_auteurs_a_traiter', array('nb_auteurs' => $i - 1));

	// tableau CSV total
	$tableau_csv_total = $tableau_csv_visiteurs + $tableau_csv_redacs + $tableau_csv_admins;

	//spip_log("tableau csv total","csvspip");
	//spip_log($tableau_csv_total,"csvspip");

	//récupération des auteurs de la bdd en 4 array
	// on ne prend pas les auteurs sans login
	// $poubelle_bdd = les auteurs à la poubelle
	// $visiteur_bdd = les visiteurs
	// $redacteur_bdd
	// $admin_restreint_bdd
	// la cle de chaque tableau est le login
	$poubelle_bdd = $visiteur_bdd = $redacteur_bdd = $admin_restreint_bdd = array();
	$visiteur_bdd_req = sql_allfetsel('*', 'spip_auteurs', array('statut="6forum"','(login!="")'));
	foreach ($visiteur_bdd_req as $key) {
		$visiteur_bdd[$key['login']] = $key;
	}
	$redacteur_bdd_req = sql_allfetsel('*', 'spip_auteurs', array('statut="1comite"','(login!="")'));
	foreach ($redacteur_bdd_req as $key) {
		$redacteur_bdd[$key['login']] = $key;
	}
	//on récupère seulement les admins restreints !!!
	$from = array( 
		"spip_auteurs AS auteurs",
		"spip_auteurs_liens AS liens"
	);
	$where = array(
		"auteurs.statut = '0minirezo'",
		"liens.objet = 'rubrique'",
		"liens.id_auteur = auteurs.id_auteur",
		'(login!="")'
	);
	$admin_restreint_bdd_req = sql_allfetsel("DISTINCT auteurs.*" ,$from, $where);
	foreach ($admin_restreint_bdd_req as $key) {
		$admin_restreint_bdd[$key['login']] = $key;
	}

	// tableau BDD total
	$tableau_bdd_total = $poubelle_bdd + $visiteur_bdd +  $redacteur_bdd + $admin_restreint_bdd;

	// traitement rubriques admin
	// construction du tableau de correspondance nom_rubrique avec leur id
	// création des rubriques n'existant pas
	$tableau_bdd_rubriques_admins = array();
	$result = sql_select(array('id_rubrique', 'titre'), 'spip_rubriques');
	while ($row = sql_fetch($result)) {
		$tableau_bdd_rubriques_admins[$row['id_rubrique']] = strtolower($row['titre']);
	}

	// traitement zones
	// construction du tableau de correspondance nom_zone avec leur id
	$tableau_bdd_zones_admins = array();
	if (test_plugin_actif("accesrestreint")) {
		$result = sql_select(array('id_zone', 'titre'), 'spip_zones');
		while ($row = sql_fetch($result)) {
			$tableau_bdd_zones_admins[$row['id_zone']] = strtolower($row['titre']);
		}
	}
	//    spip_log($tableau_bdd_zones_admins,"csvspip");

	// créer les rubriques admins du csv n'existant pas et les indexer
	// le champ ss_groupe du fichier CSV peut contenir le titre ou  l'id de la rubrique
	// pour la création il faut un titre
	$nb_rub_crees = 0;
	foreach ($tableau_csv_rubriques_admins as $num_rub => $rub) {
		if (!in_array(strtolower($rub), $tableau_bdd_rubriques_admins) AND !is_numeric($rub)) {
			$set = array('titre' => $rub);
			$id_rub = rubrique_inserer($id_rubrique_parent_admin);
			rubrique_modifier($id_rub, $set);
			$tableau_bdd_rubriques_admins[$id_rub] = strtolower($rub);
			$nb_rub_crees++;
		}
	}
	if ($nb_rub_crees > 0)
	$retour['message_ok'] .= '<br />'._T('csv2auteurs:nbe_rubriques_admin_crees', array('nb_rubriques' => $nb_rub_crees));

	//Récuperer les champs de la table auteurs
	$Tnom_champs_bdd = array();
	$desc = sql_showtable('spip_auteurs',true);
	foreach ($desc['field'] as $cle => $valeur)
	$Tnom_champs_bdd[] = $cle;


	// PARTIE I : maj ou ajout des auteurs
	$tableau_nouveaux_auteurs = array_diff_key($tableau_csv_total, $tableau_bdd_total);
	// si maj demandée
	if ($maj_utilisateur) {
		// construire le tableau des utilisateurs à mettre à jour, indexé sur le login
		$tableau_maj_auteurs = array_diff_key($tableau_csv_total, $tableau_nouveaux_auteurs);

		// construire le tableau de correspondance login csv => id_auteur bdd
		$tableau_maj_auteurs_id = array();
		$Tlogins = array_keys($tableau_maj_auteurs);
		$chaine_in = implode('","', $Tlogins);
		$chaine_in = '"'.$chaine_in.'"';
		$res = sql_select('id_auteur, login', 'spip_auteurs', array('login IN ('.$chaine_in.')'));
		while ($row = sql_fetch($res)) {
			$tableau_maj_auteurs_id[$row['login']] = $row['id_auteur'];
		}

		// si remplacer les données zones et rubriques administrées: supprimer les liens existant
		if ($type_maj == 'remplacer' AND test_plugin_actif("accesrestreint")) {
			// suppression des liens des rubriques administrées
			objet_dissocier(array("auteur" => array_values($tableau_maj_auteurs_id)), array("rubrique" => "*"));    
			// suppression des zones des auteurs
			zone_lier('',"auteur",array_values($tableau_maj_auteurs_id),'del');
			$retour['message_ok'] .= '<br />'._T('csv2auteurs:raz_rubriques_admins_zones');
		}

		// maj des données des auteurs
		$ret = '';
		foreach ($tableau_maj_auteurs as $login => $Tauteur) {
			$ret = csv2auteurs_ajout_utilisateur($login, $Tauteur, $Tnom_champs_bdd, $Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins, $tableau_maj_auteurs_id[$login]);
			if ($ret != '')
				$retour['message_ok'] .= $ret;
		}
		$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_auteurs_maj', array('nb_auteurs_maj' => count($tableau_maj_auteurs)));
	}

	// dans tous les cas ajout des nouveaux
	$ret = '';
	foreach ($tableau_nouveaux_auteurs as $login => $Tauteur) {
		$ret = csv2auteurs_ajout_utilisateur($login,$Tauteur,$Tnom_champs_bdd,$Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins);
		if ($ret != '')
		$retour['message_ok'] .= $ret;
	}
	$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_auteurs_crees', array('nb_auteurs_crees' => count($tableau_nouveaux_auteurs)));


	// PARTIE II : Suppressions des absents (changer le statut des auteurs en 5poubelle)  avec 3 choix pour la gestion des articles associés
	// 1. ras
	// 2. supprimer les articles 
	// 3. transferer les articles dans une rubrique d'archivage

	// Si choix3 : transferer les articles , création de la rubrique d'archive (en tenant compte d'une rubrique parent)
	if ($traitement_article_efface == "transferer_articles") {
		if (!$id_rubrique_archive = sql_fetsel('id_rubrique','spip_rubriques',array('titre ="'.$nom_rubrique_archive.'"',"id_parent=$id_rubrique_parent_archive"))) {
			$objet = 'rubrique';
			$set = array('titre' => $nom_rubrique_archive);
			$id_rubrique_archive = objet_inserer($objet, $id_rubrique_parent_archive);
			objet_modifier($objet, $id_rubrique_archive, $set);
			$retour['message_ok'] .= '<br />'._T('csv2auteurs:rubrique_archive_cree', array('titre_rubrique_archive' => $nom_rubrique_archive));
		}
	}

	// si l'option auteurs sans articles = suppression complète 
	// alors on supprime aussi tous les auteurs à la poubelle (sans articles)
	if ($abs_poubelle == 'supprimer') {
		// récupérer les auteurs à la poubelle avec articles
		$not_in = sql_allfetsel('auteurs.id_auteur',
			array('spip_auteurs_liens AS liens','spip_auteurs AS auteurs'),
			array('liens.id_auteur = auteurs.id_auteur', 'liens.objet="article"', 'auteurs.statut="5poubelle"'),
			array('liens.id_auteur')
		);
		$Tnot_in = 	array();
		foreach ($not_in as $index => $Tid_auteur) 
		$Tnot_in[] = $Tid_auteur['id_auteur'];
		$not_in = sql_in('id_auteur', $Tnot_in, 'NOT');

		// récupérer les auteurs à la poubelle sans articles
		$Tabs_poubelle = sql_allfetsel('id_auteur', 'spip_auteurs',array('statut="5poubelle"', $not_in));
		$Ta_suppr = array();
		foreach ($Tabs_poubelle as $index => $Tid_auteur) 
		$Ta_suppr[] = $Tid_auteur['id_auteur'];
		// effacer définitevement ces auteurs
		$in = sql_in('id_auteur', $Ta_suppr);
		sql_delete('spip_auteurs', $in);
		if (count($Tabs_poubelle) > 0)
		$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_auteurs_poubelle_effaces', array('nb_auteurs_poubelle_effaces' => count($Tabs_poubelle)));
	}

	// utilitaire pour récupérer un array simple avec les id_auteurs à partir du résultat du sql_allfetsel('id_auteur'...)
	function recup_id($Tid_obj) { return $Tid_obj['id_auteur']; }

	if ($abs_visiteurs) {
		$Tid_visiteurs = csv2auteurs_diff_absents($visiteur_bdd, $tableau_csv_visiteurs);
		// faire le ménage: pour la suppression on récupère aussi les visiteurs sans login
		$Tid_visiteurs_nologin = sql_allfetsel('id_auteur', 'spip_auteurs', array('statut="6forum"', 'login = ""'));
		$Tid_visiteurs = $Tid_visiteurs + array_map('recup_id', $Tid_visiteurs_nologin);
		csv2auteurs_supprimer_auteurs($Tid_visiteurs, '6forum', $traitement_article_efface, $id_rubrique_parent_archive);
		if (count($Tid_visiteurs) > 0)
			$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_visiteurs_effaces', array('nb_visiteurs_effaces' => count($Tid_visiteurs)));
	}
	if ($abs_redacs) {
		$Tid_redacs = csv2auteurs_diff_absents($redacteur_bdd, $tableau_csv_redacs);
		// faire le ménage: pour la suppression on récupère aussi les redacteurs sans login
		$Tid_redacs_nologin = sql_allfetsel('id_auteur', 'spip_auteurs', array('statut="1comite"', 'login = ""'));
		$Tid_redacs = $Tid_redacs + array_map('recup_id', $Tid_redacs_nologin);
		csv2auteurs_supprimer_auteurs($Tid_redacs, '1comite',$traitement_article_efface,$id_rubrique_parent_archive);
		if (count($Tid_redacs) > 0)
			$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_auteurs_effaces', array('nb_auteurs_effaces' => count($Tid_redacs)));
	}
	if ($abs_admins) {
		$Tid_admins = csv2auteurs_diff_absents($admin_restreint_bdd, $tableau_csv_admins);
		csv2auteurs_supprimer_auteurs($Tid_admins, '0minirezo',$traitement_article_efface,$id_rubrique_parent_archive);
		if (count($Tid_admins) > 0)
			$retour['message_ok'] .= '<br />'._T('csv2auteurs:nb_admins_restreints_effaces', array('nb_admins_restreints_effaces' => count($Tid_admins)));
	}

	return $retour;
}

/*
 * générer l"array des id auteurs absents à supprimer
 * @param $Tbdd: l'array indexé login/mail extrait de la base
 * @param $Tcsv: l'array indexé login/mail extrait du csv
 * @return l'array des id_auteurs
 */
function csv2auteurs_diff_absents($Tbdd, $Tcsv=array()) {
	$Tid = array();
	$T = array_diff_key($Tbdd, $Tcsv);
	foreach ($T as $val)
	$Tid[] = $val['id_auteur'];

	return $Tid;
}


/*
 * ajout d'un utilisateur
 * @param login de l'auteur
 * @param array associatif CSV: Tauteur_csv  nom_champ : valeur
 */
function csv2auteurs_ajout_utilisateur($login, $Tauteur_csv, $Tnom_champs_bdd, $Tcorrespondances, $tableau_bdd_rubriques_admins, $tableau_bdd_zones_admins, $id_auteur=0) {
	$set = $Tzones = $Trubadmin = array();
	$retour = '';

	foreach ($Tauteur_csv as $champ => $valeur) {
		// gestion des rubriques administrées par l'utilisateur
		// la rubrique peut être désignée par son titre ou son id_rubrique
		if ($champ == "ss_groupe") {
			$T = explode('|',$valeur);
			foreach ($T as $rub) {
				if (is_numeric($rub) AND array_key_exists($rub, $tableau_bdd_rubriques_admins))
				$Trubadmin[] = $rub;
				elseif ($id_r = array_search(strtolower($rub), $tableau_bdd_rubriques_admins))
				$Trubadmin[] = $id_r;
				// à priori on ne passe ici que si la rubrique est sous forme numérique mais ne correspondant pas à un id_rubrique existant
				elseif ($rub != '')
				$retour .= '<br />'._T('csv2auteurs:rubrique_admin_pas_trouvee', array('rub_pas_trouvee' => $rub, 'login_auteur' => $login));
			}
		}

		// gestion des zones de l'utilisateur
		if ($champ == "zone") {
			$T = explode('|',$valeur);
			foreach ($T as $zone) {
				// pour la liste des zones on accepte soit le titre de la zone soit son id_zone
				if (intval($zone) == $zone AND array_key_exists($zone, $tableau_bdd_zones_admins))
				$Tzones[] = $zone;
				elseif ($id_z = array_search(trim(rtrim(strtolower($zone))), $tableau_bdd_zones_admins))
				$Tzones[] = $id_z;
				elseif ($zone != '')
				$retour .= '<br />'._T('csv2auteurs:zone_pas_trouvee', array('zone_pas_trouvee' => $zone, 'login_auteur' => $login));
			}
		}

		// gestion de tous autres champs (y compris extras). 
		// On ne modifie pas la valeur du passe si son champ est vide (comportement idem l'interface d'admin des utilisateurs)
		// en revanche si nouvel auteur et pas de passe: ne pas créer
		if (in_array($champ, $Tnom_champs_bdd)) {
			if ($champ == 'pass' AND $valeur == '') {
				if (!$id_auteur) {
					return '<br />'._T('csv2auteurs:pas_nouveau_compte_sans_mdp', array('login_auteur' => $login));
				}
				else
				continue;
			}
			$set[$champ] = ($champ == "statut" AND array_key_exists($valeur, $Tcorrespondances)) ? $Tcorrespondances[$valeur] : $valeur;
		}

	}
	// si l'utilisateur est 0minirezo mais qu'il n'a pas de rubrique à administrer, le dégrader en redacteur
	if ($set['statut'] == '0minirezo' AND count($Trubadmin) == 0) {
		$set['statut'] = '1comite';
		$retour .= '<br />'._T('csv2auteurs:admin_sans_rub', array('login_auteur' => $login));
	}

	//créer l'auteur si il n'y a pas d'id_auteur transmis
	if (!$id_auteur)
	$id_auteur = auteur_inserer();

	// remplir les champs ou les maj
	$ret = auteur_modifier($id_auteur, $set);
	if ($ret != '')
	$retour .= '<br />'._T('csv2auteurs:probleme_creation_maj_compte', array('login_auteur' => $login)).$ret;

	//liaison des rubriques
	if (count($Trubadmin) AND $set["statut"] == "0minirezo")
	objet_associer(array("auteur" => $id_auteur), array("rubrique" => $Trubadmin));

	//liaison des zones
	if (count($Tzones) AND test_plugin_actif("accesrestreint") AND test_plugin_actif("accesrestreint"))
	zone_lier($Tzones, 'auteur', $id_auteur, 'add');

	return $retour;
}


/*
 * Suppression propre des auteurs 
 * changement de statut à la poubelle + traitement des liaisons spip_auteurs_liens et spip_zones_liens
 * gestion des articles des auteurs supprimés
 * @param $Tid array des id_auteurs à traiter
 * @param $statut des auteurs passent dans $Tid
 * $param $traitement : choix 2 (suppresion) ou 3 (transfere)
 * $param $id_rubrique_archive
 * 
 */
function csv2auteurs_supprimer_auteurs($Tid, $statut,$traitement="supprimer_articles",$id_rubrique_archive=1) {
	// passage à la poubelle
	$objet = 'auteur';
	$set = array('statut'=>'5poubelle');
	foreach ($Tid as $id) {
		$Tarticles = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="article"'));

		// auteur sans article et demande de suppression: suppression complète
		if (count($Tarticles) == 0 AND _request('abs_poubelle') == 'supprimer')
		sql_delete('spip_auteurs', "id_auteur=$id");
		// passage à la poubelle
		else
		objet_modifier($objet, $id, $set);

		// traitement des articles de l'auteur
		if (count($Tarticles) != 0) {

			// supprimer les articles
			$table_idarticle = array();
			if ($traitement == 'supprimer_articles') {
				objet_dissocier(array('id_auteur'=>$id), array('article'=>$Tarticles));
				foreach ($Tarticles as $idarticle) {
					$table_idarticle[]=$idarticle['id_objet'];
				}
				$inarticle = join(',',$table_idarticle);
				sql_delete('spip_articles', "id_article IN ($inarticle)");
			}
			// deplacer les articles dans la rubrique d'archivage
			if ($traitement == 'transferer_articles') {
				foreach ($Tarticles as $idarticle)
				objet_modifier('article', $idarticle['id_objet'], array('id_parent'=>$id_rubrique_archive));
			}
		}

		if (test_plugin_actif("accesrestreint")) {
			// suppression des zones de l'auteur
			$Tzones = sql_allfetsel('id_zone', 'spip_zones_liens', array('id_objet='.$id, 'objet="auteur"'));
			foreach ($Tzones as $id_zone)
			zone_lier($id_zone, 'auteur', $id, 'del');
		}
		// suppression des rubriques des admins restreints
		if ($statut == '0minirezo') {
			$Trubriques = sql_allfetsel('id_objet', 'spip_auteurs_liens', array('id_auteur='.$id, 'objet="rubrique"'));
			objet_dissocier(array('id_auteur'=>$id), array('rubrique'=>$Trubriques));
		}
	}
}

?>