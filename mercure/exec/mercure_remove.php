<?php

/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');

function _PRIVATE_presentation_taille_fichier($n){
  $c=1024;$i=0;$t=$n;
  while($c<$t && $i++<3) $t=$t/$c;
  if (0<$i) $t=sprintf('%01.2f',$t).' '.substr('KMG',$i-1,1);
  else $t.=' ';
  return $t.'o';
}

function _PRIVATE_get_dir_size($dir){
  return exec("du -sb ".$dir." | awk '{print $1}'");
}

function exec_mercure_remove() {

# elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

#
# function requises ...
#
include_spip("inc/mercure_init");
include_spip('inc/affiche_blocs');


# date jour courte sql spip
	$date_auj = date('Y-m-d');

#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('mercure:mercure_titre'), "suivi", "mercure_remove");
echo "<a name='haut_page'></a>";

# Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
}

debut_gauche();
/*---------------------------------------------------------------------------*\
Affiche le logo mercure + gros titre + datestamp des serveurs PHP et MySQL
\*---------------------------------------------------------------------------*/

 	echo entete_page();

/*---------------------------------------------------------------------------*\
Elements divers
\*---------------------------------------------------------------------------*/

creer_colonne_droite();
echo "<p class='space_20'></p>";

/*---------------------------------------------------------------------------*\
Patrick signe son mefait... si si !
\*---------------------------------------------------------------------------*/

echo "<p class='space_20'></p>";
	echo signature_plugin();

/*---------------------------------------------------------------------------*\
atteindre page php info
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		echo "\n<a href='".generer_url_ecrire("info")."'>"._T('mercure:page_phpinfo')."</a>\n";
	fin_boite_info();


/*---------------------------------------------------------------------------*\
version de mysql du serveur :
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		$vers = mysql_query("select version()");
		$rep = mysql_fetch_array($vers);
		echo "MySQL v. ".$rep[0];
	fin_boite_info();

echo "<p class='space_10'></p>";
	debut_boite_info();
	echo _T('mercure:mercure_avertissement');
	fin_boite_info();

debut_droite();

	echo onglets_mercure(_request('exec'));

	debut_boite_info();
  echo _T('mercure:procedure_remove').'<br /><br />';
	fin_boite_info();

	echo '<p class="space_10"></p>';

  # Reste-t-il un ou des fichiers de conversation dans /plugins/mercure/local ?
  # _DIR_LOCAL_MERCURE
  # Si oui... on averti l'utilisateur !
  $nb = 0;
  $liens = '';
	if (!defined('_URL_LOCAL_MERCURE')) {
    $pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/')-1);
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/'));
    define('_URL_LOCAL_MERCURE',$pageURL.'/plugins/mercure/local/');
	}  
  # SQLite ?
  if(file_exists(_DIR_LOCAL_MERCURE.'mercure_SQLITE.db')){
    $nb++;
    $stats = stat(_DIR_LOCAL_MERCURE.'mercure_SQLITE.db');
    $liens .= '<a href="'._URL_LOCAL_MERCURE.'mercure_SQLITE.db" title="Download SQLite File"><img src="'._DIR_IMG_MERCURE.'down.png" align="middle" border="0"> '._T('mercure:lien_remove_reste_conversation_SQLite').' : '._PRIVATE_presentation_taille_fichier($stats['size']).', '.date("Y-m-d, H:i:s", $stats['mtime']).'</a><br />';
  }
  # TXT ?
  if(file_exists(_DIR_LOCAL_MERCURE.'mercure/messages.txt')){
    $nb++;
    $stats = stat(_DIR_LOCAL_MERCURE.'mercure/messages.txt');
    $liens .= '<a href="'._URL_LOCAL_MERCURE.'mercure/messages.txt" title="Download Text File"><img src="'._DIR_IMG_MERCURE.'down.png" align="middle" border="0"> '._T('mercure:lien_remove_reste_conversation_TXT').' : '._PRIVATE_presentation_taille_fichier($stats['size']).', '.date("Y-m-d, H:i:s", $stats['mtime']).'</a><br />';
  }
  if($nb!=0){
	 debut_boite_info();
    echo _T('mercure:procedure_remove_reste_conversation').'<br />';    
    echo $liens.'<br />';        
	 fin_boite_info();
  }

	echo '<p class="space_10"></p>';

	debut_boite_info();
  echo _T('mercure:procedure_automatique');	
  echo '<center><a href="'.generer_url_ecrire("mercure_remove_delete_mercure").'"><img src="'._DIR_IMG_MERCURE.'remove.png" align="middle" border="0"> '._T('mercure:destruction_du_plugin').'</a></center><br />';
  
				//Suppression meta
//				effacer_meta('mercure');
  
	fin_boite_info();


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec

?>
