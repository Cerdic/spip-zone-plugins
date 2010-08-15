<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : inc/spipbb_init                               #
#  Authors : Hugues AROUX - SCOTY @ koakidi.com et als     #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#                                                          #
# qq fonctions de mise en forme, boutons ...               #
#                                                          #
#----------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);

include_spip("inc/filtres");

//
// diver bloc et boutons
//
function debut_bloc_couleur($coul_bloc) {
	echo "<div style=' background-color:$coul_bloc;  padding:1px 3px 2px 1px;
			border-top:1px solid #000000; -moz-border-radius:7px;'>\n";
}

function debut_bloc_gricont() {
	global $couleur_foncee;
	echo "<div style='padding:2px; background-color:#efefef;
			border:2px solid $couleur_foncee; -moz-border-radius:5px;
			text-align:center;'>\n";
}

function debut_ligne_foncee($retrait) {
	global $couleur_foncee;
	echo "<div style='background-color:".$couleur_foncee."; padding:3px;
			margin-left:".$retrait."px; -moz-border-radius:7px;
			color:#ffffff;' class='verdana3'>\n";
}

function debut_ligne_claire($retrait) {
	global $couleur_claire;
	echo "<div style='background-color:".$couleur_claire."; padding:3px;
			margin-left:".$retrait."px; -moz-border-radius:7px;' class='verdana3'>\n";
}

function debut_ligne_grise($retrait) {
	echo "<div style='background-color:#efefef; padding:3px;
			margin-left:".$retrait."px; -moz-border-radius:7px;' class='verdana3'>\n";
}


function fin_bloc() { echo "</div>\n"; }

function spipbb_svn_revision() {
	$version="";
	if ($svn_revision = version_svn_courante(_DIR_PLUGIN_SPIPBB)) {
		$version = ' ' . (($svn_revision < 0) ? '<!--SVN -->':'')
		. "[<a href='".str_replace('@rev_num@',abs($svn_revision),_URL_SPIPBB_SVN_CHANGESET)
		. "' onclick=\"window.open(this.href); return false;\">"
		. abs($svn_revision) . "</a>]";
	}
	return $version;
}

function signature_spipbb() {
	$aff = "<br />"
		. debut_boite_info(true)
		. _T('spipbb:sign_tempo')." ".$GLOBALS['spipbb_plug_version'].spipbb_svn_revision()
		. fin_boite_info(true);
	return $aff;
} // signature_spipbb

function signature_spipbb_admin() {
	if ( (!isset($GLOBALS['spipbb']['derniere_verif']) OR !isset($GLOBALS['spipbb']['version_distant'])) OR (time()-$GLOBALS['spipbb']['derniere_verif']>_URL_CHECK_DELAY)) {
		$GLOBALS['spipbb']['version_distant']= $version_distant = spipbb_version_distant();
		$GLOBALS['spipbb']['derniere_verif'] = time();
		spipbb_save_metas();
	} else {
		$version_distant = $GLOBALS['spipbb']['version_distant'];
	}

	// si on doit mettre a jour lien vers le zip et le numero de version, sinon message "ok"
	$maj = version_compare($version_distant,$GLOBALS['spipbb_plug_version'],">") ?
		"<a href='"._URL_SPIPBB_PLUGIN_ZIP."'>"._T('spipbb:sign_maj',array('version'=>$version_distant))."</a>"
		:
		_T('spipbb:sign_ok') ;

	$reinit="";
	if (spipbb_is_configured()) $reinit=propre(_T('spipbb:sign_reinit',array('plugin'=>generer_url_ecrire('spipbb_configuration','cmd=resetall'))));

	$aff = debut_boite_info(true) .
		propre(_T('spipbb:sign_admin', array(
		'version' => $GLOBALS['spipbb_plug_version'].spipbb_svn_revision(),
		'distant' => $maj,
		'reinit' => $reinit,
		) ) ).
		fin_boite_info(true);
	return $aff;
} // signature_spipbb_admin

// Donne la version en cours sur SVN (a partir de plugin.xml)
function spipbb_version_distant() {
	include_spip('inc/distant');
	$distant = recuperer_page(_URL_SPIPBB_PLUGIN_XML);
	$version='';
	if ($distant) $version = preg_match(',<version>[\s\n]*([0-9.]+)[\s\n]*</version>,ims', $distant, $regs)?$regs[1]:'';
	return $version;
} // spipbb_version_distant

