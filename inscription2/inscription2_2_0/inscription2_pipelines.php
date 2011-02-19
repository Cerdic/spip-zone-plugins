<?php
/**
 * Plugin Inscription2
 * Licence GPL v3
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 *
 * Insertion dans le pipeline ajouter_boutons
 * Modifie le bouton afficher les visiteurs aux webmestres
 *
 * @return
 * @param object $boutons_admin
 */
function inscription2_ajouter_boutons($boutons_admin){
	if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]) {
		if (is_object($boutons_admin['auteurs']->sousmenu['auteurs']))
			unset($boutons_admin['auteurs']->sousmenu['auteurs']);
		if (is_object($boutons_admin['bando_reactions']))
			unset($boutons_admin['bando_reactions']->sousmenu['visiteurs']);
	}
	return $boutons_admin;
}

/**
 *
 * Insertion dans le pipeline affiche_milieu
 * Dans la page auteur_infos, insertion des champs spécifiques d'Inscription2
 *
 * @return array Le $flux modifié
 * @param array $flux
 */
function inscription2_affiche_milieu($flux){
	if($flux['args']['exec'] == 'auteur_infos') {
		$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
		$legender_auteur_supp = recuperer_fond('prive/inscription2_fiche',array('id_auteur'=>$flux['args']['id_auteur'],'exceptions'=>$exceptions_des_champs_auteurs_elargis));
		$flux['data'] .= $legender_auteur_supp;
	}
	return $flux;
}

/**
 *
 * Insertion dans le pipeline editer_contenu_objet
 * Ajoute les champs I2 sur le formulaire CVT editer_auteur
 *
 * @return array Le $flux complété
 * @param array $flux
 */
function inscription2_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		include_spip('inc/legender_auteur_supp');
		/**
		 *
		 * Si on est dans la modification d'un auteur :
		 * vérification de l'existence d'une entrée correspondante dans spip_auteurs_elargis
		 * Quelquefois elle n'existe pas.
		 *
		 */
		if((is_numeric($flux['args']['contexte']['id_auteur'])) && (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$flux['args']['contexte']['id_auteur']))){
			sql_insertq('spip_auteurs_elargis',array('id_auteur'=>$flux['args']['contexte']['id_auteur']));
		}
		/**
		 *
		 * Insertion des champs dans le formulaire aprs le textarea PGP
		 *
		 */
		$inscription2 = legender_auteur_supp_saisir($flux['args']['contexte']['id_auteur']);
		$flux['data'] = preg_replace('%(<li class="editer_pgp(.*?)</li>)%is', '$1'."\n".$inscription2, $flux['data']);
	}
	return $flux;
}


/**
 *
 * Insertion dans le pipeline post_edition
 * ajouter les champs inscription2 soumis lors de la soumission du formulaire CVT editer_auteur
 *
 * @return
 * @param object $flux
 */
function inscription2_post_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		spip_log('INSCRIPTION 2 : inscription2_post_edition','inscription2');
		$exceptions_des_champs_auteurs_elargis = pipeline('i2_exceptions_des_champs_auteurs_elargis',array());
		$id_auteur = $flux['args']['id_objet'];
		foreach(lire_config('inscription2',array()) as $cle => $val){
			$cle = preg_replace("/_(obligatoire|fiche|table).*/", "", $cle);
			if($val=='on'
			AND !in_array($cle,$exceptions_des_champs_auteurs_elargis)
			AND !preg_match('/^(categories|zone|newsletter)/', $cle)) {
				$var_user[$cle] = _request($cle);
				if(is_array(_request($cle))){
					spip_log($var_user[$cle]);
					$var_user[$cle] = serialize(_request($cle));
				}
				spip_log("$cle = ".$var_user[$cle]);
			}
		}
		if (!sql_getfetsel('id_auteur','spip_auteurs_elargis','id_auteur='.$id_auteur)){
			//insertion de l'id_auteur dans spip_auteurs_elargis sinon on peut pas proceder a l'update
			$id_elargi = sql_insertq("spip_auteurs_elargis",array('id_auteur'=> $id_auteur));
		}
		sql_updateq("spip_auteurs_elargis",$var_user,"id_auteur=$id_auteur");

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

