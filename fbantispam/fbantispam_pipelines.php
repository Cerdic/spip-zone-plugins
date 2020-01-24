<?php

/**
 * Plugin FB Antispam
 * (c) 2013 Fabio Bertagnin - FBServices - www.fbservices.fr
 * Inspiré de "nospam" de Cedric Morin pour www.yterium.net (http://www.spip-contrib.net/?rubrique1165)
 * Licence GPL
 *
 */
if (!defined("_ECRIRE_INC_VERSION"))
    return;

function fbantispam_insert_head($flux) {
    return $flux;
}

function fbantispam_recuperer_fond($flux) {
    $fond = strval($flux['args']['fond']);
    if (false !== $pos = strpos($fond, 'formulaires/forum')) {
        // on ajoute le champ 'nobot' si pas present dans le formulaire
        $texte = &$flux['data']['texte'];
        $pos = strpos($texte, '</form>');
        $nobot = recuperer_fond("inclure/nobot", array('nobot' => ''));
        $texte = substr_replace($texte, $nobot, $pos, 0);

        // On ajoute le champ 'captcha' avant le bouton de submit
        // On commence de la fin, pour se positionner dans le formulaire de saisie et non dans
        // celui de prévisualisation
        $pos = strrpos($texte, '<p class="boutons">', 0);
        // S'il n'a pas trouvé le bouton, on se positionne à la fin du formulaire (moins joli !)
        if (!$pos)
            $pos = strrpos($texte, '</form', 0);
        if ($pos) {
            $type_captcha = lire_config('fbantispam/type_captcha', 'copie');
            if ($type_captcha == 'copie' || $type_captcha == '') {
                $cp = fbantispam_get_captcha();
                $cps = "$cp[0]$cp[1]$cp[2]$cp[3]";
                $captcha = recuperer_fond("inclure/captcha", array('captcha' => $cps, 'c1' => $cp[0] * 2, 'c0' => $cp[1] * 2, 'c2' => $cp[2] * 2, 'c3' => $cp[3] * 2));
                $texte = substr_replace($texte, $captcha, $pos, 0);
            }
            if ($type_captcha == 'addition') {
                $cp = fbantispam_get_captcha();
                $cps = "$cp[1]+$cp[3]";
                $captcha = recuperer_fond("inclure/captcha-addition", array('captcha' => $cps, 'c1' => $cp[0] * 2, 'c0' => $cp[1] * 2, 'c2' => $cp[2] * 2, 'c3' => $cp[3] * 2));
                $texte = substr_replace($texte, $captcha, $pos, 0);
            }
            if ($type_captcha == 'multiplication') {
                $cp = fbantispam_get_captcha();
                $cps = "$cp[1]x$cp[3]";
                $captcha = recuperer_fond("inclure/captcha-multiplication", array('captcha' => $cps, 'c1' => $cp[0] * 2, 'c0' => $cp[1] * 2, 'c2' => $cp[2] * 2, 'c3' => $cp[3] * 2));
                $texte = substr_replace($texte, $captcha, $pos, 0);
            }
            if ($type_captcha == 'recaptcha') {
                $captcha = recuperer_fond("inclure/captcha-recaptcha", array('cle_site' => lire_config('fbantispam/cle_site')));
                $texte = substr_replace($texte, $captcha, $pos, 0);
            }
        }
    }
    return $flux;
}

/**
 */
function fbantispam_formulaire_charger($flux) {
    $form = $flux['args']['form'];
    $je_suis_poste = false;
    if (isset($flux['args']['je_suis_poste']) && $flux['args']['je_suis_poste'] == 1)
        $je_suis_poste = true;
    if ($form == "forum" && !$je_suis_poste) {
        include_spip("inc/fbantispam");
        if ($charger_formulaire = charger_fonction("charger_formulaire_forum", "fbantispam", true)) {
            $flux = $charger_formulaire($flux);
        }
    }
    return $flux;
}

/**
 */
function fbantispam_formulaire_verifier($flux) {
    $form = $flux['args']['form'];
    $previsu = false;
    if (isset($flux['data']['previsu']) && $flux['data']['previsu'] != '')
        $previsu = true;

    $res = array();

    if ($form == "forum" && $previsu) {

        $type_captcha = lire_config('fbantispam/type_captcha', 'copie');
        if ($type_captcha != 'recaptcha') {
            $captcha = _request('captcha');
            $cp0 = _request('c1') / 2;
            $cp1 = _request('c0') / 2;
            $cp2 = _request('c2') / 2;
            $cp3 = _request('c3') / 2;
            $cmod = _request('cmod');
            if ($cmod != 'fbantispam') {
                $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : message non accepté (identifié à un SPAM)</p>';
                return $res;
            }
            if ($captcha == '') {
                $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'a pas été saisi</p>';
                return $res;
            }
            if ($type_captcha == 'copie' || $type_captcha == '') {
                $cps = "$cp0" . "$cp1" . "$cp2" . "$cp3";
                if ($captcha != $cps) {
                    $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
                    return $res;
                }
            } elseif ($type_captcha == 'addition') {
                $cps = $cp1 + $cp3;
                if ($captcha != $cps) {
                    $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
                    return $res;
                }
            } elseif ($type_captcha == 'multiplication') {
                $cps = $cp1 * $cp3;
                if ($captcha != $cps) {
                    $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
                    return $res;
                }
            } else {
                $res['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : pas de captcha connu</p>';
                return $res;
            }
        } else {
            require(__DIR__."/fbantispam/fb_recaptcha_lib.php");
            $recaptcha_response = _request("g-recaptcha-response");
            if ($recaptcha_response) {
                $test = fbantispam_recaptcha_verif($recaptcha_response, lire_config('fbantispam/cle_secrete'), $_SERVER["REMOTE_ADDR"]);


                if (!$test) {
                    $res['message_erreur'] = "<p style='background:#ffffaa;padding:4px'>ERREUR : le controle anti-spam n'a pas été validé</p>";
                    spip_log("ERREUR fbantispam : le controle anti-spam n'a pas été validé. POST=".print_r($_POST, 1), _LOG_ERREUR);
                    return $res;
                }
            } else {
                $res['message_erreur'] = "<p style='background:#ffffaa;padding:4px'>ERREUR : le code du controle anti-spam n'a pas été reçu</p>";
                spip_log("ERREUR fbantispam : le code du controle anti-spam n'a pas été reçu. POST=".print_r($_POST, 1), _LOG_ERREUR);
                return $res;
            }
        }
    }
    return $flux;
}

/**
 */
function fbantispam_formulaire_traiter($flux) {
    return $flux;
}

/**
 */
function fbantispam_pre_edition($flux) {
    return $flux;
}

/**
 */
function fbantispam_get_captcha() {
    $ret = array();
    for ($i = 0; $i < 4; $i++) {
        $ret[$i] = rand(0, 9);
    }
    return $ret;
}

