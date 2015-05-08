<?php
/**
 * Les fonctions du plugin Activités du jour
 *
 * @plugin     Activités du jour
 * @copyright  2015
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Actijour\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*---------------------------------------------------------------------------*\
 recense les sessions tmp/visites/ --> visites en attente de traitement
 \*---------------------------------------------------------------------------*/
function calcul_prevision_visites() {
    # requis spip
    include_spip('inc/visites');

    # h. issue de ecrire/inc/visites.php : calculer_visites()
    // Initialisations
    $visites = ''; # visites du site
    $visites_a = array(); # tableau des visites des articles
    $referers = array(); # referers du site
    $referers_a = array(); # tableau des referers des articles
    $articles = array(); # articles vus dans ce lot de visites

    // charger un certain nombre de fichiers de visites,
    // et faire les calculs correspondants

    # h. passe 5 minutes
    #Traiter jusqu'a 100 sessions datant d'au moins "5" minutes
    $sessions = preg_files(sous_repertoire(_DIR_TMP, 'visites'));
    $compteur = 100;
    $date_init = time()-5*60;

    foreach ($sessions as $item) {
    $tps_file=@filemtime($item);
    $temps[]=$tps_file;

        if ($tps_file < $date_init) {
        # lire fichier tmp/visites
        compte_fichier_visite($item,
        $visites, $visites_a, $referers, $referers_a, $articles);

        if (--$compteur <= 0)
				break;
		}
	}
	return array($temps,$visites,$visites_a);
}
