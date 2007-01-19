<?php 

function exec_aa_admin() {
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

	echo debut_page(_T('aa:titre_page'));	 
  echo "<br />";
  echo gros_titre(_T('aa:gros_titre_page'));
  echo debut_gauche();
	
  echo debut_boite_info();
	echo 'contenu de la boite info du plugin aa';
  echo fin_boite_info();
	
	echo debut_raccourcis();
	echo 'contenu de la boite des raccourcis du plugin aa';
	echo fin_raccourcis();
		
	echo debut_droite();
	echo debut_cadre_trait_couleur("plugin-24.gif", false, "", _T('titre_boite_principale'));       
	echo debut_cadre_couleur();
// simple test de lire_cfg()
  echo '<strong>lire_cfg(aa) retourne :</strong> ';
  print_r(lire_cfg('aa'));
	echo '<br /><br /><strong>lire_cfg(aa/id_aa) =</strong> '.lire_cfg('aa/id_aa');
	echo '<br /><br /><strong>lire_cfg(aa/chapo) =</strong> '.lire_cfg('aa/chapo');
	
	echo '<br /><br /><strong>une boucle dans lire_cfg(aa)</strong> ';
	foreach(lire_cfg('aa') as $cle => $val) {
	    echo '<br />$cle = '.$cle.' $val = '.$val;
	}
	
	echo fin_page();

}				 
?>