<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function numero_affiche_droite($flux){
	$out2 = "";
	$class = 'boutons_numero';
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['edition']===false
		AND include_spip("inc/numeroter")
		AND ($r = numero_info_objet($e['type']))!==false){
		$out2 = recuperer_fond("prive/squelettes/inclure/numeroter_objet",array('objet'=>$e['type'],'id_objet'=>$flux['args'][$e['id_table_objet']]));
		$class .= ' nomargin';
	}

	if (in_array($flux['args']['exec'], array('rubriques', 'rubrique'))
		AND autoriser('numeroter', 'rubrique', $flux['args']['id_rubrique'])){
		$out = "";
		$id_rubrique = $flux['args']['id_rubrique'];

		if (numero_compte_objets_enfants('rubrique',"id_parent",$id_rubrique))
			$out .= numero_affiche_boutons_objets_enfants($id_rubrique,"rubrique");

		// lister tous les types dispo, voir si ils ont un id_rubrique, et si il y en a dans cette rubrique
		$objets = lister_tables_objets_sql();
		foreach($objets as $table_sql => $desc){
			if (isset($desc['field']['id_rubrique'])
			  AND numero_compte_objets_enfants($desc['type'],"id_rubrique",$id_rubrique)){
				// verifier qu'il y en a bien dans la rubrique
				$out .= numero_affiche_boutons_objets_enfants($id_rubrique,$desc['type']);
			}
		}

		if ($out){
			$out = boite_ouvrir('', 'simple '.$class)
			  . $out
			  . boite_fermer();
			$flux['data'].= $out;
		}
	}
	if (in_array($flux['args']['exec'], array('groupe_mots'))
		AND autoriser('numeroter', 'groupe_mots', $flux['args']['id_groupe'])){
		$out = "";
		$id_groupe = $flux['args']['id_groupe'];

		if (numero_compte_objets_enfants('mot',"id_groupe",$id_groupe)){
			$out .= numero_affiche_boutons_objets_enfants($id_groupe,"mot");
		}

		if ($out){
			$out = boite_ouvrir('', 'simple '.$class)
			  . $out
			  . boite_fermer();
			$flux['data'].= $out;
		}
	}
	if (in_array($flux['args']['exec'], array('mots'))
		AND autoriser('numeroter', 'groupe_mots', 0)){
		$out = "";

		if (numero_compte_objets_enfants('groupemot',"",0)){
			$out .= numero_affiche_boutons_objets_enfants(0,"groupemot");
		}

		if ($out){
			$out = boite_ouvrir('', 'simple '.$class)
			  . $out
			  . boite_fermer();
			$flux['data'].= $out;
		}
	}
	$flux['data'].= $out2;

	return $flux;
}

function numero_compte_objets_enfants($type,$champ_parent,$id_parent){
	return sql_countsel(table_objet_sql($type),"$champ_parent=".intval($id_parent));
}

function numero_affiche_boutons_objets_enfants($id_parent,$type){

	$out = "";
	$out .= "<h4>";
	$texte_objets = _T(objet_info($type,"texte_objets"));
	$out .= "<span class='label'>".$texte_objets."</span>";
	$out .= "<span class='boutons'>";
	if ($type=="rubrique"){
		$alt = ($id_parent?_T("numero:info_denumeroter_rubriques"):_T("numero:info_denumeroter_secteurs"));
	}
	else {
		$alt = _T("numero:info_denumeroter_objets",array('objets'=>$texte_objets));
	}
	$out .= bouton_action(
		http_img_pack(find_in_theme("images/denumeroter-24.png"),$alt),
		generer_action_auteur('denumeroter', "$type-$id_parent", self('&')),
		"","",$alt
	);
	if ($type=="rubrique"){
		$alt = ($id_parent?_T("numero:info_numeroter_rubriques"):_T("numero:info_numeroter_secteurs"));
	}
	else {
		$alt = _T("numero:info_numeroter_objets",array('objets'=>$texte_objets));
	}
	$out .= bouton_action(
		http_img_pack(find_in_theme("images/numeroter-24.png"),$alt),
		generer_action_auteur('renumeroter', "$type-$id_parent", self('&')),
		"","",$alt
	);
	$out .= "</span>";
	$out .= "</h4>";

	return $out;
}
