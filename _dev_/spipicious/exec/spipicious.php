<?php
// CONFIGURATION DE SPIP.ICIO.US

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_MOTS_PARTOUT',(_DIR_PLUGINS.end($p))); 


function exec_spipicious() {
  global $connect_statut, $connect_toutes_rubriques;

  include_spip ("inc/presentation");
  include_spip ("base/abstract_sql");
  
  debut_page(_T('spipicious:install'), 'configurations', 'spipicious');
  
  // droits acces
  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	 echo _T('avis_non_acces_page');
	 exit;
  }
  
  if ($connect_statut == '0minirezo' AND $connect_toutes_rubriques ) {
	
  	$table_pref = 'spip';
  	if ($GLOBALS['table_prefix']) $table_pref = $GLOBALS['table_prefix'];
  	
  	// Creation
    
    // Affichage 
    gros_titre("<br/><br/>"._T('spipicious:install'));
    debut_gauche();
  	debut_droite();
  	
    echo "des choses a faire ds l'espace prive ?";
    fin_page();
	}
  
  
  
}
?>
