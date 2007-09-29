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
  // This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

									   'type0' => 'search engine',
									   'type1' => 'suspicious',
									   'type2' => 'harvester',
									   'type3' => 'suspicious & harvester',
									   'type4' => 'comment spammer',
									   'type5' => 'suspicious & comment spammer',
									   'type6' => 'harvester & comment spammer',
									   'type7' => 'suspicious & harvester & comment spammer',

									   'cfg_titre' => 'Black List access restrictions',
									   'cfg_descriptif' => 'The honeypot plugin can use the database provided by the Project Honey Pot to limit the access to the site to suspicious visitors.

 See [the HTTP Black List documentation->http://projecthoneypot.org/httpbl_configure.php] for more information. 

To be able to use this feature on your site, you first have to [get an API key from the project honey pot->http://www.projecthoneypot.org/httpbl_configure.php].',
									   'cfg_apikey' => 'P.H.Pot provided access key: ',
									   'cfg_sivisiteur' => 'If the visitor is considered a :',
									   'cfg_bloquer' => 'refuse access to the site',
									   'cfg_rien' => 'do nothing',
									   'cfg_tohoneypot' => 'send it to the honeypot',
									   'cfg_cacheremail' => 'hide email address',
									   'cfg_cacherforum' => 'hide the forums',
									   'cfg_cachertout' => 'hide the forums and the email address',
									   'cfg_minthreat' => 'when the thread is greater than :',
									   'cfg_remplacement_email' => 'Hide the email addresses by replacing them with: ',
									   'cfg_log' => 'Log detected access :',
									   'cfg_log_all' => 'All known access type',
									   'cfg_log_blocked' => 'Only filtered access'
									   );

?>
