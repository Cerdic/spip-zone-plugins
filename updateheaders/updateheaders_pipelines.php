<?php
	if (!defined("_ECRIRE_INC_VERSION")) return;
	
	include_spip('inc/config');

	define('UPDATEHEADERS_DEFAULT',	'Hidden');

	function updateheaders_affichage_entetes_final($entetes){
		//	Recovery of the backend data
		$default	=	lire_config('updateheaders/default_replace');
		if(!$default)
			$default	=	UPDATEHEADERS_DEFAULT;
		$powered	=	lire_config('updateheaders/hide_powered');
		$composed	=	lire_config('updateheaders/hide_composed');
		$advanced	=	lire_config('updateheaders/advanced');
		$expires_q	=	lire_config('updateheaders/expires_quantity');
		$expires_u	=	lire_config('updateheaders/expires_unity');
		
		//	Assignation of checkbox values
		if($powered)
			$entetes['X-Powered-By']	=	$default;
		if($composed)
			$entetes['Composed-By']		=	$default;
			
		//	Assignation of Expires values
		if($expires_q && $expires_u) {
			$expires_time		=	strtotime('+'.$expires_q.' '.$expires_u);
			$entetes['Expires']	=	gmdate('D, d M Y H:i:s', $expires_time).' GMT';
		}
		
		//	Assignation of advanced configuration
		if($advanced) {
			$tab_advanced		=	explode("\n", $advanced);
			foreach($tab_advanced as $key => $value) {
				unset($tab_advanced[$key]);
				$tab	=	explode(':', $value, 2);
				if(!is_array($tab))
					$tab	=	array($tab => UPDATEHEADERS_DEFAULT);
				else {
					if(trim($tab[1]) == '')
						$tab[1]	=	UPDATEHEADERS_DEFAULT;
				}
				if(!empty($tab[0]))
					$entetes[$tab[0]]	=	$tab[1];
			}
		}
		return $entetes;
	}
	
	function updateheaders_header_prive($flux) {
		$flux	.=	'<link rel="stylesheet" type="text/css" media="all" href="'.find_in_path('prive/themes/spip/css/updateheaders.css').'">';
		return $flux;
	}
?>