//
// bouton retour haut de page
function bouton_retour_haut() {
	echo "<div style='float:right; margin-top:6px;' class='icone36' title='"._T('spipbb:haut_page')."'>\n";
	echo "<a href='#haut_page'>";
	echo "<img src='"._DIR_IMG_PACK."spip_out.gif' border='0' align='absmiddle' />\n";
	echo "</a></div>";
	echo "<div style='clear:both;'></div>\n";
}

//
// bouton popup ecrire post
function bouton_ecrire_post($id_article, $id_sujet, $id_citer="") {
	if ($id_sujet) {
		if($id_citer) {
			$icone="gaf_citer.png";
			$texte_icone=_T('spipbb:citer');
			$citer_sujet = "&citer=".$id_citer;
		} else {
			$icone="gaf_post.gif";
			$texte_icone=_T('spipbb:repondre');
		}
		$ico_sup = "edit.gif";
	}
	else
		{ $icone="gaf_sujet.gif"; $texte_icone=_T('spipbb:sujet_nouveau'); $ico_sup="creer.gif"; }


	$url = generer_url_ecrire("spipbb_formpost","forum=".$id_article."&sujet=".$id_sujet.$citer_sujet);

	echo "
		<div style='float:right; margin-left:3px;' title='$texte_icone' class='icone36' >\n
		<a href=\"".$url."\"
		target=\"redige_post\"
		onclick=\"javascript:window.open(this.href, 'redige_post',
		'width=650, height=550, menubar=no, scrollbars=yes'); return false;\"\n>
		<img src='"._DIR_IMG_PACK.$ico_sup."' align='absmiddle' border='0'
		style='background-image:url(\""._DIR_IMG_SPIPBB.$icone."\"); background-repeat:no-repeat;
			background-position:center;' width='24' height='24' />\n
		</a>
		</div>\n";
}


//
// Fixe l'icone du statut d'un post
function icone_statut_post($statut_post) {
	// icone état du post
	switch ($statut_post) {
		case"off":
		$aff_statut = "<div style='float:right;' title='"._T('spipbb:sujet_rejete')."'>
					<img src='"._DIR_IMG_SPIPBB."gaf_p_off.gif'></div>";
		break;
		case"prop":
		$aff_statut = "<div style='float:right;' title='"._T('spipbb:sujet_valide')."'>
					<img src='"._DIR_IMG_SPIPBB."gaf_p_prop.gif'></div>";
		break;
		case"publie":
		$aff_statut = "";
		break;
		default:
			return;
	}
	return $aff_statut;
}


//
// form bouton 'ferme' / 'libere'
// appel exec/spipbb_forum.php ;; mode == 'ferme' // 'maintenance', 'libere', 'libere_maintenance'
function formulaire_bouton_libereferme($id_forum, $mode, $src_img) {
	global $connect_id_auteur;
	echo "<div style='float:right; padding:3px;'>";
	echo "<form action='".generer_url_action("spipbb_action", "arg=fermelibere-".$id_forum)."' method='post'>";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("spipbb_forum", "id_article=".$id_forum)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("spipbb_action-fermelibere-".$id_forum)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "<input type='hidden' name='id_mot_ferme' value='".$GLOBALS['spipbb']['id_mot_ferme']."' />\n";
	echo "<input type='hidden' name='mode' value='".$mode."' />";
	echo "<input type='image' src='".$src_img."' title='"._T('spipbb:title_'.$mode)."'/>";
	echo "</form></div>";
}

//
// form bouton 'ferme' / 'libere'
function formulaire_bouton_ferlibsujet($id_sujet, $mode, $src_img) {
	global $connect_id_auteur;
	echo "<div style='float:right; padding:3px;'>";
	echo "<form action='".generer_url_action("spipbb_action", "arg=ferlibsujet-".$id_sujet)."' method='post'>";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("spipbb_action-ferlibsujet-".$id_sujet)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "<input type='hidden' name='id_mot_ferme' value='".$GLOBALS['spipbb']['id_mot_ferme']."' />\n";
	echo "<input type='hidden' name='mode' value='".$mode."' />";
	echo "<input type='image' src='".$src_img."' title='"._T('spipbb:title_sujet_'.$mode)."'/>";
	echo "</form></div>";
}

