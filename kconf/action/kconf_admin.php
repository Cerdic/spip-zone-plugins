<?php

// ACTION

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/kconf_utils');

function action_kconf_admin() {
	global $kconf;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list ($id_objet,$I,$O,$delete) = explode(',',$arg);
	$id_objet = intval($id_objet);
// 	spip_log("$arg ");
	//	LOGOS
	if (preg_match(',^([+-])logo-(.*)$,',$delete, $r)) {
		$clef = $r[2];
		$clef_logo = "kconf_$clef-$I-$id_objet-$O";
		include_spip("action/iconifier");
		if (list($val,$type,$cascade) = kconf_tester_heritage($I,$id_objet,$clef,$clef_logo,$O)) {
			if ($r[1] == '+') {
				spip_log("ajout du logo $clef_logo;");
				action_spip_image_ajouter_dist($clef_logo, _request('sousaction2'), _request('source'));
				$conf = kconf_chercher_logo($clef_logo);
				$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['valeur'] = $conf;
				$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['type'] = ($type=="protege") ? "prive" : $type;
				$changed = true;
			} else {
				$conf = kconf_chercher_logo($clef_logo);
				if (!$conf) $conf = kconf_chercher_logo("kconf_".$clef); // pour les vieux logos nommé comme la clef
				spip_log("efface du logo $conf");
				action_spip_image_effacer_dist($conf);
				unset($kconf['i'][$I][$O][$id_objet]['clefs'][$clef]);
				$changed = true;
			}
		}
	// ADMIN: AJOUT DE SQUELETTE
	} else if (preg_match(',^[+]kconf_admin.*$,',$delete, $r)) {
		$skels = _request('fichiers');
// 		spip_log(var_export($skels,true));
		$nom_objet = ($O=='o') ? $kconf['i'][$I]['objet'] : $kconf['i'][$I]['c_objet'];
		foreach ($skels as $skel) {
			$present = sql_countsel("spip_kconfs",array("fichier='$skel'", "id_objet=$id_objet", "objet='$nom_objet'"));
			if (_request("ok_$skel")=="oui") {
				$type = _request("type_$skel");
				if (!$present) {
					$r = sql_insertq('spip_kconfs' ,array('fichier'=>"$skel", 'id_objet'=>$id_objet, 'objet'=>$nom_objet, 'type'=>"$type", 'mtime'=>'NOW()'));
				} else {
					$r = sql_updateq("spip_kconfs",array('type'=>"$type"),array("fichier='$skel'", "id_objet=$id_objet", "objet='$nom_objet'"));
				}
// 				spip_log("enregistrons $skel sur $id_objet $nom_objet");
			} else {
				if ($present) {
					sql_delete('spip_kconfs',array("fichier='$skel'", "id_objet=$id_objet", "objet='$nom_objet'"));
					$changed = true;
// 					spip_log("effaçons $skel sur $id_objet $nom_objet");
				}
			}
		}
		if ($changed) {
			kconf_nettoyer_valeurs();
		}
	// ENREGISTREMENT DES VALEURS
	} else {
		if (!$O) $O = 'o';
		$pages = kconf_determiner_pages($I,$id_objet,$O);
		if (!empty($pages)) {
			foreach ($pages as $row) {
				$type = $row['type'];
				// serialize dans kconf_balise_admin l 29
				if (is_array($vals = unserialize($row['valeur']))) {
					foreach ($vals as $l) {
						list($clef,$widget,$valeurs) = $l;
// 						spip_log("POST: $id_objet,$I: $clef,$widget,$valeurs,$type,$O");
						if (($conf = _request($clef)) !== null) {
							if (kconf_tester_heritage($I,$id_objet,$clef,$conf,$O)) {
								$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['valeur'] = $conf;
								$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['type'] = ($type=="protege") ? "prive" : $type;
								$changed = true;
							} else {
// 								spip_log("la valeur $conf de $clef envoyé correspond à l'héritage, on enregistre pas");
							}
						} else {
							if ($widget=="checkbox" && _request("chk$clef")) {
								$conf = "non";
								if (kconf_tester_heritage($I,$id_objet,$clef,$conf,$O)) {
									$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['valeur'] = $conf;
									$kconf['i'][$I][$O][$id_objet]['clefs'][$clef]['type'] = ($type=="protege") ? "prive" : $type;
									$changed = true;
								}
							}
						}
						if ($delete==$clef) {
							unset($kconf['i'][$I][$O][$id_objet]['clefs'][$clef]);
							$changed = true;
						}
					}
				}
			}
		}
	}
	if ($changed) {
		kconf_enregistre($I,$id_objet,$O);
	}
	if(_request("iframe") == 'iframe') {
		$iframe_redirect = urldecode(_request('iframe_redirect'));
		$redirect = urldecode($iframe_redirect."&iframe=iframe");
	} else {
		$redirect = _request('redirect');
		$redirect = rawurldecode($redirect);
	}
	redirige_par_entete($redirect);
}

// si la valeur correspond à celle qui est hérité, ne pas enregistrer
function kconf_tester_heritage($I,$id_objet,$clef,$conf,$O) {
	list($val,$type,$cascade) = kconf_recevoir_valeur($I,$id_objet,$clef,$O);
	return !($val == $conf && is_int($cascade)) ? array($val,$type,$cascade) : false;	
}

function kconf_chercher_logo($clef) {
	global $formats_logos;
	foreach ($formats_logos as $format) { // il faut le chercher
		if (@file_exists($f = (_DIR_LOGOS . $clef . '.' . $format))) {
			return "$clef.$format";
			break;
		}
	}
	return '';
}
?>