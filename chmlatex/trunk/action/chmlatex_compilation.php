<?php


function action_chmlatex_compilation_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $arg = $securiser_action();

    list($format,$langue) = explode('_',$arg);

    $sDirExport = _DIR_RACINE . _NOM_TEMPORAIRES_INACCESSIBLES;
    $sDirExport .= $arg.'/';

    $sCmdLine = lire_config('chmlatex/compilateur_'.$format);

    $sCheminSource = _DIR_RACINE . _NOM_TEMPORAIRES_INACCESSIBLES . $arg . '/';
    $sFile = 'chmlatex_' . $langue. '.';
    switch($format) {
        case('html'):
            $sCmdLine .= ' "'.$sCheminSource.'chmlatex.hhp"';
            break;
        case('tex');
            $sCmdLine .= ' "'.$sCheminSource.$sFile.'tex"';
            exec($sCmdLine);
    }
    exec($sCmdLine); // 2 fois pour Latex

    $tExt = array(
        'html'=>'chm',
        'tex'=>'pdf');

    $sFile .= $tExt[$format];

    $sDest = _DIR_RACINE . _NOM_TEMPORAIRES_ACCESSIBLES . $sFile;
    spip_log($sCmdLine,'compil');
    spip_log(is_file($CheminSource . $sFile),'compil');
    rename(
        $CheminSource . $sFile,
        $sDest
    );
    include_spip('inc/headers');
    redirige_url_ecrire('compilation_ok','href='.$sDest.'&lien='._T('chmlatex:telecharger_'.$tExt[$format]));
}

?>
