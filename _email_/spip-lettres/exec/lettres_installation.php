<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');


	function exec_lettres_installation() {
		global $couleur_foncee;
	 	include_spip('inc/presentation');
		include_spip('inc/config');

		lettres_verifier_droits();

		if (!lettres_verifier_existence_tables() AND !empty($_POST['creer_tables'])) {

			$spip_abonnes =	"CREATE TABLE `spip_abonnes` (
							  `id_abonne` bigint(21) NOT NULL auto_increment,
							  `email` varchar(255) NOT NULL default '',
							  `code` varchar(255) NOT NULL default '',
							  `format` enum('html','texte','mixte') NOT NULL default 'mixte',
							  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
							  PRIMARY KEY  (`id_abonne`),
							  UNIQUE KEY `email` (`email`),
							  UNIQUE KEY `code` (`code`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_abonnes_ok = spip_query($spip_abonnes);
			
			$spip_abonnes_archives = "CREATE TABLE `spip_abonnes_archives` (
									  `id_abonne` bigint(21) NOT NULL default '0',
									  `id_archive` bigint(21) NOT NULL default '0',
									  `statut` enum('a_envoyer','envoye','echec') NOT NULL default 'a_envoyer',
									  `format` enum('mixte','html','texte') NOT NULL default 'mixte',
									  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
									  PRIMARY KEY  (`id_abonne`,`id_archive`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_abonnes_archives_ok = spip_query($spip_abonnes_archives);

			$spip_abonnes_lettres =	"CREATE TABLE `spip_abonnes_lettres` (
									  `id_abonne` bigint(21) NOT NULL default '0',
									  `id_lettre` bigint(21) NOT NULL default '0',
									  `date_inscription` datetime NOT NULL default '0000-00-00 00:00:00',
									  `statut` enum('a_valider','valide') NOT NULL default 'a_valider',
									  PRIMARY KEY  (`id_abonne`,`id_lettre`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_abonnes_lettres_ok = spip_query($spip_abonnes_lettres);

			$spip_archives = "CREATE TABLE `spip_archives` (
							  `id_archive` bigint(21) NOT NULL auto_increment,
							  `id_lettre` bigint(21) NOT NULL default '0',
							  `titre` text NOT NULL,
							  `message_html` longblob NOT NULL,
							  `message_texte` longblob NOT NULL,
							  `date` datetime NOT NULL default '0000-00-00 00:00:00',
							  `nb_emails_envoyes` bigint(21) NOT NULL default '0',
							  `nb_emails_non_envoyes` bigint(21) NOT NULL default '0',
							  `nb_emails_echec` bigint(21) NOT NULL default '0',
							  `date_debut_envoi` datetime NOT NULL default '0000-00-00 00:00:00',
							  `date_fin_envoi` datetime NOT NULL default '0000-00-00 00:00:00',
							  PRIMARY KEY  (`id_archive`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=9;";
			$spip_archives_ok = spip_query($spip_archives);
			
			$spip_archives_statistiques = "CREATE TABLE `spip_archives_statistiques` (
											  `id_archive` bigint(21) NOT NULL auto_increment,
											  `url` varchar(255) NOT NULL default '',
											  `hits` bigint(21) NOT NULL default '0',
											  PRIMARY KEY  (`id_archive`)
											) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_archives_statistiques_ok = spip_query($spip_archives_statistiques);

			$spip_lettres =	"CREATE TABLE `spip_lettres` (
							  `id_lettre` bigint(21) NOT NULL auto_increment,
							  `titre` text NOT NULL,
							  `descriptif` text NOT NULL,
							  `texte` longblob NOT NULL,
							  `date` datetime NOT NULL default '0000-00-00 00:00:00',
							  `lang` varchar(10) NOT NULL default '',
							  `maj` datetime NOT NULL default '0000-00-00 00:00:00',
							  `statut` enum('brouillon','publie','envoi_en_cours') NOT NULL default 'brouillon',
							  PRIMARY KEY  (`id_lettre`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_lettres_ok = spip_query($spip_lettres);
			
			$spip_mots_lettres = "CREATE TABLE `spip_mots_lettres` (
								  `id_mot` bigint(21) NOT NULL default '0',
								  `id_lettre` bigint(21) NOT NULL default '0',
								  PRIMARY KEY  (`id_mot`,`id_lettre`),
								  KEY `id_mot` (`id_mot`),
								  KEY `id_lettre` (`id_lettre`)
								) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_mots_lettres_ok = spip_query($spip_mots_lettres);

			$spip_auteurs_lettres = "CREATE TABLE `spip_auteurs_lettres` (
									  `id_auteur` bigint(21) NOT NULL default '0',
									  `id_lettre` bigint(21) NOT NULL default '0',
									  PRIMARY KEY  (`id_auteur`,`id_lettre`),
									  KEY `id_auteur` (`id_auteur`),
									  KEY `id_lettre` (`id_lettre`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
			$spip_auteurs_lettres_ok = spip_query($spip_auteurs_lettres);
			
			if ($spip_abonnes_ok 
			AND $spip_abonnes_archives_ok 
			AND $spip_abonnes_lettres_ok 
			AND $spip_archives_ok
			AND $spip_archives_statistiques_ok 
			AND $spip_lettres_ok
			AND $spip_mots_lettres_ok
			AND $spip_auteurs_lettres_ok) {
				$url = generer_url_ecrire('lettres_configuration');
				lettres_rediriger_javascript($url);
			}
		}
	
		if (!lettres_verifier_existence_tables()) {

			debut_page(_T('lettres:installation'), "administration", "lettres");
			echo "<br><br>";
			gros_titre(_T('lettres:installation'));

			debut_gauche();
			debut_boite_info();
			echo _T('lettres:installation_note');
			fin_boite_info();

	    	debut_droite();

			debut_cadre_relief();

			echo '<table border="0" cellspacing="0" cellpadding="5" width="100%">';
			echo '<tr><td bgcolor="'.$couleur_foncee.'"><b>';
			echo '<font face="Verdana,Arial,Sans,sans-serif" size="3" color="#FFFFFF">';
			echo _T('lettres:creation_des_tables_mysql').'</font></b></td></tr>';
			echo "<tr><td class='serif'>";
			echo generer_url_post_ecrire("lettres_installation").'<p align="justify">'._T('lettres:installation_texte').'</p>';
			echo '<div align="right"><input class="fondo" name="creer_tables" type="submit" value="'._T('lettres:creer_tables_mysql').'"></div></form>';

			echo "</td></tr>";
			echo "</table>";

			fin_cadre_relief();

			echo "<br />";


		}

		fin_page();
	}


?>