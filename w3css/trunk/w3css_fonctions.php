<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// changer le prefixe w3- à la volée dans le CSS généré
function w3css_namespace($fond){

  $ns = substr(lire_config('w3css/namespace') ,0,5); // 5 caractères max

	if ( $ns != 'w3-') {
		$fond = str_replace('w3-', $ns, $fond);
	}
  return $fond;

}
