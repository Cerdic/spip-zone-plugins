<?php
/*
 * Envoi de sms
 *
 * Auteur : bertrand@toggg.com
 * © 2006 - Distribue sous licence LGPL
 *
 */

function exec_config_sms_dist()
{
	$champs = array('prestataire', 'user', 'password', 'api_id',
					'text', 'from', 'to', 'id');
	foreach ($champs as $champ) {
	    $contexte[$champ] = _request($champ);
    }
	$result = $message = null;
/*	if (_request('envoi')) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		$resultat = transmet_prestataire($contexte);
		$message = $resultat ? _L('erreur') . ':<br />'. $resultat
							: _L('envoi_correct_pour') . ' ' . $contexte['to'];
	}
	$message = print_r(cherche_prestataires(), true);
*/
	config_sms_debut_page($message);
// pour developpement, fixe clickatell
	$contexte['driver'] = 'clickatell_http';
	echo config_sms_fond($contexte);
	
	config_sms_fin_page();
			
}

/**
    * Retourne une liste des prestataires disponibles driver=>(name=>...,desc=>...)
    *
    * @return array  An array of available drivers.
    */
function cherche_prestataires()
{
    static $drivers = array();
    if (!empty($drivers)) {
        return $drivers;
    }
	include_spip('inc/sms');
    $preg = '#^cfg_(\w+)\.html$#';

    if ($driver_dir = opendir(dirname(__FILE__) . '/../fonds/')) {
        while (false !== ($file = readdir($driver_dir))) {
            /* ne garder que les cfg_xxx.html */
            if (preg_match($preg, $file, $matches)) {
                $driver = $matches[1];
                $drivers[$driver] = Net_SMS::getGatewayInfo($driver);
            }
        }
        closedir($driver_dir);
    }

    return $drivers;
}

function boite_liste($titre = "", $elements = array())
{
	if (!$elements) {
		return '';
	}
	$dedans = debut_boite_info(true);
	if ($titre) {
		$dedans .= '<h4>' . _L($titre) . '</h4>';
	}
	if (is_string($elements)) {
		return $dedans . $elements;
	}
	$dedans .= '<ul>';
	foreach ($elements as $elt) {
		$dedans .= '<li>' . $elt . '</li>';
	}
	$dedans .= '</ul>' . fin_boite_info(true);
	return $dedans;
}

function liste_existants()
{
}

function creer_nouveau()
{
	$liste = array();
	foreach (cherche_prestataires() as $driver => $info) {
		$liste[] = '<a href="' .
			generer_url_ecrire('config_sms', 'adddriver=' . $driver ) . '">' .
			$info['name'] . '</a><br />' . $info['desc'];
	}
	return boite_liste(_L('creer_nouveau_compte'), $liste);
}

/*
 Fabriquer les balises des champs d'apres un modele fonds/cfg_<driver>.html
	$contexte est un tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
*/
function config_sms_fond($contexte = array())
{
    $contexte['lang'] = $GLOBALS['spip_lang'];
    $contexte['arg'] = 'config_sms-0.1.0';
    $contexte['hash'] =  calculer_action_auteur('-' . $contexte['arg']);

    include_spip('public/assembler');
    return recuperer_fond('fonds/cfg_' . $contexte['driver'], $contexte);
}

function config_sms_debut_page($message = '')
{
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_L('Configuration SMS'), 'sms', 'config_sms');
	
	debut_gauche();
	
	debut_boite_info();
	echo propre(_L('Cette page permet de configurer vos prestataires SMS'));
	fin_boite_info();
	
	if ($message) {
		debut_boite_info();
		echo propre($message);
		fin_boite_info();
	}
	
	echo creer_nouveau();
	
	debut_droite();
	
	gros_titre(_L("Configuration SMS"));
	
	
	debut_cadre_trait_couleur('','','',_L("Parametres comptes SMS"));

}

function config_sms_fin_page()
{
	fin_cadre_trait_couleur();
	
	echo fin_page();
}
?>
