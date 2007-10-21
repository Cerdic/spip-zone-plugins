<?php
// base/spiplistes_upgrade.php
/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$


/*
	Script appelé à chaque appel par mes_options
	
	spiplistes_upgrade() : si mise à jour de spiplistes
	
	spiplistes_upgrade_base() : si mise à jour de la base spiplistes
*/


function spiplistes_upgrade () {

spiplistes_log("spiplistes_upgrade() <<", LOG_DEBUG);

	$spiplistes_name = __plugin_real_prefix_get();
	$spiplistes_current_version =  __plugin_current_version_get('spiplistes');
	$spiplistes_real_version = __plugin_real_version_get();
	$spiplistes_current_version_base = __plugin_current_version_base_get('spiplistes');
	$spiplistes_real_version_base = __plugin_real_version_base_get();

	if(!$spiplistes_current_version) {
	// SPIP-Listes n'a jamais été installé ? 
		include_spip('base/spiplistes_init');
		$spiplistes_current_version_base = spiplistes_base_creer();
	}

	if($spiplistes_current_version_base < $spiplistes_real_version_base) {
	// upgrade de la base ?
		$spiplistes_current_version_base = spiplistes_upgrade_base(
			$spiplistes_name
			, $spiplistes_current_version
			, $spiplistes_current_version_base
			, $spiplistes_real_version_base
			);
	}
	
	if($spiplistes_current_version < $spiplistes_real_version) {

		spiplistes_log("UPGRADING $spiplistes_name $spiplistes_current_version TO $spiplistes_real_version", LOG_DEBUG);


		if($spiplistes_current_version < 1.98008) {
		// Commenter, ca aide ! ;-) (CP-20071016)
			// Ne modifie pas le schéma. Ajoute juste une légende sur les tables
			spip_query("ALTER TABLE spip_listes COMMENT 'Listes de diffusion'");
			spip_query("ALTER TABLE spip_courriers COMMENT 'Paniers des courriers'");
			spip_query("ALTER TABLE spip_auteurs_courriers COMMENT 'Queue des envois de courriers'");
			spip_query("ALTER TABLE spip_auteurs_mod_listes COMMENT 'Moderateurs des listes de diffusion'");
			spip_query("ALTER TABLE spip_auteurs_listes COMMENT 'Carnet des abonnements'");
			spip_query("ALTER TABLE spip_auteurs_elargis COMMENT 'Preferences des auteurs'");
			$spiplistes_current_version_base = 1.98008;
		}


/* ... */


	// Ajouter au dessus de cette ligne les patches si besoin pour nouvelle version de SPIP-Listes
	// qui ne concerne pas la base (changement de nom de script, de patron, etc.)

	// fin des ajouts de patches
		ecrire_meta('spiplistes_version', $spiplistes_real_version);
		spiplistes_ecrire_metas();
	}
	
	return($spiplistes_current_version);
}

