<?php

/**
 * Plugin FB Antispam
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 */
function fbantispam_verifier_formulaire_forum_dist($flux){
	$ret = array();
	$form = $flux['args']['form'];
        $type_captcha = lire_config('fbantispam/type_captcha', 'copie');
        if ($form == "forum")
        {
            if ($type_captcha == 'recaptcha')
            {
                include_spip("fbantispam/recaptchalib.php");
                if ($_POST["recaptcha_response_field"]) {
                        $resp = recaptcha_check_answer (lire_config('fbantispam/cle_secrete'),
                                                        $_SERVER["REMOTE_ADDR"],
                                                        $_POST["recaptcha_challenge_field"],
                                                        $_POST["recaptcha_response_field"]);

                        if (!$resp->is_valid) {
                            $error = $resp->error;
                            $ret['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
                        }
                }
                // echo "<pre>";print_r($resp);echo "</pre>";exit;
            }
            else {
                $texte = _request('texte');
                $captcha = _request('captcha');
                $cp0 = _request('c1');
                $cp1 = _request('c0');
                $cp2 = _request('c2');
                $cp3 = _request('c3');
                $cps = "$cp0"."$cp1"."$cp2"."$cp3";
                include_spip("inc/fbantispam");

                if ($captcha != $cps) 
                {
                        $ret['message_erreur'] = '<p style="background:#ffffaa;padding:4px">ERREUR : le code anti-spam n\'est pas correct</p>';
                }
            }
        }
	return $ret;
}
