<?php
/**
 * Plugin Kaltura
 * (c) 2008 Cedric MORIN, www.yterium.com
 *
 */

include_spip('inc/filtres');
$GLOBALS['kaltura_partner_id'] = table_valeur($GLOBALS['meta']['kaltura'],'partner_id');
$GLOBALS['kaltura_subp_id'] = table_valeur($GLOBALS['meta']['kaltura'],'subp_id');
$GLOBALS['kaltura_secret'] = table_valeur($GLOBALS['meta']['kaltura'],'secret');
$GLOBALS['kaltura_partner_name'] = $GLOBALS['meta']['nom_site'];

/**
 * Fonction pour recuperer une option specifiee ou la valeur par defaut sinon
 *
 * @param string $name
 * @param array $options
 * @param mixed $default
 * @return mixed
 */
function kaltura_option($name,$options,$default=null){
	$val = $default;
	if (isset($options[$name]))
		$val = $options[$name];
	return $val;
}

/**
 * Instancier un id de video kaltura
 *
 * $options permet de passer un 'id_auteur', 'nom', et 'titre' 'descriptif' 'tags' de la video
 * @param array $options
 * @return unknown
 */
function kaltura_instancie($options){

	$id_auteur = kaltura_option('id_auteur',$options,$GLOBALS['visiteur_session']['id_auteur']);
	$nom = kaltura_option('nom',$options,($id_auteur==$GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['nom']:"#$id_auteur");
	$titre = kaltura_option('titre',$options,_T("kaltura:video_de_nom",array('nom'=>$nom)));
	$descriptif = kaltura_option('descriptif',$options,"");
	$tags = kaltura_option('tags',$options,"");
	

	include_spip('inc/charsets');
	$titre = charset2unicode(html2unicode($titre,false));
	$descriptif = charset2unicode(html2unicode($descriptif,false));
	$tags = charset2unicode(html2unicode($tags,false));

	if (!$id_auteur)
		return false;

	include_spip('inc/kalturaapi_php5_lib');

	$kaltura_user = new kalturaUser();
	$kaltura_user->puser_id=$id_auteur;
	$kaltura_user->puser_name=$nom;
	
	$kaltura_service = kalturaService::getInstance( $kaltura_user );
	$params = array
		(
			"kshow_name" => $titre,
			"kshow_description" => $descriptif,
			"kshow_tags" => $tags,//"sample, nice" ,
		);
	
	$res = $kaltura_service->addkshow ( $kaltura_user , $params );
	$kshow = @$res["result"]["kshow"];
	if ( !$kshow ){
		return false;
	}
	return array($kshow["id"],$id_auteur);	
}

define('_KALTURA_VIDEO_PLAYER',1);
define('_KALTURA_VIDEO_WIKI',3);
/**
 * Creer le widget kaltura en html
 *
 * @param int $kshow_id
 * @param int $user_id
 * @param array $options
 * @return string
 */
function kaltura_html_widget ( $kshow_id , $user_id , $options = array() ){
	include_spip('inc/kalturaapi_php5_lib');
	
	$entry_id = null;
	
	$size = kaltura_option('size',$options,'l');
	$version = kaltura_option('version',$options,'-1');
	$media_type = kaltura_option('media_type',$options,2);
	$widget_type = kaltura_option('widget_type',$options,_KALTURA_VIDEO_WIKI);
	$entry_id = kaltura_option('entry_id',$options,'-1');

	$version_kshow_name=kaltura_option('version_kshow_name',$options);
	$version_kshow_description=kaltura_option('version_kshow_description',$options);
    
	// add the version as an additional parameter
	$domain = $GLOBALS['WIDGET_HOST']; //"http://www.kaltura.com";
	$swf_url = "/index.php/widget/$kshow_id/" . 
		( $entry_id ? $entry_id : "-1" ) . "/" .
		( $media_type ? $media_type : "-1" ) . "/" .
		( $widget_type ? $widget_type : _KALTURA_VIDEO_WIKI ) . "/" . // widget_type=3 -> WIKIA
		( "$version" );

	#$current_widget_kshow_id_list[] = $kshow_id;
	
	$kshowCallUrl = "$domain/index.php/browse?kshow_id=$kshow_id";
	$widgetCallUrl = "$kshowCallUrl&browseCmd=";
	$editCallUrl = "$domain/index.php/edit?kshow_id=$kshow_id";

	/* 
  widget3:
  url:  /widget/:kshow_id/:entry_id/:kmedia_type/:widget_type/:version
  param: { module: browse , action: widget }
	*/
	if ( $size == "m"){
  	// medium size
  	$height = 198 + 105;
  	$width = 267;
	}
  else {
		// large size
		$height = 300 + 105 + 20;
		$width = 400;
	}
  
	#$root_url = "" ; //getRootUrl();
	#$external_url = "http://" . @$_SERVER["HTTP_HOST"] ."$root_url";
	$external_url = self('&',true);
	$share = "TODO" ; //$titleObj->getFullUrl ();
    
	// this is a shorthand version of the kdata
	$links_arr = array (
		"base" => "$external_url/" , 
		"add" =>  "Special:KalturaContributionWizard?kshow_id=$kshow_id" ,
		"edit" => "Special:KalturaVideoEditor?kshow_id=$kshow_id" ,
		"share" => $share ,
	);
 	#var_dump($links_arr);   	
	$links_str = str_replace ( array ( "|" , "/") , array ( "|01" , "|02" ) , base64_encode ( serialize ( $links_arr ) ) ) ;
 	#var_dump($links_str);
	$kaltura_link_str = _T("kaltura:propulse_par",array('nom'=>$GLOBALS['kaltura_partner_name']));
	$url_wizard = generer_url_public('kaltura_contributionwizard',"uid=$user_id&kshow_id=$kshow_id",true,true);
	$js_func_suff = "_$user_id_$kshow_id";
	$js = "
<script type='text/javascript'>
if (true){ /* || typeof('gotoCW$js_func_suff')==undefined*/
function gotoCW$js_func_suff(){	kalturaInitModalBox ( \"$url_wizard\");}
function gotoEditor$js_func_suff(){	alert ( \"Editor - Will be implemented in the near future.\" );	return;}
}
</script>
	";
	
	$flash_vars = array (  
		"CW" => "gotoCW$js_func_suff" ,
		"Edit" => "gotoEdit" ,
		"Editor" => "gotoEditor$js_func_suff" ,
		"Kaltura" => "",//gotoKalturaArticle" ,
		"Generate" => "" , //gotoGenerate" ,
		"share" => "" , //$share ,
		"WidgetSize" => $size,
//		"kshow_id" => $kshow_id,
	);

	// add only if not null 							
	if ( $version_kshow_name ) $flash_vars["Title"] = $version_kshow_name;
	if ( $version_kshow_description ) $flash_vars["Description"] = $version_kshow_description;	

	$swf_url .= "/" . $links_str;
	$flash_vars_str = http_build_query( $flash_vars , "" , "&" )		;	

	$widget = /*$extra_links .*/
		'<object id="kaltura_player_' . (int)microtime(true) . '" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
		'<param name="allowScriptAccess" value="always" />'.
		'<param name="allowNetworking" value="all" />'.
		'<param name="bgcolor" value=#000000 />'.
		'<param name="movie" value="'.$domain. $swf_url . '"/>'.
		'<param name="flashVars" value="' . $flash_vars_str . '"/>'.
		'<param name="wmode" value="opaque"/>'.
		$kaltura_link_str .
		'</object>' 
		// .	"<div class='kaltura_powered'>$kaltura_link_str</div>"
		;

	return $js . $widget ;
}

function kaltura_html_wizard($kshow_id , $user_id, $options=array()){
	include_spip('inc/kalturaapi_php5_lib');
	$domain = $GLOBALS['WIDGET_HOST'];

	$lang = kaltura_option('lang',$options,$GLOBALS['lang']);
	$height = kaltura_option('height',$options,360);
	$width = kaltura_option('height',$options,680);
	
	// retrouver la session si non fournie
	if (!$ks=$GLOBALS['kaltura_ks']){
		$kaltura_user = new kalturaUser();
		$kaltura_user->puser_id=$user_id;
		$kaltura_user->puser_name=$user_id;
		 
		$kaltura_service = kalturaService::getInstance( $kaltura_user );
		$ks = $kaltura_service->getKs();
	}

	// hum, vilain hack pour les core-dev de kaltura ?
	if ( strpos ( $domain , "localhost"  ) !== false )		$host = 2;
	elseif ( strpos ( $domain , "kaldev" ) !== false ) 		$host = 0;
	else													$host = 1;

	$swf_url = "/swf/ContributionWizard.swf";
		
	$flashvars = 		'userId=' . $user_id .
						'&sessionId=' . $ks. 
						'&partnerId=' . $GLOBALS['kaltura_partner_id'] .
						'&subPartnerId=' . $GLOBALS['kaltura_subp_id'] . 
						'&kshow_id=' . $kshow_id . 
						'&host=' . $host . //$domain; it's an enum
						'&afterAddentry=addentry' .
						'&close=finished' .
						'&lang=' . $lang . 
						'&terms_of_use=http://www.kaltura.com/index.php/static/tandc' ;

	$str = "";
							
	$extra_links  = "<a href='javascript:addentry()'>addentry<a><br> " ;
    
				
	$widget = 
		'<object id="kaltura_contribution_wizard" type="application/x-shockwave-flash" allowScriptAccess="always" allowNetworking="all" height="' . $height . '" width="' . $width . '" data="'.$domain. $swf_url . '">'.
		'<param name="allowScriptAccess" value="always" />'.
		'<param name="allowNetworking" value="all" />'.
		'<param name="bgcolor" value=#000000 />'.
		'<param name="movie" value="'.$domain. $swf_url . '"/>'.
  	'<param name="flashVars" value="' . $flashvars . '" />' .
		'</object>';
			
	return $widget;

	//echo "<pre style='color:white'>" . print_r ( explode ( "&" , $flashvars ) , true ) . "</pre>";
}

?>