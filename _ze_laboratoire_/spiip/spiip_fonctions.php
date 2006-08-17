<?
function params_spip_to_params_html($texte){
	//transforme les valeurs de parm ecrie de valeur=xx|valeur2=xxx en <param name='valeur1' value='xxx'>...

	$texte = str_replace("=","' value='",$texte);
	$texte = str_replace("|","' />\n<param name='",$texte);
	$texte = ereg_replace("$","' />\n",$texte);
	$texte = ereg_replace("^","<param name='",$texte);
	
	return $texte;
	}
	
?>