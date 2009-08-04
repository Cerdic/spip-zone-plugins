<?php 
function formulaires_configurer_multidomaines_charger_dist()
{
	return array('url'=> sql_getfetsel("valeur", "spip_meta", "nom ='multidomaines_url'"),'squelettes'=> sql_getfetsel("valeur", "spip_meta", "nom ='multidomaines_squelettes'") );
}

function formulaires_configurer_multidomaines_verifier_dist()
{
	$erreurs = array();
	if (strlen(_request('url'))==0)
		$erreurs['url']=_T("info_obligatoire");
	if (strlen(_request('squelettes'))==0)
		$erreurs['squelettes']=_T("info_obligatoire");
	return $erreurs;
}

function formulaires_configurer_multidomaines_traiter_dist()
{
	sql_replace('spip_meta',array('nom'=>'multidomaines_url','valeur'=>_request('url')));
	sql_replace('spip_meta',array('nom'=>'multidomaines_squelettes','valeur'=>_request('squelettes')));	
}
?>