/**
 *
 * Insertion dans le pipeline i2_exceptions_des_champs_auteurs_elargis
 * qui empêche la création de certains champs dans la table
 * après les avoir configuré
 *
 * @return array Un tableau des champs correspondant au "name" de son input de configuration dans le CFG
 * @param array $array Prend un tableau en argument qui doit être complété en fonction des besoins
 */

function inscription2_i2_exceptions_des_champs_auteurs_elargis($array){
	// liste des champs pour lesquels on ne doit pas créer de champs dans la table spip_auteurs_elargis

	// Principalement les champs déjà présents dans spip_auteurs
	$array[] = 'id_auteur';
	$array[] = 'bio';
	$array[] = 'nom';
	$array[] = 'pass';
	$array[] = 'login';
	$array[] = 'email';
	$array[] = 'statut';
	$array[] = 'pgp';
	$array[] = 'url_site';
	$array[] = 'nom_site';

	// Des choses spécifiques à inscription2
	$array[] = 'logo_auteur';
	$array[] = 'username';
	$array[] = 'statut_nouveau';
	$array[] = 'statut_int';
	$array[] = 'statut_interne';
	$array[] = 'accesrestreint';
	$array[] = 'password';
	$array[] = 'affordance_form';
	$array[] = 'reglement';
	$array[] = 'reglement_article';
	$array[] = 'validation_numero_international';
	$array[] = 'pays_defaut';

	return $array;
}

/**
 *
 * Insertion dans le pipeline i2_verifications_specifiques du plugin inscription2
 *
 * @return array Tableau contenant plusieurs tableaux en fonction du type de champs
 * @param object $array Doit recevoir un tableau du même type
 */

function inscription2_i2_verifications_specifiques($array){

	// Les emails : fonction inc/inscrition2_valide_email
	$array['email'] = 'valide_email';

	// Les logins : fonction inc/inscription2_valide_login
	$array['login'] = 'valide_login';

	// Les logins : fonction inc/inscription2_valide_nom
	$array['nom'] = 'valide_nom';

	// Les statuts : fonction inc/inscription2_valide_statut
	$array['statut'] = 'valide_statut';

	// Les codes postaux : fonction inc/inscription2_valide_cp
	$array['code_postal'] = 'valide_cp';
	$array['code_postal_pro'] = 'valide_cp';

	// Les numéros de téléphone : fonction inc/inscription2_valide_numero
	$array['telephone'] = 'valide_numero';
	$array['fax'] = 'valide_numero';
	$array['mobile'] = 'valide_numero';
	$array['telephone_pro'] = 'valide_numero';
	$array['fax_pro'] = 'valide_numero';
	$array['mobile_pro'] = 'valide_numero';
	
	// Verifie que la case du reglement est cochée
	$array['reglement'] = 'valide_reglement';

	return $array;
}

/**
 *
 * Insertion dans le pipeline affiche_droite
 * Dans certaines pages définies, afficher le lien d'accès à la page des comptes utilisateurs
 *
 * @return array Le même tableau qu'il reçoit en argument
 * @param array $flux Un tableau donnant des informations sur le contenu passé au pipeline
 */

function inscription2_affiche_droite($flux){
	if(((preg_match('/^inscription2/',$flux['args']['exec']))
		 || (preg_match('/^auteur/',$flux['args']['exec']))
		 || (preg_match('/^i2_/',$flux['args']['exec']))
		 || (($flux['args']['exec'] == 'cfg') && ((_request('cfg') == 'inscription2') || preg_match('/^i2_/',_request('cfg'))))
		)
		 && ($flux['args']['exec'] != 'inscription2_adherents')){
    	$flux['data'] .= recuperer_fond('prive/inscription2_affiche_droite');
	}
	return $flux;
}
?>
