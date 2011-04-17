<?php

function association_post_edition($flux){
	$id = $flux['args']['id_objet'];
	if ($id
	AND $flux['args']['table']=='spip_auteurs'
	AND !_ASSOCIATION_INSCRIPTION2) {
		$old_aut = sql_fetsel('*', 'spip_auteurs', "id_auteur=$id");
		if ($data['statut'] == '5poubelle') return $data;
		if (! ($nom = $data['nom'])) $nom = $old_aut['nom'];

		if ($nom) 
			list($nom, $prenom) = preg_split('/\s+/', $nom, 2);
		else {$nom = _T('asso:activite_entete_adherent'); $prenom = $id;}

		if (! ($bio = $data['bio'])) $bio = $old_aut['bio'];

		if (preg_match_all('/(.+)$/m', $bio, $r)
		AND preg_match('/^\s*(\d{5})\s+(.*)/', $r[0][4], $m))
		      $modif = array(
			'fonction' => trim($r[0][0]),
			'telephone' => telephone_std($r[0][1]),
			'mobile' => telephone_std($r[0][2]),
			'adresse' => trim($r[0][3]),
			'code_postal' => $m[1],
			'ville' => trim($m[2])
				     );
		else $modif = array();

		$modif['nom_famille'] = $nom;
		$modif['prenom'] = $prenom;
		$modif['email'] = $data['email'];


		if (sql_getfetsel('id_auteur', 'spip_asso_membres', "id_auteur=$id"))
		  sql_updateq('spip_asso_membres', $modif, "id_auteur=$id");
		else {
		  $modif['statut_interne'] = 'echu';
		  $modif['id_auteur'] = $id;
		  sql_replace('spip_asso_membres', $modif);
		}
	}
	return $data;
}

function telephone_std($num)
{
	$num = preg_replace('/\D/', '', $num);
	if ($num AND strlen($num) < 10) $num = '0'.$num;
	$num = preg_replace('/(\d\d)/', '\1 ', $num);
	return rtrim($num);
}

?>
