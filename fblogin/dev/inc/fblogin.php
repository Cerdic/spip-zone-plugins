<?php
/**
 * Plugin fblogin
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

@define('_fblogin_LOG', true);

/**
 * Ajout au formulaire de login
 *
 * @param string $texte
 * @param array $contexte
 * @return string
 */
function fblogin_login_form($texte,$contexte){

$scriptfblogin = "";

	$texte .= "<div id='fb-root'></div>
      <script src='http://connect.facebook.net/fr_FR/all.js'>
      </script>
      <script>
      window.fbAsyncInit = function() {
         FB.init({ 
            appId:'_FB_APP_ID', cookie:true, 
            status:true, xfbml:true 
         });
        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
      }
      </script>
      <fb:login-button perms='email' onlogin='FB_JS.reload();'>
         Login with Facebook
      </fb:login-button>";
      
     
	return $texte;

	
}




/**
 * Logs pour fblogin, avec plusieurs niveaux pour le debug (1 a 3)
 *
 * @param mixed $data : contenu du log
 * @param int(1) $niveau : niveau de complexite du log
 * @return null
**/
function fblogin_log($data, $niveau=1){
	if (!defined('_fblogin_LOG') OR _fblogin_LOG < $niveau) return;
	spip_log('fblogin: '.$data, 'fblogin');
}




?>