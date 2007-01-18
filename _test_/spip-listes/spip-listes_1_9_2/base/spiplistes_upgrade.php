<?php

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


	$GLOBALS['spiplistes_version'] = 1.92;
	function spiplistes_verifier_base(){
		$accepter_visiteurs = lire_meta('accepter_visiteurs');

		if($accepter_visiteurs != 'oui'){
			$accepter_visiteurs = 'oui';
			ecrire_meta("accepter_visiteurs", $accepter_visiteurs);
			ecrire_metas();
			echo _T('spiplistes:autorisation_inscription');
		}
		
		$version_base = $GLOBALS['spiplistes_version'];
		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta']['spiplistes_version']) )
				|| (($current_version = $GLOBALS['meta']['spiplistes_version'])!=$version_base)){
			include_spip('base/spip-listes');
			
			// si etait deja installe mais dans une vieille version, on reprend a zero
			$desc = spip_abstract_showtable("spip_listes", '', true);
			if (!isset($desc['field']['id_liste']))
				$current_version = 0.0;
			if (spip_query('SELECT *	FROM spip_articles	WHERE statut in ("liste","inact","poublist")'))
				$current_version=0.0;
			
			if ($current_version==0.0){
				// Verifie que les tables spip_listes existent, sinon les creer
				spip_log('creation des tables spip_listes');
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				
				//Mise a jour des listes anciennes // a mettre en fonction
				$resultat_aff = spip_query('SELECT *	FROM spip_articles	WHERE statut in ("liste","inact","poublist")');
				if(@spip_num_rows($resultat_aff) > 0){
					echo "<h3>SPIP-listes va mettre a jour</h3>";
					while ($row = spip_fetch_array($resultat_aff)) {
						$id_article=$row['id_article'];
						$titre_liste=addslashes(corriger_caracteres($row['titre']));
						$texte_liste = addslashes(corriger_caracteres($row['texte']));
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
						
						//pied de page
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
						spip_query("DELETE FROM spip_auteurs_listes WHERE id_liste ="._q($id_liste));
						spip_query("INSERT INTO spip_auteurs_listes (id_auteur, id_liste) VALUES ("._q($connect_id_auteur).","._q($id_liste).")");
						//recuperer les abonnes (peut etre plus tard ?)
						$abos=spip_query("SELECT id_auteur, id_article FROM spip_auteurs_articles WHERE id_article="._q($id_article));
						
						while($abonnes=spip_fetch_array($abos)){
							$abo=$abonnes["id_auteur"];
							spip_query("INSERT INTO spip_abonnes_listes (id_auteur, id_liste) VALUES ("._q($abo).","._q($id_liste).")");
						}
						
						//effacer les anciens articles/abo
						spip_query("DELETE FROM spip_articles WHERE id_article ="._q($id_article));
						spip_query("DELETE FROM spip_auteurs_articles WHERE id_article ="._q($id_article));
			
						//manquent les courriers
					}
				}
				ecrire_meta('spiplistes_version',$current_version=$version_base,'non');
			}
			if ($current_version<1.92){
				echo "<br /> Maj 1.92<br />";
				spip_query("ALTER TABLE spip_listes ADD titre_message varchar(255) NOT NULL default '';");
				spip_query("ALTER TABLE spip_listes ADD pied_page longblob NOT NULL;");
				ecrire_meta('spiplistes_version', $current_version=1.92);
			}
			ecrire_metas();
		}
	}
	
	function spiplistes_vider_tables() {
		include_spip('base/agenda_evenements');
		include_spip('base/abstract_sql');
		// suppression du champ evenements a la table spip_groupe_mots
		spip_query("DROP TABLE spip_courriers");
		spip_query("DROP TABLE spip_listes");
		spip_query("DROP TABLE spip_abonnes_courriers");
		spip_query("DROP TABLE spip_abonnes_listes");
		spip_query("DROP TABLE spip_auteurs_listes");
		effacer_meta('spiplistes_version');
		ecrire_metas();
	}
	
	function spiplistes_install($action){
		$version_base = $GLOBALS['spiplistes_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['spiplistes_version']) AND ($GLOBALS['meta']['spiplistes_version']>=$version_base));
				break;
			case 'install':
				spiplistes_verifier_base();
				break;
			case 'uninstall':
				spiplistes_vider_tables();
				break;
		}
	}	
?>