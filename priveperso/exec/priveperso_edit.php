<?php

if (!defined("_ECRIRE_INC_VERSION")) {
    return;
}

include_spip('inc/presentation');

function exec_priveperso_edit_dist()
{

    // si pas autorise : message d'erreur
    if (!autoriser('configurer', 'priveperso')) {
        include_spip('inc/minipres');
        echo minipres();
        die();
    }

    // pipeline d'initialisation
    pipeline('exec_init', array('args'=>array('exec'=>'priveperso_edit'),'data'=>''));

    // entetes
    $commencer_page = charger_fonction('commencer_page', 'inc');
    echo $commencer_page(_T('priveperso:personnaliser_espace_prive'), "configuration", "configuration");

    // barre d'onglets
    // echo barre_onglets("configuration", "priveperso");

    // colonne gauche
    echo debut_gauche('', true);
    echo pipeline('affiche_gauche', array('args'=>array('exec'=>'priveperso_edit'),'data'=>''));

    // colonne droite
    echo creer_colonne_droite('', true);
    echo pipeline('affiche_droite', array('args'=>array('exec'=>'priveperso_edit'),'data'=>''));

    // centre
    echo debut_droite('', true);

    // titre
    echo gros_titre(_T('priveperso:personnaliser_espace_prive'), '', false);
    // contenu
    $rub_id = _request('rub_id');
    echo recuperer_fond('prive/editer/priveperso', array(
        'titre' => ($rub_id!==null)
        ? _T('priveperso:info_modif_priveperso', array("rubrique"=>$rub_id))
        : _T('priveperso:info_nouveau_priveperso'),
        'redirect' => generer_url_ecrire("priveperso"),
        'rub_id'=> $rub_id,
        'icone_retour' => icone_inline(
            _T('icone_retour'),
            generer_url_ecrire('priveperso'),
            find_in_path("prive/themes/spip/images/priveperso-24.png"),
            "rien.gif",
            $GLOBALS['spip_lang_left']
        ),
    ));

    echo pipeline('affiche_milieu', array('args'=>array('exec'=>'priveperso_edit'),'data'=>''));

    echo fin_gauche(), fin_page();
}

?>