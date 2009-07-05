<?php

include_spip('base/abstract_sql');
include_spip('inc/kconf_public_utils');

// cherche si un objet du contexte est applicable au kconf
function kconf_determiner_objets() {
	global $kconf;
	foreach ($kconf['i'] as $I => $v) {
		if ($exec = _request('exec')) {
			if ($exec=='kconf_admin') $exec = _request('script');
			if ($exec==$kconf['i'][$I]['exec']) {
				$id_objet=intval(_request("id_".$kconf['i'][$I]['objet']));
				if (!$id_objet) $id_objet=0;
				$objets[$I]['id_objet'] = $id_objet;
				$objets[$I]['O'] = 'o';
			} else if ($exec==$kconf['i'][$I]['exec_c']) {
				$id_objet=intval(_request("id_".$kconf['i'][$I]['conteneur']));
				if (!$id_objet) $id_objet=0;
				$objets[$I]['id_objet'] = $id_objet;
				$objets[$I]['O'] = 'c';
			}
		}
	}
	return $objets;
}

function kconf_hierarchie_pages($I,$id_objet,$O='o') {
	global $kconf;
	$k_c = $kconf['i'][$I]['c_objet'];
	if ($I && $id_objet>=0) {
		if ($O=='o')
			$q = "(objet='".$kconf['i'][$I]['objet']."' AND id_objet=".($id_objet?$id_objet:'0')." AND type IN ('prive','protege','public'))";
		else
			$q = "(objet='$k_c' AND id_objet=".($id_objet?$id_objet:'0')." AND type IN ('prive','protege','public'))";
		if ($hs = kconf_hierarchie($I,$id_objet,$O)) {
			// on ne redemande pas deux fois le même
			foreach ($hs as $v)
				if (!$kconf['i'][$I]['c'][$v]['pagesset'])
					$h[] = $v;
			if ($h) {
				$hin = join(',',$h);
				$q .=  "OR (objet='$k_c' AND (id_objet IN ($hin) AND type IN ('prive','protege','public')))";
			}
		}
		$req = sql_select('*','spip_kconfs',$q);
		while ($row = sql_fetch($req)) {
			$o = ($row['objet']!=$kconf['i'][$I]['objet']) ? 'c' : 'o';
			$kconf['i'][$I][$o][$row['id_objet']]['pages'][] = $row;
		}
		// on marque les choses déjà faites
		$kconf['i'][$I]['o'][$id_objet]['pagesset'] = true;
		if ($h)
			foreach ($h as $id)
				$kconf['i'][$I]['c'][$id]['pagesset'] = true;
	}
// 	spip_log("h_pages: ".var_export($kconf['i'][$I][$o][$row['id_objet']]['pages'],true));
}

function kconf_determiner_pages($I,$id_objet,$O='o') {
	global $kconf;
	$pages = array();
	if (!$kconf['i'][$I][$O][$id_objet]['pagesset']) {
		kconf_hierarchie_pages($I,$id_objet,$O);
	}
	$id = $id_objet;
	$i=0;
	$o = $O;
	while (is_int($id)) {
// 		spip_log("pages pour $id: ".var_export($kconf['i'][$I][$o][$id]['pages'],true));
		if (is_array($p = $kconf['i'][$I][$o][$id]['pages'])) {
			foreach ($p as $v) {
				$type = $v['type'];
				if ($i==0 || ($i==1 && $type=='protege') || $type=="public") {
					$v['I'] = $I;
					$v['O'] = $o;
					$pages[] = $v;
				}
			}
		}
		if ($id==0) break;
		$id = $kconf['i'][$I][$o][$id]['parent'];
		$i++;
		$o = 'c';
	}
// 	spip_log("pages pour $id_objet: ".var_export($pages,true));
	return $pages;
}

function kconf_determiner_valeurs($pages) {
	$valeurs = array();
	foreach ($pages as $p) {
		$vals = unserialize($p['valeur']);
		foreach ($vals as $v) {
			$valeurs[] = $v[0];
		}
	}
	return $valeurs;
}

function kconf_enregistre($I,$id_objet,$O='o') {
	global $kconf;

	$k_c = ($O=='o') ? $kconf['i'][$I]['objet'] : $kconf['i'][$I]['c_objet'];
	$o = ($O=='o') ? $kconf['i'][$I]['objet'] : $kconf['i'][$I]['conteneur'];
	$k = $kconf['i'][$I][$O][$id_objet]['clefs'];
	if (is_array($k) && !empty($k)) {
		$k = kconf_serialize($k);
// 		spip_log("enregistre \$kconf['i'][$I][$O][$id_objet]['clefs'] sur spip_kconf_${k_c}s: $k");
		if (!sql_countsel("spip_kconf_${k_c}s","id_$o=$id_objet"))
			$r = sql_insertq("spip_kconf_${k_c}s",array("id_".$o=>$id_objet,'valeur'=>$k));
		else
			$r = sql_updateq("spip_kconf_${k_c}s",array('valeur'=>$k),"id_$o=$id_objet");
	} else {
		$r = sql_delete("spip_kconf_${k_c}s","id_$o=$id_objet");
	}
	return $r;
}

