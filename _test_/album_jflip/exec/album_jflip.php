<?php 
function exec_album_jflip() {
   include_spip("inc/presentation");
    // vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "albumjflip_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }

$icone = _DIR_PLUGIN_ALBUM_JFLIP."/img_pack/jflip.png";
$commencer_page = charger_fonction('commencer_page', 'inc');

   echo $commencer_page(_T('albumjflip:titre_page'),'','','');	 
   echo "<br />";
   
   echo gros_titre(_T('albumjflip:titre_page'),'',false);
   echo debut_gauche('',true);
	
   echo debut_boite_info(true);
   echo _T('albumjflip:boite_info');

	echo _T('albumjflip:texte_descriptif');
   echo '<br /><br /> Documentation officielle EVA-WEB :';
   echo '<br /><a href="http://eva-web.edres74.net/spip.php?rubrique4" target="_blank" >Documentation eva-web</a>';
   echo fin_boite_info(true);
	
   //echo debut_raccourcis();
   //echo 'contenu de la boite des raccourcis du plugin';
   //echo fin_raccourcis();
		
   echo debut_droite('',true);
   echo debut_cadre_trait_couleur($icone, true,'', _T('albumjflip:titre_boite_principale'));
   echo debut_cadre_couleur('',true);

	$nomeffet=array(0=>"FadeIn et Out",1=>"Glissement de haut en bas",2=>"De gauche à droite",3=>"");   
   echo _T('albumjflip:texte_descriptif');
   echo "<br />"._T('albumjflip:largeur').lire_config('album_jflip/largeur');
   echo "<br /><br />"._T('albumjflip:hauteur').lire_config('album_jflip/hauteur');
   echo "<br /><br />"._T('albumjflip:vitesse').lire_config('album_jflip/vitesse');
   echo "<br /><br />"._T('albumjflip:pausevitesse').lire_config('album_jflip/pausevitesse');   
   echo '<br />';
   
   echo fin_cadre_couleur(true);
   
   echo '<form method="post" action="?exec=cfg&cfg=album_jflip">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('albumjflip:modif_renseignements');
    echo '" />';
    echo '</form>';
   echo '<br />';

   echo '<form method="post" action="../">';
    echo '<input type="submit" class="fondo" value="';
    echo _T('albumjflip:page_publique');
    echo '" />';
    echo '</form>';
   
   echo fin_cadre_trait_couleur(true);
   echo fin_gauche(), fin_page();
}				 
?>