// appel exec/spipbb_sujet.php
// mode == annonce / desannonce
function bouton_formulaire_annonce($id_sujet, $mode, $src_img) {
	global $connect_id_auteur;
	echo "<div style='float:right; padding:3px;'>\n";
	echo "<form action='".generer_url_action("spipbb_action", "arg=sujetannonce-".$id_sujet)."' method='post'>\n";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("spipbb_action-sujetannonce-".$id_sujet)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "<input type='hidden' name='id_mot_annonce' value='".$GLOBALS['spipbb']['id_mot_annonce']."' />\n";
	echo "<input type='hidden' name='mode' value='".$mode."' />\n";
	echo "<input type='image' src='".$src_img."' title='"._T('spipbb:fil_annonce_'.$mode)."' />\n";
	echo "</form></div>\n";
}

// appel exec/spipbb_forum.php
// mode == annonce / desannonce
function bouton_formulaire_forum_annonce($id_article, $mode, $src_img) {
	global $connect_id_auteur;
	echo "<div style='float:right; padding:3px;'>\n";
	echo "<form action='".generer_url_action("spipbb_action", "arg=forumannonce-".$id_article)."' method='post'>\n";
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("spipbb_forum", "id_article=".$id_article)."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("spipbb_action-forumannonce-".$id_article)."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	echo "<input type='hidden' name='id_mot_annonce' value='".$GLOBALS['spipbb']['id_mot_annonce']."' />\n";
	echo "<input type='hidden' name='mode' value='".$mode."' />\n";
	echo "<input type='image' src='".$src_img."' title='"._T('spipbb:forum_annonce_'.$mode)."' />\n";
	echo "</form></div>\n";

}

# h. 28/11
# bouton monter/descendre .. : ordonner art/rub
#
function bouton_ordonne_forum($id_forum,$redirect) {
	return bouton_ordonne_objet('article',$id_forum,$redirect);
}
function bouton_ordonne_salon($id_rubrique,$redirect) {
	return bouton_ordonne_objet('rubrique',$id_rubrique,$redirect);
}
function bouton_ordonne_objet($objet,$id_objet,$redirect) {
	$aff="";

	$up = generer_action_auteur('spipbb_move',"$objet-$id_objet-up",$redirect);
	$down = generer_action_auteur('spipbb_move',"$objet-$id_objet-down",$redirect);
	$aff.= "<a href='$up'>"
			. http_img_pack('monter-16.png', 'up', '', _T('spipbb:admin_form_monter'))
			. "</a>\n"
			. "<a href='$down'>"
			. http_img_pack('descendre-16.png', 'down', '', _T('spipbb:admin_form_monter'))
			. "</a>\n";
	return $aff;
}

//
// url dernier post dans la bonne tranche
//
function url_post_tranche($id_post, $id_sujet, $compt_rang="") {
	$fixlimit = $GLOBALS['spipbb']['fixlimit'];

	if ($compt_rang) {
		$val_vl=$fixlimit*(ceil($compt_rang/$fixlimit)-1);
		$url_post = generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet."&vl=".$val_vl."#".$id_post);
	}
	elseif ($id_post==$id_sujet) {
		$url_post = generer_url_ecrire("spipbb_sujet","id_sujet=".$id_sujet);
	}
	else {
		$nbr_id=sql_count(sql_select("id_forum","spip_forum","id_thread=$id_sujet"));
		if ($nbr_id<=$fixlimit)
			{ $url_post = generer_url_ecrire("spipbb_sujet", "&id_sujet=".$id_sujet."#".$id_post); }
		else {
			$val_vl=$fixlimit*(floor($nbr_id/$fixlimit));
			$url_post = generer_url_ecrire("spipbb_sujet", "id_sujet=".$id_sujet."&vl=".$val_vl."#".$id_post);
		}
	}
	return $url_post;
}