function spiplistes_upgrade_base ($spiplistes_name, $spiplistes_current_version, $spiplistes_current_version_base, $spiplistes_real_version_base) {
	
	if($spiplistes_current_version_base && ($spiplistes_current_version_base >= $spiplistes_real_version_base)) {
	// La base est à jour
		return($spiplistes_current_version_base);
	}
	
	// faire la mise à jour
	spiplistes_log("UPGRADING DATABASE $spiplistes_name $spiplistes_current_version_base TO $spiplistes_real_version_base", LOG_DEBUG);
	

	// 'version_base' n'apparait que dans SPIP-Listes 1.98001
	// Cherche sur $spiplistes_version pour les versions précédentes 

	//install
	$version_base = 1.91; // ou inférieur ?
	
	if (   
		(!$spiplistes_current_version)
		|| ($spiplistes_current_version < 1.98001)
		) {
		
		// si etait deja installe mais dans une vieille version, on reprend a zero
		include_spip('base/abstract_sql');
		$desc = spip_abstract_showtable("spip_listes", '', true);
		if (!isset($desc['field']['id_liste']))
			$current_version = 0.0;
		if (
			($res=spip_query("SELECT * FROM spip_articles WHERE statut='liste' OR statut='inact' OR statut='poublist'"))
			AND ($row = spip_fetch_array($res)) )
			$current_version=0.0;

		if ($current_version==0.0){
			// Verifie que les tables spip_listes existent, sinon les creer
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			//Migrer des listes anciennes // a deplacer dans une en fonction
			$resultat_aff = spip_query("SELECT * FROM spip_articles WHERE statut='liste' OR statut='inact' OR statut='poublist'");
			if(@spip_num_rows($resultat_aff) > 0){
				echo _T('spiplistes:mettre_a_jour');
				while ($row = spip_fetch_array($resultat_aff)) {
					$id_article=$row['id_article'];
					$titre_liste=corriger_caracteres($row['titre']);
					$texte_liste = corriger_caracteres($row['texte']);
					$date_liste = $row['date'];
					$langue=$row["lang"];
					$statut = $row['statut'];
					$extra=unserialize($row['extra']);
					$patron_liste=$extra["squelette"];
					$periode_liste=$extra["periode"];
					$maj_liste=$extra["majnouv"];
					$email_envoi=$extra["email_envoi"];
					$message_auto=$extra["auto"];
					$options="<p>".$titre_liste."<br/>";
					echo $options."</p>";
					
					// ajout du pied de page
					include_spip('public/assembler');
					$contexte_pied = array('lang'=>$langue);
					$pied = recuperer_fond('modeles/piedmail', $contexte_pied);
					
					spip_query("INSERT INTO spip_listes (titre, texte, statut, date, lang, pied_page) VALUES ("
						._q($titre_liste).","._q($texte_liste).","._q($statut).","._q($date_liste).","._q($langue).","._q($pied).")" );
					$id_liste=spip_insert_id();
					if($message_auto=="oui")
						spip_query("UPDATE spip_listes SET patron="._q($patron_liste).", periode="._q($periode_liste)
						  . ", maj=FROM_UNIXTIME("._q($maj_liste)."), email_envoi="._q($email_envoi)
						  . ", message_auto="._q($message_auto)." WHERE id_liste="._q($id_liste));
					
					//Auteur de la liste (moderateur)
					spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_liste ="._q($id_liste));
					spip_query("INSERT INTO spip_auteurs_mod_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
					
					//recuperer les abonnes (peut etre plus tard ?)
					$abos=spip_query("SELECT id_auteur, id_article FROM spip_auteurs_articles WHERE id_article="._q($id_article));
					while($abonnes=spip_fetch_array($abos)){
						$abo=$abonnes["id_auteur"];
						spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ("._q($abo).","._q($id_liste).")");
					}
					
					//effacer les anciens articles/abo
					spip_query("DELETE FROM spip_articles WHERE id_article ="._q($id_article));
					spip_query("DELETE FROM spip_auteurs_articles WHERE id_article ="._q($id_article));
		
					//manque un traitement pour récuperer les courriers
				}
			//evaluer les extras de tous les auteurs et les virer
			$result = spip_query(
			  'SELECT extra, spip_auteurs.id_auteur FROM spip_auteurs');
			while ($row = spip_fetch_array($result, SPIP_NUM)) {
				$abo = unserialize($row[0]);
				$format = $abo['abo'] ;
			if($format=="texte" OR $format=="html")
			spip_query("INSERT INTO `spip_auteurs_elargis` (`id_auteur`, `spip_listes_format`) 
			VALUES ("._q($row[1]).","._q($format).") ");
			else
			spip_query("INSERT INTO `spip_auteurs_elargis` (`id_auteur`, `spip_listes_format`) 
			VALUES ("._q($row[1]).","._q('non').") ");
			}
			
			echo _T('spiplistes:regulariser');

			$result = spip_query("SELECT a.`email`, a.id_auteur FROM `spip_auteurs` a, `spip_auteurs_listes` l, `spip_auteurs_elargis` f
			WHERE a.id_auteur=f.id_auteur 
			AND f.spip_listes_format = 'non'
			AND a.id_auteur = l.id_auteur
			AND a.statut!='5poubelle' 
			GROUP BY email
			");
			
			while($res = spip_fetch_array($result)){
			spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur =".$res['id_auteur']) ;			
			} 
			
			
			}
			ecrire_meta('spiplistes_version',$current_version=$version_base,'non');
		}
		
		if ($current_version<1.92){
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			echo "SpipListes Maj 1.92<br />";
			spip_query("ALTER TABLE spip_listes ADD titre_message varchar(255) NOT NULL default '';");
			spip_query("ALTER TABLE spip_listes ADD pied_page longblob NOT NULL;");
			ecrire_meta('spiplistes_version', $current_version=1.92);
		}
		if ($current_version<1.94){
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			echo "SpipListes Maj 1.94<br />";
			include_spip('base/abstract_sql');
			if (($res=spip_query("SELECT id_auteur FROM spip_auteurs_mod_listes"))
				AND (!spip_fetch_array($res))
			  AND ($desc = spip_abstract_showtable("spip_abonnes_listes", '', true))
			  AND isset($desc['field']['id_auteur'])) {
				spip_query("DROP TABLE spip_auteurs_mod_listes"); // elle vient d'etre cree par un creer_base inopportun
				spip_query("DROP TABLE spip_auteurs_courriers"); // elle vient d'etre cree par un creer_base inopportun
			}
			spip_query("ALTER TABLE spip_auteurs_listes RENAME spip_auteurs_mod_listes;");
			spip_query("ALTER TABLE spip_abonnes_listes RENAME spip_auteurs_listes;");
			spip_query("ALTER TABLE spip_abonnes_courriers RENAME spip_auteurs_courriers;");
			ecrire_meta('spiplistes_version', $current_version=1.94);
		}
		if ($current_version<1.95){
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			echo "SpipListes Maj 1.95<br />";
			include_spip('base/abstract_sql');
			spip_query("ALTER TABLE spip_auteurs_courriers ADD etat varchar(5) NOT NULL default '' AFTER statut");
			ecrire_meta('spiplistes_version', $current_version=1.95);
		}
		
		if ($current_version<1.96){
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			echo "SpipListes Maj 1.96<br />";
			include_spip('base/abstract_sql');
			
			//installer la table spip_auteurs_elargis si besoin
			$table_nom = "spip_auteurs_elargis";
			spip_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (
			`id_auteur` BIGINT NOT NULL ,
			`spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL
			 ) ");
			
			//evaluer les extras de tous les auteurs + compter tous les auteurs
			$result = spip_query(
			  'SELECT extra, spip_auteurs.id_auteur FROM spip_auteurs');
			$nb_inscrits = 0;
		
			//repartition des extras
			$cmpt = array('texte'=>0, 'html'=>0, 'non'=>0);
			
			while ($row = spip_fetch_array($result, SPIP_NUM)) {
				$nb_inscrits ++ ;
				$abo = unserialize($row[0]);
				$format = $abo['abo'] ;
			if($format=="texte" OR $format=="html")
			spip_query("INSERT INTO `spip_auteurs_elargis` (`id_auteur`, `spip_listes_format`) 
			VALUES ("._q($row[1]).","._q($format).") ");
			else
			spip_query("INSERT INTO `spip_auteurs_elargis` (`id_auteur`, `spip_listes_format`) 
			VALUES ("._q($row[1]).","._q('non').") ");
			
				if ($abo['abo']) {
					$cmpt[$abo['abo']] ++;
				}
			}
			
			echo "<br />html : ".$cmpt['html']." <br />texte : ".$cmpt['texte']."<br />non : ".$cmpt['non']."<br />somme :".$nb_inscrits  ;

			ecrire_meta('spiplistes_version', $current_version=1.96);
		}
		
		if ($current_version<1.97){
//spiplistes_log("UPGRADE: current_version: $current_version", LOG_DEBUG);
			echo "SpipListes Maj 1.97<br />";
			include_spip('base/abstract_sql');

		echo "regulariser les desabonnes avec listes...<br />";

		$result = spip_query("SELECT a.`email`, a.id_auteur FROM `spip_auteurs` a, `spip_auteurs_listes` l, `spip_auteurs_elargis` f
		WHERE a.id_auteur=f.id_auteur 
		AND f.spip_listes_format = 'non'
		AND a.id_auteur = l.id_auteur
		AND a.statut!='5poubelle' 
		GROUP BY email
		");
		
		$nb_inscrits = spip_num_rows($result);
		echo $nb_inscrits ;
		
		while($res = spip_fetch_array($result)){
		spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur =".$res['id_auteur']) ;			
		} 


			ecrire_meta('spiplistes_version', $current_version=1.97);
		}
		
		
		if ($current_version<1.98){
			
			echo "SpipListes Maj 1.98<br />";
			include_spip('base/abstract_sql');
		
		echo "regulariser l'index";
		$table_nom = "spip_auteurs_elargis";
		//ajout des index
		$desc = spip_abstract_showtable($table_nom, '', true);
		if($desc['key']['PRIMARY KEY']!='id'){
			spip_query("ALTER TABLE ".$table_nom." DROP PRIMARY KEY");
				if(!isset($desc['fields']['id']))
				spip_query("ALTER TABLE ".$table_nom." ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY");
				else 
				spip_query("ALTER TABLE ".$table_nom." ADD PRIMARY KEY (id)");
		}
		if($desc['key']['KEY id_auteur'])
		spip_query("ALTER TABLE ".$table_nom." DROP INDEX id_auteur, ADD INDEX id_auteur (id_auteur)");
		else
		spip_query("ALTER TABLE ".$table_nom." ADD INDEX id_auteur (id_auteur)");
		
		ecrire_meta('spiplistes_version', $current_version=1.98);
		}
		
		spiplistes_ecrire_metas();
	}

	// A partir de SPIP-Listes 1.98001, on se base sur le vrai numero de version de
	// la base, (plugin.xml: <version_base>)
	if($spiplistes_current_version_base < $spiplistes_real_version_base) {

spiplistes_log("UPGRADING DATABASE version_base: $spiplistes_current_version_base TO $spiplistes_real_version_base", LOG_DEBUG);



/* ... */


	// ajouter au dessus de cette ligne les patches si besoin pour nouvelle version de la base
	// fin des ajouts de patches
		ecrire_meta('spiplistes_base_version', $spiplistes_current_version_base);
		spiplistes_ecrire_metas();
	}

	return($spiplistes_current_version_base);
}

?>