<?php
	
	$GLOBALS['agenda_base_version'] = 0.13;
	function Agenda_verifier_base(){
		$version_base = $GLOBALS['agenda_base_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['agenda_base_version']) )
				|| (($current_version = $GLOBALS['meta']['agenda_base_version'])!=$version_base)){
			include_spip('base/agenda_evenements');
			if ($current_version==0.0){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				// ajout du champ evenements a la table spip_groupe_mots
				// si pas deja existant
				$desc = sql_showtable("spip_groupes_mots", true, '');
				if (!isset($desc['field']['evenements'])){
					spip_query("ALTER TABLE spip_groupes_mots ADD `evenements` VARCHAR(3) NOT NULL AFTER `syndic`");
				}
				ecrire_meta('agenda_base_version',$current_version=$version_base,'non');
			}
			if ($current_version<0.11){
				spip_query("ALTER TABLE spip_evenements ADD `horaire` ENUM('oui','non') DEFAULT 'oui' NOT NULL AFTER `lieu`");
				ecrire_meta('agenda_base_version',$current_version=0.11,'non');
			}
			if ($current_version<0.12){
				spip_query("ALTER TABLE spip_evenements ADD `id_article` bigint(21) DEFAULT '0' NOT NULL AFTER `id_evenement`");
				spip_query("ALTER TABLE spip_evenements ADD INDEX ( `id_article` )");
				$res = spip_query ("SELECT * FROM spip_evenements_articles");
				while ($row = sql_fetch($res)){
					$id_article = $row['id_article'];
					$id_evenement = $row['id_evenement'];
					spip_query("UPDATE spip_evenements SET id_article=$id_article WHERE id_evenement=$id_evenement");
				}
				spip_query("DROP TABLE spip_evenements_articles");
				ecrire_meta('agenda_base_version',$current_version=0.12,'non');
			}
			if ($current_version<0.13){
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta('agenda_base_version',$current_version=0.13,'non');
			}
	
			ecrire_metas();
		}
		
		if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
			$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
			if (!isset($INDEX_elements_objet['spip_evenements'])){
				$INDEX_elements_objet['spip_evenements'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_objet_associes'])){
			$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
			if (!isset($INDEX_objet_associes['spip_articles']['spip_evenements'])){
				$INDEX_objet_associes['spip_articles']['spip_evenements'] = 1;
				ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
				ecrire_metas();
			}
		}
		if (isset($GLOBALS['meta']['INDEX_elements_associes'])){
			$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
			if (!isset($INDEX_elements_associes['spip_evenements'])){
				$INDEX_elements_associes['spip_evenements'] = array('titre'=>2,'descriptif'=>1);
				ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
				ecrire_metas();
			}
		}
	}
	
	function Agenda_vider_tables() {
		include_spip('base/agenda_evenements');
		include_spip('base/abstract_sql');
		// suppression du champ evenements a la table spip_groupe_mots
		spip_query("ALTER TABLE spip_groupes_mots DROP evenements");
		spip_query("DROP TABLE spip_evenements");
		spip_query("DROP TABLE spip_mots_evenements");
		effacer_meta('agenda_base_version');
		ecrire_metas();
	}
	
	function Agenda_install($action){
		$version_base = $GLOBALS['agenda_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['agenda_base_version']) AND ($GLOBALS['meta']['agenda_base_version']>=$version_base)
				AND isset($GLOBALS['meta']['INDEX_elements_objet'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_elements_objet'])
				AND isset($t['spip_evenements'])
				AND isset($GLOBALS['meta']['INDEX_objet_associes'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_objet_associes'])
				AND isset($t['spip_articles']['spip_evenements'])
				AND isset($GLOBALS['meta']['INDEX_elements_associes'])
				AND $t = unserialize($GLOBALS['meta']['INDEX_elements_associes'])
				AND isset($t['spip_evenements']));
				break;
			case 'install':
				Agenda_verifier_base();
				break;
			case 'uninstall':
				Agenda_vider_tables();
				break;
		}
	}	
?>