<?php
/*
+--------------------------------------------+
| ICOP 1.0 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| lister/afficher les icones du repertoire
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');



function exec_icop_listing(){
// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// pack a afficher
$pack=_request('pack');
// tout voir (inclus fichier jpg !)
$tv=_request('tv');
// fond cellules icones
$coul = '#f9f9f9';

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('icop:toutes_icones'), "configuration", "icop_listing");


debut_gauche();

	echo "<div style='float:left; margin-right:5px; min-height:100px;'>"; 
	echo "<img src='"._DIR_PLUGIN_ICONESPRIVE."/img_pack/icop-48.png' alt='icop' />";
	echo "</div>";
	gros_titre(_T('icop:gros_titre_toutes_icones'));
	echo "<div class='nettoyeur'></div>";

	debut_boite_info();
		echo icone_horizontale(_T('icop:retour_selecteur'), generer_url_ecrire("icop_selecteur"), _DIR_PLUGIN_ICONESPRIVE.'/img_pack/'."icop_menu.png", '',false);
	if(!$tv) {
		echo icone_horizontale(_T('icop:tout_repertoire'), generer_url_ecrire("icop_listing","pack=".$pack."&tv=oui"), _DIR_PLUGIN_ICONESPRIVE.'/img_pack/'."icop_menu.png", '',false);
	} else {
		echo icone_horizontale(_T('icop:icones_interface'), generer_url_ecrire("icop_listing","pack=".$pack), _DIR_PLUGIN_ICONESPRIVE.'/img_pack/'."icop_menu.png", '',false);
	}
	fin_boite_info();
	echo "<br />";
	debut_boite_info();
		echo _T('icop:signature');
	fin_boite_info();


creer_colonne_droite();


debut_droite();

if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
}


	gros_titre($pack);
	echo "<br />";


	#
	# lister les icones
	#	
	if($pack=='spip') {
		$repert =_DIR_IMG_PACK;
	} else {
		$repert = _DIR_PLUGIN_ICONESPRIVE.'/packs/'.$pack."/";
	}
	$myDir = opendir($repert);

	if($tv) { $ereg="\.(png|gif|jpg)$"; }
	else { $ereg="\.(png|gif)$"; }
	
	$n=0;
	while($file = readdir($myDir)) {
		if (ereg($ereg, $file)) {
			$t=getimagesize($repert.$file);
			$files[$file]=$t[0];
			$n++;
		}
	}
	
	#
	# tableau
	#
	debut_cadre_relief("");
		echo _T('icop:nb_fichiers', array('n' => $n));
	fin_cadre_relief();
	
	debut_cadre_relief('');
	
	$i=0;
	foreach($files as $file => $larg) {
		$decoup = explode('.',$file);
		$nom= $decoup[0];
		$ext=$decoup[1];
		$i++;
		$width=23;

		if($larg>120 && $larg<200) {
			$i++; $width=47;
			if($i>4) { echo "<div style='clear:both; padding:2px;'></div>"; $i=2; }
		}
		elseif($larg>200) {
			echo "<div style='clear:both; padding:2px;'></div>";
			$i=4; $width=98;
		}
		echo "<div class='verdana1' style='float:left; background-color:$coul; width:".$width."%; min-height:80px; margin:1px; padding:2px; border:1px solid $couleur_foncee; -moz-border-radius:5px; text-align:center;'>".
				$nom = wordwrap($nom,20,' ',1).
				"<div style='padding:8px 2px 2px 2px;'><img src='".$repert.$file."' alt='$file' title='$file' /></div>".
				"<br />".$ext.
				"</div>\n";

		if($i>3) { echo "<div style='clear:both; padding:2px;'></div>"; $i=0; }
			
	}
	
	fin_cadre_relief();

	
echo fin_gauche(), fin_page();
}
?>
