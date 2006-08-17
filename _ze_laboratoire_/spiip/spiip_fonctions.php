<?
function params_spip_to_params_html($texte){
	//transforme les valeurs de param écries de maniere "valeur=xx|valeur2=xxx" en <param name='valeur1' value='xxx'>...

	$texte = str_replace("=","' value='",$texte);
	$texte = str_replace("|","' />\n<param name='",$texte);
	$texte = ereg_replace("$","' />\n",$texte);
	$texte = ereg_replace("^","<param name='",$texte);
	
	return $texte;
	}

function params_spip_to_attributs_html($texte){
	
	$texte = str_replace("=","='",$texte);
	$texte = str_replace("|","' ",$texte);
	
	
	
	return $texte."'";}

?>