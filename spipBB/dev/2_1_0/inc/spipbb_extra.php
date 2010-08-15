<?php
/*
|
*/

spipbb_log("included",3,__FILE__);

# base  script :inc/extra.php : function extra_recup_saisie()
# qq modifs, retourne un array()
// recupere les valeurs postees pour reconstituer l'extra
// http://doc.spip.org/@extra_recup_saisie
function spipbb_extra_recup_saisie($type, $c=false) {
	$champs = $GLOBALS['champs_extra'][$type];
	$extra = Array();
	if (is_array($champs)) {
		foreach($champs as $champ => $config) {
			if (($val = _request("$champ",$c)) !== NULL) {
				list($style, $filtre, , $choix,) = explode("|", $config);
				list(, $filtre) = explode(",", $filtre);
				switch ($style) {
				case "multiple":
					$choix =  explode(",", $choix);
					$multiple = array();
					for ($i=0; $i < count($choix); $i++) {
						$val2 = _request("$champ$i",$c);
						if ($filtre && function_exists($filtre))
							 $multiple[$i] = $filtre($val2);
						else
							$multiple[$i] = $val2;
					}
					$extra[$champ] = $multiple;
					break;
	
				case 'case':
				case 'checkbox':
					if (_request("{$champ}_check") == 'on')
						$val = 'true';
					else
						$val = 'false';
					// pas de break; on continue
	
				default:
					#traiter date prim enreg.
					if($champ=='date_crea_spipbb' && ($val=='' || $val==false )) {
						$val=date('Y-m-d H:i:s');
					}
					if($champ=='refus_suivi_thread' && is_array($val)) {
						$val = join(',',$val);
					}
					if ($filtre && function_exists($filtre))
						$extra[$champ] = $filtre($val);
					else
						$extra[$champ] = $val;	
					break;
				}
			}
		}
	}
	return $extra;
}
?>
