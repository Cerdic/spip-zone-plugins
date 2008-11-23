<?php 
function exec_eva_news() {
   include_spip("inc/presentation");
    // vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "evanews_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }

$icone = _DIR_PLUGIN_EVANEWS."/img_pack/evanews.png";
$commencer_page = charger_fonction('commencer_page', 'inc');

   echo $commencer_page(_T('evanews:titre_page'),'','','');	 
   echo "<br />";
   
   echo gros_titre(_T('evanews:titre_page'),'',false);
   echo debut_gauche('',true);
	
   echo debut_boite_info(true);
   echo _T('evanews:boite_info');

	echo _T('evanews:texte_descriptif');
   echo '<br /><br /> Documentation officielle EVA-WEB :';
   echo '<br /><a href="http://eva-web.edres74.net/spip.php?rubrique4" target="_blank" >Documentation eva-web</a>';
   echo fin_boite_info(true);
	
   //echo debut_raccourcis();
   //echo 'contenu de la boite des raccourcis du plugin';
   //echo fin_raccourcis();
		
   echo debut_droite('',true);
   echo debut_cadre_trait_couleur($icone, true,'', _T('evanews:titre_boite_principale'));
   echo debut_cadre_couleur('',true);

	$nomeffet=array(0=>"FadeIn et Out",1=>"Glissement de haut en bas",2=>"De gauche à droite",3=>"");   
   echo _T('evanews:texte_descriptif');
   echo "<br />"._T('evanews:titre').lire_config('eva_news/titre');
   echo "<br /><br />"._T('evanews:effet').lire_config('eva_news/effet');
   echo "<br /><br />"._T('evanews:vitesse').lire_config('eva_news/vitesse');
   echo "<br /><br />"._T('evanews:pausevitesse').lire_config('eva_news/pausevitesse');   
   echo '<br />';
   
   echo fin_cadre_couleur(true);
   
   echo '<form method="post" action="?exec=cfg&cfg=eva_news">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('evanews:modif_renseignements');
    echo '" />';
    echo '</form>';
   echo '<br />';

   echo '<form method="post" action="../">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('evanews:page_publique');
    echo '" />';
    echo '</form>';
   
   echo fin_cadre_trait_couleur(true);
   echo fin_gauche(), fin_page();
}				 
?>
