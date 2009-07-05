<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/kconf_utils');

function exec_kconf_admin_dist() {
global $kconf;

include_spip('inc/presentation');

// savoir sur quoi on travail
$objets = kconf_determiner_objets();
// spip_log("interface sur l'objet: ".var_export($objets,true));

kconf_nettoyage();

// sur quels squelettes ?
foreach ($objets as $I => $v) {
	$objets[$I]['pages'] = kconf_determiner_pages($I, $v['id_objet'], $v['O']);
// 	spip_log("pages sur l'objet $I: ".var_export($objets[$I]['pages'],true));
	if (!empty($objets[$I]['pages']) || $kconf['i']['rubrique']['o'][0]['clefs']['kconf_admin']['valeur']=='oui') $montre = true;
	else unset($objets[$I]);
}

// autorisations
include_spip('inc/autoriser');
list($I, $id_objet, $O) = interface_par_defaut($objets);
$autoriser = $O=='o' ? $kconf['i'][$I]['autoriser'] : $kconf['i'][$I]['autoriser_c'];
$nom_objet = $O=='o' ? $kconf['i'][$I]['objet'] : $kconf['i'][$I]['conteneur'];
if (!autoriser($autoriser, $nom_objet, $id_objet))
	return "";

if ($montre) {
	if (_request('kconf')!==null || _request('kobjet')!==null) {
		$formulaire = kconf_formulaire($objets);
	} else {
		$formulaire = kconf_formulaire_petit($objets);
	}
} else $formulaire="";

if (_request('iframe'))
 echo "<div class='upload_answer upload_document_added'>".$formulaire."</div>";
else if (_request('script'))
	return ajax_retour($formulaire);
else 
	return $formulaire;
}

