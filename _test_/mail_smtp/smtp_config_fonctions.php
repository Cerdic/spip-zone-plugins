<?php

function smtp_affiche_milieu($flux){
	$exec = $flux['args']['exec'];
	$out = "";
	if ($exec=='config_fonctions'){	
		global $spip_lang_right, $spip_lang_left;
	
		$out .= debut_cadre_trait_couleur(_DIR_PLUGIN_SMTP."images/smtp-24.gif", true, "", _T('smtp:info_smtp'));
	
		// Masquer un eventuel password authentifiant
		if ($http_proxy = $GLOBALS['meta']["http_proxy"]) {
			include_spip('inc/distant');
			$http_proxy=entites_html(no_password_proxy_url($http_proxy));
		}
		
		$out .= "<div class='verdana2'>";
		$out .= _T('smtp:texte_smtp');
		$out .= "</div>";
		
		$changed = false;
		if (_request('smtp_host')!==NULL){
			$smtp_host = isset($GLOBALS['meta']['smtp_host'])?$GLOBALS['meta']['smtp_host']:"";
			$smtp_port = isset($GLOBALS['meta']['smtp_port'])?$GLOBALS['meta']['smtp_port']:"25";
			$smtp_auth = isset($GLOBALS['meta']['smtp_auth'])?$GLOBALS['meta']['smtp_auth']:"non";
			$smtp_username = isset($GLOBALS['meta']['smtp_username'])?$GLOBALS['meta']['smtp_username']:"";
			$smtp_password = isset($GLOBALS['meta']['smtp_password'])?$GLOBALS['meta']['smtp_password']:"";
			$changed = _request('smtp_host')!=$smtp_host;
			$changed = $changed | (_request('smtp_port')!=$smtp_port);
			$changed = $changed | (_request('smtp_auth')!=$smtp_auth);
			$changed = $changed | (_request('smtp_username')!=$smtp_username);
			$changed = $changed | (_request('smtp_password')!=$smtp_password);
			ecrire_meta('smtp_host',_request('smtp_host'));
			ecrire_meta('smtp_port',_request('smtp_port'));
			ecrire_meta('smtp_auth',_request('smtp_auth'));
			ecrire_meta('smtp_username',_request('smtp_username'));
			ecrire_meta('smtp_password',_request('smtp_password'));
			ecrire_metas();
		}

		$smtp_host = isset($GLOBALS['meta']['smtp_host'])?$GLOBALS['meta']['smtp_host']:"";
		$smtp_port = isset($GLOBALS['meta']['smtp_port'])?$GLOBALS['meta']['smtp_port']:"25";
		$smtp_auth = isset($GLOBALS['meta']['smtp_auth'])?$GLOBALS['meta']['smtp_auth']:"non";
		$smtp_username = isset($GLOBALS['meta']['smtp_username'])?$GLOBALS['meta']['smtp_username']:"";
		$smtp_password = isset($GLOBALS['meta']['smtp_password'])?$GLOBALS['meta']['smtp_password']:"";


		$out .= "<div class='verdana2'>";
		$out .= "<label for='smtp_host'>"._T('smtp:smtp_host')."</label><br/>";
		$out .= "<input type='text' name='smtp_host' id='smtp_host' value='$smtp_host' size='40' class='forml' />";
		$out .= "</div>";
		
		$out .= "<div class='verdana2'>";
		$out .= "<label for='smtp_port'>"._T('smtp:smtp_port')."</label>";
		$out .= "<input type='text' name='smtp_port' id='smtp_port' value='$smtp_port' size='40' class='forml' />";
		$out .= "</div>";

		$out .= "<p><div class='verdana2'>";
		$out .= "<input type='checkbox' name='smtp_auth' id='smtp_auth' ".(($smtp_auth=='oui')?"checked='checked' ":"")."value='oui' />";
		$out .= "<label for='smtp_auth'>"._T('smtp:smtp_auth')."</label>";
		$out .= "</div>";

		$out .= "<div class='verdana2'>";
		$out .= "<label for='smtp_username'>"._T('smtp:smtp_username')."</label><br/>";
		$out .= "<input type='text' name='smtp_username' id='smtp_username' value='$smtp_username' size='40' class='forml' />";
		$out .= "</div>";
	
		$out .= "<div class='verdana2'>";
		$out .= "<label for='smtp_password'>"._T('smtp:smtp_password')."</label><br/>";
		$out .= "<input type='password' name='smtp_password' id='smtp_password' value='$smtp_password' size='40' class='forml' />";
		$out .= "</div></p>";
	
		$out .= "<div style='text-align:$spip_lang_right'><input type='submit' name='Tester_smtp' value='"._T('bouton_valider')."' CLASS='fondo'></div>";
		
		if (($changed) OR (_request('Tester_smtp')!==NULL)){
			$out .= "<div class='verdana2'>";
			$out .= _T('smtp:test_mail',array('email'=>$GLOBALS['meta']["email_webmaster"]))."<br/>";
			$out .= " <img src='".generer_url_public('smtp_test_envoi_mail')."' alt='"._T("smtp:test_mail_echec")."' />";
		
			$out .= "</div>";
		}
		
		$out .= fin_cadre_trait_couleur(true);
		$out .= "<p>";
	}
	$flux['data'].=$out;
	return $flux;
}

?>