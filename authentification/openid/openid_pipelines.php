<?php

// ajouter un champ openID sur le formulaire CVT editer_auteur
function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		$flux['args']['contexte']['openid'] = sql_getfetsel('openid','spip_auteurs','id_auteur='.sql_quote($flux['args']['contexte']['id_auteur']));
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}

// ajouter l'open_id soumis lors de la soumission du formulaire CVT editer_auteur
function openid_pre_edition($flux){
	if ($flux['args']['table']=='spip_auteurs') {
		if ($openid = _request('openid')) {
			$openid = vider_url($openid, false);
			$openid = rtrim($openid,'/');
			// si pas de protocole, mettre http://
			if ($openid  AND !preg_match(';^[a-z]{3,6}://;i',$openid ))
				$openid = "http://".$openid;

			$flux['data']['openid'] = $openid;
		}
	}
	return $flux;
}

/**
 * Afficher l'openid sur la fiche de l'auteur
 * @param array $flux 
 */
function openid_afficher_contenu_objet($flux){
	if ($flux['args']['type']=='auteur'
		AND $id_auteur = $flux['args']['id_objet']
		AND $openid = sql_getfetsel('openid','spip_auteurs','id_auteur='.intval($id_auteur))
	){
		$flux['data'] .= "<div><img src='".find_in_path('images/login_auth_openid.gif')
			."' alt='"._T('openid:openid')."' width='16' height='16' />"
			.  " <a href='" . $openid . "'>$openid</a></div>";

	}

	return $flux;
}

/**
 * Afficher l'openid sur le formulaire de login
 * @param <type> $flux
 * @return <type>
 */
function openid_recuperer_fond($flux){
	if ($flux['args']['fond']=='formulaires/login'){
		$login = pipeline('social_login_links','');
		$flux['data']['texte'] .= "<style type='text/css'>"
		."input#var_login {background-image : url(".find_in_path('images/login_auth_openid.gif').");background-repeat:no-repeat;background-position:center left;padding-left:18px;}"
		."</style>"
		."<script type='text/javascript'>"
		."jQuery(document).ready(function(){jQuery('input#var_login').after('<div class=\'explication\'>".addslashes(_T('openid:form_login_openid'))."</div>');});"
		."</script>";
	}
	/*if ($flux['args']['fond']=='formulaires/inscription'){
		$insc = pipeline('social_inscription_links','');
		$flux['data']['texte'] = str_replace('<form',$insc . '<form',$flux['data']['texte']);
	}*/
	return $flux;
}

?>