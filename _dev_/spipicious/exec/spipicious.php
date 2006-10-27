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
    spip_query("CREATE TABLE IF NOT EXISTS `{$table_pref}_spipicious` (`id_mot` bigint(21) NOT NULL default '0', `id_auteur` bigint(21) NOT NULL default '0',`id_article` bigint(21) NOT NULL default '0', KEY `id_mot` (`id_mot`));");
    
    $result = spip_query("SELECT id_groupe FROM `{$table_pref}_groupes_mots` WHERE titre = '- tags -'"); // creation du groupe de mots cles uniquement si n'existe pas
		if (spip_num_rows($result) == 0) {
      spip_query("INSERT INTO `{$table_pref}_groupes_mots` ( `id_groupe` , `titre` , `descriptif` , `texte` , `unseul` , `obligatoire` , `articles` , `breves` , `rubriques` , `syndic` , `minirezo` , `comite` , `forum` , `maj` , `auteurs` )
                                           VALUES ('', '- tags -', '', '', 'non', 'non', 'oui', 'non', 'non', 'non', 'non', 'non', 'non', NOW( ) , 'non');");
    }   
    
    // Affichage 
    gros_titre("<br/><br/>"._T('spipicious:install'));
    debut_gauche();
  	debut_droite();
  	
  	echo "<ul>";
  	echo "<li>"._T('spipicious:installed')."</li>";
  	echo "<li>"._T('spipicious:installed_group')."</li>";
  	echo "</ul>";
    fin_page();
	}
  
  
  
}
?>
