<?php
	function PIMAgenda_upgrade($nom_meta_base_version,$version_cible){
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/pim_agenda');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				// ajout du champ pim_agenda a la table spip_groupe_mots
				// si pas deja existant
				$desc = spip_abstract_showtable("spip_groupes_mots",'',true);
				if (!isset($desc['field']['pim_agenda'])){
					spip_query("ALTER TABLE spip_groupes_mots ADD pim_agenda VARCHAR(3) NOT NULL AFTER syndic");
				}
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
	
			ecrire_metas();
		}

		if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_pim_agenda'])){
				$INDEX_elements_objet['spip_pim_agenda'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_objet_associes'])){
			$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
			if (!isset($INDEX_objet_associes['spip_pim_agenda']['spip_articles'])){
				$INDEX_objet_associes['spip_pim_agenda']['spip_articles'] = 1;
				ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_elements_associes'])){
			$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
			if (!isset($INDEX_elements_associes['spip_articles'])){
				$INDEX_elements_associes['spip_articles'] = array('titre'=>2,'descriptif'=>1);
				ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
				ecrire_metas();
			}
		}
	}
	
	function PIMAgenda_vider_tables($nom_meta_base_version) {
		include_spip('base/pim_agenda');
		include_spip('base/abstract_sql');
		// suppression du champ evenements a la table spip_groupe_mots
		spip_query("ALTER TABLE spip_groupes_mots DROP pim_agenda");
		spip_query("DROP TABLE spip_pim_agenda");
		spip_query("DROP TABLE spip_mots_pim_agenda");
		effacer_meta($nom_meta_base_version);
		ecrire_metas();
	}

?>