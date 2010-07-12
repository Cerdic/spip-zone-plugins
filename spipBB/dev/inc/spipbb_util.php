<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb_init                               #
#  Authors : Scoty, 2007 et als                            #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#                                                          #
# diverses fonction communes : sur exec_ et/ou action_     #
#                                                          #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/plugin'); // pour version du plugin
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

//----------------------------------------------------------------------------
// [fr] Verifie que spipbb est bien configure et a jour
// [en] Checks that spipbb is configured and uptodate
//----------------------------------------------------------------------------
function spipbb_is_configured() {
	$etat = true;
	// La, on dit direct que c'est pas bon
	if (lire_config('spipbb/activer_spipbb', '') != 'on')
		$etat = false;
	
	// Verifier si le secteur est defini
	if (!(lire_config('spipbb/secteur_spipbb', '') > 0))
		$etat = false;
		
	// Verifier si le groupe est defini
	if (!(lire_config('spipbb/groupe_spipbb', '') > 0))
		$etat = false;
	
	// Verifier si ferme est defini
	if (!(lire_config('spipbb/mot_ferme', '') > 0))
		$etat = false;
		
	// Verifier si annonce est defini
	if (!(lire_config('spipbb/mot_annonce', '') > 0))
		$etat = false;
		
	// Verifier si postit est defini
	if (!(lire_config('spipbb/mot_postit', '') > 0))
		$etat = false;
spip_log($etat, 'spipbb_test');
	return $etat;
} // spipbb_is_configured


// ------------------------------------------------------------------------------
// [fr] Verifie que les rubriques et les articles sont bien numerotes et les
// [fr] renumerote si besoin
// ------------------------------------------------------------------------------
## h. 28/11 function utile sur exec/spipbb_admin .. spipbb_forum, action/ spipbb_move
function spipbb_renumerote()
{
	$id_secteur = $GLOBALS['spipbb']['id_secteur'];
	// les rubriques

	$result = sql_select("id_rubrique, titre", "spip_rubriques", array(
			"id_secteur='".$id_secteur."'",
			"id_rubrique!='".$id_secteur."'" ),	// array where
			'', 
			'titre');
	$numero = 10;
	while ( $row = sql_fetch($result) )
	{
		$titre = supprimer_numero($row['titre']);
		$id_rubrique = $row['id_rubrique'];
		$titre = $numero . ". ".trim($titre);
		@sql_updateq('spip_rubriques', array(
						'titre'=>$titre
						),
				"id_rubrique='$id_rubrique'");
		$numero = $numero + 10;
	} // while

	// les articles

	$result = sql_select("A.id_article , A.titre", array("spip_articles AS A", "spip_rubriques AS R"),
			array("A.id_rubrique=R.id_rubrique","A.id_secteur='".$id_secteur."'"),
			'', array("R.titre", "A.titre") );
	$numero = 10;
	while ( $row = sql_fetch($result) )
	{
		$titre = supprimer_numero($row['titre']);
		$id_article = $row['id_article'];
		$titre = $numero . ". ".trim($titre);
		@sql_updateq('spip_articles', array(
						'titre'=>$titre
						),
				"id_article='$id_article'");
		$numero = $numero + 10;
	} // while
} // spipbb_renumerote


// ------------------------------------------------------------------------------
// [fr] Formatte une sortie de print_r
// [en] Html-ize print_r output
// ------------------------------------------------------------------------------
function print_r_html($var,$return_data=false)
{
    $data = print_r($var,true);
    $data = str_replace( "  ","&nbsp;&nbsp;", $data);
    $data = str_replace( "\r\n","<br />\r\n", $data);
    $data = str_replace( "\r","<br />\r", $data);
    $data = str_replace( "\n","<br />\n", $data);

    if (!$return_data)
        echo $data;
    else
        return $data;
}

# alternative tableau  a la precedente function
function affiche_metas_spipbb($var) {
	global $couleur_claire;
	$aff="<table width='100%' cellpadding='3' cellspacing='0' border='0'>\n";
	$i=1;
	foreach($var as $k => $v) {
		$bg = ($i==2)?'':$couleur_claire;
		$i==2?$i--:$i++;
		$aff.="<tr bgcolor='$bg' align='top' class='verdana2'>"
			. "<td width='45%' style='font-weight:bold;'>".$k."</td>"
			. "<td width='55%'>".$v."</td>"
			. "</tr>\n";
	}
	$aff.="</table>\n";
	return $aff;
}


//
// Article est-il ferme, ferme-maintenance ?
//
function verif_article_ferme($id_article=0,$id_mot_ferme=0) {
	if (empty($id_article) or empty($id_mot)) return;
	$rf="";
	$res=sql_select("*","spip_mots_articles","id_mot=$id_mot_ferme AND id_article=$id_article");
	if ($row=sql_count($res)) {
		$rf ="ferme";
		if ($ds = @opendir(_DIR_SESSIONS)) {
			while (($file = @readdir($ds)) !== false) {
				if (preg_match('/^spipbbart_([0-9]+)-([0-9]+)\.lck$/', $file, $match)) {
					if($match[1] == $id_article)
						{ $rf ="maintenance"; }
				}
			}
		}
	}
	return $rf;
}


//
// sujet est-il ferme ?
//
function verif_sujet_ferme($id_sujet,$id_mot_ferme) {
	$res=sql_select("*","spip_mots_forum","id_mot=$id_mot_ferme AND id_forum=$id_sujet");
	if ($row=sql_count($res)) { $rf ="ferme"; }
	return $rf;
}


//
// autorité GAF pour un déplacement
//
function auth_deplace_connecte() {
	$id_auth = $GLOBALS['auteur_session']['id_auteur'];
	if ($dh = @opendir(_DIR_SESSIONS))
		while (($file = @readdir($dh)) !== false)
				if (preg_match('/^spipbbart_([0-9]+)-([0-9]+)\.lck$/', $file, $match))
					if ($match[2] == $id_auth)
						return true;
}


//
// verif si sujet de type "annonce" (lier a ce mot)
//
function verif_sujet_annonce($id_sujet) {
	$req=sql_select("id_forum","spip_mots_forum","id_mot=".$GLOBALS['spipbb']['id_mot_annonce']." AND id_forum=$id_sujet");
	$res=sql_count($req);
	if($res) { return true; }
}
#
# verif si forum de type "annonce" (lier a ce mot)
#
function verif_forum_annonce($id_article) {
	$req=sql_select("id_article","spip_mots_articles","id_mot=".$GLOBALS['spipbb']['id_mot_annonce']." AND id_article=$id_article");
	if(sql_count($req)) {
		return true;
	}
}

//
// calcul/affiche : tranches ... pagination
//
function tranches_liste_forum($encours, $retour_gaf, $nligne) {
	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	$fract=ceil($nligne/$fixlimit);
	for ($i=0; $i<$fract; $i++) {
		$debaff=($i*$fixlimit)+1;
		$f_aff=($i*$fixlimit)+$fixlimit;
		$liais=$i*$fixlimit;
		if ($f_aff<$nligne) { $finaff=$f_aff; $sep = " | "; }
		else { $finaff=$nligne; $sep = ""; }
		if ($debaff==$encours) {
			echo "<b>$debaff - $finaff</b> $sep\n";
		}
		else {
			echo "<a href='".$retour_gaf."&vl=$liais'>$debaff - $finaff</a> $sep\n";
		}
	}
}

?>
