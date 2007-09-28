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
include_spip('cfg_options');
if((_DIR_RACINE =='') && lire_config('honeypot/httpbl/apikey')) {
  include_spip('inc/httpbl'); 
  $info = httpbl_test($_SERVER['REMOTE_ADDR'],lire_config('honeypot/httpbl/apikey'));
  //pour le test
  //$info = httpbl_test('127.1.40.1',lire_config('honeypot/httpbl/apikey'));
  $config = lire_config('honeypot/httpbl');
  if($info) {
	if(min(intval($config['type'.$info['type'].'_threat'],255)) <=  $info['threat']){
	  if($config['type'.$info['type'].'_filter'] == 'bloquer'){
		include_spip('inc/headers');
		http_status(403);
		include_spip('public/assembler');
		echo recuperer_fond("fonds/403",array(
											  'ip'=>$_SERVER['REMOTE_ADDR'],
											  'type'=>_T('honeypothttpbl:type'.$info['type']),
											  'threat'=>$info['threat'],
											  'age'=>$info['age'],
											  ));
		//log pour l'instant, TODO faire mieux
		spip_log("bloquer ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
		exit();
	  } else if($config['type'.$info['type'].'_filter'] == 'tohoneypot') {
		include_spip('inc/headers');
		spip_log("envoyer vers le pot de miel ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
		redirige_par_entete($GLOBALS['meta']['adresse_site'].'/'.lire_config('honeypot/hpfile').'.php');
	  }
	}
  }
 }


?>
