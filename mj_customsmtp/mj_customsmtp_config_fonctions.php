<?php

function mj_customsmtp_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	$out = "";
	if ($exec=='config_fonctions'){
		global $spip_lang_right, $spip_lang_left;

		// Masquer un eventuel password authentifiant
		if ($http_proxy = $GLOBALS['meta']["http_proxy"]) {
			include_spip('inc/distant');
			$http_proxy=entites_html(no_password_proxy_url($http_proxy));
		}

	    $mj_customsmtp_enabled = '';
	    $mj_customsmtp_test = '';
	    $mj_customsmtp_test_address = '';
	    $mj_customsmtp_username = '';
	    $mj_customsmtp_password = '';

	    $errors = array ();
        $smtp_error = '';

		if (_request('mj_customsmtp_username')!==NULL)
		{
		    $mj_customsmtp_enabled = _request('mj_customsmtp_enabled');
		    $mj_customsmtp_test = _request('mj_customsmtp_test');
		    $mj_customsmtp_test_address = _request('mj_customsmtp_test_address');
		    $mj_customsmtp_username = _request('mj_customsmtp_username');
		    $mj_customsmtp_password = _request('mj_customsmtp_password');

		    if (empty ($mj_customsmtp_username))
		    {
		        $errors [] = 'mj_customsmtp_username';
		    }

		    if (empty ($mj_customsmtp_password))
		    {
		        $errors [] = 'mj_customsmtp_password';
		    }

		    if ($mj_customsmtp_test && empty ($mj_customsmtp_test_address))
		    {
		        $errors [] = 'mj_customsmtp_test_address';
		    }

		    ecrire_meta('mj_customsmtp_enabled',_request('mj_customsmtp_enabled'));
		    ecrire_meta('mj_customsmtp_test',_request('mj_customsmtp_test'));
		    ecrire_meta('mj_customsmtp_test_address',_request('mj_customsmtp_test_address'));
		    ecrire_meta('mj_customsmtp_username',_request('mj_customsmtp_username'));
		    ecrire_meta('mj_customsmtp_password',_request('mj_customsmtp_password'));

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
    		        ecrire_meta ('mj_customsmtp_host', $configs [$i] [0].$host);
    		        ecrire_meta ('mj_customsmtp_port', $configs [$i] [1]);
    		    }
    		    else
    		    {
    		        $smtp_error = sPrintF (_T ('mj_customsmtp:mj_error_autoconfig'), $errno, $errstr);
    		    }
		    }

			ecrire_metas ();

		    if (empty ($smtp_error) && $mj_customsmtp_test)
		    {
		        $GLOBALS ['meta'] ['mj_customsmtp_enabled'] = 1;

		        $envoyer_mail = charger_fonction ('envoyer_mail','inc');
		        $envoyer_mail ($mj_customsmtp_test_address, _T ('mj_customsmtp:mj_test_mail_subject'), _T ('mj_customsmtp:mj_test_mail_message'));

		        $GLOBALS ['meta'] ['mj_customsmtp_enabled'] = $mj_customsmtp_enabled;
		    }
		}
        else
        {
    		$mj_customsmtp_enabled = isset ($GLOBALS['meta'] ['mj_customsmtp_enabled']) ? $GLOBALS['meta'] ['mj_customsmtp_enabled'] : '';
    		$mj_customsmtp_test = isset ($GLOBALS['meta'] ['mj_customsmtp_test']) ? $GLOBALS['meta'] ['mj_customsmtp_test'] : '';
    		$mj_customsmtp_test_address = isset ($GLOBALS['meta'] ['mj_customsmtp_test_address']) ? $GLOBALS['meta'] ['mj_customsmtp_test_address'] : '';
    		$mj_customsmtp_username = isset ($GLOBALS['meta'] ['mj_customsmtp_username']) ? $GLOBALS['meta'] ['mj_customsmtp_username'] : '';
    		$mj_customsmtp_password = isset ($GLOBALS['meta'] ['mj_customsmtp_password']) ? $GLOBALS['meta'] ['mj_customsmtp_password'] : '';
        }

	    if ($mj_customsmtp_enabled)
	    {
	        $mj_customsmtp_enabled = 'checked="checked"';
	    }

	    if ($mj_customsmtp_test)
	    {
	        $mj_customsmtp_test = 'checked="checked"';
	    }

	    if (count ($errors))
	    {
	        $out .= '<p class="verdana2" style="color: red;">'._T ('mj_customsmtp:mj_customsmtp_form_error').'</p>';
	    }
	    elseif (! empty ($smtp_error))
	    {
	        $out .= '<p class="verdana2" style="color: red;">'.$smtp_error.'</p>';
	    }

	    $out .= '<form action="'.$_SERVER ['REQUEST_URI'].'#smtp" method="post">';

	    $out .= debut_cadre_relief ("", true, "", _T('mj_customsmtp:mj_global_settings'));

        $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mj_customsmtp_enabled'>"._T('mj_customsmtp:mj_customsmtp_enabled')."</label>";
	    $out .= "<input type='checkbox' name='mj_customsmtp_enabled' id='mj_customsmtp_enabled' $mj_customsmtp_enabled size='40' class='forml' style='width: 10px; display: inline;' value='1' />";
	    $out .= "</div>";

	    $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mj_customsmtp_test'>"._T('mj_customsmtp:mj_customsmtp_test')."</label>";
	    $out .= "<input type='checkbox' name='mj_customsmtp_test' id='mj_customsmtp_test' $mj_customsmtp_test size='40' class='forml' style='width: 10px; display: inline;' value='1' />";
	    $out .= "</div>";

	    $out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
	    $out .= "<label for='mj_customsmtp_test_address'";

	    if (in_array ('mj_customsmtp_test_address', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mj_customsmtp:mj_customsmtp_test_address')."</label><br/>";
	    $out .= "<input type='text' name='mj_customsmtp_test_address' id='mj_customsmtp_test_address' value='$mj_customsmtp_test_address' size='40' class='forml' />";
	    $out .= "</div>";

	    $out .= fin_cadre_relief(true);

	    $out .= debut_cadre_relief ("", true, "", _T('mj_customsmtp:mj_settings'));

		$out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
		$out .= "<label for='mj_customsmtp_username'";

	    if (in_array ('mj_customsmtp_username', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mj_customsmtp:mj_customsmtp_username')."</label><br/>";
		$out .= "<input type='text' name='mj_customsmtp_username' id='mj_customsmtp_username' value='$mj_customsmtp_username' size='40' class='forml' />";
		$out .= "</div>";

		$out .= "<div class='verdana2' style='padding-bottom: 5px;'>";
		$out .= "<label for='mj_customsmtp_password'";

	    if (in_array ('mj_customsmtp_password', $errors))
	    {
	        $out .= ' style="color: red;" ';
	    }

        $out .= ">"._T('mj_customsmtp:mj_customsmtp_password')."</label><br/>";
		$out .= "<input type='text' name='mj_customsmtp_password' id='mj_customsmtp_password' value='$mj_customsmtp_password' size='40' class='forml' />";
		$out .= "</div></p>";

	    $out .= fin_cadre_relief(true);

	    $out .= '<p style="text-align: right;"><input type="submit" value="'._T ('bouton_valider').'" /></p>';
	    $out .= '</form>';

        $out = '<br /><a name="smtp"></a>'.debut_cadre_trait_couleur(_DIR_PLUGIN_MJ_CUSTOMSMTP."images/mailjet.ico", true, "", _T('mj_customsmtp:mj_customsmtp_title'))
                .   $out
                .  fin_cadre_trait_couleur(true);

        $flux ['data'] = $out;
    }

    return $flux;
}

?>