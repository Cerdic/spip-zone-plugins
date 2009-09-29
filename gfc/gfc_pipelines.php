<?php

function gfc_insert_head($flux){
	$flux .= "<script src='http://www.google.com/jsapi'></script>";
	$flux .= "<script type='text/javascript'>google.load('friendconnect', '0.8');</script>";
    $flux .= 
	"<script type='text/javascript'>    
	  google.friendconnect.container.setParentUrl('/' /* location of rpc_relay.html and canvas.html */);
	  google.friendconnect.container.initOpenSocialApi({
		site: '".$GLOBALS['gfc']['consumer_id']."',
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
		  document.cookie = 'name=".$GLOBALS['gfc']['cookie_name'].";path=\"/\";expires=Thu, 01-Jan-1970 00:00:01 GMT;';
		  google.friendconnect.requestSignOut();
		  window.top.location.href = logout_url;
	  };
	</script>";
	return $flux;
}
?>