function kconf_nettoyage() {
	global $kconf;
// 	spip_log("Nettoyer kconf début: ".date('r'));
	kconf_hierarchie('rubrique',0,'o');
	// cherche tous les fichiers
	$parametrer = charger_fonction('parametrer', 'public');
	$req = sql_select("fichier, mtime","spip_kconfs","","");
	$var_mode = $GLOBALS['var_mode'];
	$GLOBALS['var_mode'] = 'recalcul';
	include_spip('public/kconf_balise_admin');
	while ($row = sql_fetch($req)) {
		if (is_file(($fichier = _DIR_RACINE.$kconf['i']['rubrique']['o'][0]['clefs']['kconf_chemin']['valeur']."".$row['fichier'].".html")));
		else if (is_file(($fichier = _DIR_PLUGIN_KCONF.$row['fichier'].".html")));
		else $fichier = false;
		if ($fichier) {
			$mtime = filemtime($fichier);
			$Mtime = strtotime($row['mtime']);
// 			spip_log("$mtime et $Mtime sur $fichier correspondent");
			// si le fichier a été mis à jour
			if ($mtime!=$Mtime) {
				$changed = true;
				$srt_mtime = date("Y-m-d H:i:s",$mtime);
// 				spip_log($row['fichier'].",$Mtime: $fichier,$mtime Doit être mis à jour");
				$req2 = sql_select("*","spip_kconfs","fichier=".sql_quote($row['fichier']));
				// pour chacun des objets liés
				while ($row2 = sql_fetch($req2)) {
// 					spip_log("mise à jour de ".$row['fichier']." sur ".var_export($row2,true));
					// lance le squelette pour remettre les valeurs par defaut
					foreach (array_keys($kconf['i']) as $interface) {
						if ($row2['objet']==$kconf['i'][$interface]['objet']) {
							$I = $interface;
							$O = 'o';
							$objet = $kconf['i'][$interface]['objet'];
							break;
						} else if ($row2['objet']==$kconf['i'][$interface]['objet']."_".$kconf['i'][$interface]['conteneur']) {
							$I = $interface;
							$O = 'c';
							$objet = $kconf['i'][$interface]['conteneur'];
							break;
						}
					}
					// vide la description du fichier
					$kconf['fichiers'][$row['fichier']]['valeur'] = array();
					// calcul du fichier squelette pour bien nettoyer la base
					$skel = preg_replace("/.html$/","",$fichier);
					$t = time();
					$envs['date'] = date('Y-m-d H:i:s', $t);
					$envs["id_$objet"] = $row2["id_objet"];
					$envs['kconf']['page'] = array('fichier'=>$row['fichier'],'type'=>$row2['type'],"I"=>$I,"id_objet"=>$row2["id_objet"], 'O'=>$O);
					$envs['kconf']['contexte'] = array("I"=>$I,"id_objet"=>$row2["id_objet"], 'O'=>$O);
					$parametrer($skel, $envs, 'kconf');
					// mise à jour du fichier dans la table
					sql_updateq("spip_kconfs", array("valeur"=>serialize($kconf['fichiers'][$row['fichier']]['valeur']), "mtime" => $srt_mtime), array("fichier=".sql_quote($row['fichier']), "objet=".sql_quote($row2['objet']), "id_objet=".$row2['id_objet']) );
					kconf_enregistre($I,$row2["id_objet"],$O);
				}
			}
		} else {
			spip_log($row['fichier']." n'existe plus, on efface de la base");
			sql_delete("spip_kconfs","fichier=".sql_quote($row['fichier']));
			$changed = true;
		}
	}
	if ($changed) {
		kconf_nettoyer_valeurs();
	}
	$GLOBALS['var_mode'] = $var_mode;
// 	spip_log("Nettoyer kconf fin: ".date('r'));
}

// scanne tous les objets et enlève les vieilles définitions
function kconf_nettoyer_valeurs() {
	global $kconf;
	foreach (array_keys($kconf['i']) as $i) {
		foreach (array('o','c') as $o) {
			$nom_objet = ($O=='o') ? $kconf['i'][$i]['objet'] : $kconf['i'][$i]['c_objet'];
			$nom_id = ($O=='o') ? $kconf['i'][$i]['objet'] : $kconf['i'][$i]['conteneur'];
			$req = sql_select("id_${nom_id} as id, valeur","spip_kconf_${nom_objet}s");
			while ($row = sql_fetch($req)) {
				$changed = false;
				$id = intval($row['id']);
				$pages = kconf_determiner_pages($i,$id,$o);
				$valeurs = kconf_determiner_valeurs($pages);
	// 						spip_log("valeurs possible pour $i,$id,$O: ".var_export($valeurs,true));
				if (is_array($kconf['i'][$i][$o][$id]['clefs'])) {
					foreach(array_keys($kconf['i'][$i][$o][$id]['clefs']) as $clef) {
						if (in_array($clef,$valeurs)) {
		// 								spip_log("$clef fait bien partie des valeurs possible pour $i,$id,$O");
						} else {
		// 								spip_log("$clef ne fait pas partie des valeurs possible pour $i,$id,$O");
							unset($kconf['i'][$i][$o][$id]['clefs'][$clef]);
							$changed = true;
						}
					}
				}
				if ($changed) {
					kconf_enregistre($i,$id,$o);
// 					spip_log("nettoyage de $i,$id,$o: ".var_export($kconf['i'][$i][$o][$id]['clefs'],true));
				}
			}
			if ($kconf['i'][$i]['objet']==$kconf['i'][$i]['conteneur']) break;
		}
	}
}

?>