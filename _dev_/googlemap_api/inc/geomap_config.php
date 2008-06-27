<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonzalez, Berio Molina
 * (c) 2007 - Distribudo baixo licencia GNU/GPL
 *
 */

function inc_geomap_config(){
	global $spip_lang_right;
	$out = gros_titre(_T('geomap:msgplugin'),'',false).'<br>';
	$out .= debut_cadre('r', _DIR_PLUGIN_GEOMAP."img_pack/correxir.png");
	if(_request('ok')){
		include_spip('inc/meta');
		ecrire_meta('geomap_googlemapkey',_request('key'));
		$version = _request('api_version');
			if($version=="2.")
				 $version .= _request('api_version_number');
		ecrire_meta('geomap_googlemapversion',$version); 
		ecrire_metas();
	}
	$apikey = isset($GLOBALS['meta']['geomap_googlemapkey'])?$GLOBALS['meta']['geomap_googlemapkey']:"";
	$apiversion = isset($GLOBALS['meta']['geomap_googlemapversion'])?$GLOBALS['meta']['geomap_googlemapversion']:"";
    if(!$apiversion)
		$apiversion = "2";
    $apiversionnumber = "";
    if(preg_match(",(2\.)(\d+),",$apiversion,$m)) {
		$apiversion = $m[1];
		$apiversionnumber = $m[2];
    } 
	$out .= '
	<br/>
	<a href="http://www.google.com/apis/maps" target="_blank" ><img src="'._DIR_PLUGIN_GEOMAP.'img_pack/logo_google.gif" border="0" align="left" hspace="10" ></a>
	<div style="float:left">
		<form name="googlemapkey" method="post" action="'.self().'"><br/>
			<label>Google Map API Key <a href="http://www.google.com/apis/maps/signup.html" target="_blank" >'._T('geomap:conseguir').'</a></label> <input type="text" name="key" value="'.$apikey.'" size="30" /><br/>
			<div>API Version <a href="http://code.google.com/apis/maps/documentation/index.html#API_Updates" target="_blank">info</a>:</div>
			<div><label><input type="radio" name="api_version" value="2" '.($apiversion==="2"?'checked="checked" ':'').'/>Current version</label></div>
			<div><label><input type="radio" name="api_version" value="2.x" '.($apiversion==="2.x"?'checked="checked" ':'').'/>Latest version</label></div>
			<div><label><input type="radio" name="api_version" value="2.s" '.($apiversion==="2.s"?'checked="checked" ':'').'/>Stable version</label></div>
			<div><label><input type="radio" name="api_version" value="2." '.($apiversion==="2."?'checked="checked" ':'').'/>Other version</label> <input type="text" name="api_version_number" value="'.$apiversionnumber.'" /></div>
			<input type="submit" name="ok" value="'._T('bouton_enregistrer').'" />
		</form>
	</div>';
	if(_request('ok')){
		$out .= '
		<div align="center" style="margin:20px auto">
			'._T('geomap:clave_engadida').'<code>'._request('key').'</code>
		</div>';
	}
	if (strlen($apikey)){
		if(_request('choisir')){
			include_spip('inc/meta');
			$lat= _request('form_lat');
			if (strlen($lat) AND is_numeric($lat))
				ecrire_meta('gis_default_lat',$lat);
			$lonx= _request('form_lonx');
			if (strlen($lonx) AND is_numeric($lonx))
				ecrire_meta('gis_default_lonx',$lonx);
			$zoom= _request('form_zoom');
			if (strlen($zoom) AND is_numeric($zoom))
				ecrire_meta('gis_default_zoom',$zoom);
			ecrire_metas();
		}
		$glat = isset($GLOBALS['meta']['gis_default_lat'])?$GLOBALS['meta']['gis_default_lat']:'42.7631';
		$glonx = isset($GLOBALS['meta']['gis_default_lonx'])?$GLOBALS['meta']['gis_default_lonx']:'-7.9321';
		$gzoom = isset($GLOBALS['meta']['gis_default_zoom'])?$GLOBALS['meta']['gis_default_zoom']:'7';
		
		$geomap_append_moveend_map = charger_fonction('geomap_append_moveend_map','inc');
		
		$out .= '
		<form name="position" method="post" action="'.self().'">
			<div id="cadroFormulario" style="clear:left;border:1px solid #000;margin-top:30px;padding:10px;tex-align:center;">
				<p>'._T('geomap:default_geoloc').'</p>
				<div id="formMap" name="formMap" style="width: 470px; height: 350px;margin:10px auto;"></div>'
				.$geomap_append_moveend_map('formMap','form_lat','form_lonx',$glat,$glonx,'form_zoom',$gzoom,true).'
				<input type="text" name="form_lat" id="form_lat" value="'.$glat.'" />
				<input type="text" name="form_lonx" id="form_lonx" value="'.$glonx.'" />
				<input type="text" name="form_zoom" id="form_zoom" value="'.$gzoom.'" />
				<div style="text-align:$spip_lang_right">
					<input type="submit" name="choisir" value="'._T('bouton_choisir').'" />
				</div>
			</div>
		</form>';
	}
	$out .= '
		<form action="#" onsubmit="showAddress($(\'#address\').attr(\'value\')); return false">
			<p>
				<input size="60" name="address" id="address" value="" type="text">
				<input value="Go!" type="submit">
      		</p>
      </form>';
	$out .= fin_cadre('r');
	return $out;
}

?>
