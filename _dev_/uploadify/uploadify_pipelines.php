<?php
function uploadify_ajouterBoutons($boutons_admin) {
		// si on est admin ou admin restreint
		if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
		//AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
			$boutons_admin['naviguer']->sousmenu['uploadify_admin']= new Bouton(
			"../"._DIR_PLUGIN_UPLOADIFY."/uploadify-24.png",  // icone
			_T('uploadify:uploadify')	// titre
			);
		}
		return $boutons_admin;
}


function uploadify_header_prive($flux) {
global $connect_statut,$connect_toutes_rubriques;

$dir_plugin = _DIR_PLUGIN_UPLOADIFY;
$js = find_in_path('jquery.uploadify.js');
$css = $dir_plugin.'uploadify.css';
$url_upload = '';
$type = 'article';
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

//if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
if ($exec=='articles') {
if  ($GLOBALS['meta']["documents_$type"]=='non'
	OR !autoriser('joindredocument', $type, $id_article))
		$flux = '';
	else {
	$flux .= "<!-- uploadify -->
	<link href='$css' rel='stylesheet' type='text/css' />
	<script type='text/javascript' src='$js'></script>\n";
	
	$flux .= "<script type= \"text/javascript\">
	$(document).ready(function() {
		$('#fileInput').fileUpload({
		'uploader':'$dir_plugin"."uploader.swf',
		'script':'$url_upload',
		'cancelImg':'$dir_plugin"."cancel.png',
		'scriptData':{'exec':'uploadify_upload','id_article':$id_article},
		'multi':true,
		'displayData': 'speed',
    	'buttonText':   'Parcourir',
    	'wmode' : 'transparent',
		'fileExt' : '$fileExt',
		'fileDesc' : '$fileExt',
		$simUploadLimit_txt
		'sizeLimit' : '$sizeLimit',
		'onAllComplete': function(){
      		alert('complete'),
			location.reload()
	  		}

	});
	});
	</script>";
	}
	}
	return $flux;
}

function uploadify_affiche_milieu($flux){
global $connect_statut,$connect_toutes_rubriques;

 $args = $flux['args'];
 $out = "";
 $id_article = $args['id_article'];
 $type = "article";

//if ($GLOBALS['connect_statut'] == "0minirezo" || $GLOBALS["connect_toutes_rubriques"]) {
if ($args['exec']=='articles') {
if  ($GLOBALS['meta']["documents_$type"]=='non'
	OR !autoriser('joindredocument', $type, $id_article))
		$out = '';
	else {
	$bouton = _T('uploadify:titre_uploadify');
	$out .= debut_cadre_enfonce(_DIR_PLUGIN_UPLOADIFY.'uploadify-24.png', true, "", 
			bouton_block_depliable($bouton,$flag === 'ajax','uploadify'))
			. debut_block_depliable($flag === 'ajax','uploadify');
	$out .= '<div>'._T('uploadify:texte_uploadify_article').'</div>';
	$out .= '<div>
	<div style="float:left;width:100px;"><input type="file" id="fileInput" name="fileInput" /></div>
  	<div style="float:right;padding:10px;"><a href="javascript:$(\'#fileInput\').fileUploadStart();">'._T('uploadify:texte_boutonupload').'</a> | <a href="javascript:$(\'#fileInput\').fileUploadClearQueue();">'._T('uploadify:texte_cancelupload').'</a></div>
</div>';

	$out .= fin_block()
			. fin_cadre_enfonce(true);
	}
	$flux['data'].= $out;
	}
	return $flux;	
}
?>