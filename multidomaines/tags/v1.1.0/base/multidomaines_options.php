<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
//$GLOBALS['dossier_squelettes'] = 'squelettes/'. $_SERVER['HTTP_HOST'];

$hostArray = explode('.',$_SERVER['HTTP_HOST']);

if (count($hostArray)==1) return $return = $hostArray;
$extention = array_pop($hostArray);
	
do
{
	$return[] = $GLOBALS['meta']['multidomaines_squelettes'].'/'.implode('.',$hostArray).'.'.$extention.'.'.$_SERVER["SERVER_PORT"];
	$return[] = $GLOBALS['meta']['multidomaines_squelettes'].'/'.implode('.',$hostArray).'.'.$_SERVER["SERVER_PORT"];
	array_shift($hostArray);
}while (count($hostArray)>0);

$hostArray = explode('.',$_SERVER['HTTP_HOST']);

if (count($hostArray)==1) return $return = $hostArray;
$extention = array_pop($hostArray);
	
do
{
	$return[] = $GLOBALS['meta']['multidomaines_squelettes'].'/'.implode('.',$hostArray).'.'.$extention;
	$return[] = $GLOBALS['meta']['multidomaines_squelettes'].'/'.implode('.',$hostArray);
	array_shift($hostArray);
}while (count($hostArray)>0);

$GLOBALS['dossier_squelettes'] = ((strlen($GLOBALS['dossier_squelettes'])>0)?$GLOBALS['dossier_squelettes'].':':null).implode(':',$return);




function autoriser_rubrique_host_modifierextra_dist($faire, $type, $id, $qui, $opt){
	return $opt['contexte']['id_rubrique'] == trim(sql_getfetsel('id_secteur','spip_rubriques','id_rubrique = "'.$opt['contexte']['id_rubrique'].'"',null,null,1)); 
}

function autoriser_rubrique_host_voirextra_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifierextra', $type, $id, $qui, $opt);
}

?>
