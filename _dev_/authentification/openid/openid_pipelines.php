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
			$flux['data']['openid'] = vider_url($openid, false);
		}
	}
	return $flux;
}

?>
