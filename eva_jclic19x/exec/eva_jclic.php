<?php
if (!defined('_DIR_PLUGIN_EVAJCLIC')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_EVAJCLIC',(_DIR_PLUGINS.end($p)));
}

function exec_eva_jclic() {
   include_spip('base/db_mysql');
	include_spip('base/abstract_sql');
   include_spip('inc/presentation');
   include_spip('base/spip-mots');



   echo debut_page(_T('evajclic:EVA_nom'));	 
   echo "<br />";
   echo gros_titre(_T('evajclic:gros_titre_page'));
   
   echo debut_gauche();
   echo debut_boite_info();
	echo '<img src="'._DIR_PLUGIN_EVAJCLIC.'img_pack/jclic_logo.gif" border="0" alt="Jclic">';
	echo '<br/>';
	echo _T('evajclic:titre_presentation');
	
	echo _T('evajclic:presentation');
	echo '<br/>';
  	echo _T('evajclic:siteoff1');
	echo _T('evajclic:lienoff1');
	echo '<br/>';
  	echo _T('evajclic:siteoff2');
	echo _T('evajclic:lienoff2');
	echo '<br/>';
	echo fin_boite_info();
	   
   echo debut_droite();
   echo debut_cadre_trait_couleur("jclic.gif", false, "", _T('evajclic:titre_boite_principale'));       
   echo debut_cadre_couleur();

	$table = 'spip_mots';
	$champ = 'titre';
   $requete = "SELECT id_mot FROM ".$table." WHERE ".$champ."='jclic'";
   $resultat1 = spip_query($requete);
   $nombre = spip_num_rows($resultat1);

	echo _T('evajclic:verification_mot');
	if ($nombre > 1) {
	echo _T('evajclic:probleme_base');
	fin();
	}
	else if ($nombre == 1){
	echo _T('evajclic:mot_deja');
	} else {
	echo _T('evajclic:mot_pas');

	recherche_idgroupe();
	$idgrp = recherche_idgroupe();
	
	crea_mot($idgrp, $table);
	}
	
   
   while ($resultat1_tableau = spip_fetch_array($resultat1))
    {
	 $idjclic = $resultat1_tableau["id_mot"];
    }
	
	//Dans la table spip_mots_articles, r&eacute;cup&eacute;rer tous les ID articles associ&eacute;s à ID jclic
	$table2 = 'spip_mots_articles';
	$champ2 = 'id_mot';
	$rq2 = "SELECT id_article FROM ".$table2." WHERE ".$champ2."=".$idjclic;
   $resultat2 = spip_query($rq2);
	$nombre2 = spip_num_rows($resultat2);
	$idgrp = 0;
	
   while ($row2 = spip_fetch_array($resultat2))
    {
	$id[] =	$row2["id_article"];
    }

	echo _T('evajclic:liste_activites');
	   
 for($i=0;$i<=$nombre2;$i+1)
    {
		$rq3 = 'SELECT * FROM spip_articles WHERE id_article ='.$id[$i];
		$resultat3 = spip_query($rq3);
		   while ($row3 = spip_fetch_array($resultat3))
	    {

	echo '<p>';
	echo '<a href=?exec=articles&id_article='.$row3["id_article"].' title="Cliquez ici pour &eacute;diter" >';
	echo '<img src="'._DIR_PLUGIN_EVAJCLIC.'img_pack/jclic_aqua.png" width="30" height="30" border="0" alt="">';
	echo '</a> ';
	echo _T('evajclic:article_num').$row3["id_article"].'<br/>';
	echo _T('evajclic:titre_activite').$row3["titre"].'<br/>';
	echo _T('evajclic:discipline').$row3["surtitre"].'<br/>';
	echo _T('evajclic:competences').$row3["soustitre"].'<br/>';	
	echo _T('evajclic:descriptif').$row3["descriptif"].'<br/>';
	echo _T('evajclic:texte_article').$row3["texte"].'<br/>';
	echo '</p>';
	
	    }
	    $i++;
	}

	$nba = $i - 1;
	echo _T('evajclic:nb_act1').$nba._T('evajclic:nb_act2');

echo fin_gauche();
echo fin_page();
}				

####################

function recherche_idgroupe() {

	$table_grp = 'spip_groupes_mots';
	$champ_grp = 'titre';
   $requete_grp = "SELECT id_groupe FROM ".$table_grp." WHERE  ".$champ_grp."='Activites'";
   $resultat1_grp = spip_query($requete_grp);
   $nb_grp = spip_num_rows($resultat1_grp);

	if ($nb_grp == 0){
	echo _T('evajclic:groupe_non');
	$id = 1;
	return ($id);
	} else {
#   echo '<br/> Table :'.$table_grp;   
#   echo '<br/> Requete :'.$requete_grp;
#   echo '<br/> Resultat :'.$resultat1_grp;
#   echo '<br/> Nombre :'.$nb_grp;

   while ($tab_grp = spip_fetch_array($resultat1_grp))
    {
	 $id = $tab_grp["id_groupe"];
    }
#   echo '<br/> ID du groupe ='.$idgrp;
	return ($id);
	}
}

####################

function crea_mot($idgrp, $table) {

	$ajout = "INSERT INTO ".$table." (titre,id_groupe,type,idx) VALUES ('jclic','".$idgrp."','Activites','oui')";
   $resultat2 = spip_query($ajout);
   echo _T('evajclic:creation_terminee');
}

####################

function fin() {

}
	
?>
	