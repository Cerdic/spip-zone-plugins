<?php

function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		$flux['args']['contexte']['openid'] = sql_getfetsel('openid','spip_auteurs','id_auteur='.sql_quote($flux['args']['contexte']['id_auteur']));
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}

?>
