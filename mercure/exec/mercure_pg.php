<?php

/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com
*/

session_start();

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

function exec_mercure_pg(){

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

if($GLOBALS['mercure']['first_use'] == TRUE){
  header('location:?exec=mercure_conf');
}

# date jour courte sql spip
	$date_auj = date('Y-m-d');

#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('mercure:mercure_titre'), "suivi", "mercure_pg");
echo "<a name='haut_page'></a>";

debut_gauche();

/*---------------------------------------------------------------------------*\
Affiche le logo mercure + gros titre + datestamp des serveurs PHP et MySQL
\*---------------------------------------------------------------------------*/

 	echo entete_page();

/*---------------------------------------------------------------------------*\
Encart de commandes
\*---------------------------------------------------------------------------*/

  echo encart_commandes($GLOBALS['mercure']['notify']);

/*---------------------------------------------------------------------------*\
Les rédacteurs connectés
\*---------------------------------------------------------------------------*/

	echo "<p class='space_10'></p>";
  echo redacteurs_connectes();

	echo "<p class='space_20'></p>";

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

/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_mercure(_request('exec'));

	debut_boite_info();
	echo espace_de_discussion();
	fin_boite_info();

/*
	debut_boite_info();
  foreach($GLOBALS['mercure'] as $key => $value){
    echo '['.$key.'] = ['.$value.']<br>';
  }
	fin_boite_info();

	debut_boite_info();
  foreach($_SESSION as $key => $value){
    echo '['.$key.'] = ['.$value.']<br>';
  }
	fin_boite_info();
*/

# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec

?>
