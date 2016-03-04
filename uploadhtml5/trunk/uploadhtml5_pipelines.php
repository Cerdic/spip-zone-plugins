<?php
/**
 * Utilisations de pipelines par Formulaire upload html5
 *
 * @plugin     Formulaire upload html5
 * @copyright  2014
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Uploadhtml5\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function uploadhtml5_jquery_plugins($scripts) {
	include_spip('inc/config');
    $config = lire_config('uploadhtml5');
    if ((isset($config['charger_public']) and $config['charger_public']) // Si on doit charger dans l'espace publique
        or test_espace_prive() // Ou que l'on est dans l'espace privé
    ) {
        $scripts[] = 'lib/dropzone/dropzone.js'; // Charger Dropzone
    }

    return $scripts;
}

function uploadhtml5_insert_head_css($flux) {
	include_spip('inc/config');
    $config = lire_config('uploadhtml5');
    if ((isset($config['charger_public']) and $config['charger_public']) // Si on doit charger dans l'espace publique
        or test_espace_prive() // Ou que l'on est dans l'espace privé
    ) {
        $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

        $flux .= '<link rel="stylesheet" href="'.find_in_path('css/saisie_upload.css').'" type="text/css" media="screen" />';
    }

    return $flux;
}

function uploadhtml5_header_prive($flux) {
    $flux .= '<link rel="stylesheet" href="'.find_in_path('lib/dropzone/dropzone.css').'" type="text/css" media="screen" />';

    $flux .= '<link rel="stylesheet" href="'.find_in_path('prive/css/dropzone_prive.css').'" type="text/css" media="screen" />';


    return $flux;
}

function uploadhtml5_formulaire_fond($flux) {
	include_spip('inc/config');
	$config = lire_config('uploadhtml5');

    // Simplification de variable
    $objet = isset($flux['args']['contexte']['objet']) ? $flux['args']['contexte']['objet'] : '';
    $id_objet = isset($flux['args']['contexte']['id_objet']) ? $flux['args']['contexte']['id_objet'] : 0;

    if ($flux['args']['form'] == 'joindre_document') {

	    /**
	     * Si on est pas sur l'espace privé et que les scripts
	     * n'ont pas été charger sur l'espace public,
	     * on ne fait rien au formulaire
	     */
	    if (!test_espace_prive() and !$config['charger_public']) {
		    return $flux;
	    }

        // Récupérer le formulaire d'upload en html5 et lui passer une partie du contexte de joindre_document
        $uploadhtml5 = recuperer_fond(
            'prive/squelettes/inclure/uploadhtml5',
            array(
                'type' => $objet,
                'id' => $id_objet
            )
        );

        // Injecter uloadhtml5 au dessus du formulaire joindre_document.
        $flux['data'] = $uploadhtml5.$flux['data'];
    } elseif ($flux['args']['form'] == 'editer_logo') {

	    /**
	     * Si on est pas sur l'espace privé et que les scripts
	     * n'ont pas été charger sur l'espace public,
	     * on ne fait rien au formulaire
	     */
	    if (!test_espace_prive() and !$config['charger_public']) {
		    return $flux;
	    }

        $chercher_logo = charger_fonction('chercher_logo', 'inc');
        if (!$chercher_logo($id_objet, id_table_objet($objet))) {


            // Bloc ajax par défaut
            $ajaxReload = 'navigation';

            // Cas spécial: si on édite le logo du site, il faut recharger le contenu et non la navigation
            if ($id_objet == 0 and $objet == 'site') {
                $ajaxReload = 'contenu';
            }

            // Récupérer le formulaire d'upload en html5 et lui passer une partie du contexte
            $uploadhtml5 = recuperer_fond(
                'prive/squelettes/inclure/uploadhtml5_logo',
                array(
                    'type' => $objet,
                    'id' => $id_objet,
                    'ajaxReload' => $ajaxReload
                )
            );

            $config = lire_config('uploadhtml5');
            // Injecter uloadhtml5 au dessus du formulaire joindre_document.
            if (isset($config['remplacer_editer_logo'])
                and $config['remplacer_editer_logo']) {
                $flux['data'] = $uploadhtml5;
            } else {
                $flux['data'] = $uploadhtml5.$flux['data'];
            }
        }
    }

    return $flux;
}


/**
 * Lacher le cron de nettoyage des fichiers media temporaire toute les 24 heures
 *
 * @param mixed $taches
 * @access public
 * @return mixed
 */
function uploadhtml5_taches_generales_cron($taches) {
    $taches['nettoyer_document_temporaire'] = 24*3600;
    return $taches;
}

function uploadhtml5_formulaire_verifier($flux) {

	include_spip('inc/saisies');

	// Est-ce que le formulaire soumis possède des saisies upload ?
	$form = $flux['args']['form'];
	// Ce n'est pas une faute de frappe
	// le pipeline renvoi les argument dans un double args
	$form_args = $flux['args']['args'];
	$saisies = saisies_chercher_formulaire($form, $form_args);

	// S'il n'y a pas de saisies, il n'y a rien à vérifier
	if (!$saisies) {
		return $flux;
	}

	// Chercher si une saisie upload ce trouve dans le tableau
	include_spip('inc/saisie_upload');
	$saisie = chercher_saisie_upload($saisies);

	// Une saisie upload obligatoire a été trouvée,
	// il faut donc la vérifier
	if (isset($saisie['options']['obligatoire'])) {

		// On commence par supprimer l'erreur générique.
		// Comme la dropzone n'est pas un <input> classique,
		// l'erreur générique sera toujours présente.
		unset($flux['data'][$saisie['options']['nom']]);

		// On vérifie qu'il y a des documents dans la session
		include_spip('inc/saisie_upload');
		$documents = saisie_upload_get();

		// Pas de document dans la session ?
		if (empty($documents['document'])) {
			// Erreur !
			$flux['data'][$saisie['options']['nom']] = _T('info_obligatoire');
		}

		// On vérifie le nombre d'erreur pour savoir
		// s'il faut garder message_erreur
		if (count($flux['data']) == 1) {
			// une seul erreur, c'est message_erreur qui est seul.
			unset($flux['data']['message_erreur']);
		}
	}

	return $flux;
}
