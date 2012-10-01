<?php

function uploadify_header_prive($flux) {
	
	global $connect_statut,$connect_toutes_rubriques;

	$id_article=intval(_request('id_article'));

	if (_request('exec') == 'articles' AND autoriser('modifier','article',$id_article)) {

		$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('uploadify.css').'" media="all" />'."\n";
		$flux .= "<script type='text/javascript' src='".find_in_path('jquery.uploadify.v2.1.4.min.js')."'></script>\n";
		$flux .= "<script type='text/javascript' src='".find_in_path('swfobject.js')."'></script>\n";


		$jsFile = generer_url_public('uploadify.js','id_article='.$id_article);

		$flux .= "<script type='text/javascript' src='$jsFile'></script>\n";
	}
	return $flux;
/*
if ((_request('exec') == 'articles')  AND ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"])) {
	$session = session_id();
	$dir_plugin = _DIR_PLUGIN_UPLOADIFY;
	$js = find_in_path('jquery.uploadify.v2.1.4.min.js');
	$css = $dir_plugin.'uploadify.css';
	$swfobject = $dir_plugin.'swfobject.js';
	$url_upload = '../?page=uploadify';
	//'scriptData':{'exec':'uploadify_upload','id_article':$id_article},
	//$type = "article";
	$id_article=intval(_request('id_article'));
	$exec = _request('exec');


// si cfg dispo, on charge les valeurs
if (function_exists(lire_config))  {   
  $sizeLimit = lire_config('uploadify/sizeLimit');
  $fileExt = lire_config('uploadify/fileExt');
  $simUploadLimit = lire_config('uploadify/simUploadLimit');
}

if (!$sizeLimit || $sizeLimit == '_' || $sizeLimit == '') $sizeLimit = '2097152' ;
if (!$fileExt || $fileExt == '_' || $fileExt == '') $fileExt = "*.jpg;*.gif;*.png" ;
if (!$simUploadLimit || $simUploadLimit == '_' || $simUploadLimit == ''  || $simUploadLimit == '0') $simUploadLimit_txt = "";
else $simUploadLimit_txt = "'simUploadLimit':'$simUploadLimit'," ;
if (!$debug || $debug == '_' || $debug == '') $debug = "false" ;

//,if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
//if ($exec=='articles') {
//if  ($GLOBALS['meta']["documents_$type"]=='non') {
//	OR !autoriser('joindredocument', $type, $id_article))
//		$flux = '';}
//	else {
	$flux .= "<!-- uploadify -->
	<link href='$css' rel='stylesheet' type='text/css' />
	<script type='text/javascript' src='$js'></script>\n";
	$flux .= "<script 'text/javascript' src='$swfobject'></script>\n";
	$flux .= "<script type= \"text/javascript\">
	$(document).ready(function() {
		$('#fileInput').uploadify({
		'uploader':'$dir_plugin"."uploadify.swf',
		'script':'$url_upload',
		'scriptData':{'id_article':$id_article},
		'cancelImg':'$dir_plugin"."cancel.png',
		'multi':true,
		'displayData': 'speed',
    	'buttonText':   'Parcourir',
    	'wmode' : 'transparent',
		'fileExt' : '$fileExt',
		'fileDesc' : '$fileExt',
		$simUploadLimit_txt
		'sizeLimit' : '$sizeLimit',
		'onQueueFull': function (evt, queueID, fileObj, response, data) {
         alert(response);
	  		}

	});
	});
	</script>";
	}

//	}

	return $flux;
*/
}

function uploadify_affiche_milieu($flux){
global $connect_statut,$connect_toutes_rubriques;

 $args = $flux['args'];
 $out = "";
 $id_article = $args['id_article'];

//if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
if (_request('exec') == 'articles' AND autoriser('modifier','article',$id_article)) {
//if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
//if ($args['exec']=='articles') {
	if  ($GLOBALS['meta']["documents_$type"]=='non'
		OR !autoriser('joindredocument', $type, $id_article))
			$out = '';
	else {
	$bouton = _T('uploadify:titre_uploadify');
	if(function_exists('bouton_block_depliable')) 
		$out .= debut_cadre_enfonce(_DIR_PLUGIN_UPLOADIFY.'images/uploadify-24.png', true, "", 
			bouton_block_depliable($bouton,$flag === 'ajax','uploadify'))
			. debut_block_depliable($flag === 'ajax','uploadify');
  	else 
  	  	$out .= debut_cadre_enfonce(_DIR_PLUGIN_UPLOADIFY.'images/uploadify-24.png', true, "", "");
	
	$out .= '<div>'._T('uploadify:texte_uploadify_article').'</div>';
	$out .= '<div>
	<div style="float:left;width:150px;"><input type="file" id="fileInput" name="fileInput" /></div>
  	<div style="float:left;padding:5px 8px 0px 8px;height:25px;border:1px #CCCCCC solid;font-size:120%;"><a href="javascript:$(\'#fileInput\').uploadifyUpload();" class="icone36"><span>'._T('uploadify:texte_boutonupload').'</span></a></div>
	<div style="float:left;padding:5px 8px 0px 8px;height:25px;border:1px #CCCCCC solid;font-size:120%;margin-left:5px"><a href="javascript:$(\'#fileInput\').uploadifyClearQueue();" class="icone36"><span>'._T('uploadify:texte_cancelupload').'</span></a></div>
	<div style="clear:both;border:1px #CCCCCC solid;font-size:120%;padding:5px 8px 0px 8px;height:25px;width:120px"><a href="" class="icone36"><span>Actualiser la page</span></a></div>
	</div>';

	$out .= fin_block()
			. fin_cadre_enfonce(true);
		}	
	$flux['data'].= $out;
	}
	return $flux;	
}
?>
