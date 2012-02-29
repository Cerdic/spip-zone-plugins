<?php
/*
 * Plugin Mailjet
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function mailjet_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	$out = "";
	if ($exec=='config_fonctions'){
		global $spip_lang_right, $spip_lang_left;

		// Masquer un eventuel password authentifiant
		if ($http_proxy = $GLOBALS['meta']["http_proxy"]) {
			include_spip('inc/distant');
			$http_proxy=entites_html(no_password_proxy_url($http_proxy));
		}

	    $mailjet_enabled = '';
	    $mailjet_test = '';
	    $mailjet_test_address = '';
	    $mailjet_username = '';
	    $mailjet_password = '';

	    $errors = array ();
        $smtp_error = '';

		if (_request('mailjet_username')!==NULL)
		{
		    $mailjet_enabled = _request('mailjet_enabled');
		    $mailjet_test = _request('mailjet_test');
		    $mailjet_test_address = _request('mailjet_test_address');
		    $mailjet_username = _request('mailjet_username');
		    $mailjet_password = _request('mailjet_password');

		    if (empty ($mailjet_username))
		    {
		        $errors [] = 'mailjet_username';
		    }

		    if (empty ($mailjet_password))
		    {
		        $errors [] = 'mailjet_password';
		    }

		    if ($mailjet_test && empty ($mailjet_test_address))
		    {
		        $errors [] = 'mailjet_test_address';
		    }

		    ecrire_meta('mailjet_enabled',_request('mailjet_enabled'));
		    ecrire_meta('mailjet_test',_request('mailjet_test'));
		    ecrire_meta('mailjet_test_address',_request('mailjet_test_address'));
		    ecrire_meta('mailjet_username',_request('mailjet_username'));
		    ecrire_meta('mailjet_password',_request('mailjet_password'));

		    if (! count ($errors))
		    {
    		    $configs = array (array ('ssl://', 465),
                           array ('tls://', 587),
                           array ('', 587),
                           array ('', 588),
                           array ('tls://', 25),
                           array ('', 25));

    		    $host = 'in.mailjet.com';
    		    $connected = FALSE;

    		    for ($i = 0; $i < count ($configs); ++$i)
    		    {
    		        $soc = @ fSockOpen ($configs [$i] [0].$host, $configs [$i] [1], $errno, $errstr, 5);

    		        if ($soc)
    		        {
    		            fClose ($soc);

    		            $connected = TRUE;

    		            break;
    		        }
    		    }

    		    if ($connected)
    		    {
    		        ecrire_meta ('mailjet_host', $configs [$i] [0].$host);
    		        ecrire_meta ('mailjet_port', $configs [$i] [1]);
    		    }
    		    else
    		    {
    		        $smtp_error = sPrintF (_T ('mailjet:mj_error_autoconfig'), $errno, $errstr);
    		    }
		    }

			ecrire_metas ();

		    if (empty ($smtp_error) && $mailjet_test)
		    {
		        $GLOBALS ['meta'] ['mailjet_enabled'] = 1;

		        $envoyer_mail = charger_fonction ('envoyer_mail','inc');
		        $envoyer_mail ($mailjet_test_address, _T ('mailjet:mj_test_mail_subject'), _T ('mailjet:mj_test_mail_message'));

		        $GLOBALS ['meta'] ['mailjet_enabled'] = $mailjet_enabled;
		    }
		}
        else
        {
    		$mailjet_enabled = isset ($GLOBALS['meta'] ['mailjet_enabled']) ? $GLOBALS['meta'] ['mailjet_enabled'] : '';
    		$mailjet_test = isset ($GLOBALS['meta'] ['mailjet_test']) ? $GLOBALS['meta'] ['mailjet_test'] : '';
    		$mailjet_test_address = isset ($GLOBALS['meta'] ['mailjet_test_address']) ? $GLOBALS['meta'] ['mailjet_test_address'] : '';
    		$mailjet_username = isset ($GLOBALS['meta'] ['mailjet_username']) ? $GLOBALS['meta'] ['mailjet_username'] : '';
    		$mailjet_password = isset ($GLOBALS['meta'] ['mailjet_password']) ? $GLOBALS['meta'] ['mailjet_password'] : '';
        }

	    if ($mailjet_enabled)
	    {
	        $mailjet_enabled = 'checked="checked"';
	    }

	    if ($mailjet_test)
	    {
	        $mailjet_test = 'checked="checked"';
	    }

	    if (count ($errors))
	    {
	        $out .= '<p class="verdana2" style="color: red;">'._T ('mailjet:mailjet_form_error').'</p>';
	    }
	    elseif (! empty ($smtp_error))
	    {
	        $out .= '<p class="verdana2" style="color: red;">'.$smtp_error.'</p>';
	    }

	    $out .= '<form action="'.$_SERVER ['REQUEST_URI'].'#smtp" method="post">';

	    $out .= debut_cadre_relief ("", true, "", _T('mailjet:mj_global_settings'));

        $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mailjet_enabled'>"._T('mailjet:mailjet_enabled')."</label>";
	    $out .= "<input type='checkbox' name='mailjet_enabled' id='mailjet_enabled' $mailjet_enabled size='40' class='forml' style='width: 10px; display: inline;' value='1' />";
	    $out .= "</div>";

	    $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mailjet_test'>"._T('mailjet:mailjet_test')."</label>";
	    $out .= "<input type='checkbox' name='mailjet_test' id='mailjet_test' $mailjet_test size='40' class='forml' style='width: 10px; display: inline;' value='1' />";
	    $out .= "</div>";

	    $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mailjet_test_address'";

	    if (in_array ('mailjet_test_address', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mailjet:mailjet_test_address')."</label><br/>";
	    $out .= "<input type='text' name='mailjet_test_address' id='mailjet_test_address' value='$mailjet_test_address' size='40' class='forml' />";
	    $out .= "</div>";

	    $out .= fin_cadre_relief(true);

	    $out .= debut_cadre_relief ("", true, "", _T('mailjet:mj_settings'));

		$out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
		$out .= "<label for='mailjet_username'";

	    if (in_array ('mailjet_username', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mailjet:mailjet_username')."</label><br/>";
		$out .= "<input type='text' name='mailjet_username' id='mailjet_username' value='$mailjet_username' size='40' class='forml' />";
		$out .= "</div>";

		$out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
		$out .= "<label for='mailjet_password'";

	    if (in_array ('mailjet_password', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mailjet:mailjet_password')."</label><br/>";
		$out .= "<input type='text' name='mailjet_password' id='mailjet_password' value='$mailjet_password' size='40' class='forml' />";
		$out .= "</div></p>";

	    $out .= fin_cadre_relief(true);

	    $out .= '<p style="text-align: right;"><input type="submit" value="'._T ('bouton_valider').'" /></p>';
	    $out .= '</form>';

        $out = '<br /><a name="smtp"></a>'.debut_cadre_trait_couleur(_DIR_PLUGIN_Mailjet."images/mailjet.ico", true, "", _T('mailjet:mailjet_title'))
                .   $out
                .  fin_cadre_trait_couleur(true);

        $flux ['data'] = $out;
    }

    return $flux;
}

?>