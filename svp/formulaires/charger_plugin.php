<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_charger_plugin_charger_dist(){
	return array('phrase' => _request('phrase'),
				'categorie' => _request('categorie'),
				'etat' => _request('etat'),
				'depot' => _request('depot'),
				'doublon' => _request('doublon'));
}

function formulaires_charger_plugin_verifier_dist(){

    $erreurs = array();
    $a_installer = array();

    if (!_request('rechercher')
    and (!_request('valider_actions'))
    and (!_request('annuler_actions'))) {
        // On construit le tableau des ids de paquets conformement a l'interface du decideur
        if (_request('installer')) {
            // L'utilisateur a demander une installation multiple de paquets
            // -- on verifie la liste des id_paquets uniquement
            if ($id_paquets = _request('id_paquets')) {
                foreach ($id_paquets as $_id_paquet)
                    $a_installer[$_id_paquet] = 'on';
            }
        }
        else {
            // L'utilisateur a demander l'installation d'un paquet en cliquant sur le bouton en regard
            // du resume du plugin -> installer_paquet
            if ($install = _request('installer_paquet'))
                if ($id_paquet = key($install))
                    $a_installer[$id_paquet] = 'on';
        }

        if (!$a_installer)
            $erreurs = _T('svp:message_nok_aucun_plugin_selectionne');
        else {
            // On fait appel au decideur pour determiner la liste exacte des commandes apres
            // verification des dependances
            include_spip('inc/svp_decider');
            $decideur = new Decideur;
            $decideur->log = true;
            $decideur->verifier_dependances($a_installer);

            if (!$decideur->ok) {
                $erreurs['decideur_erreurs'] = array();
                foreach ($decideur->err as $id=>$errs) {
                    foreach($errs as $err) {
                        $erreurs['decideur_erreurs'][] = $err;
                    }
                }
          	}
            else {
                $erreurs['decideur_propositions'] 	= $decideur->presenter_actions('changes');
                $erreurs['decideur_demandes'] 		= $decideur->presenter_actions('ask');
                $erreurs['decideur_actions'] 		= $decideur->presenter_actions('todo');

                // c'est pas vraiment des erreurs... a suivre...
                $todo = array();
                foreach ($decideur->todo as $_todo) {
                    $todo[$_todo['i']] = $_todo['todo'];
                }

                set_request('_todo', serialize($todo));
            }
        }
    }
	return $erreurs;
}

function formulaires_charger_plugin_traiter_dist(){

	$retour = array();
	
	if (_request('rechercher')) {
		include_spip('inc/svp_rechercher');

		// On a demande une recherche (bouton rechercher)
		$phrase = _request('phrase');
		$categorie = _request('categorie');
		$etat = _request('etat');
		$depot = _request('depot');
		$doublon = (_request('doublon') == 'oui') ? true : false;
		$tri = ($phrase) ? 'score' : 'nom';
		$version_spip = $GLOBALS['spip_version_branche'].".".$GLOBALS['spip_version_code'];
		$afficher_exclusions = false;
	
		// On recupere la liste des paquets:
		// - sans doublons, ie on ne garde que la version la plus recente 
		// - correspondant a ces criteres
		// - compatible avec la version SPIP installee sur le site
		// - et n'etant pas deja installes (ces paquets peuvent toutefois etre affiches)
		// tries par nom ou score
		$plugins = svp_rechercher_plugins_spip($phrase, $categorie, $etat, $depot, $version_spip,
												svp_lister_plugins_installes(), $afficher_exclusions, $doublon, $tri);
	
		// Determination des messages de retour
		if (!$plugins)
			$retour['message_erreur'] = _T('svp:message_ok_aucun_plugin_trouve');
		else {
			$retour['message_ok']['resume'] = _T('svp:message_ok_plugins_trouves', 
												array('nb_plugins' => count($plugins),
													'tri' => _T('svp:info_tri_' . $tri)));
			$retour['message_ok']['plugins'] = $plugins;
		}
	}
    elseif (_request('valider_actions')) {

    }
    elseif (_request('annuler_actions')) {

    }
	else {
		// On a demande une installation, "installer" ou "installer_paquet" la fonction verifier a appele
        // le decideur afin de definir la liste des commandes a actionner en fonction des dependances
        set_request('traitement_fait',1);
	}
	$retour['editable'] = true;

	return $retour;
}
?>
