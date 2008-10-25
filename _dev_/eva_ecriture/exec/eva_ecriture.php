<?php
if (!defined('_DIR_PLUGIN_EVAECRITURE')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
	define('_DIR_PLUGIN_EVAECRITURE',(_DIR_PLUGINS.end($p)));
}

	include_spip('base/db_mysql');
	include_spip('base/abstract_sql');
  	include_spip('inc/presentation'); 

#############
function exec_eva_ecriture() {
	$bloc = 0;

  GLOBAL $connect_statut;
  if ($connect_statut != '0minirezo') {
    debut_page();
    echo _T('avis_non_acces_page');
    fin_page();
    exit;
  }
	
$icone = _DIR_PLUGIN_EVAECRITURE."/img_pack/logo_ecriture.png";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('evaecriture:gros_titre_page') , 'eva_ecriture_admin', 'plugin');
	echo gros_titre(_T('evaecriture:gros_titre_page'), '', false);	
  //echo gros_titre(_T('evaecriture:gros_titre_page'));
   
echo debut_gauche('', true);
echo debut_boite_info(true);
	echo '<img src="'._DIR_PLUGIN_EVAECRITURE.'/img_pack/logo_ecriture.png" border="0" alt="logo du plugin eva_ecriture"><br/>';
        echo _T('evaecriture:Gestion des polices de caractères du plugin');
	echo '<br/><br/>';
  	echo _T('evaecriture:siteoff1');
	echo _T('evaecriture:lienoff1');
	
    echo debut_cadre_trait_couleur(_DIR_PLUGIN_EVA_ECRITURE."img_pack/eva.gif", true, '', '');	
	echo bouton_block_depliable('Ajouter des polices',false,'');
   echo debut_block_depliable(false);
   echo '<br />&nbsp;<br />';
   echo '<form action="'.generer_url_ecrire("eva_ecriture").'" method="post" enctype="multipart/form-data">';
   echo '<input type="file" name="image_eva_habillage_envoi" />';
   echo '<br />&nbsp;<br /><input type="submit" value="Envoyer" /></form></center>';
	echo fin_block();
	echo fin_cadre_trait_couleur(true);
echo fin_boite_info(true);

################
echo debut_droite('', true);
echo debut_cadre_enfonce($icone, false, '', _T('evaecriture:eva_ecriture'));
	echo _T('evaecriture:presentation');
	//echo "<ecriture1|texte=Ma première phrase.|police=Boite_pleine.ttf|left>";
        
	echo debut_cadre_trait_couleur('',true);
		//echo bouton_block_invisible($bloc);
   	echo _T('evaecriture:Liste des polices présentes dans le plugin :');
   	//echo debut_block_invisible($bloc);
		
$rep = _DIR_PLUGIN_EVAECRITURE."/polices/";
echo "répertoire d'installation des polices :".$rep;
$dir = opendir($rep);

	//if (($nom_fichier!='.') AND ($nom_fichier!='..') AND ((strpos($nom_fichier,'.PNG')))) {
   echo '<br />';
   
//}

while ($f = readdir($dir)) {
   if(is_file($rep.$f) AND ($f!='.') AND ($f!='..') AND (strpos($f,'.ttf'))) {
      echo "<li>Nom de la police: <strong>".$f."</strong></li>";
      echo "<li>Taille du fichier: ".filesize($rep.$f)." octets</li>";
      echo "<li>Exemple : bientot ici</li>";
      echo "<li>[(#TEXTE|police=".$f.")]</li>";
      echo "<br><br>";
   }
   }
closedir($dir);
   
	//echo fin_block();
	echo fin_cadre_trait_couleur(true);

echo fin_cadre_enfonce(true);
echo fin_gauche();
echo fin_page();
}

?>