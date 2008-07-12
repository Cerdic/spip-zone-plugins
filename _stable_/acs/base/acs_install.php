<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

function acs_install($action){
  switch ($action)
  {
    case 'test':
      return isset($GLOBALS['meta']['acsOnglets1']); // TODO: remove any Model's components dependency
      break;

    case 'install':
      acs_set_default_values();
      break;

    case 'uninstall':
      acs_reset_vars();
      break;
  }
}


function acs_set_default_values() {// TODO: remove any Model's components dependency
  $def = array('acsModel' => 'cat',
								'acsOnglets1' => 'sommaire',
	    					'acsOnglets2' => 'resume',
  							'acsOnglets3' => 'plan',
  							'acsOnglets4' => 'sites',
  							'acsOnglets5' => 'forums',
  							'acsOngletsFondColor' => 'ffffff',
  							'acsOngletsBordColor' => 'dfdfdf',
    						'acsOngletsCouleurInactif' => 'efefef',
    						'acsOngletsCouleurSurvol' => 'ffffff',
  /*
                '' => '',
                '' => ''
*/
                );
  foreach($def as $var=>$value) {
    ecrire_meta($var, $value);
  }
  ecrire_metas();
}
function acs_reset_vars() {
  spip_query("delete FROM spip_meta where left(nom,3)='acs'");
}
?>