<?php
// validation xhtml validator
include_spip('inc/distant');

function validateur_w3c_xhtml_validator_dist($action, $url= ""){
	$w3cvalidator='http://validator.w3.org/check?uri=%s';
	$urlvalidator=str_replace('%s',urlencode($url),$w3cvalidator);
	
	switch ($action){
		case 'infos':
			return "<a href='http://validator.w3.org/'>W3C XHTML Validator</a>";
			break;
		case 'test':
			$test = recuperer_page($urlvalidator);
			if (preg_match('/passed validation/is',$test))
				$erreurs=0;
			else{
				$erreurs=1;
				if (preg_match('/([0-9]*)\s+error[s]?.*/is',$test,$regs))
					$erreurs=intval($regs[1]);
			}
			$texte = "Erreurs : $erreurs";
			return array($erreurs==0,$erreurs,$texte);
			break;
		case 'visu':
			include_spip('inc/headers');
			redirige_par_entete($urlvalidator);
			break;
	}
	return false;
}

?>