<?php
/**
 * Insertion dans le pipeline insert_head
 * 
 * @param array $flux
 * @return array $flux (le flux modifie)
 */
function gfc_insert_head($flux){
	if(function_exists('lire_config')){
		$key = lire_config('gfc/consumer_id') ? lire_config('gfc/consumer_id') : _GFC_CONSUMER_ID;
		$cookie_name = lire_config('gfc/cookie_name') ? lire_config('gfc/cookie_name') : _GFC_COOKIE_NAME;
	}else{
		$key = _GFC_CONSUMER_ID;
	}
	$cookie_name = 'fcauth'.$key;
	
	$flux .= "<script type='text/javascript' src='http://www.google.com/jsapi'></script>";
	$flux .= "<script type='text/javascript'>google.load('friendconnect', '0.8');</script>";
    $flux .= 
	"<script type='text/javascript'>    
	  google.friendconnect.container.setParentUrl('/' /* location of rpc_relay.html and canvas.html */);
	  google.friendconnect.container.initOpenSocialApi({
		site: '".$key."',
		onload: function() {
			if (!window.timesloaded) window.timesloaded = 1;
			else window.timesloaded++;
			if (window.timesloaded > 1) {
				window.top.location.href = '/spip.php?action=gfc_auth';
			}   
		}
	  });
	  function gfc_signout(logout_url){
		  //we add this line to manage our login synchronisation, and wether we display the google login button or not
		  document.cookie = 'name=".$cookie_name.";path=\"/\";expires=Thu, 01-Jan-1970 00:00:01 GMT;';
		  google.friendconnect.requestSignOut();
		  if(logout_url){
		  	window.top.location.href = logout_url;
		  }
	  };
	</script>";
	return $flux;
}
?>
