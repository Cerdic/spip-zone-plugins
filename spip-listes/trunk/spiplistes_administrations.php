<?php
/**
 * Fonctions de mise a jour du plugin
 *
 * Script appele par mes_options.php si le numero de version
 * ou version_base a ete modifie'.
 * Le numero de version actualise' est ensuite enregistre"
 * dans les metas.
 *
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/


if (!defined('_ECRIRE_INC_VERSION')) return;


function spiplistes_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
			array('maj_tables', array('spip_courriers')),
			array('maj_tables', array('spip_listes')),
			array('maj_tables', array('spip_auteurs_listes')),
			array('maj_tables', array('spip_auteurs_courriers')),
			array('maj_tables', array('spip_auteurs_mod_listes')),
			array('maj_tables', array('spip_auteurs_elargis')));
	$maj['1.98']=array(
			array('upgrade_base',array())
			);
	$maj['1.9923']=array(
			array('sql_alter',"TABLE spip_listes COMMENT ".sql_quote("Listes de diffusion")),
			array('sql_alter',"TABLE spip_courriers COMMENT ".sql_quote("Panier des courriers (casiers)")),
			array('sql_alter',"TABLE spip_auteurs_courriers COMMENT ".sql_quote("Queue des envois de courriers")),
			array('sql_alter',"TABLE spip_auteurs_listes COMMENT ".sql_quote("Listes de abonnements aux listes")),
			array('sql_alter',"TABLE spip_auteurs_mod_listes COMMENT ".sql_quote("Moderateurs des listes de diffusion")),
			array('sql_alter',"TABLE spip_auteurs_elargis COMMENT ".sql_quote("Preferences des auteurs/abonnes (formats recept.)")),
			);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);

}


/**
 * Mise à jour de la base de données (tables SPIP-Listes uniquement)
 *
 * @return string
 */