function kconf_formulaire($objets) {
	global $kconf;
	// determiner l'objet
	list($I, $id_objet, $O) = interface_par_defaut($objets);
	$pages = $objets[$I]['pages'];
// 	spip_log("pages sur l'objet $I: ".var_export($objets[$I]['pages'],true));
	if ($O=='o') {
		$exec = $kconf['i'][$I]['exec'];
		$nom_objet = $kconf['i'][$I]['objet'];
	} else {
		$exec = $kconf['i'][$I]['exec_c'];
		$nom_objet = $kconf['i'][$I]['conteneur'];
	}

	// interface d'administration
	if ($kconf['i']['rubrique']['o'][0]['clefs']['kconf_admin']['valeur']=='oui')
		array_unshift($pages,array('fichier'=>'kconf_admin'));
		
	// sur quel page on travaille ?
	$page = _request('kconf');
	foreach ($pages as $p) {
		if ($page==$p['fichier']) {
			$page = $p;
			break;
		}
	}
	if (!$page) $page = $pages[0];

	// construire les onglets d'interface
	$onglets = "<ul>";
	foreach(array_keys($objets) as $interface) {
		$texte = _T("kca:onglet_$interface");
		if ($interface != $I) {
			//$lien_modal = "<a onclick=\"this.href='$url_modal'\" href='$url' class='modal'>$texte</a>";
			if ($objets[$interface]['O']=='o') {
				$lien = ajax_action_auteur('kconf_admin', $id_objet.",".$interface.",o", $kconf['i'][$interface]['exec'], "id_".$kconf['i'][$interface]['objet']."=".$id_objet."&kobjet=$interface", array($texte),'','');
			} else {
				$lien = ajax_action_auteur('kconf_admin', $id_objet.",".$interface.",c", $kconf['i'][$interface]['exec_c'], "id_".$kconf['i'][$interface]['conteneur']."=".$id_objet."&kobjet=$interface", array($texte),'','');
			}
			$onglets .= "\n<li class='tab'>$lien</li>";
		} else {
			$onglets .= "\n<li class='tab tabon'><div>$texte</div></li>";
		}
	}
	$onglets .= "</ul>\n";

	// construire les boutons (presentation.php 577)
// 	$boutons = "<div style='clear:both; text-align:center; width:459px; margin:0 auto; line-height:1.5em; '>";
	foreach($pages as $row) {
		$bouton = $row['fichier'];
		$url = generer_url_ecrire($kconf['i'][$I]['exec'],"id_$nom_objet=".$id_objet."&kconf=".$bouton);
		$url_modal = generer_url_ecrire('kconf_admin',"id_$nom_objet=".$id_objet."&kconf=".$bouton);
		$texte = (preg_match("/^kconf/",$bouton)) ? _T("kca:bouton_$bouton") : _T("kconf:bouton_$bouton");
		$boutons .= "\n<div class='kadr_config1_ong'>";
		if ($bouton != $page['fichier']) {
			//$lien_modal = "<a onclick=\"this.href='$url_modal'\" href='$url' class='modal'>$texte</a>";
			$lien = ajax_action_auteur('kconf_admin', $id_objet.",".$I.",".$O, $exec, "id_$nom_objet=".$id_objet."&kconf=$bouton&kobjet=$I", array($texte),'','');
			$boutons .= "\n<div class='konglet_off'>$lien</div>";
		} else {
			$boutons .= "\n<div class='konglet_on'>$texte</div>";
		}
		$boutons .= "</div>\n";
	}

	// calcul du squelette
	if ($page['fichier']!='kconf_admin') {
		include_spip('public/kconf_balise_admin');
		kconf_hierarchie('rubrique',0,'o'); // <>< devrait plus en avoir besoin
		if (is_file(($skel = _DIR_RACINE.$kconf['i']['rubrique']['o'][0]['clefs']['kconf_chemin']['valeur']."".$page['fichier']).".html"));
		else if (is_file(($skel = _DIR_PLUGIN_KCONF.$page['fichier']).".html"));
		else $skel = "";
// 		spip_log("Fichier squelette: $skel");
		
// 		$GLOBALS['var_mode']='recalcul';
		$t = time();
		$parametrer = charger_fonction('parametrer', 'public');
		$envs['date'] = date('Y-m-d H:i:s', $t);
		$envs['id_rubrique'] = $id_rubrique;
		$envs['kconf']['page'] = $page;
		$envs['kconf']['contexte'] = array("I"=>$I,"id_objet"=>$id_objet, 'O'=>$O);
		$skel = $parametrer($skel, $envs);
		$ret = $skel['texte'];
// 		print_r($skel);

	} else {
		$ret = kconf_squelette_admin($I, $id_objet, $O, $pages);
		$delete = "+kconf_admin";
	}

	// un peu de blabla en plus
	// 	$ret .= var_export($kconf,true);

	// Bouton du POST du kconf, sauf si il y a un logo
	if (!$kconf['fichiers'][$page['fichier']]['logo']) {
		$ret = ajax_action_auteur('kconf_admin',
				"$id_objet,$I,$O,$delete", // id_objet, objet
				$exec, // naviguer
				"id_$nom_objet=".$id_objet."&kconf=".$page['fichier']."&kobjet=$I", // id_rubrique=12 & kconf=squelette
				$ret . "<p><input type='submit' value='"._T('kca:change')."' id='bouton' />",'','');
	} else {
		$js .= http_script('',  'async_upload.js')
			. http_script('$("form.form_upload_icon").async_upload(async_upload_icon)');
	}

	// Lien GET pour faire joli
	$update = ajax_action_auteur('kconf_admin',
		"$id_objet,$I,$O",
		$exec,
		"id_$nom_objet=".$id_objet."&kconf=".$page['fichier']."&kobjet=$I",
		array("<img src='"._DIR_PLUGIN_KCONF."images/bullet_bleue.png' />",''),'','')
		."<br/>";
	$update = "<div class='bulletverte'>$update</div>";

	// Lien pour fermer la config
	$close = ajax_action_auteur('kconf_admin',
		"$id_objet,$I,$O",
		$exec,
		"id_$nom_objet=".$id_objet,
		array("<img src='"._DIR_PLUGIN_KCONF."images/dialog-close.png' />",''),'','')
		."<br/>";
	$close = "<div class='bulletclose'>$close</div>";

	// dans un joli cadre_enfoncé
	$ret = "<div style='position:absolute'>&nbsp;</div>" // place pour l'animation pendant Ajax
		."<div><img class='cadre-icone' src='"._DIR_PLUGIN_KCONF."images/kconf-24x24.png'/>"
		."<div class='onglets'>$update$close$onglets</div>"
		."<div class='config'><div class='kadr_config1'>$boutons<div style='clear:both'></div></div>"
		."<div class='kadr_config2'>$ret</div>"
		."</div></div>";

	return ajax_action_greffe("kconf_admin","$id_objet", $ret).$js;
}

