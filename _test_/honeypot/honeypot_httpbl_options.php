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

  /*if(_DIR_RACINE =='' && isset(lire_config('honeypot/httpbl/apikey'))) {
  include_spip('inc/httpbl'); 
  //$info = httpbl_test($_SERVER['REMOTE_ADDR'],lire_config('honeypot/httpbl/apikey'));
  //pour le test
    $info = httpbl_test('89.149.227.31',lire_config('honeypot/httpbl/apikey'));
  if($info) {
//TODO:
//- un systeme de regle plus avance en fonction du type de visiteur
    if($info['type'] > 0){
	  
	  include_spip('inc/headers');
	  http_status(403);
	  include_spip('public/assembler');
	  echo recuperer_fond("fonds/403",array(
											'ip'=>$_SERVER['REMOTE_ADDR'],
											'type'=>_T('honeypothttpbl:type'.$info['type']),
											'threat'=>$info['threat'],
											'age'=>$info['age'],
											));
	  
	  exit();
	}
  }
  }*/


?>
