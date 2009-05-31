<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

// pointer la page courante dans bloc d'admin
### note sur pointe_page() : le premier elem. du array doit être la page à atteindre
function pointe_page($case_page, $fonction) {
	$pg_exec = _request('exec');
	// --> if() : cas de voir la fiche
	if($fonction) {
		echo "<br /><img border='0' src='"._DIR_IMG_PACK."fleche-right.png' />&nbsp;".
			"<a href='".generer_url_ecrire($case_page[0])."'>".$fonction."</a>";
	}

	if (in_array($pg_exec,$case_page)) {
		echo "&nbsp;<img border='0' src='"._DIR_IMG_PACK."fleche-left.png'>";
	}
	echo "<br />";
}

function debut_bloc_lien_page()
	{
	echo "<div style='margin-top:2px;' class='bouton36blanc' 
		onMouseOver=\"changeclass(this,'bouton36gris')\"
		onMouseOut=\"changeclass(this,'bouton36blanc')\">\n";
	}

function fin_bloc()
	{
	echo "</div>\n";
	}

// lien page TiSpiP-sKeLeT : Aide en ligne
function bloc_ico_aide_ligne()
	{
	debut_bloc_lien_page();
	echo "<a href=' http://www.etab.ac-caen.fr/bureaudestests/TiSpip/spip.php?article189' title='"._T('rmc:title_aide_01')."' target='_blank'>";
	echo "<img src='"._DIR_IMG_PACK."racine-24.gif' border='0' align='absmiddle'>&nbsp;";
	echo "<span class='verdana2'>"._T('rmc:aide')."</span>";
	echo "</a>";
	fin_bloc();
	}

// --
function debut_band_titre($coul, $police="", $arg="")
	{
	global $couleur_foncee;
	$color = ($coul == $couleur_foncee) ? "white" : "#000000";
	if(!$police) { $police="verdana2"; }
	echo "<div class='bande_titre ".$police." ".$arg."' style='background-color:".$coul."; color:".$color."'>\n";
	}
// --
function debut_boite_erreur($message)
	{
	echo "<div style='border:4px solid #c00; color:#c00; text-align:center; font-weight:bold;padding:10px;-moz-border-radius:10px'>";
	echo $message;
	echo "</div>";
	}

// --
function menu_admin() {
	debut_cadre_enfonce(_DIR_IMG_DW2."configure.gif");
		echo "<div class='verdana2' style='padding:4px;'><b>"._T('rmc:conf')."<br />";
			pointe_page(array("rec_mc"), _T('rmc:conf'));
			pointe_page(array("cfg&cfg=Recherche%20multicritere"), _T('rmc:conf_public'));
		echo "</b></div>";
	fin_cadre_enfonce();
}
?>
