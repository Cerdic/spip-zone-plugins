<?php

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

function action_recherche_sti_configuration()
{
	global $_POST;

	//***************** groupes de mots clés *******************************
	//Récupération de la variable Array associée aux Checkbox cochées pour les groupes de mots clés
	$tab_id_groupes_mots_cles=$_POST['groupes_mots_select'];
	$tab_affichage_mots_cles=$_POST['type_affichage_mots_cles']; //pour l'affichage type liste déroulante ou case à cocher
	//on enregistre dans la table les groupes de mots clés cochés avec leurs titres
	foreach ($tab_id_groupes_mots_cles as $id_groupes_mots_cles)
	{
		//echo "id groupes mots cles= ".$id_groupes_mots_cles."<br>";
		$titres_groupes_mots_cles = sql_query("SELECT titre FROM spip_groupes_mots WHERE id_groupe='$id_groupes_mots_cles'");
		while ($titre = sql_fetch($titres_groupes_mots_cles))
		{
			$casecocher=0;
			foreach ($tab_affichage_mots_cles as $affichage)
			{
				if ($affichage == $id_groupes_mots_cles) $casecocher=1;
			}
			sql_query("DELETE FROM spip_sti_groupes_mots_cles WHERE id_groupes_mots_cles='$id_groupes_mots_cles'");//pas très jolie !!!
			if ($casecocher == 1) sql_insertq("spip_sti_groupes_mots_cles", array('id_groupes_mots_cles' => $id_groupes_mots_cles, 'titre' => $titre['titre'] ,'mode_presentation' => '1'));
			else sql_insertq("spip_sti_groupes_mots_cles", array('id_groupes_mots_cles' => $id_groupes_mots_cles, 'titre' => $titre['titre'] ,'mode_presentation' => '0'));
			$casecocher=0;
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
	
	//code qui ne marche pas !! $redirect est toujours vide... pourquoi ?
	/*if ($redirect = _request('redirect');
	{
		include_spip('inc/headers');
		redirige_par_entete($redirect);
	}*/
	
}
?>
