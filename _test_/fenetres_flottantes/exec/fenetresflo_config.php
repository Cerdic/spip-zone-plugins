<?php
 
 function exec_fenetresflo_config() {
	
   // les fonctions de spip necessaires pour afficher les elements de l'interface
   include_spip("inc/presentation");		
   include_spip("inc/fenetresflo_form");		

  debut_page(_T('fenetresflottantes:configuration_titre'), "configuration", "fenetreflo_config");
  debut_gauche();
  //Colonne gauche
  debut_cadre_relief(""._DIR_PLUGIN_FENETREFLO."/images/fenetre.gif", false, "", _T('fenetresflottantes:module_titre'));
  echo(_T('fenetresflottantes:module_introduction'));
  fin_cadre_relief(false);

  creer_colonne_droite();
  //Colonne droite

  debut_droite();

  //Colonne du milieu
  //-----------------
  gros_titre(_T('fenetresflottantes:configuration_titre'));

  //dimenssion fenetre
  debut_cadre_enfonce(""._DIR_PLUGIN_FENETREFLO."/images/fenetre.gif", false, "", _T('fenetresflottantes:reglage1_titre'));
  echo fenetresflo_form_reg1();
  fin_cadre_enfonce(false);

  //contenu de la fentre
  debut_cadre_enfonce(""._DIR_PLUGIN_FENETREFLO."/images/fenetre.gif", false, "", _T('fenetresflottantes:reglage2_titre'));
  echo fenetresflo_form_reg2();
  fin_cadre_enfonce(false);

  fin_page();
 

 }

?>