// la boite de config fermée
function kconf_formulaire_petit($objets) {
	global $kconf;
	list($I, $id_objet, $O) = interface_par_defaut($objets);
	$nom_objet = $kconf['i'][$I]['objet'];

	// Lien d'ouverture
	$configurer = ajax_action_auteur('kconf_admin',
		"$id_objet,$I,$O",
		$kconf['i'][$I]['exec'],
		"id_$nom_objet=".$id_objet."&kconf",
		array("<span class='linkconfig'>"._T('kca:configurer')."</span>",''),'','');

	$ret = "<div style='position:absolute'>&nbsp;</div>" // place pour l'animation pendant Ajax
		."<div><img class='cadre-icone' src='"._DIR_PLUGIN_KCONF."images/kconf-24x24.png'/>"
		."<div class='onglets'>$configurer</div></div>";

	return ajax_action_greffe("kconf_admin","$id_objet", $ret);
}

// "squelette" de la page d'administration
function kconf_squelette_admin($I, $id_objet, $O='o', $pages) {
	global $kconf;
	$dir = $kconf['i']['rubrique']['o'][0]['clefs']['kconf_chemin']['valeur'];
	if (!is_dir("../$dir")) return _T('kca:probleme_dossier',array('dossier',$dir));
	
	$ret = "<h1>Dossier $dir</h1>";
	foreach($pages as $p) {
		$fichiers[$p['fichier']] = $p;
	}
	$skels = preg_files(_DIR_RACINE."$dir", '.html$');
	if (!$skels) return _T('kca:pas_de_squelette',array('dossier',$dir));

	$ret = '<table>';
	$ret .= "<tr><th>"._T("kca:nom_fichier")."</th><th>"._T("kca:actif")."</th><th>"._T("kca:scope")."</th></tr>";
	foreach ($skels as $skel) {
		$skel = preg_replace("/.html$/","",basename($skel));
		$checked = $disabled = "";
		if ($fichiers[$skel]) {
			$checked = "checked=checked";
			if ($fichiers[$skel]['id_objet']!=$id_objet)
				$disabled = "disabled";
		}
		$ret .= "<tr><td>$skel</td>";
		$ret .= "<input type='hidden' name='fichiers[]' value='$skel'>";
		$ret .= "<td><input type='checkbox' name='ok_$skel' value='oui' $checked $disabled></td>";
		$ret .= "<td><select name='type_$skel'>";
		foreach (array('public','protege','prive') as $t) {
			$selected = $fichiers[$skel]['type']==$t ? "selected=selected" : "";
			$ret .= "<option value='$t' $selected $disabled>"._T("kca:$t",array("objet"=>$kconf['i'][$I]['objet']))."</option>";
		}
		$ret .= "</select>";
		if ($disabled) {
			$ret .= "hérité de ".$fichiers[$skel]['I'].", ".$fichiers[$skel]['O'].", ".$fichiers[$skel]['id_objet'];
		}
		$ret .= "</td></tr>";
	}
	$ret .= "</table>";
// 	$ret .= "<div class='spip_xx-small'>"._T("kca:doc_scope",array("objet"=>$kconf['i'][$I]['objet']))."</div>";
	return $ret;
}

function interface_par_defaut($objets) {
	if (!($objet = _request('kobjet')) && !$objets[$objet]) {
		foreach ($objets as $I => $v) {
			if ($v['O'] == 'o') {
				$objet = $I;
				break;
			}
		}
	}
	return array($objet, $objets[$objet]['id_objet'], $objets[$objet]['O']);
}

?>