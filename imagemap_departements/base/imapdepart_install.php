<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function imapdepart_installation($num_version){
		include_spip('base/imapdepart_base');
		include_spip('base/create');
		include_spip('base/abstract_sql');

		// creation de spip_imapdepart si la table n'existe pas (?)
		creer_base();

		// si elle est vide, remplir la table a partir du fichier CSV des departements
		$nb_departs = sql_countsel("spip_imap_departements");
		if ($nb_departs == 0) {
			$chem_csv = find_in_path('imap_departements.csv');
			$Tdepart = file($chem_csv);
			$Ta_inserer = array();
			foreach ($Tdepart as $d){
				$Td = explode(";",$d);
				$Ta_inserer[] = array(
					"id_departement" => "NULL",
					"num_departement" => $Td[0],
					"nom" => $Td[1],
					"region" => $Td[2],
					"nom_web" => $Td[3],
					"coordonnees" => $Td[4]
				);
			}
			sql_insertq_multi("spip_imap_departements", $Ta_inserer);
			if (sql_error() == '') {  
				// stocker le num de version dans spip_meta
				ecrire_meta("imapdepart_version",$num_version);
			}
			else echo '<br><br>Erreur dans l\'insertion des donnees des departements dans la table spip_imap_departements: '.sql_error();
		}
	}
	
	function imapdepart_desinstallation() {
		sql_drop_table("spip_imap_departements");
		effacer_meta("imapdepart_version");
		ecrire_metas();
	}

	function imapdepart_install($action){
		// vérifier les droits
		global $connect_statut, $connect_toutes_rubriques;
		if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
			debut_page(_T('titre'), "imapdepart", "plugin");
			echo _T('avis_non_acces_page');
			fin_page();
			exit;
		}

		// récupérer le numéro de version
		$Tplugins_actifs = liste_plugin_actifs();
		$version_script = $Tplugins_actifs['IMAPDEPART']['version'];

		// install/désinstall ? 
		switch ($action){
			case 'test':
				return (lire_meta('imapdepart_version') == $version_script);
			case 'install':
				if (lire_meta('imapdepart_version') != $version_script)
					imapdepart_installation($version_script);
				break;
			case 'uninstall':
				imapdepart_desinstallation();
				break;
		}
	}

?>
