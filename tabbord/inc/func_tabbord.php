<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifiÈ KOAK2.0 strict, mais si !
+--------------------------------------------+
| functions volume disque et BDD
| d'apres originales : Bernard Martin-Rabaud
| http://www.spip-contrib.net/Tableau-de-bord-SPIP
+--------------------------------------------+
*/

 // recherche le fichier [spip_cache.php3 (ou spip_cache.php)]
 // h.01/04 .. spip 1.9.1 ==> spip.php
 // pour determiner le chemin de la racine
function cherche_racine_spip() {
       global $base_dir;
       $fichier = "spip.php";
       $rel = "../";
       for ($i=0;$i<5;$i++) {
          if (file_exists($fichier)) break;
          else {
		     $fichier = ($i ? "" : "./") . $rel . $fichier;
			 $base_dir .= "/..";
          }
       }
       if ($i < 5) return true;
       else return false;
}


//
// affiche l'espace disque
//
function espace_disque() {
	global $taille_disque, $ch_texte, $base_dir;
	dirsizeof($base_dir);
	gros_titre(_T('tabbord:espace_disque_var',array('taille'=>$taille_disque)));
	echo "<br />";
	echo "<table cellspacing='0' cellpadding='2' border='0' class='tabbord' width='100%'>";
	echo "<tr><th>"._T('tabbord:repertoire')."</th><th width='20%' align='center'>Taille</th></tr>";
	echo $ch_texte;
	echo "<tr class='comment'><td colspan='2'>"._T('tabbord:info_mesure_espace_disque')."</td></tr>";
	echo "</table>";
	#echo "base_dir : ".$base_dir;
}


//
// taille du site SPIP
//
function dirsizeof($dir) {
    global $taille_disque, $ch_texte, $base_dir; 
    $cluster = 6144;
	$taille_dir = 512;
	$myDir = opendir($dir);
	$entryName = readdir($myDir);
	while( "-".$entryName != "-" ) {
		if (!is_dir("$dir/$entryName")) {
			$size += max(filesize("$dir/$entryName"), $cluster);
		}
		else if (is_dir("$dir/$entryName") AND !(ereg("^(\.\.?|CVS)$",$entryName))) {
			$size += dirsizeof("$dir/$entryName") + $taille_dir;
		}
		$entryName = @readdir($myDir);
	}
	$nom_dir = substr($dir, strlen($base_dir)+1);
	if ($dir == $base_dir) {
	  $taille_disque = taille_en_octets($size);
	  $ch_texte .= ligne_texte_valeur(_T('tabbord:total'), taille_en_octets($size), "entete");
	}
	else if (!ereg("\/",$nom_dir)) 
	  $ch_texte .= ligne_texte_valeur($nom_dir, taille_en_octets($size), "stotal") . ligne_vide();
	else $ch_texte .= ligne_texte_valeur($nom_dir, taille_en_octets($size), "liste");
	@closedir($myDir);
	return ($size);
}


//
// taille d'une base
//
function taille_base() {

## h.12/06 .. reperer les tables de plugins chargees par mes_options
$tbl_plug=array();
foreach($GLOBALS['tables_principales'] as $tp => $v ) {
	$tbl_plug[]=$tp;
}
foreach($GLOBALS['tables_auxiliaires'] as $tp => $v ) {
	$tbl_plug[]=$tp;
}
##

	include_spip('base/serial');
	include_spip('base/auxiliaires');
	/**/
	global $tables_principales;
	global $tables_auxiliaires;


	$tbl_spip=array();
	
	foreach($tables_principales as $k => $v) {
		if(!in_array($k,$tbl_plug)) { $tbl_spip[]=$k; }
	}
	foreach($tables_auxiliaires as $k => $v) {
		if(!in_array($k,$tbl_plug)) { $tbl_spip[]=$k; }
	}

	$tbl_spip[]="spip_test";
 
	$nom_base = $GLOBALS['spip_mysql_db'];
	$requete = "SHOW TABLE STATUS FROM $nom_base";
	$ressource = spip_query($requete);
	$total = 0;
	$total_plug = 0;
	
	$ch_texte = "<tr><th>"._T('tabbord:table')."</th>".
				"<th width='20%' align='center'>"._T('tabbord:enregistrement_s')."</th>".
				"<th width='20%' align='right'>"._T('tabbord:taille')."</th></tr>";
				
	while ($resultat = spip_fetch_array($ressource)) {
		$taille = $resultat['Data_length']+$resultat['Index_length'];
		$lignes = $resultat['Rows'];
		if(!$lignes) { $lignes = '-'; }
		$ch_texte .= ligne_texte_valeur($resultat['Name'], taille_en_octets($taille), "liste", $lignes, $tbl_spip);
		$total += $taille;
		if(!in_array($resultat['Name'],$tbl_spip)) { $total_plug+=$taille; }
	}
	$ch_texte .= "</table>";
	gros_titre(_T('tabbord:taille_base_donnees',array('taille'=>taille_en_octets($total))))."<br />";
	echo _T('tabbord:taille_tables_plug', array('taille_plug'=>taille_en_octets($total_plug)))."<br />";
	echo "<table cellspacing='0' cellpadding='2' border='0' class='tabbord' width='100%'>";
	echo $ch_texte;
}

//
// affiche une ligne de tableau avec un texte et une valeur, ainsi que la classe CSS
//
function ligne_texte_valeur($texte, $valeur, $css, $lignes='', $table='') {
	global $couleur_claire;
	if(is_array($table)) {
		if(!in_array($texte,$table)) { $aff="style='background-color:$couleur_claire;'"; }
	}
	$retour = "<tr class='$css' $aff ><td>$texte</td>";
	if($lignes) {
		$retour.="<td class='center'><i>$lignes</i></td>";
	}
	$retour .= "<td class='right'>$valeur</td></tr>";
	
	return $retour;
}

//
// affiche une ligne de tableau vide (sur 2 colonnes fusionnées)
//
function ligne_vide() {
  return "<tr><td colspan='2'>&nbsp;</td></tr>";
}

?>
