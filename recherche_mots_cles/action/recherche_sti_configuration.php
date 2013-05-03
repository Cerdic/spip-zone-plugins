<?php

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function action_recherche_sti_configuration()
{
	global $_POST;
	
	//nombre de colonnes pour l'affichage
	$nombre_colonnes = $_POST['nbre_colonnes'];//on récupère le nombre de colonne saisie
	
	//***************** groupes de mots clés *******************************
	//Récupération de la variable Array associée aux Checkbox cochées pour les groupes de mots clés
	$tab_id_groupes_mots_cles=$_POST['groupes_mots_select']; 
	//contenu des boutons radios pour l'affichage type liste déroulante ou case à cocher
	$tab_affichage_mots_cles=$_POST['type_affichage_mots_cles'];
	
	//on enregistre dans la table les groupes de mots clés cochés avec leurs titres et le mode de présentation
	foreach ($tab_id_groupes_mots_cles as $id_groupes_mots_cles) //on scrute chaque groupes de mots clés sélectionnés
	{
		//echo "<br>id groupes mots cles= ".$id_groupes_mots_cles."<br>";//pour le debug
		//on récupère le titre associé à chaque groupes de mots clés sélectionnés
		$titres_groupes_mots_cles = sql_query("SELECT titre FROM spip_groupes_mots WHERE id_groupe='$id_groupes_mots_cles'");
		while ($titre = sql_fetch($titres_groupes_mots_cles)) //on scrute tous les titres
		{
			$indexBoutonRadio=$titre['titre'];
			$mode_affichage=$tab_affichage_mots_cles[$indexBoutonRadio];//on récupère la valeur du bouton radio qui est indexé par le titre
			sql_query("DELETE FROM spip_sti_groupes_mots_cles WHERE id_groupes_mots_cles='$id_groupes_mots_cles'");//pas très jolie !!!
			//on écrit dans la table les nouvelles valeurs
			sql_insertq("spip_sti_groupes_mots_cles", array('id_groupes_mots_cles' => $id_groupes_mots_cles, 'titre' => $titre['titre'] ,'mode_presentation' => $mode_affichage, 'nbre_colonnes' => $nombre_colonnes));
		}		
	}
	
	//on efface dans la table les anciens groupes de mots clés enregistrées qui n'ont
	//pas été cochées
	$enregistrement=0;
	$id_groupes_mots_cles_enregistrees = sql_query("SELECT id_groupes_mots_cles FROM spip_sti_groupes_mots_cles");
	while ($var = sql_fetch($id_groupes_mots_cles_enregistrees))
	{
		foreach ($tab_id_groupes_mots_cles as $id_groupes_mots_cles)
		{
				if ($var['id_groupes_mots_cles'] == $id_groupes_mots_cles) $enregistrement=1;
		}
		if ($enregistrement == 0)
		{//on efface 
			$efface=$var['id_groupes_mots_cles'];
			sql_query("DELETE FROM spip_sti_groupes_mots_cles WHERE id_groupes_mots_cles='$efface'");
		}
		$enregistrement = 0; //on remet à zéro pour le prochain test
	}
	
	//on revient sur la page de configuration du plugin
	redirige_par_entete($GLOBALS['meta']['adresse_site'].'/ecrire/?exec=recherche_sti_boutons');	
}
?>
