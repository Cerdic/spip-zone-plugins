<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

// charger
function formulaires_langonet_traduire_charger() {
	$valeurs = array();
	$champs = array('fichier_langue', 'affichage' , 'langue_cible');
	foreach($champs as $_champ){
		$valeurs[$_champ] = _request($_champ);
	}
	return $valeurs;
}

// verifier
function formulaires_langonet_traduire_verifier() {
	$erreurs = array();
	if (_request('fichier_langue') == '0') {
		$erreurs['fichier_langue'] = _T('langonet:message_nok_champ_obligatoire');
	}
    if (!_request('langue_cible')) {
		$erreurs['langue_cible'] = _T('langonet:message_nok_champ_obligatoire');
	}
	return $erreurs;
}


// traiter
function formulaires_langonet_traduire_traiter() {
	$retour = array();

	// Recuperation des champs du formulaire
    // ex. porte_plume:barreoutils:pt_br:plugins-dist/porte_plume/lang/
    //
	//   $module     -> prefixe du fichier de langue
	//                  'langonet' pour 'langonet_fr.php'
	//                  parfois different du 'nom' du plugin
	//   $langue     -> index du nom de langue
	//                  'fr' pour 'langonet_fr.php'
	//   $ou_langue  -> chemin vers le fichier de langue à vérifier
	//                  'plugins/auto/langonet/lang'
    list($plugin, $module, $langue, $ou_langue) = explode(':', _request('fichier_langue'));
    $langue_cible = trim(_request('langue_cible'));
    $nouvelle_traduction = (_request('etape_edition') == _request('langue_cible')."-"._request('fichier_langue')) ? true : false;

	// Chargement de la fonction d'affichage
	$langonet_lister_items = charger_fonction('lister_items','inc');

    // Cible
    $dossier_cible = sous_repertoire(_DIR_TMP, "langonet");
	$dossier_cible = sous_repertoire($dossier_cible, "generation");

	// Recuperation des items du fichier et formatage des resultats pour affichage
    $resultats = $langonet_lister_items($module, $langue, $ou_langue);

    // Mode edition: Existe il une édition générée en cours ?
    $ou_langue_cible = $dossier_cible .  $module . "_" . $langue_cible. ".php";
    if (file_exists($ou_langue_cible)) {
        // oui, on la charge
        $resultats_cible = $langonet_lister_items($module, $langue_cible, $dossier_cible);
    } else {
        $resultats_cible = $resultats;
    }

	if (isset($resultats['erreur'])) {
		$retour['message_erreur'] = $resultats['erreur'];
	} else {
        $items = $resultats['items'];
        $items_simples = array();

        // On re-construit le tableau items
        foreach ($resultats['items'] as $item_cle => $item_valeur ) {
                   // source
                   $source = (isset($resultats['items'][$item_cle]['traduction'])) ? $resultats['items'][$item_cle]['traduction'] : "*** vide ? ***";

                   // cible
                   if (_request('champ-' . $item_cle) && $nouvelle_traduction) {
                        $cible = trim(_request('champ-' . $item_cle ));
                   } else if (isset($resultats_cible['items'][$item_cle]['traduction'])) {
                        $cible = $resultats_cible['items'][$item_cle]['traduction'];
                   } else {
                        $cible = "";
                   }
                   $items[$item_cle] = array(
                                            'traduction' =>  $cible,
                                            'source' => $source,
                                            'etat' => 'ok'
                   );
                   $items_simples[$item_cle] = trim(_request('champ-' . $item_cle ));
        }

        // Etape éditions ?
        if ($nouvelle_traduction) {
             // Traitements: On enregistre cette version
             include_spip("inc/generer_fichier");
             $fichier_langue = ecrire_fichier_langue_php($dossier_cible, $langue_cible, $module, $items_simples, $bandeau = '', $langue);
        }

        // Retours
		if (isset($fichier_langue))
                $retour['message_ok']['resume'] = _T('langonet:message_ok_fichier_traduit', array('fichier' => $fichier_langue));
		$retour['message_ok']['items'] = $items;
		$retour['message_ok']['tradlang'] = $resultats['tradlang'];
		$retour['message_ok']['reference'] = $resultats['reference'];
		$retour['message_ok']['affichage'] = _request('affichage');

	}
	$retour['editable'] = true;


    return $retour;
}