<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

$GLOBALS['liste_des_authentifications']['openid'] = 'openid';

/**
 * Pipeline permettant de modifier le tableau des informations passée à l'action
 * finale d'authentification après récupération des informations du provider
 *
 * cf : inc/openid.php
 */
$GLOBALS['spip_pipeline']['openid_recuperer_identite'] = '';

/**
 * Pipeline permettant de modifier l'url de redirection de l'action
 * finale d'identification pour y ajouter en paramètre les champs demandés
 *
 * cf : action/inscrire_openid.php
 */
$GLOBALS['spip_pipeline']['openid_inscrire_redirect'] = '';


/**
 * Afficher l'openid sur le formulaire de login
 *	->Utilise uniquement pour spip 2.0.x
 * et sur le form inscription
 *
 * @param <type> $flux
 * @return <type>
 */
function openid_recuperer_fond($flux) {
	if ($flux['args']['fond']=='formulaires/login' AND version_compare($GLOBALS['spip_version_branche'],"2.1.0 dev","<")){
		include_spip('inc/openid');
		$flux['data']['texte'] = openid_login_form($flux['data']['texte'],$flux['data']['contexte']);
	}
	if ($flux['args']['fond']=='formulaires/inscription'){

		$insc = recuperer_fond('formulaires/inc-inscription-openid',$flux['data']['contexte']);
		$flux['data']['texte'] = str_replace('<ul',$insc . '<ul',$flux['data']['texte']);

	}
	return $flux;
}

?>