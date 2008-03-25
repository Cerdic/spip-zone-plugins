<?php
/*
| diverses fonction communes : sur exec_ et/ou action_
*/

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

//----------------------------------------------------------------------------
// [fr] Verifie que spipbb est bien configure et a jour
// [en] Checks that spipbb is configured and uptodate
//----------------------------------------------------------------------------
function spipbb_is_configured() {
	# pas de spipbb
	if(!isset($GLOBALS['meta']['spipbb'])) return false;
	if(!isset($GLOBALS['spipbb'])) $GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
	# desactivation de spipbb
	if($GLOBALS['spipbb']['configure']=='non') return false;
	# prem. vers. spipbb chrys -> maj
	if(empty($GLOBALS['spipbb']['version'])) return false;
	# les metas suivant DOIVENT etre =='oui' pour le minimum de config
	if($GLOBALS['spipbb']['config_id_secteur']=='non'
		OR $GLOBALS['spipbb']['config_groupe_mots']=='non'
		OR $GLOBALS['spipbb']['config_mot_cles']=='non') return false;
	# nouvelle version -> maj
	if (!isset($GLOBALS['spipbb_plug_version']))
	{
		if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');
		$infos=plugin_get_infos(_DIR_PLUGIN_SPIPBB);
		$GLOBALS['spipbb_plug_version'] = $infos['version'];
	}
	if(version_compare(substr($GLOBALS['spipbb']['version'],0,5),$GLOBALS['spipbb_plug_version'],'<')) return false; // _SPIPBB version sur 0.4.5 == 5 char
	# sinon
	return true;
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
			'', array('titre') );
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
	$res=sql_query("SELECT * FROM spip_mots_articles WHERE id_mot=$id_mot_ferme AND id_article=$id_article");
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
	$res=spip_query("SELECT * FROM spip_mots_forum WHERE id_mot=$id_mot_ferme AND id_forum=$id_sujet");
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
	$req=spip_query("SELECT id_forum FROM spip_mots_forum
					WHERE id_mot=".$GLOBALS['spipbb']['id_mot_annonce']." AND id_forum=$id_sujet");
	$res=sql_count($req);
	if($res) { return true; }
}
#
# verif si forum de type "annonce" (lier a ce mot)
#
function verif_forum_annonce($id_article) {
	$req=spip_query("SELECT id_article FROM spip_mots_articles
					WHERE id_mot=".$GLOBALS['spipbb']['id_mot_annonce']." AND id_article=$id_article");
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


//
// gaf - verifier info plugin en cours
# au cas ou, ... car plus utiliser sur fonction signature_spipbb()
function verifier_infos_plugin($item) {
	if (!function_exists('plugin_get_infos')) include_spip('inc/plugin');

	$info_plugin = plugin_get_infos('spipbb');
	return $info_plugin[$item];
}

?>