#
# affiche icone verrou ou annonce
#
function bloc_info_etat($type_ferme, $obj='',$annonce='') {
	global $couleur_foncee;

	$aff='';
	if($obj=='sujet') {
		$leg_ferme = _T('spipbb:sujet_ferme');
		$leg_titre = _T('spipbb:info_annonce_ferme');
	}
	elseif($obj=='forum') {
		$leg_ferme = _T('spipbb:forum_ferme');
		$leg_titre = _T('spipbb:info_ferme');
	}

	if($obj) {
		$aff.="<div class='verdana2'>".$leg_titre."</div>\n";
	}

	if ($type_ferme=="ferme") {
		$aff.= "<span class='verdana3' style='color:$couleur_foncee; font-weight:bold;'>\n"
			. "<img src='"._DIR_IMG_SPIPBB."gaf_verrou1.gif' align='absmiddle' />"
			. $leg_ferme
			. "</span>\n";
	}

	if ($type_ferme=="maintenance") {
		$aff.= "<div class='verdana3' style='color:#B23232; font-weight:bold;'>\n"
			. "<img src='"._DIR_IMG_SPIPBB."gaf_verrou2.gif' align='absmiddle' />\n"
			. _T('spipbb:maintenance_ferme')
			. "</div>\n";
	}

	if($annonce) {
		if($obj) {
			$aff.="<span class='verdana3' style='color:$couleur_foncee; font-weight:bold;'>\n";
		}
		$aff.="<img src='"._DIR_IMG_SPIPBB."gaf_annonce.gif' align='absmiddle' title='"._T('spipbb:'.$obj.'_annonce')."' />";
		if($obj) {
			$aff.= _T('spipbb:'.$obj.'_annonce') . "</span>\n";
		}
	}
	echo $aff;
}




//
// bouton deplace thread
//
function bouton_deplace_sujet($id_forum, $id_sujet) {
	$id_auth = $GLOBALS['auteur_session']['id_auteur'];
	$file="spipbbart_$id_forum-$id_auth.lck";
	if(@file_exists(_DIR_SESSIONS.$file)
		AND (time()-@filemtime(_DIR_SESSIONS.$file) > $GLOBALS['spipbb']['lockmaint'] ))
		{
		echo "<div style='padding:3px;'>\n";
		echo "<form action='".generer_url_ecrire("spipbb_affect")."' method='post'>\n";
		echo "<input type='hidden' name='id_article' value='".$id_forum."' />\n";
		echo "<input type='hidden' name='id_sujet' value='".$id_sujet."' />\n";
		echo "<input type='image' src='"._DIR_IMG_SPIPBB."deplac_thread.gif' border='0' title='"._T('spipbb:fil_deplace')."' />\n";
		echo "</form></div>\n";
		}
}




//
// branche articles (spipbb_admin : 20 derniers posts)
//
function branche_rubriques($id) {
	$liste = array();
	$res = sql_select("id_rubrique","spip_rubriques","id_parent=$id");
	if(sql_count($res)) {
		while($row=sql_fetch($res)) {
			$liste[]=$row['id_rubrique'];
		}
		foreach($liste as $ids) {
			$list = branche_rubriques($ids); // array
			$liste = array_merge($liste, $list); // array verifies
		}
	}
	return $liste;
}

function branche_articles($id) {
	// lister rubriques
	$a = branche_rubriques($id);
	// ajout rub $id
	$a[]=$id;
	// lister articles
	$listart = array();
	foreach($a as $r) {
		$ra = sql_select("id_article","spip_articles","id_rubrique=$r");
		while($row=sql_fetch($ra)) {
			$listart[]=$row['id_article'];
		}
	}
	// prepa chaine
	$liste_art=join(', ',$listart);
	return $liste_art;
}


##
## h.24/11/07 .. invalider .. because appel de assembler.php
## par : interface_admin.php
##

