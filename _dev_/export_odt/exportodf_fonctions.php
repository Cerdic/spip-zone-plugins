<?

function env2url($env){
	if (is_string($env)) $env = unserialize($env);
	$params = "";
	foreach($env as $key=>$value)
		if (!in_array($key,array('fond','recurs')))
		$params .= (strlen($params)?"&":"") . "$key=".urlencode($value);
	return $params;
}

function spip2odt($env){
	include_spip('inc/odt_api');
	if (is_string($env)) $env = unserialize($env);
	if (isset($env['fond'])) unset($env['fond']);
	if (isset($env['page'])) unset($env['page']);
	if (isset($env['recurs'])) unset($env['recurs']);

	$template = 'article.odt';
	if (isset($env['template'])){
		$template = $env['template'];
		unset($env['template']);
	}
	$nom_fichier = 'export';
	if (isset($env['nom_fichier'])){
		$nom_fichier = $env['nom_fichier'];
		unset($env['nom_fichier']);
	}
	include_spip("inc/charsets");
	$nom_fichier = preg_replace(",[^\w],","_",translitteration($nom_fichier)).".odt";
	$nom_fichier = preg_replace(",_[_]+,","_",$nom_fichier).".odt";
	
	$template = find_in_path("templates/$template");
	if (!strlen($template))
		return;
	$unzip = spipodf_unzip($template);
	// styliser
	spip2odt_styliser_contenu($unzip,$env);
	
	
	$odt = spipodf_zip($unzip,_DIR_TMP . $nom_fichier);
	return $odt;
}

?>