<?php

// Ajoute le bouton d'amin aux webmestres

if (!defined("_ECRIRE_INC_VERSION")) return;
function inscription2_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		$boutons_admin['auteurs']->sousmenu['inscription2_adherents']= new Bouton(
		"../"._DIR_PLUGIN_INSCRIPTION2."images/inscription2_icone.png", // icone
		_T("inscription2:adherents") //titre
		);
	}
	return $boutons_admin;
}

function inscription2_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {	
		$legender_auteur_supp = charger_fonction('legender_auteur_supp','exec');
		$id_auteur = $flux['args']['id_auteur'];
		$flux['data'] .= $legender_auteur_supp($id_auteur);
	}
	return $flux;
}

// ajouter les champs I2 sur le formulaire CVT editer_auteur
function inscription2_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		spip_log('INSCRIPTION 2 : inscription2_editer_contenu_objet','inscription2');
		include_spip('public/assembler');
		include_spip('inc/legender_auteur_supp');
		// ici on verifies que l'entree dans spip_auteurs_elargis existe ...
		// il y a des cas ou elle n'existe pas ...
		// Donc on la cree si on n'est pas dans le cas de la creation d'un nouvel auteur
		if((is_numeric($flux['args']['contexte']['id_auteur'])) && (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$flux['args']['contexte']['id_auteur']))){
			sql_insertq('spip_auteurs_elargis',array('id_auteur'=>$flux['args']['contexte']['id_auteur']));
		}
		$inscription2 = legender_auteur_supp_saisir($flux['args']['contexte']['id_auteur']);
		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$inscription2, $flux['data']);
	}
	return $flux;
}

// ajouter les champs inscription2 soumis lors de la soumission du formulaire CVT editer_auteur
function inscription2_post_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		spip_log('INSCRIPTION 2 : inscription2_post_edition','inscription2');
		$exceptions_des_champs_auteurs_elargis = pipeline('I2_exceptions_des_champs_auteurs_elargis',array());
		$id_auteur = $flux['args']['id_objet'];
		foreach(lire_config('inscription2',array()) as $cle => $val){
			$cle = ereg_replace("_(obligatoire|fiche|table).*$", "", $cle);
			if($val=='on' AND !in_array($cle,$exceptions_des_champs_auteurs_elargis) and !ereg("^(categories|zone|newsletter).*$", $cle)){
				$var_user[$cle] = sql_quote(_request($cle));
			}
		}
		if (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$id_auteur)){
			//insertion de l'id_auteur dans spip_auteurs_elargis sinon on peut pas proceder a l'update
			$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=> $id_auteur));
		}
		sql_update("spip_auteurs_elargis",$var_user,"id_auteur=$id_auteur");
			
		// Notifications, gestion des revisions, reindexation...
		pipeline('post_edition',
			array(
				'args' => array(
					'table' => 'spip_auteurs_elargis',
					'id_objet' => $id_auteur
				),
				'data' => $auteur
			)
		);
	}
	return $flux;
}

function inscription2_I2_exceptions_des_champs_auteurs_elargis($array){
	// liste des champs pour lesquels on ne doit pas créer de champs dans la table spip_auteurs_elargis
	
	// Principalement les champs déjà présents dans spip_auteurs
	$array[] = 'id_auteur';
	$array[] = 'bio';
	$array[] = 'nom';
	$array[] = 'pass';
	$array[] = 'login';
	$array[] = 'email';
	$array[] = 'statut';
	$array[] = 'gpg';
	
	// Des choses spécifiques à inscription2
	$array[] = 'username';
	$array[] = 'statut_nouveau';
	$array[] = 'statut_int';
	$array[] = 'statut_interne';
	$array[] = 'accesrestreint';
	$array[] = 'password';
	
	return $array;
}
?>