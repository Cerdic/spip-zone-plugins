<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

/**
 * Enter description here...
 *
 * @param unknown_type $flux
 */
function fblogin_recuperer_fond($flux){
	if ($flux['args']['fond']=='formulaires/login'){
		$login = pipeline('social_login_links','');
		$flux['data']['texte'] = str_replace('</form>','</form>'.$login,$flux['data']['texte']);
	}
	if ($flux['args']['fond']=='formulaires/inscription'){
		$insc = pipeline('social_inscription_links','');
		$flux['data']['texte'] = str_replace('<form',$insc . '<form',$flux['data']['texte']);
	}
	return $flux;
}

/**
 * Pipeline social_links pour ajouter les liens vers FB lors de l'authentification
 *
 * @param string $flux
 * @return string
 */
function fblogin_social_login_links($flux){
	if (defined('_FB_API_KEY')
	 && !isset($GLOBALS['visiteur_session']['fb_session'])
	 ){
		$h = recuperer_fond('modeles/fblogin_login_link',array('fb_auth'=>isset($_SESSION['fb_session'])?' ':''));
		$flux .= $h;
	}
	return $flux;
}

/**
 * Pipeline social_inscription_links pour ajouter les liens vers FB lors de l'inscription
 *
 * @param string $flux
 * @return string
 */
function fblogin_social_inscription_links($flux){
	if (defined('_FB_API_KEY')
	 && !isset($_SESSION['fb_session'])
	 && !isset($GLOBALS['visiteur_session']['fb_session'])
	 ){
		$h = recuperer_fond('modeles/fblogin_insc_link');
		$flux .= $h;
	}
	return $flux;
}

/**
 * Pipeline social_profil_links pour ajouter les liens vers FB dans la page profil
 *
 * @param string $flux
 * @return string
 */
function fblogin_social_profil_links($flux){
	if (defined('_FB_API_KEY')){
		$h = recuperer_fond('modeles/fblogin_profil_link');
		$flux .= $h;
	}
	return $flux;
}

/**
 * Pipeline affichage_final
 * lorsqu'une page est appelee dans une iframe par FB, tous les liens sont modifies en jquery
 * pour s'ouvrir dans le _parent afin de renvoyer de FB vers le site principal
 *
 * @param string $flux
 * @return string
 */
function fblogin_affichage_final($flux){
	if (defined('_FB_API_KEY')
	&& _request('fb_sig_in_iframe')){
		$js = "<script type='text/javascript'>$('body').addClass('fb_iframe');$('a').attr('target','_parent');</script>";
		$flux = str_replace('</body>',"$js</body>",$flux);
	}
	return $flux;
}

/**
 * Pipeline social_lister_amis
 * appele au moment de lister les amis
 * permet de completer les amis declares sur le site par les amis declares sur FB
 * mais que l'on a pas le droit d'enregistrer explicitement dans le site (Term of Uses de l'API FB)
 *
 * @param string $flux
 * @return string
 */
function fblogin_social_lister_amis($flux){
	if (defined('_FB_API_KEY')
	&& ($GLOBALS['visiteur_session']['id_auteur']==$flux['args']['id_auteur'])
	&& isset($GLOBALS['visiteur_session']['fb_session'])){
		include_spip('inc/fb_lib');
		try {
			$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET, $GLOBALS['visiteur_session']['fb_session']['session_key']);
			$uid = $api_client->users_getLoggedInUser();
			$res = $api_client->fql_query("SELECT uid2 FROM friend WHERE uid1=".$uid);
			if (count($res)){
				$liste_uids = array(0);
				foreach ($res as $r)
					$liste_uids[] = intval($r['uid2']);
				$res = sql_select('id_auteur','spip_auteurs','fb_uid IN ('.implode(',',$liste_uids).')');
				while ($row = sql_fetch($res)){
					$flux['data'][$row['id_auteur']]=true;
				}
			}
		}
		// Une exception est levee uniquement si une erreur est trouvee
		catch (Exception $e) {
			spip_log('Exception api FB, pipeline social_lister_amis');
		}
		
	}
	return $flux;
}

/**
 * Pipeline definir_session utilise pour la gestion du cache spip et #SESSION
 * permet de prendre en compte $_SESSION['fb_session'] utilise lors de l'authent
 *
 * @param string $flux
 * @return string
 */
function fblogin_definir_session($flux){
	$flux .= (isset($_SESSION['fb_session'])?serialize(isset($_SESSION['fb_session'])):'');
	return $flux;
}

/**
 * Pipeline social_charger_profil
 * permet de recuperer les infos de profil du visiteur qui en train de s'inscrire
 * et s'est deja identifie avec FB
 * ces infos sont reinjectee dans le formulaire pour le preremplir et lui permettre de valider
 * (la recopie directe n'etant pas permise par les Terms of Use de l'API FB)
 *
 * @param array $valeurs
 * @return array
 */
function fblogin_social_charger_profil($valeurs){
	if (isset($_SESSION['fb_session'])){
		include_spip('inc/fb_lib');
		try {
			$api_client = new FacebookRestClient(_FB_API_KEY, _FB_SECRET, $_SESSION['fb_session']['session_key']);
			$uid = $api_client->users_getLoggedInUser();
			$res = $api_client->fql_query("SELECT uid, first_name, last_name, "
			//."name, pic_small, "
			."pic_big, "
			//."pic_square, pic, affiliations, profile_update_time, timezone, religion,"
			."birthday,"
			//." sex, hometown_location, meeting_sex, meeting_for, relationship_status, significant_other_id, political,"
			." current_location "
			//.", activities, interests, is_app_user, music, tv, movies, books, quotes, about_me, hs_info, education_history, work_history, notes_count, wall_count, status, has_added_app"
			." FROM user WHERE uid=$uid");
			if (count($res)) {
				$res = reset($res);
				$valeurs['fb_uid'] = $uid;
				if ($res['first_name']) $valeurs['prenom']=$res['first_name'];
				if ($res['last_name']) $valeurs['nom']=$res['last_name'];
				if ($res['pic_big']) $valeurs['logo_distant']=$res['pic_big'];
				if ($res['birthday']) $valeurs['date_naissance']=date('d/m/Y',strtotime($res['birthday']));
				if (isset($res['current_location']['country'])
				AND defined('_DIR_PLUGIN_GEOGRAPHIE')){
					$pays = $res['current_location']['country'];
					$id_pays = 0;
					if ($row = sql_fetsel('id_pays','spip_geo_pays','nom='.sql_quote($pays)))
						$id_pays = $valeurs['id_pays'] = $row['id_pays'];
					$ville = $res['current_location']['city'];
					if ($row = sql_fetsel('id_commune,nom,code_postal','spip_geo_communes','nom='.sql_quote($ville).($id_pays?" AND id_pays=".intval($id_pays):""))){
						$valeurs['commune']=$row['nom'];
						$valeurs['id_commune']=$row['id_commune'];
						$valeurs['cp'] = $row['code_postal'];
					}
				}
			}
		}
		// Une exception est levee uniquement si une erreur est trouvee
		catch (Exception $e) {
			unset($_SESSION['fb_session']);
		}
	}
	return $valeurs;
}
?>
