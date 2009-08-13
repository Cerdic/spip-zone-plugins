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

function _PRIVATE_clearDir($dossier) {
	$ouverture=@opendir($dossier);
	if (!$ouverture) return;
	while($fichier=readdir($ouverture)) {
		if ($fichier == '.' || $fichier == '..') continue;
			if (is_dir($dossier."/".$fichier)) {
				$r= _PRIVATE_clearDir($dossier."/".$fichier);
				if (!$r) return false;
			}
			else {
				$r=@unlink($dossier."/".$fichier);
				if (!$r) return false;
			}
	}
  closedir($ouverture);
  $r=@rmdir($dossier);
  if (!$r) return false;
	  return true;
}

function exec_mercure_remove_delete_mercure() {
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
echo $commencer_page(_T('mercure:mercure_titre'), "suivi", "mercure_pg");
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

	debut_boite_info();
	$q=spip_query("SELECT DATE_FORMAT(NOW(),'%d/%m/%Y %H:%i') as date_serveur");
	$r=spip_fetch_array($q);
	$datetime_sql=$r['date_serveur'];

	$aff.= "<div style='float:left; margin-right:5px; min-height:55px;'>" 
		. "<img src='"._DIR_IMG_MERCURE."mercure_48.png' alt='mercure' />"
		. "</div>";

	$aff.= gros_titre(_T('mercure:mercure_titre'),'',false);

	$aff.= "<div style='clear:both;'></div>"
		. "<div class='cell_info verdana2'>"
		. "<img src='"._DIR_IMG_MERCURE."icon_php.png' align='absmiddle' title='"._T('mercure:date_serveur_php')."' />\n"
		. date('d/m/Y H:i')."<br />"
		. "<img src='"._DIR_IMG_MERCURE."icon_mysql.png' align='absmiddle' title='"._T('mercure:date_serveur_mysql')."' />\n"
		. $datetime_sql
		. "</div>"
		. "<p class='space_10'></p>";
	
  echo $aff;
	fin_boite_info();

/*---------------------------------------------------------------------------*\
Elements divers
\*---------------------------------------------------------------------------*/

	echo "<p class='space_10'></p>";
	debut_boite_info();
	echo _T('mercure:mercure_avertissement');
	fin_boite_info();

creer_colonne_droite();
echo "<p class='space_20'></p>";

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

/*---------------------------------------------------------------------------*\
Patrick signe son mefait... si si !
\*---------------------------------------------------------------------------*/

echo "<p class='space_20'></p>";

	echo signature_plugin();




debut_droite();

	echo onglets_mercure(_request('exec'));

	debut_boite_info();
  echo _T('mercure:procedure_remove_action_begin').'<br /><br />';
	fin_boite_info();

	echo '<p class="space_20"></p>';
  
	debut_boite_info();
  # destruction de /tmp/mercure
  echo _T('mercure:procedure_remove_action_delete_tmp_mercure_begin'); 
  _PRIVATE_clearDir(_DIR_LOCAL_MERCURE);  
  echo _T('mercure:procedure_remove_action_delete_tmp_mercure_end').'<br />'; 

  # destruction de /plugins/mercure
  echo _T('mercure:procedure_remove_action_delete_plugin_mercure_begin'); 
  if(_PRIVATE_clearDir(_DIR_REMOVE_MERCURE)){ // tout est OK
    echo _T('mercure:procedure_remove_action_delete_plugin_mercure_end').'<br />';   
  }else{ // Quelque chose n'a pas pu être effacé !!!
    echo _T('mercure:procedure_remove_action_delete_plugin_mercure_problem').'<br />';     
  }	
  # destruction des méta-données
  echo _T('mercure:procedure_remove_action_delete_meta_mercure_begin'); 
	effacer_meta('mercure');
  echo _T('mercure:procedure_remove_action_delete_meta_mercure_end').'<br />'; 
	fin_boite_info();

	echo '<p class="space_20"></p>';

	debut_boite_info();
  echo _T('mercure:procedure_remove_action_end').'<br /><br />';
	fin_boite_info();

# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec

?>
