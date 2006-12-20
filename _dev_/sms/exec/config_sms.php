<?php
/*
 * Config du plugin sms pour Net_SMS de Horde
 *
 * Auteur : bertrand@toggg.com
 * © 2006 - Distribue sous licence LGPL
 *
 */

function exec_config_sms_dist()
{
	$modifier = $enregistrer = $supprimer = false;
	$config = meta_config_sms();
	$contexte = array('base_url' => generer_url_ecrire('config_sms', ''));
	if (($addDriver = _request('adddriver'))) {
		$contexte['driver'] = $addDriver;
	} else {
		if (($modifier = _request('modifier'))) {
			$contexte['driver'] = $config[$modifier]['driver'];
		} else {
			$contexte['driver'] = _request('driver');
			$contexte['compte'] = _request('compte');
			$contexte['was_compte'] = _request('was_compte');
		}
	}

	$champs = array('compte' => array('inp' => 'text', 'typ' => 'id'));
	($message = empty($contexte['driver']) ? _L('creer_un_compte') : '') ||
	($message = champs('cfg_' . $contexte['driver'], $champs));

	if ($modifier) {
		$contexte['compte'] = $modifier;
		foreach ($champs as $name => $def) {
			if (isset($config[$modifier][$name])) {
				$contexte[$name] = $config[$modifier][$name];
			}
		}
	} elseif (!$message && (($enregistrer = _request('ok')) ||
						($supprimer = _request('delete')))) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$securiser_action();
		if ($supprimer ||
		 (!_request('copier') && $contexte['compte'] != $contexte['was_compte'])) {
			$config = meta_config_sms($contexte['was_compte']);
		}
		if ($supprimer) {
			unset($contexte['compte']);
			$message = _L('compte_supprime') . ' ' . $contexte['was_compte'];
		} elseif (!is_string($message = controle($champs, $contexte))) {
			unset($message['compte']);
			$message['driver'] = $contexte['driver'];
			$config = meta_config_sms($contexte['compte'], $message);
			$message = _L('compte_enregistre') . ' <b>' . $contexte['compte'] . '</b>';
		}
	}
//	$message .= print_r($champs, true);
//	$message .= print_r($contexte, true);

	echo
		config_sms_debut_page($message, $config) .

		config_sms_fond($contexte) .
		
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

// sans parametre: lecture seule, sans data = suppression sinon update/delete
function meta_config_sms($compte = '', $data = array())
{
	// recuperer le tableau de config dans meta , pas tres securit tout ça ...
	lire_metas();
    global $meta;
    if (empty($meta['config_sms'])) {
    	$cfg = array();
    } else {
    	$cfg = unserialize($meta['config_sms']);
    }
	// pas de compte , c'est juste pour lire
    if (!$compte) {
	    return $cfg;
    }
    // donnees => actualise ou cree , detruit sinon
    if ($data) {
    	$cfg[$compte] = $data;
    } else {
    	unset($cfg[$compte]);
    }
    if (count($cfg)) {
	    ecrire_meta('config_sms', serialize($cfg));
    } else {
	    effacer_meta('config_sms');
    }
    return $cfg;
}

function champs($form, &$champs)
{
	$fichier = find_in_path($nom = 'fonds/' . $form .'.html');
	if (!lire_fichier($fichier, $controldata)) {
		return _L('erreur_lecture_') . $nom;
	}
	if (!preg_match_all(
	  '#<(?:(select)|input type="(text|password)") name="(\w+)"(?: class="type_(\w+).*")?.+>#',
					$controldata, $matches, PREG_SET_ORDER)) {
		return _L('pas_de_champs_dans_') . $nom;
	}
	foreach ($matches as $regs) {
	    if (!empty($regs[1])) {
	    	$regs[2] = 'select';
	    }
	    $champs[$regs[3]] = array('inp' => $regs[2], 'typ' => '');
	    if (!empty($regs[4])) {
	    	$champs[$regs[3]]['typ'] = $regs[4];
	    }
    }
    return '';
}

function controle($champs, &$contexte)
{
	$chk = array(
	  'id' => array('#^[a-z_]\w*$#i', _L('lettre ou &#095; suivie de lettres, chiffres ou &#095;')),
	  'idnum' => array('#^\d+$#', _L('chiffres')),
	  'pwd' => array('#^\w+$#',  _L('lettres, &#095; ou chiffres')));
    $return = '';
    $valeurs = array();
	foreach ($champs as $name => $def) {
	    $contexte[$name] = _request($name);
	    if (!empty($def['typ']) && isset($chk[$def['typ']])) {
	    	if (!preg_match($chk[$def['typ']][0], $contexte[$name])) {
	    		$return .= _L($name) . '&nbsp;:<br />' .
	    		  $chk[$def['typ']][1] . '<br />';
	    	}
	    }
	    $valeurs[$name] = _request($name);
    }
    return $return ? $return : $valeurs;
}

function boite_liens($titre = "", $elements = array())
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
		$dedans .= '<li>';
		if (!empty($elt['get'])) {
			$dedans .= '<a href="' .
			  generer_url_ecrire('config_sms', $elt['get'] ) . '">' .
			  (empty($elt['name']) ? $elt['get'] : $elt['name']) . '</a>';
		}
		$dedans .=  (empty($elt['desc']) ? '' : '<br />' . $elt['desc']) . '</li>';
	}
	$dedans .= '</ul>' . fin_boite_info(true);
	return $dedans;
}

function liens_existants($config)
{
	$liste = array();
	foreach ($config as $compte => $info) {
		$liste[] = array('get' => 'modifier=' . $compte, 'name' => $compte,
						'desc' => '(' . $info['driver'] . ')');
	}
	return boite_liens(_L('modifier_un_compte'), $liste);
}

function liens_nouveaux()
{
	$liste = array();
	foreach (cherche_prestataires() as $driver => $info) {
		$info['get'] = 'adddriver=' . $driver;
		$liste[] = $info;
	}
	return boite_liens(_L('creer_un_nouveau_compte'), $liste);
}

/*
 Fabriquer les balises des champs d'apres un modele fonds/cfg_<driver>.html
	$contexte est un tableau (nom=>valeur) qui sera enrichi puis passe à recuperer_fond
*/
function config_sms_fond($contexte = array())
{
    if (empty($contexte['driver'])) {
    	return _L('configuration_vide');
    }
    $contexte['lang'] = $GLOBALS['spip_lang'];
    $contexte['arg'] = 'config_sms-0.1.0';
    $contexte['hash'] =  calculer_action_auteur('-' . $contexte['arg']);

    include_spip('public/assembler');
    return recuperer_fond('fonds/cfg_' . $contexte['driver'], $contexte);
}

function config_sms_debut_page($message = '', $config = array())
{
	include_spip('inc/presentation');

	$commencer_page = charger_fonction('commencer_page', 'inc');
	
	return $commencer_page(_L('Configuration SMS'), 'sms', 'config_sms') .
	
		debut_gauche("accueil", true) .
	
		debut_boite_info(true) .
		propre(_L('Cette page permet de configurer vos prestataires SMS')) .
		fin_boite_info(true) .
	
		($message ? 
			debut_boite_info(true) .
			propre($message) .
			fin_boite_info(true)
		: '') .
	
		liens_existants($config) .
		liens_nouveaux() .
	
		debut_droite("", true) .
		
		gros_titre(_L("Configuration SMS"), '', true) .
		
		debut_cadre_trait_couleur('', true, '', _L("Parametres comptes SMS"));
}

function config_sms_fin_page()
{
	return fin_cadre_trait_couleur(true) .
		fin_gauche() .
	
		fin_page();
}
?>
