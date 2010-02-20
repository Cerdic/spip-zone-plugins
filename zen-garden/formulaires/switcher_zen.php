<?php
function formulaires_switcher_zen_charger_dist($auto=''){
    return array('auto'=>$auto);
}

function formulaires_switcher_zen_verifier_dist(){
    return array();
}

function formulaires_switcher_zen_traiter_dist(){
    include_spip('inc/cookie');
    spip_setcookie('spip_zengarden_switch_theme', _request('theme'),-24*3600);
    return array('message_ok'=>'ok','redirect'=>self());
}
?>