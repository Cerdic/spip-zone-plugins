<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Liste des repertoires du site sur le serveur
| Volume de cette espace disque (Mo)
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function decompose_array($val) {
	global $couleur_claire, $couleur_foncee;
	$ifond=0;
	echo "<table cellpadding='2' cellspacing='0' border='0' width='100%'>";
		foreach($val as $k => $v) {
			$ifond = $ifond ^ 1;
			$couleur = ($ifond) ? '#FFFFFF' : $couleur_claire;
			echo "<tr bgcolor='$couleur'>";
			if(is_array($v)) {
				$nb=count($v);
			}
			echo "<td width='35%' valign='top' style='color:$couleur_foncee;'><b>$k</b></td><td width='65%'>";
			if(is_array($v)) {
				decompose_array($v);
			}
			else 
				echo wordwrap($v,40,' ',1)."</td></tr>";
		}

	echo "</table>";
}


function exec_tabbord_metas() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee,
		$table_prefix;

//
// requis
//
include_spip('inc/tabbord_pres');
include_spip('inc/func_tabbord');


//
// prepa
//

$tbl_metas=array();
$q=spip_query("SELECT * , DATE_FORMAT(maj,'%d/%m/%Y %H:%i') as dermaj FROM spip_meta ORDER BY nom");
while($r=spip_fetch_array($q)) {
	$tbl_metas[$r['nom']]=array($r['valeur'],$r['dermaj']);
}
$nb_metas=count($tbl_metas);


//
// affichage
//

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('tabbord:titre_plugin'), "suivi", "tabbord_gen", '');
	echo "<br />";



// Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques)
	{
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
	}

debut_gauche();

menu_gen_tabbord();


debut_droite();

	debut_cadre_formulaire();
		gros_titre(_T('tabbord:table_metas'));
		echo _T('tabbord:enreg_table_metas', array("nb_metas" => $nb_metas, "prefix" => $table_prefix));
		echo "<br /><br />";
		echo "<table cellpadding='2' cellspacing='0' border='0'  class='tabbord'>";
		foreach($tbl_metas as $k => $v) {
			if($tbl=unserialize($v[0])) {
				echo "<tr class='liste'><td class='center' colspan='2' title='".$v[1]."'><b>".$k."</b></td></tr>";
				echo "<tr><td colspan='2'>";
				decompose_array($tbl);
				echo "&nbsp;</td></tr>";
			}
			else {
				if(ereg('[\,]',$v[0])) 
					$rv=ereg_replace(',',', ',$v[0]);
				else $rv = wordwrap($v[0],40,' ',1);
				
				echo "<tr class='liste'><td title='".$v[1]."'><b>".$k."</b></td><td>".$rv."</td></tr>";
			}
		}
		echo "</table>";

	fin_cadre_formulaire();



//
//
echo fin_gauche(), fin_page();
}
?>
