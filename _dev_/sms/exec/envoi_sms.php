<?php
/*
 * Envoi de sms
 *
 * Auteur : bertrand@toggg.com
 * © 2006 - Distribue sous licence LGPL
 *
 */

function exec_envoi_sms_dist() {
	
//	$securiser_action = charger_fonction('securiser_action', 'inc');
//	$securiser_action();

	include_spip("inc/texte");
	envoi_sms_debut_page();

	echo envoi_sms_fond();
//	echo redirige_action_auteur('', '','envoi_sms','',$html);
	
	envoi_sms_fin_page();
			
}

/*
 Fabriquer les balises des champs d'apres un modele controleurs/(type_)modele.html
	$contexte est un tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
*/
function envoi_sms_fond($contexte = array()) {
    $contexte['lang'] = $GLOBALS['spip_lang'];
    $contexte['prestataire'] = _request('prestataire');
    $contexte['message'] = _L('taper_message');
    include_spip('public/assembler');
    return recuperer_fond('fonds/envoi_sms', $contexte);
}

function envoi_sms_debut_page() {
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('Envoi de SMS'), 'sms', 'envoi_sms');
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Vous pouvez envoyer des SMS depuis cette page'));
	
	fin_boite_info();
	
	debut_droite();
	
	gros_titre(_L("Envoi de SMS"));
	
	
	debut_cadre_trait_couleur('','','',_L("Parametres d'envoi"));

}

function envoi_sms_fin_page()
{
	fin_cadre_trait_couleur();
	
	echo fin_page();
}
?>