# h.30/09/07 .. modif pour adapt. gafospip mise en forme dans tableau post
// tous les boutons de controle d'un forum
// nb : les forums prives (privrac ou prive), une fois effaces
// (privoff), ne sont pas revalidables ; le forum d'admin (privadm)
// n'est pas effacable
// http://doc.spip.org/@boutons_controle_forum
/*
function boutons_controle_forum($id_forum, $forum_stat, $forum_id_auteur=0, $ref, $forum_ip) {
	$controle = '';

	// selection du logo et des boutons correspondant a l'etat du forum
	switch ($forum_stat) {
		# forum sous un article dans l'espace prive
		case "prive":
			$logo = "forum-interne-24.gif";
			$valider = false;
			$valider_repondre = false;
			$suppression = 'privoff';
			break;
		# forum des administrateurs
		case "privadmin":
			$logo = "forum-admin-24.gif";
			$valider = false;
			$valider_repondre = false;
			$suppression = false;
			break;
		# forum de l'espace prive, supprime (non revalidable,
		# d'ailleurs on ne sait plus a quel type de forum il appartenait)
		case "privoff":
			$logo = "forum-interne-24.gif";
			$valider = false;
			$valider_repondre = false;
			$suppression = false;
			break;
		# forum general de l'espace prive
		case "privrac":
			$logo = "forum-interne-24.gif";
			$valider = false;
			$valider_repondre = false;
			$suppression = 'privoff';
			break;

		# forum publie sur le site public
		case "publie":
			#$logo = "forum-public-24.gif";
			$logo = _DIR_IMG_GAF."gaf_post.gif";
			$valider = false;
			$valider_repondre = false;
			$suppression = 'off';
			break;
		# forum supprime sur le site public
		case "off":
			#$logo = "forum-public-24.gif";
			$logo = _DIR_IMG_GAF."gaf_post.gif";
			$valider = 'publie';
			$valider_repondre = false;
			$suppression = false;
			#$controle = "<br /><span style='color: red; font-weight: bold;'>"._T('info_message_supprime')." $forum_ip</span>";
			$controle = "<div style='float:right; color: red; font-weight: bold;'>"._T('info_message_supprime')." $forum_ip</div>";
			if($forum_id_auteur)
				$controle .= " - <a href='" . generer_url_ecrire('auteur_infos', "id_auteur=$forum_id_auteur") .
				  "'>" ._T('lien_voir_auteur'). "</a>";
			break;
		# forum propose (a moderer) sur le site public
		case "prop":
			#$logo = "forum-public-24.gif";
			$logo = _DIR_IMG_GAF."gaf_post.gif";
			$valider = 'publie';
			$valider_repondre = true;
			$suppression = 'off';
			break;
		# forum original (reponse a un forum modifie) sur le site public
		case "original":
			$logo = "forum-public-24.gif";
			$original = true;
			break;
		default:
			return;
	}

	#$lien = str_replace('&amp;', '&', self()) . "#id$id_forum";
	$lien = str_replace('&amp;', '&', self()) . "#$id_forum";
	if ($suppression)
	  $controle .= icone(_T('icone_supprimer_message'), generer_action_auteur('instituer_forum',"$id_forum-$suppression", _DIR_RESTREINT_ABS . $lien),
			$logo,
			"supprimer.gif", 'right', 'non');

	if ($valider)
	  $controle .= icone(_T('icone_valider_message'), generer_action_auteur('instituer_forum',"$id_forum-$valider", _DIR_RESTREINT_ABS . $lien),
			$logo,
			"creer.gif", 'left', 'non');
			#"creer.gif");

	if ($valider_repondre) {
	  $dblret =  rawurlencode(_DIR_RESTREINT_ABS . $lien);
	  $controle .= icone(_T('icone_valider_message') . " &amp; " .   _T('lien_repondre_message'), generer_action_auteur('instituer_forum',"$id_forum-$valider", generer_url_public('forum', "$ref&id_forum=$id_forum&retour=$dblret", true)),
			     $logo,
			     "creer.gif", 'right', 'non');
	}

	// TODO: un bouton retablir l'original ?
	if ($original) {
		$controle .= "<div style='float:".$GLOBALS['spip_lang_right'].";color:green'>"
		."("
		._T('forum_info_original')
		.")</div>";
	}

	return $controle;
}
*/

// ------------------------------------------------------------------------------
// Fonction supprimee en SVN... a remplacer ?
// etait dans : inc/editer_article
// ------------------------------------------------------------------------------

if (version_compare($GLOBALS['spip_version_code'],_SPIPBB_REV_EDITER_ARTRUB,'>')) {
if (!function_exists('editer_article_rubrique')) {
function editer_article_rubrique($id_rubrique, $id_secteur, $config, $aider)
{
	$chercher_rubrique = charger_fonction('chercher_rubrique', 'inc');

	$opt = $chercher_rubrique($id_rubrique, 'article', $config['restreint']);

	$msg = _T('titre_cadre_interieur_rubrique') .
	  ((preg_match('/^<input[^>]*hidden[^<]*$/', $opt)) ? '' : $aider("artrub"));

	if ($id_rubrique == 0) $logo = "racine-site-24.gif";
	elseif ($id_secteur == $id_rubrique) $logo = "secteur-24.gif";
	else $logo = "rubrique-24.gif";

	return debut_cadre_couleur($logo, true, "", $msg) . $opt .fin_cadre_couleur(true);
} } } // editer_article_rubrique

?>
