<?php 
function exec_inscription_admin() {
   include_spip("inc/presentation");
// vérifier les droits
   global $connect_statut;
   global $connect_toutes_rubriques;
   if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
       debut_page(_T('titre'), "saveauto_admin", "plugin");
       echo _T('avis_non_acces_page');
       fin_page();
       exit;
   }

   echo debut_page(_T('inscription2:conf_plugin'));	 
   echo "<br />";
   echo gros_titre(_T('inscription2:conf_plugin_page'));
   echo debut_gauche();
	
   echo debut_boite_info();
   echo _T('inscription2:description_page');
   echo fin_boite_info();
	
   echo debut_droite();
   echo debut_cadre_trait_couleur("../"._DIR_PLUGIN_INSCRIPTION."Inscription_icone.PNG", false, "", _T('inscription2:configs'));       
   echo debut_cadre_couleur();
// simple test de lire_config()
   echo '<strong>contenu de "Inscription2" :</strong> ';
   $hola = lire_config('inscription2');
   print_r($hola);
   
   echo '<br /><br /><strong>une boucle dans lire_config(inscription2)</strong> ';
   foreach(lire_config('inscription2') as $cle => $val) {
       if($val!='')
	   echo '<br />$cle = '.$cle.' $val = '.$val;
   }
	
   echo fin_page();
}				 
?>