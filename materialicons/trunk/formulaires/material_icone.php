<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_material_icone_charger_dist($objet,$id_objet,$style,$categorie,$icone,$svg){
	// autorisation : #ENV{editable} est evite car on veut toujours voir le formulaire meme apres validation
	$editable = true;
// 	if (!autoriser('modifier', $objet, $id_objet)) {
// 		$editable = false;
// 	}
	// chargement des valeurs du formulaire
	$valeurs = array(
		'objet' => $objet,
		'id_objet' => $id_objet,
		'style' => $style,
		'categorie' => $categorie,
		'icone' => $icone,
		'svg' => $svg,
		'supprimer' => '',
		"editable" => $editable,
	);
	return $valeurs;
}

function formulaires_material_icone_traiter_dist($objet,$id_objet,$style,$categorie,$icone,$svg){
	if (_request('supprimer')){
		// requête sql dans spip_materialicons_liens pour supprimer la ligne où #ID_OBJET = $id_objet et #OBJET = $objet
		sql_delete("spip_materialicons_liens", "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
		set_request('style','');
		set_request('categorie','');
		set_request('icone','');
	}
	else {
		$style = _request('style');
		$categorie = _request('categorie');
		$icone = _request('icone');
		$svg = _request('svg');
		$where =  "objet=".sql_quote($objet)." AND id_objet=".intval($id_objet);
		// si la ligne $id_objet / $objet existe dans la table spip_materialicons_liens alors on fait sql_updateq
		if (sql_countsel('spip_materialicons_liens', array(
			"objet=" . sql_quote($objet),
			"id_objet=" . sql_quote($id_objet)
		))) {
			sql_updateq('spip_materialicons_liens', array('style' => $style,'categorie' => $categorie,'icone' => $icone), $where);
		} else {
			// si la ligne $id_objet / $objet n'existe pas dans la table spip_materialicons_liens alors on fait sql_insertq
			sql_insertq('spip_materialicons_liens', array('id_objet' => $id_objet, 'objet' => $objet, 'style' => $style,'categorie' => $categorie, 'icone' => $icone));
		}
	}
}
