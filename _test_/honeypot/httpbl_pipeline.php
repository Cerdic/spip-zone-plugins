<?php
/*
 *   Plugin HoneyPot
 *   Copyright (C) 2007 Pierre Andrews
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

  //mailcrypt depuis couteau suisse (original de Paolo?)
@define('_httpbl_mailcrypt_AUTORISE', '\!\#\$\%\&\'\*\+\-\/\=\?\^\_\`\.\{\|\}\~a-zA-Z0-9');
@define('_httpbl_mailcrypt_REGEXPR', ',\b['._httpbl_mailcrypt_AUTORISE.']*@[a-zA-Z][a-zA-Z0-9-.]*\.[a-zA-Z]+(\?['._httpbl_mailcrypt_AUTORISE.']*)?,');

//on utilise le pipeline affichage final, en dehors du cache, pour enlever les emails de la page.
function honeypot_affichage_final($flux) {
  include_spip('inc/httpbl'); 
  $info = httpbl_test($_SERVER['REMOTE_ADDR'],lire_config('honeypot/httpbl/apikey'));
  //  $info = httpbl_test('127.1.40.1',lire_config('honeypot/httpbl/apikey'));
  $config = lire_config('honeypot/httpbl');
  if(min(intval($config['type'.$info['type'].'_threat']),255) <=  $info['threat']){
	if(($config['type'.$info['type'].'_filter'] == 'cacheremail') || ($config['type'.$info['type'].'_filter'] == 'cachertout')){
	  $flux = preg_replace(_httpbl_mailcrypt_REGEXPR, sinon($config['remplacement_email'],'NOSPAM'), $flux);	
	  if($config['stats'] == 'on') {
		$date = date("Y-m-d", time() - 1800);
		
		spip_query("INSERT IGNORE INTO spip_honeypot_stats (date,type,filtre) VALUES ('$date',".intval($info['type']).",5)");
		spip_query("UPDATE spip_honeypot_stats SET cnt = cnt+1,  threat = threat+".intval($info['threat'])." WHERE date='$date' AND type=".intval($info['type'])." AND filtre=5");
	  }
	  //log pour l'instant, TODO faire mieux
	  spip_log("caché les emails à ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
	}
  }

  return $flux;

}

?>
