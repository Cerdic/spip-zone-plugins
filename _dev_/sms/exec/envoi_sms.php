<?php
/*
 * Envoi de sms
 *
 * Auteur : bertrand@toggg.com
 * © 2006 - Distribue sous licence LGPL
 *
 */

function exec_envoi_sms_dist()
{
	$champs = array('prestataire', 'user', 'password', 'api_id',
					'text', 'from', 'to', 'id');
	foreach ($champs as $champ) {
	    $contexte[$champ] = _request($champ);
    }
	$result = $message = null;
	if (_request('envoi')) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		$resultat = transmet_prestataire($contexte);
		$message = $resultat ? _L('erreur') . ':<br />'. $resultat
							: _L('envoi_correct_pour') . ' ' . $contexte['to'];
	}
	include_spip("inc/texte");
	envoi_sms_debut_page($message);

	echo envoi_sms_fond($contexte);
	
	envoi_sms_fin_page();
			
}

/*
 Vérifier les parametre et faire la requete d'envoi du sms
	$contexte est un tableau (nom=>valeur) qui sera enrichi
	Retourne '' si tou s'est bien passé , message d'erreur sinon
*/
function transmet_prestataire(&$contexte)
{
	include_spip('inc/sms');
	$contexte['resultat'] = '';
	$contexte['resultat'] = print_r($contexte, true);
	return $contexte['resultat'];
}

/*
 Fabriquer les balises des champs d'apres un modele fonds/envoi_sms.html
	$contexte est un tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
*/
function envoi_sms_fond($contexte = array()) {
    $contexte['lang'] = $GLOBALS['spip_lang'];
    $contexte['arg'] = 'envoi_sms-0.1.0';
    $contexte['hash'] =  calculer_action_auteur('-' . $contexte['arg']);

    include_spip('public/assembler');
    return recuperer_fond('fonds/envoi_sms', $contexte);
}

function envoi_sms_debut_page($message = '') {
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('Envoi de SMS'), 'sms', 'envoi_sms');
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Vous pouvez envoyer des SMS depuis cette page'));
	fin_boite_info();
	
	if ($message) {
		debut_boite_info();
		echo propre($message);
		fin_boite_info();
	}
	
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
