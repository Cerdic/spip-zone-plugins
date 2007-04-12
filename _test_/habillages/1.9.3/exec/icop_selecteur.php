<?php
/*
+--------------------------------------------+
| ICOP 1.0 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| selecteur de pack icones et ajout couleur interface
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/meta');

function exec_icop_selecteur(){
// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee,
		$mes_couleurs;

#
# changer le pack icones
#
		if (($cp=_request('change_pack'))!==NULL ) {
			ecrire_meta('icop_img_pack',$cp);
			ecrire_metas();
		}

#
# Def.
#
	$check = "checked='checked'";

#
# affichage page
#

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('icop:selecteur_icones'), "configuration", "icop_selecteur");



debut_gauche();

	echo "<div style='float:left; margin-right:5px; min-height:100px;'>"; 
	echo "<img src='"._DIR_PLUGIN_ICONESPRIVE."/img_pack/icop-48.png' alt='icop' />";
	echo "</div>";
	gros_titre(_T('icop:gros_titre_selecteur'));
	echo "<div class='nettoyeur'></div>";
	
	debut_boite_info();
		echo _T('icop:def_page_selecteur');
	fin_boite_info();
	echo "<br />";
	debut_boite_info();
		echo _T('icop:signature');
	fin_boite_info();


creer_colonne_droite();

#
# lister couleur dispo
#

$meta_coul = array();
if($GLOBALS['meta']['icop_couleurs']!='') {
	$meta_coul = explode(',',$GLOBALS['meta']['icop_couleurs']);
}

	debut_cadre_trait_couleur("",false,"",_T('icop:ajout_couleur'));
	echo ;
	# h.20/03 .. echo '<form action="'.generer_url_action('icop_ecrirecouleur', "arg=rien").'" method="post">';
	echo '<form action="'.generer_url_action('icop_ecrirecouleur').'" method="post">';
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("icop_selecteur")."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("ecrirecouleur-rien")."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	
	echo "<table width='105' cellpadding='2' cellspacing='0' border='0' align='center'>";
	$i=0;
	foreach($mes_couleurs as $nc => $val) {
		$i++;
		$aff_check = (in_array($nc,$meta_coul))? $check: '';
		// au dela de 9 (icop) => couleurs perso : mes_couleurs.php
		if($i==10) {
			echo "<tr bgcolor='".$couleur_claire."'><td colspan='3'></td></tr>\n";
		}
		echo "<tr><td width='20'>".
			"<input type='checkbox' name='ajout_coul[]' value='$nc' ".$aff_check." /></td>".
			"<td>".http_img_pack("rien.gif",'',"width='35' height='15' style='background-color:".$val['couleur_foncee'].";'")."</td>".
			"<td>".http_img_pack("rien.gif",'',"width='35' height='15' style='background-color:".$val['couleur_claire'].";'")."</td>".
			"</tr>";
		
	}

	echo "</table>";
	echo "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>";
	echo "</form>";
	echo _T('icop:def_couleurs'); 
	
	fin_cadre_trait_couleur();
	
	debut_boite_info();
		echo _T('icop:def_couleurs');
	fin_boite_info();


debut_droite();

if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
}
#
# lister les repertoires d'icones
#
	$meta_pack = $GLOBALS['meta']['icop_img_pack'];
	$derrep = strtok($meta_pack,'/');
	while($derrep = strtok('/')) { $pack_actif=$derrep; }
	
	if($meta_pack=='' || $meta_pack==_DIR_IMG_PACK) {
		$meta_pack=_DIR_IMG_PACK;
		$pack_actif='Spip';
	}

	
	// on force spip en premier !
	$packs=array();
	$packs[]='spip';
	
	$d = dir(_DIR_PLUGIN_ICONESPRIVE.'/packs/');
    while (false !== ($entry = $d->read()) ) {
		if($entry!= "." && $entry != "..") {
			$packs[]=$entry;
		}
	}
	$d->close();

#
# affichage
#
	debut_cadre_relief('');
	gros_titre(_T('icop:pack_actif', array('pack' => majuscules($pack_actif))));
	fin_cadre_relief();
	
	echo '<form action="'.generer_url_ecrire('icop_selecteur').'" method="post">';
	echo "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>";
	
	foreach($packs as $pack) {
		
		if($pack=='spip') {
			$repert = _DIR_IMG_PACK;
			$nom_theme = 'Spip';
			#$value='spip';
		}
		else {
			$repert = _DIR_PLUGIN_ICONESPRIVE.'/packs/'.$pack."/";
			$theme = $repert."/theme.xml";
			lire_fichier($theme, $texte);
				$arbre = parse_plugin_xml($texte);
				$arbre = $arbre['theme'][0];
				$type_theme = trim(applatit_arbre($arbre['type']));
				$nom_theme = applatit_arbre($arbre['nom']);
				$auteur_theme = applatit_arbre($arbre['auteur']);
				$version_theme = applatit_arbre($arbre['version']);
				$description_theme = applatit_arbre($arbre['description']);
			#$value = $repert;
		}
		$pack_select = "<img src='".$repert."puce-verte.gif' />";
		

		
		debut_cadre_trait_couleur('rien.gif');
		#h.20/03 supprimer : , 'visibility', 'visible' .. du changestyle des td contenant icones
		echo "<table width='100%' border='0' cellpadding='2' cellspacing='0'>";
		echo "<tr>
		<td rowspan='3' width='4%' valign='top'>
			<input type='radio' name='change_pack' value='$repert' ".(($meta_pack==$repert)? $check : '')." />
		</td>
		<td colspan='7'>".
			(($meta_pack==$repert)? debut_cadre_couleur('',true) : debut_cadre_relief('', true)).
			(($meta_pack==$repert)? $pack_select."&nbsp;" : '')."<span class='verdana3'><b>".$nom_theme."</b></span>
			&nbsp;&middot;&middot;&nbsp;<span class='verdana2'>".$version_theme."</span>".
			(($meta_pack==$repert)? fin_cadre_couleur() : fin_cadre_relief('', true))."
		</td>
		<td>
			<div class='icone36'>
			<a href='".generer_url_ecrire('icop_listing','pack='.$pack)."' title='"._T('icop:voir_toutes_icones')."'>
			<img src='".$repert."cal-suivi.png' />
			</a>
			</div>
		</tr>
		<tr>
			<td colspan='8'>".propre($description_theme)."<br />".propre($auteur_theme)."</td>
		</tr>
		<tr>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."asuivre-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."documents-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."messagerie-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."redacteurs-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."statistiques-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."administration-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."aide-48.png' title='' alt='ico' /></a></div>
		</td>
		<td width='12%' class='cellule48' onmouseover='changestyle('bandeauaccueil');'>
			<div align='center'><a href='#'><img src='".$repert."visiter-48.png' title='' alt='ico' /></a></div>
		</td>
		</tr></table><br />";
	

		fin_cadre_trait_couleur();
		
	}
	echo "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>";
	echo "</form>";
	
	
echo fin_gauche(), fin_page();
}
?>
