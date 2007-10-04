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

function httpbl_send403($info) {
  include_spip('inc/headers');
  http_status(403);
  include_spip('public/assembler');
  echo recuperer_fond("fonds/httpbl_403",array(
										'ip'=>$_SERVER['REMOTE_ADDR'],
										'type'=>_T('honeypothttpbl:type'.$info['type']),
										'threat'=>$info['threat'],
										'age'=>$info['age'],
										));
  exit();
}

include_spip('cfg_options');

if((_DIR_RACINE =='') && 
   (strpos($_SERVER['PHP_SELF'],_DIR_RESTREINT_ABS) === false) &&
   lire_config('honeypot/httpbl/apikey')) {
  include_spip('inc/httpbl'); 
  $info = httpbl_test($_SERVER['REMOTE_ADDR'],lire_config('honeypot/httpbl/apikey'));
  //pour le test
  //  $info = httpbl_test('127.1.1.0',lire_config('honeypot/httpbl/apikey'));
  $config = lire_config('honeypot/httpbl');
  if($info) {
	if(min(intval($config['type'.$info['type'].'_threat']),255) <=  $info['threat']){
	  if($config['type'.$info['type'].'_filter'] == 'bloquer'){
		//log pour l'instant, TODO faire mieux
		spip_log("bloqué ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
		httpbl_send403($info);		
	  } else if($config['type'.$info['type'].'_filter'] == 'tohoneypot') {
		include_spip('inc/headers');
		//log pour l'instant, TODO faire mieux
		spip_log("envoyé vers le pot de miel ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
		redirige_par_entete($GLOBALS['meta']['adresse_site'].'/'.preg_replace('/\.php/$','',lire_config('honeypot/hpfile')).'.php');
	  } else if(count($_POST) &&
				($config['type'.$info['type'].'_filter'] == 'cacherforum' || $config['type'.$info['type'].'_filter'] == 'cachertout' )) {
		// champs de formulaires a visiter (depuis couteau suisse)
		//    un message en forum : texte, titre, auteur
		//    un message a un auteur : texte_message_auteur_XX, sujet_message_auteur_XX, email_message_auteur_XX
		$spam_POST_reg = ',^(texte|titre|sujet|auteur|email),i';
	
		// on regarde si c'est un post qui nous interesse
		foreach (array_keys($_POST) as $key)
		  if (preg_match($spam_POST_reg, $key)) {
			//log pour l'instant, TODO faire mieux
			spip_log("caché forum ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
			httpbl_send403($info);		
			break;
		  }
	  }
	} else if($config['loglevel'] == 'all') {
	  //log pour l'instant, TODO faire mieux
	  spip_log("non filtré ".$_SERVER['REMOTE_ADDR']." parce que ".$info['raw'],'httpbl');
	}
  }
 }


?>
