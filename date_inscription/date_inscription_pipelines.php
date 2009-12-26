<?php

/**
 * Enregistrer la date d'inscription dans la base au traitement du formulaire d'inscription
 * Enregistrer la date d'inscription dans la base au traitement du formulaire editer auteur si il y a creation
 *
 * @param array $flux
 * @return array
 */
function date_inscription_formulaire_traiter($flux){
	if ($flux['args']['form']=='inscription'){
		$mail = _request('mail_inscription');
		if (function_exists('test_inscription'))
			$f = 'test_inscription';
		else 	$f = 'test_inscription_dist';
		$desc = $f($mode, $mail, $flux['args']['args'][0], $flux['args']['args'][2]);
		if (is_array($desc)
		  AND $mail = $desc['email']){
			include_spip('base/abstract_sql');
			sql_updateq("spip_auteurs", array("date_inscription"=>"NOW()"),"statut='nouveau' AND email=" . sql_quote($mail));
		}
	}
	if ($flux['args']['form']=='editer_auteur'){
		if (!intval($flux['args']['args'][0])
			AND intval($flux['data']['id_auteur'])
		){
			$id_auteur = $flux['data']['id_auteur'];
			include_spip('base/abstract_sql');
			sql_updateq("spip_auteurs", array("date_inscription"=>"NOW()"),"id_auteur=$id_auteur");
		}
	}
	return $flux;
}

/**
 * Afficher la date d'inscription sur la fiche de l'auteur
 * @param array $flux 
 */
function date_inscription_afficher_contenu_objet($flux){
	if ($flux['args']['type']=='auteur'
		AND $id_auteur = $flux['args']['id_objet']
		AND $date_inscription = sql_getfetsel('date_inscription','spip_auteurs','id_auteur='.intval($id_auteur))
	){
		$date_inscription = ($date_inscription == '0000-00-00 00:00:00') ? _T('date_inscription:non_renseignee') : affdate($date_inscription);
		$flux['data'] .= "<div>" . propre(_T('date_inscription:date_inscription') . " : " . $date_inscription) ."</div>";
	}
	return $flux;
}

?>