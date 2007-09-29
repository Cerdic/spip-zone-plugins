<?php

  //mailcrypt depuis couteau suisse (original de Paolo?)
@define('_httpbl_mailcrypt_AUTORISE', '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
@define('_httpbl_mailcrypt_REGEXPR', ',\b['._httpbl_mailcrypt_AUTORISE.']*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._httpbl_mailcrypt_AUTORISE.']*)?,');

function honeypot_affichage_final($flux) {
  include_spip('inc/httpbl'); 
  $info = httpbl_test($_SERVER['REMOTE_ADDR'],lire_config('honeypot/httpbl/apikey'));
//  $info = httpbl_test('127.1.40.1',lire_config('honeypot/httpbl/apikey'));
  $config = lire_config('honeypot/httpbl');
  if(min(intval($config['type'.$info['type'].'_threat']),255) <=  $info['threat']){
	if(($config['type'.$info['type'].'_filter'] == 'cacheremail') || ($config['type'.$info['type'].'_filter'] == 'cachertout')){
	  $flux = preg_replace(_httpbl_mailcrypt_REGEXPR, sinon($config['remplacement_email'],'NOSPAM'), $flux);	
		//log pour l'instant, TODO faire mieux
		spip_log("cacher les emails à ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');

	}
  }

  return $flux;

}

?>