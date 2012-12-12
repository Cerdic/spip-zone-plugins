<?php
// compat PHP4.
// on charge ecrire/inc/json pour disposer de json_encode 

if(!function_exists('json_encode')) {  
	include_spip("inc/json");
}




?>