function spiplistes_upgrade_base(){

		
		// si etait deja installe mais dans une vieille version, on reprend a zero
		include_spip('base/abstract_sql');
		$desc = sql_showtable("spip_listes",true);
		if (!isset($desc['field']['id_liste']))
			$current_version = 0.0;
		if(
			sql_getfetsel("*", 'spip_articles'
				, "statut=".sql_quote('liste')." OR statut=".sql_quote('inact')." OR statut=".sql_quote('poublist'))
		) {
			$current_version=0.0;
		}

		if ($current_version==0.0){
			// Verifie que les tables spip_listes existent, sinon les creer
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			include_spip('base/create');
			include_spip('base/abstract_sql');
			
			//Migrer des listes anciennes // a deplacer dans une en fonction
			$resultat_aff = sql_select("*", 'spip_articles'
				, "statut=".sql_quote('liste')." OR statut=".sql_quote('inact')." OR statut=".sql_quote('poublist'));
			if(@sql_count($resultat_aff) > 0) {
				echo _T('spiplistes:mettre_a_jour');
				while ($row = sql_fetch($resultat_aff)) {
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
					
					$id_liste = sql_insertq(
						"spip_listes"
						, array(
							'titre' => $titre_liste
							, 'texte' => $texte_liste
							, 'statut' => $statut
							, 'date' => $date_liste
							, 'lang' => $langue
							, 'pied_page' => $pied
						)
					);
					if($message_auto=="oui")
						sql_update(
							'spip_listes'
							, array(
								'patron' => sql_quote($patron_liste)
								, 'periode' => sql_quote($periode_liste)
								, 'maj' => "FROM_UNIXTIME(".sql_quote($maj_liste).")"
								, 'email_envoi' => sql_quote($email_envoi)
								, 'message_auto' => sql_quote($message_auto)
								)
							, "id_liste=".sql_quote($id_liste)
							);
					
					//Auteur de la liste (moderateur)
					sql_delete('spip_auteurs_mod_listes', "id_liste =".sql_quote($id_liste));
					sql_insert(
						'spip_auteurs_mod_listes'
						, "(id_auteur, id_liste)"
						, "(".sql_quote($connect_id_auteur).",".sql_quote($id_liste).")"
					);
					
					//recuperer les abonnes (peut etre plus tard ?)
					$abos = sql_select('id_auteur,id_article', 'spip_auteurs_articles'
						, "id_article=".sql_quote($id_article));
					while($abonnes = sql_fetch($abos)){
						$abo = intval($abonnes['id_auteur']);
						sql_insert('spip_auteurs_listes'
							, "(id_auteur, id_liste)"
							, "(".sql_quote($abo).",".sql_quote($id_liste).")"
							);
					}
					
					//effacer les anciens articles/abo
					sql_delete('spip_articles', "id_article =".sql_quote($id_article));
					sql_delete('spip_auteurs_articles', "id_article =".sql_quote($id_article));
		
					//manque un traitement pour recuperer les courriers
				}
				//evaluer les extras de tous les auteurs et les virer
				$result = sql_select(
					"extra AS e, spip_auteurs.id_auteur AS i"
					, 'spip_auteurs'
				);
				while ($row = sql_fetch($result)) {
					$abo = unserialize($row['e']);
					$format = $abo['abo'] ;
					if($format=="texte" || $format=="html") {
						sql_insert(
							'spip_auteurs_elargis'
							, "(id_auteur,`spip_listes_format`)"
							, "(".sql_quote($row['i']).",".sql_quote($format).")"
						);
					}
					else {
						sql_insert(
							'spip_auteurs_elargis'
							, "(id_auteur, `spip_listes_format`)"
							, "(".sql_quote($row['i']).",".sql_quote('non').")"
						);
					}
				} // end while
				
				echo _T('spiplistes:regulariser');
	
				$result = sql_select(
					"a.email, a.id_auteur"
					, "spip_auteurs AS a, spip_auteurs_listes AS l, spip_auteurs_elargis AS f"
					, array(
						"a.id_auteur=f.id_auteur"
						, "f.spip_listes_format=".sql_quote('non')
						, "a.id_auteur=l.id_auteur"
						, "a.statut!=".sql_quote('5poubelle')
					)
					, array("email")
				); //
				
				while($res = sql_fetch($result)) {
					sql_delete('spip_auteurs_listes', "id_auteur =".sql_quote($res['id_auteur'])) ;			
				} 
			} // end if(@sql_count($resultat_aff) > 0)
			
			return true; 
		}
		
		if ($current_version<1.92){
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			echo "SpipListes Maj 1.92<br />";
			sql_alter("TABLE spip_listes ADD titre_message varchar(255) NOT NULL default ''");
			sql_alter("TABLE spip_listes ADD pied_page longblob NOT NULL");

		}
		if ($current_version<1.94){
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			echo "SpipListes Maj 1.94<br />";
			include_spip('base/abstract_sql');
			if (($res = sql_select('id_auteur', 'spip_auteurs_mod_listes'))
				&& (!sql_fetch($res))
				&& ($desc = sql_showtable("spip_abonnes_listes",true))
				&& isset($desc['field']['id_auteur'])
			) {
				sql_drop_table("spip_auteurs_mod_listes"); // elle vient d'etre cree par un creer_base inopportun
				sql_drop_table("spip_auteurs_courriers"); // elle vient d'etre cree par un creer_base inopportun
			}
			sql_alter("TABLE spip_auteurs_listes RENAME spip_auteurs_mod_listes");
			sql_alter("TABLE spip_abonnes_listes RENAME spip_auteurs_listes");
			sql_alter("TABLE spip_abonnes_courriers RENAME spip_auteurs_courriers");

		}
		if ($current_version<1.95){
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			echo "SpipListes Maj 1.95<br />";
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_auteurs_courriers ADD etat varchar(5) NOT NULL default '' AFTER statut");

		}
		
		if ($current_version<1.96){
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			echo "SpipListes Maj 1.96<br />";
			include_spip('base/abstract_sql');
			
			//installer la table spip_auteurs_elargis si besoin
			$table_nom = "spip_auteurs_elargis";
			sql_query("CREATE TABLE IF NOT EXISTS ".$table_nom." (
				`id_auteur` BIGINT NOT NULL ,
				`spip_listes_format` VARCHAR( 8 ) DEFAULT 'non' NOT NULL
			 ) ");
			
			//evaluer les extras de tous les auteurs + compter tous les auteurs
			$result = sql_select(
				"extra AS e,spip_auteurs.id_auteur AS i"
				, 'spip_auteurs');
			$nb_inscrits = 0;
		
			//repartition des extras
			$cmpt = array('texte'=>0, 'html'=>0, 'non'=>0);
			
			while ($row = sql_fetch($result)) {
				$nb_inscrits++ ;
				$abo = unserialize($row['e']);
				$format = $abo['abo'] ;
			if($format=="texte" || $format=="html") {
				sql_insert(
					'spip_auteurs_elargis'
					, "(id_auteur, `spip_listes_format`)"
					, "(".sql_quote($row['i']).",".sql_quote($format).")"
				);
			}
			else {
				sql_insert(
					'spip_auteurs_elargis'
					, "(id_auteur, `spip_listes_format`)"
					, "(".sql_quote($row['i']).",".sql_quote('non').") "
				);
			}
				if ($abo['abo']) {
					$cmpt[$abo['abo']] ++;
				}
			}
			
			echo "<br />html : ".$cmpt['html']." <br />texte : ".$cmpt['texte']."<br />non : ".$cmpt['non']."<br />somme :".$nb_inscrits  ;


		}
		
		if ($current_version<1.97) {
//spiplistes_debug_log("UPGRADE: current_version: $current_version");
			echo "SpipListes Maj 1.97<br />";
			include_spip('base/abstract_sql');

			echo "regulariser les desabonnes avec listes...<br />";
	
			$result = sql_select(
				"a.email,a.id_auteur"
				, "spip_auteurs AS a, spip_auteurs_listes AS l, spip_auteurs_elargis AS f"
				, array(
					"a.id_auteur=f.id_auteur"
					, "f.spip_listes_format=".sql_quote('non')
					, "a.id_auteur=l.id_auteur"
					, "a.statut!=".sql_quote('5poubelle' )
				)
				, array("email")
			); //
			
			$nb_inscrits = sql_count($result);
			echo($nb_inscrits);
			
			while($res = sql_fetch($result)) {
				sql_delete("spip_auteurs_listes", "id_auteur =".sql_quote($res['id_auteur'])) ;			
			} 

		} // end if ($current_version<1.97)
		
		
		if ($current_version<1.98) {
			
			echo "SpipListes Maj 1.98<br />";
			include_spip('base/abstract_sql');
		
			echo "regulariser l'index";
			$table_nom = "spip_auteurs_elargis";
			//ajout des index
			$desc = sql_showtable($table_nom,true);
			if($desc['key']['PRIMARY KEY']!='id'){
				sql_alter("TABLE ".$table_nom." DROP PRIMARY KEY");
				if(!isset($desc['fields']['id'])) {
					sql_alter("TABLE ".$table_nom." ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY");
				}
				else {
					sql_alter("TABLE ".$table_nom." ADD PRIMARY KEY (id)");
				}
			}
			if($desc['key']['KEY id_auteur']) {
				sql_alter("TABLE ".$table_nom." DROP INDEX id_auteur, ADD INDEX id_auteur (id_auteur)");
			}
			else {
				sql_alter("TABLE ".$table_nom." ADD INDEX id_auteur (id_auteur)");
			}
			

		}

}


function spiplistes_base_creer() {


	if(!isset($descauteurs['field']['spip_listes_format'])){
		// si la table spip_auteurs_elargis existe déjà
		sql_alter("TABLE spip_auteurs_elargis ADD `spip_listes_format` VARCHAR(8) DEFAULT 'non' NOT NULL");
	}
	
}


function spiplistes_vider_tables($nom_meta_base_version) {

foreach(array('spip_listes'
	  , 'spip_courriers'
	  , 'spip_auteurs_courriers'
	  , 'spip_auteurs_listes'
	  , 'spip_auteurs_mod_listes'
	 ) as $table) {
	sql_drop_table($table);
	}
	effacer_meta($nom_meta_base_version);
}


?>