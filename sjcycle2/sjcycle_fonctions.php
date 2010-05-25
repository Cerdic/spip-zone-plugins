<?php
function init_sjcycle_default($choice='fieldsname_list'){
	$sjcycle_default = array (
							array('fieldname' => 'sjcycle_tooltip', 'default_value' => 0,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_tooltip_carac', 'default_value' => 0,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_fancy', 'default_value' => 0,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_fx', 'default_value' => 'fade','preg_match_rule'=>null,"obligatoire"=>true),
							array('fieldname' => 'sjcycle_sync', 'default_value' => 1,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_speed', 'default_value' => 2000,'preg_match_rule'=>'`^0*([0-9]{3,5})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_timeout', 'default_value' => 4000,'preg_match_rule'=>'`^0*([0-9]{3,5})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_pause', 'default_value' => 1,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_random', 'default_value' => 0,'preg_match_rule'=>'`^(0|1)$`',"obligatoire"=>true),
							//array('fieldname' => 'sjcycle_prevnext', 'default_value' => 'null','preg_match_rule'=>null,"obligatoire"=>false),
							//array('fieldname' => 'sjcycle_pager', 'default_value' => 'null','preg_match_rule'=>null,"obligatoire"=>false),
							array('fieldname' => 'sjcycle_class', 'default_value' => 'dsjcycle','preg_match_rule'=>'`^\.?([a-z_-]{2,30}[^_-]+)$`i',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_width', 'default_value' => 400,'preg_match_rule'=>null,"obligatoire"=>false),
							array('fieldname' => 'sjcycle_height', 'default_value' => 400,'preg_match_rule'=>null,"obligatoire"=>false),
							array('fieldname' => 'sjcycle_margin', 'default_value' => 0,'preg_match_rule'=>'`^0*([0-9]{1,2})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_img_margin', 'default_value' => 0,'preg_match_rule'=>'`^0*([0-9]{1,2})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_background', 'default_value' => 'ffffff','preg_match_rule'=>'`^#?([a-f0-9]{6}|transparent)$`i',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_img_position', 'default_value' => 'center','preg_match_rule'=>null,"obligatoire"=>true),
							array('fieldname' => 'sjcycle_img_width', 'default_value' => 400,'preg_match_rule'=>'`^0*([0-9]{1,3})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_img_height', 'default_value' => 400,'preg_match_rule'=>'`^0*([0-9]{1,3})$`',"obligatoire"=>true),
							array('fieldname' => 'sjcycle_img_background', 'default_value' => 'ffffff','preg_match_rule'=>'`^#?([a-f0-9]{6}|transparent)$`i',"obligatoire"=>true)
						);
		$lenght_sjcycle_default = count($sjcycle_default);
		$return = array();
		
		if ($choice == 'fieldsname_list') {
			for($i=0;$i<$lenght_sjcycle_default;$i++){
				$return[] = $sjcycle_default[$i]['fieldname'];
			}
		}
		if ($choice == 'obligatoire_list') {
			for($i=0;$i<$lenght_sjcycle_default;$i++){
				if ($sjcycle_default[$i]['obligatoire']) {
					$return[] = $sjcycle_default[$i]['fieldname'];
				}
				
			}
		}
		if ($choice == 'default_value_list') {
			for($i=0;$i<$lenght_sjcycle_default;$i++){
				$return[$sjcycle_default[$i]['fieldname']] = $sjcycle_default[$i]['default_value'];
			}
		}
		if ($choice == 'preg_match_rule_list') {
			for($i=0;$i<$lenght_sjcycle_default;$i++){
				$return[$sjcycle_default[$i]['fieldname']] = $sjcycle_default[$i]['preg_match_rule'];
			}
		}
		
		return $return;
}

function checkRulz ($value,$rule){
	if(preg_match($rule,$value,$out)){
		return $out[1];
	} else {
		return "";
	}
}


function randomString($length = 8)
{
  $passe = "";
  $consonnes = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n", "p", "q", "r", "s", "t", "v", "z", "bl", "br", "cl", "cr", "ch", "dr", "fl", "fr"
, "gl", "gr", "pl", "pr", "qu", "sl", "sr");
  $voyelles = array("a", "e", "i", "o", "u", "ae", "ai", "au", "eu", "ia", "io", "iu", "oa", "oi", "ou", "ua", "ue", "ui");

  $nbrC = count($consonnes) - 1;
  $nbrV = count($voyelles) - 1;

  for ($i = 0; $i < $length; $i++)
    {
    $passe .= $consonnes[rand(0, $nbrC)] . $voyelles[rand(0, $nbrV)];
    }
	return substr($passe, 0, $length);
}
?>