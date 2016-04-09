<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_csv2auteurs_exportation_charger_dist() {
	$nom_champs = csv2auteurs_exportation();
	$valeurs = array( "choix_statut" => "6forum", 
		"nom_champs" => $nom_champs,
		"choix_format" => "id");

	return $valeurs;
}

function formulaires_csv2auteurs_exportation_verifier_dist() {
	$erreurs = array();
	// seuls les webmestres ont le droit d'utiliser cet outil!
	if ($GLOBALS['visiteur_session']['webmestre'] != 'oui') 
		$erreurs['message_erreur'] = _T('csv2auteurs:non_autorise');

	return $erreurs;
}

function formulaires_csv2auteurs_exportation_traiter_dist() {
	$nom_champs   = _request('nom_champs');
	$choix_statut = _request('choix_statut');
	$choix_format = _request('choix_format');

	$retour = $login_restreint = array();
	$correspondances_statuts = array( "0minirezo" => "administrateur", "1comite" => "redacteur", "6forum" => "visiteur");

	// creation du nom du fichier
	$date_du_jour=date(Y_m_d);
	$nom_fichier_csv = $date_du_jour.'_export_table_auteurs.csv';

	// récupération des données dans la tables spip_auteurs que l'on place dans le champ "ss_groupe"
	// Ecriture de la premiere ligne d'entete
	foreach ($nom_champs as $entete) 
	$tableau_csv[0][$entete] = $entete;

	// ajout de l'admin restreint
	$tableau_csv[0]["ss_groupe"] = "ss_groupe";
	// ajout de l'acces restreint s'il existe
	if (test_plugin_actif ("accesrestreint"))
	$tableau_csv[0]["zone"] = "zone";

	// création d'un array contenant tous les logins des admins restreints
	if (in_array("0minirezo", $choix_statut)) {
		$r = sql_select("DISTINCT auteur.login AS login",
			array("spip_auteurs AS auteur","spip_auteurs_liens AS liens"),
			array("auteur.statut='0minirezo'","liens.id_auteur=auteur.id_auteur","liens.objet='rubrique'")
		);
		while ($row = sql_fetch($r)) 
		$login_restreint[] = $row['login'];
	}

	// boucler dans tous les auteurs sélectionnés en fonction de leur statut
	if ($res = sql_select('*', 'spip_auteurs')) {
		$i = 1;
		while ($row = sql_fetch($res)) {
			// test les statuts demandés
			if (in_array($row['statut'], $choix_statut)) {
				// si c'est un admin, on ne selectionne que les admins restreints !!!
				if ((($row['statut'] == "0minirezo") AND (in_array($row['login'], $login_restreint))) 
				OR $row['statut'] == "1comite" 
				OR $row['statut'] == "6forum") {
					// Prise en compte de tous les champs selectionnés
					foreach ($nom_champs as $nom_champ) {
						// Prise en compte du champ statut
						if ($nom_champ == "statut") {
							$tableau_csv[$i]["statut"] = $correspondances_statuts[$row['statut']];
						}
						elseif ($nom_champ == "pass") {
							$tableau_csv[$i][$nom_champ] = '';
						}
						else
						$tableau_csv[$i][$nom_champ] = $row[$nom_champ];
					}
					// on selectionne les noms des rubriques ou les id_rubriques pour les admins restreints
					if ($choix_format == 'titre')
					$select1 = array("rub.titre AS id_titre");
					else 
					$select1 = array("lien.id_objet AS id_titre");

					$from1 = array("spip_rubriques AS rub", 
					"spip_auteurs_liens AS lien");
					$where1 = array("rub.id_rubrique = lien.id_objet",
					"lien.id_auteur  = ".$row['id_auteur'],
					"lien.objet      = 'rubrique'");
					if ($res2 = sql_select($select1, $from1, $where1)) {
						$rubs = array();
						while ($row2 = sql_fetch($res2)) 
						$rubs[] = $row2['id_titre'];
						if (count($rubs)) {
							$tableau_csv[$i]["ss_groupe"] = implode('|', $rubs);
						}
						else 
						$tableau_csv[$i]["ss_groupe"] = "";
					}
					// Prise en compte des zones restreintes : si le plugin est installe
					if (test_plugin_actif ("accesrestreint")) {
						if ($choix_format == 'titre') 
						$select2 = array("zones.titre AS id_titre");
						else 
						$select2 = array("zones.id_zone AS id_titre");

						$from2 = array("spip_zones_liens AS liens", "spip_zones AS zones");
						$where2 = array("liens.objet = 'auteur'", 
						"liens.id_objet = ".$row['id_auteur'],
						"liens.id_zone = zones.id_zone");
						if ($res3 = sql_select($select2, $from2, $where2)) {
							$zones = array();
							while ($row3 = sql_fetch($res3)) {
								$zones[] = $row3['id_titre'];
								$tableau_csv[$i]['zone'] = implode('|', $zones);
							}
						}
					}
				}
			}
			// on passe à l'auteur suivant
			$i++;
		}
	}
	// création de la variable contenant l'intégralité des donnees
	$a_ecrire = '';
	include_spip('inc/config');
	$separateur = lire_config("csv2auteurs_separateur");
	foreach ($tableau_csv as $ligne) {
		$a_ecrire .= implode("$separateur", $ligne);
		$a_ecrire .= "\r\n";
	}
	// telechargement du fichier csv	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=$nom_fichier_csv");
	header("Content-Length: ".strlen($a_ecrire));
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	echo $a_ecrire; 
	exit;

	return $retour;
}

// fonction pour récupérer un array avec les noms de schamps de la table spip_auteurs
function csv2auteurs_exportation() {
	//récupération des noms des champs
	$Tnom_champs= array();
	$Tchamps_exclus = array('id_auteur', 'low_sec', 'maj', 'htpass', 'en_ligne', 
	'alea_actuel', 'alea_futur', 'prefs', 'cookie_oubli', 'source', 'imessage', 'messagerie');

	$desc = sql_showtable('spip_auteurs', true);
	foreach ($desc['field'] as $cle => $valeur) {
		if (!in_array($cle, $Tchamps_exclus))
		$Tnom_champs[$cle] = $cle;
	}

	return $Tnom_champs;
}
?>