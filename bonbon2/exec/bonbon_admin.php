<?php 
function exec_bonbon_admin_dist() {
   include_spip("inc/presentation");
    // vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "bonbon", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
}
include_spip('inc/plugin');
include_spip("cahier-de-texte-fonctions");
$icone = _DIR_PLUGIN_BONBON."/images/bonbon.png";
$commencer_page = charger_fonction('commencer_page', 'inc');
$infos = plugin_get_infos(_DIR_PLUGIN_BONBON);
$version = $infos['version'];

echo $commencer_page(_T('bonbon:titre_page'),'','',''); 
echo "<br />";
   
   echo gros_titre(_T('bonbon:titre_page'),'',false);
   echo debut_gauche('',true);
	
   echo debut_boite_info(true);
   echo _T('bonbon:info');
	echo '<br />';
   echo 'Version '.$version;
   echo '<br />';
   echo _T('bonbon:presentation');
	echo '<br />';   
   echo '<br /><br /> Documentation officielle EVA-WEB :';
   echo '<br /><a href="http://eva-web.edres74.net/spip.php?rubrique4" target="_blank" >Documentation eva-web</a>';
   echo fin_boite_info(true);
	
   //echo debut_raccourcis();
   //echo 'contenu de la boite des raccourcis du plugin';
   //echo fin_raccourcis();
		
   echo debut_droite('',true);
   echo debut_cadre_trait_couleur($icone, true,'', _T('bonbon:titre_boite_principale'));
   echo debut_cadre_couleur('',true);

   echo _T('bonbon:conf1');
   echo _T('bonbon:conf2');
   
   echo "<br />"._T('bonbon:classes').lire_config('bonbon/classes');
   echo "<br /><br />"._T('bonbon:groupes').lire_config('bonbon/groupes');
   echo "<br /><br />"._T('bonbon:matieres').lire_config('bonbon/matieres');
   echo "<br /><br />";

	echo '<form method="post" action="?exec=cfg&cfg=bonbon">';
	echo '<input type="submit" class="fondo" value="';
	echo _T('bonbon:modif_renseignements');
	echo '" />';
	echo '</form>';
echo fin_cadre_couleur(true);

echo debut_cadre_couleur('',true);
	echo _T('bonbon:installation');
	echo '<form method="post" action="../spip.php?page=cahier-de-texte-installation-maj">';
	echo '<input type="submit" class="fondo" value="';
	echo _T('bonbon:installation_bouton');
	echo '" />';
	echo '</form>';
echo fin_cadre_couleur(true);   
echo fin_cadre_trait_couleur(true);
echo fin_gauche(), fin_page();
}
			 
?>
