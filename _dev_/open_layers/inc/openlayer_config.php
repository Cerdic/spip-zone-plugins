<?php
/*
 * Open Layers plugin
 * free WMS map layers for SPIP
 *
 * Authors :
 * Horacio Gonz‡lez
 * (c) 2007 - Distributed under GNU/GPL licence
 *
 */

function inc_openlayer_config(){
	global $spip_lang_right;
	$out = gros_titre(_T('openlayer:msgplugin'),'',false).'<br>';
	$out .= debut_cadre('r', _DIR_PLUGIN_OPENLAYER."img_pack/correxir.png");
	//guardar la capa wms cuando se ha solicitado
	if(_request('ok')){
		include_spip('inc/meta');
		ecrire_meta('openlayer_wmsname',_request('wmsname'));
		ecrire_meta('openlayer_wmsurl',_request('wmsurl'));
		ecrire_metas();
	}
	//formulario para la capa WMS
	$name = isset($GLOBALS['meta']['openlayer_wmsname'])?$GLOBALS['meta']['openlayer_wmsname']:"OpenLayers WMS";
	$wms = isset($GLOBALS['meta']['openlayer_wmsurl'])?$GLOBALS['meta']['openlayer_wmsurl']:"http://labs.metacarta.com/wms/vmap0";
	$out .= '<br/>
	<div style="float:left">
		<form name="googlemapkey" method="post" action="'.self().'"><br/>
			<label>'._T('openlayer:wmslayername').'</label><input type="text" name="wmsname" value="'.$name.'" size="15" /><br/>
			<label>'._T('openlayer:wmslayerurl').'</label><input type="text" name="wmsurl" value="'.$wms.'" size="30" /><br/>
			<input type="submit" name="ok" value="'._T('bouton_enregistrer').'" />
		</form>
	</div>';
	//respuesta del formulario una vez guardada la capa wms
	if(_request('ok')){
		$out .= '<div align="center" style="margin:20px auto">
			'._T('openlayer:wms_engadida').'<code>'._request('wms').'</code>
		</div>';
	}
	//guardar valores predeterminados de latitud, longitud y zoom cuando se ha solicitado 
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
	//formulario para valores predeterminados de latitud, longitud y zoom
	$glat = isset($GLOBALS['meta']['gis_default_lat'])?$GLOBALS['meta']['gis_default_lat']:'42.7631';
	$glonx = isset($GLOBALS['meta']['gis_default_lonx'])?$GLOBALS['meta']['gis_default_lonx']:'-7.9321';
	$gzoom = isset($GLOBALS['meta']['gis_default_zoom'])?$GLOBALS['meta']['gis_default_zoom']:'7';
	$openlayer_append_moveend_map = charger_fonction('openlayer_append_moveend_map','inc');
	$out .= '<form name="position" method="post" action="'.self().'">
		<div id="cadroFormulario" style="clear:left;border:1px solid #000;margin-top:30px;padding:10px;tex-align:center;">
			<p>'._T('geomap:default_geoloc').'</p>
			<div id="formMap" name="formMap" style="width: 470px; height: 350px;margin:10px auto;"></div>
			' . $openlayer_append_moveend_map('formMap', $name, $wms, 'form_lat','form_lonx',$glat, $glonx,'form_zoom', $gzoom, true) . '
			<input type="text" name="form_lat" id="form_lat" value="'.$glat.'" />
			<input type="text" name="form_lonx" id="form_lonx" value="'.$glonx.'" />
			<input type="text" name="form_zoom" id="form_zoom" value="'.$gzoom.'" />
			<div style="text-align:$spip_lang_right">
				<input type="submit" name="choisir" value="'._T('bouton_choisir').'" />
			</div>
		</div>
	</form>';
	/* el buscador de calles y lugares de geonames no funciona por un problema con el XMLHttpRequest
	$out .= '<form action="#" onsubmit="showAddress($(\'#address\').attr(\'value\')); return false">
		<p>
        	<input size="60" name="address" id="address" value="" type="text">
        	<input value="Go!" type="submit">
		</p>
	</form>';
	*/
	$out .= fin_cadre('r');
	return $out;
}
?>