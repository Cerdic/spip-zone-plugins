<?php
######################################################################
# RECHERCHE PAR MOTS CLES	                                         #
# Tables de recherches : spip_mots et spip_groupes_mots				 #
# Table d'enregistrement des préférences de recherche :				 #
# spip_sti_groupes_mots_cles										 #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

//sécurisation de l'accès à ce fichier
//pas d'accès en direct, il faut que ce soit SPIP qui le lance.
if (!defined('_ECRIRE_INC_VERSION')) return; 

//cette fonction est executée par un clic sur l'icône "recherche par mots clés"
//présent dans l'onglet configuration de la partie privé du site
//après installation du plugin
function exec_recherche_sti_boutons_dist($class = null)
{
	
	global $connect_statut;
	
	// la configuration est réservée aux admins 
	
	if($connect_statut != '0minirezo') {
		include_spip('inc/minipres');
		echo minipres(); //on affiche une page html "accès interdit"
		exit;
	}

	include_spip('inc/presentation');
		
	//début du code ici
	//inclusion des fonctions de presentation de notre plugin
	include_spip("inc/recherche_sti_inc_pres");
		
	//pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'nom'),'data'=>''));
	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
		
	// titre, partie, sous_partie (pour le menu)
	echo $commencer_page(_T('recherche_sti:titre_pages_admin'), "suivi", "recherche_sti");
		
	//titre
	echo "<br /><br	/><br />\n";
	echo gros_titre(_T('recherche_sti:titre_page_admin'), '', false);
			
	//colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'nom'),'data'=>''));
		
	echo "<br />";
	echo debut_boite_info(true);
	echo _T('recherche_sti:signature');
	echo fin_boite_info(true);
	echo "<br />";
		
	//colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'nom'),'data'=>''));
			
	//centre
	echo debut_droite('', true);
	echo debut_cadre_relief( _DIR_IMG_RECHERCHE_STI."recherche_sti.png");
		
	//contenu
	echo gros_titre(_T('recherche_sti:titre_config'),'',false);
	echo "<br />";
		
	//verification de l'existence d'au moins un groupe de mots clés
	$g=sql_query("SELECT * FROM spip_groupes_mots");
	$nb_g=sql_count($g);
		
	if($nb_g==0) echo debut_boite_erreur(_T('recherche_sti:erreur_groupes_mots'));
		else
			 {
				echo '<form action="'.generer_url_action('recherche_sti_configuration').'" method="post">';
				
				//Selection du nombre de colonnes pour l'affichage dans la partie publique
				echo debut_cadre_trait_couleur ('', true, '', _T('recherche_sti:nombre_colonnes'));
				echo _T('recherche_sti:text_select_nombre_colonnes');
				$g = sql_query("SELECT nbre_colonnes FROM spip_sti_groupes_mots_cles LIMIT 0,1");// Lecture de notre table pour récupérer le champ nbre_colonnes
				$table_vide = sql_count ($g);
				if ($table_vide == 0) {
					echo "<h2><select name=\"nbre_colonnes\"> <option value=1> 1 </option> <option value=2 selected='selected'> 2 </option> <option value=3> 3 </option> <option value=4> 4 </option> </select>";
				} 
				else 
				{
					while ($table_nbre_colonnes = sql_fetch($g))	// Scrutation de l'entrée de cette table
					{
					  echo "<h2><select name=\"nbre_colonnes\">";
					  switch ($table_nbre_colonnes['nbre_colonnes'])
					  {
						  case 1:
								echo "<option value=1 selected='selected'> 1 </option> <option value=2> 2 </option> <option value=3> 3 </option> <option value=4> 4 </option>";
								break;
						  case 2:
								echo "<option value=1> 1 </option> <option value=2 selected='selected'> 2 </option> <option value=3> 3 </option> <option value=4> 4 </option>";
								break;
						  case 3:
								echo "<option value=1> 1 </option> <option value=2> 2 </option> <option value=3 selected='selected'> 3 </option> <option value=4> 4 </option>";
								break;
						  case 4 :
								echo "<option value=1> 1 </option> <option value=2> 2 </option> <option value=3 > 3 </option> <option value=4 selected='selected'> 4 </option>";
								break;
					  }
					  
					  //if ($table_nbre_colonnes['nbre_colonnes'] == 1) echo "<option value=1 selected='selected'> 1 </option> <option value=2> 2 </option> <option value=3> 3 </option>";
					  //else if ($table_nbre_colonnes['nbre_colonnes'] == 2) echo "<option value=1> 1 </option> <option value=2 selected='selected'> 2 </option> <option value=3> 3 </option>";
					    //   else echo "<option value=1> 1 </option> <option value=2> 2 </option> <option value=3 selected='selected'> 3 </option>";
					  echo "</select>";
					}
				}
				echo fin_cadre_trait_couleur(true);
				
				
				//Selection des groupes de mots clés et du type d'affichage dans la partie publique
				echo debut_cadre_trait_couleur ('', true, '', _T('recherche_sti:groupes_mots'));
				echo _T('recherche_sti:text_select_groupes_mots_cles');
					
				//on vérifie si une configuration des groupes de mots clés n'a pas déjà été enregistrée
				$r=sql_query("SELECT * FROM spip_sti_groupes_mots_cles");
				$nb_groupes_mots_cles=sql_count($r);
				if ($nb_groupes_mots_cles == 0) 
				{ //pas d'enregistrement
					$table_groupes_mots_cles = sql_query("SELECT id_groupe,titre FROM spip_groupes_mots");// Lecture de la table groupe de mots clés
					while ($groupes_mots_cles = sql_fetch($table_groupes_mots_cles))	// Scrutation des entrées de cette table
					{
						$id_groupe =$groupes_mots_cles['id_groupe'];		//Récupération de l'identifiant groupe
						// Lecture de la table mots clés correspondant au groupe de mots clés
						$table_mots_cles = sql_query("SELECT titre,id_groupe,id_mot FROM spip_mots WHERE id_groupe='$id_groupe'");	
						//on associe la valeur des checkbox à id_groupe
						echo "<h2><input type=\"checkbox\" name=\"groupes_mots_select[]\" value=\" ".$groupes_mots_cles['id_groupe']."\" >";
						echo $groupes_mots_cles['titre']."</h2>";		//Affichage du nom du groupe de mots clés
	
						//on associe le nom des boutons RADIO au titre des groupes de mots clés
						echo "<SPAN style=\"color: red\"> Mode d'affichage : </SPAN>";
						echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=0 >liste d&eacute;roulante";
						echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=1 checked >case &agrave; cocher";
						echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=2 >case &agrave; cocher + ic&ocirc;nes<br/>";
						
						//affichage de la liste des mots clés associés à un groupe de mots clés
						echo "<div style=\"background-color:#d7d7d7; border:1px; font-size:1.2em; \">";
						while ($mots_cles = sql_fetch($table_mots_cles))		// Scrutation des entrées de la table mots clés
						{
							echo $mots_cles['titre']."<br>"; // Affichage pour rappel des mots clés associés à un groupe de mots clés
						}
						echo "</div>";
					}
				}
				else
				{   //rappel de la configuration enregistrée dans la table spip_sti_groupes_mots_cles
					$table_groupes_mots_cles = sql_query("SELECT id_groupe, titre FROM spip_groupes_mots");
					$existe=0;//cette variable sera à 1 quand id du groupe des mots clés a déjà été enregistrée
					$presentation=1;//cette variable sera à 1 quand mode_presentation = case coché (mode par défaut)
					while ($groupes_mots_cles = sql_fetch($table_groupes_mots_cles))	// Scrutation des entrées de la table
					{
						$table_groupes_mots_cles_enregistres = sql_query("SELECT id_groupes_mots_cles, mode_presentation FROM spip_sti_groupes_mots_cles");
						while ($groupes_mots_cles_enregistres = sql_fetch($table_groupes_mots_cles_enregistres))	// Scrutation des entrées de la table
						{
							if ($groupes_mots_cles['id_groupe'] == $groupes_mots_cles_enregistres['id_groupes_mots_cles'])
							{ 
								$existe=1;
								$presentation=$groupes_mots_cles_enregistres['mode_presentation'];//on récupère le mode d'affichage enregistré
							}
						}			
						//affichage des titres des groupes de mots clés 
						if ($existe==1) //déjà enregistré
						{
							echo "<h2><input type=\"checkbox\" name=\"groupes_mots_select[]\" value=\" ".$groupes_mots_cles['id_groupe']."\" CHECKED>";
							echo $groupes_mots_cles['titre']."</h2>";		//Affichage du nom du groupe de mots clés
						}
						else
						{
							echo "<h2><input type=\"checkbox\" name=\"groupes_mots_select[]\" value=\" ".$groupes_mots_cles['id_groupe']."\" >";
							echo $groupes_mots_cles['titre']."</h2>";		//Affichage du nom du groupe de mots clés
						}
						
						//affichage des boutons RADIO
						//on associe le nom des boutons RADIO au titre des groupes de mots clés
						echo "<SPAN style=\"color: red\"> Mode d'affichage : </SPAN>";
						switch ($presentation)
						{
							case 0:
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=0 checked >liste d&eacute;roulante";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=1 >case &agrave; cocher";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=2 >case &agrave; cocher + ic&ocirc;nes<br/>";
								break;
							case 2:
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=0 >liste d&eacute;roulante";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=1 >case &agrave; cocher";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=2 checked >case &agrave; cocher + ic&ocirc;nes<br/>";
								break;
							default :
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=0 >liste d&eacute;roulante";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=1 checked >case &agrave; cocher";
								echo "<input type=\"radio\" name=\"type_affichage_mots_cles[".$groupes_mots_cles['titre']."]\" value=2 >case &agrave; cocher + ic&ocirc;nes<br/>";
						}
												
						$existe=0;
						$presentation=1;
						
						$id_groupe =$groupes_mots_cles['id_groupe'];		//Récupération de l'identifiant groupe
						$table_mots_cles = sql_query("SELECT titre,id_groupe,id_mot FROM spip_mots WHERE id_groupe='$id_groupe'");	
						echo "<div style=\"background-color:#d7d7d7; border:1px; font-size:1.2em; \">";
						while ($mots_cles = sql_fetch($table_mots_cles))		// Scrutation des entrées de la table mots clés
						{
							echo $mots_cles['titre']."<br>"; // Affichage pour rappel des mots clés associés à un groupe de mots clés
						}
						echo "</div>";			
					}
				}
				echo fin_cadre_trait_couleur(true);
				
				//fin du formulaire
				echo "<br><input type=\"submit\" name=\"Valider\" value=\"Valider\" />";
				echo "</form>";
			 }
		
		
	echo fin_cadre_relief();
	//fin du contenu	
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'nom'),'data'=>''));
	echo fin_gauche(), fin_page();
}
?>
