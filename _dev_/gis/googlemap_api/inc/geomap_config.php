<?php
/*
 * Spip Geomap/GoogleMap plugin
 * Insetar google maps en SPIP
 *
 * Autores :
 * Horacio Gonz‡lez, Berio Molina
 * (c) 2007 - Distribu’do baixo licencia GNU/GPL
 *
 */

function inc_geomap_config(){
		global $spip_lang_right;
		$out = "";
		$out .= debut_cadre('r', _DIR_PLUGIN_GEOMAP."img_pack/correxir.png");
		if(_request('ok')){
			include_spip('inc/meta');
			ecrire_meta('geomap_googlemapkey',$_POST['key']);
			ecrire_metas();
		}
		$apikey = isset($GLOBALS['meta']['geomap_googlemapkey'])?$GLOBALS['meta']['geomap_googlemapkey']:"";
		$out .= '<br/>';
		$out .= '<a href="http://www.google.com/apis/maps" target="_blank" ><img src="'._DIR_PLUGIN_GEOMAP.'img_pack/logo_google.gif" border="0" align="left" hspace="10" ></a>';
		$out .= '<form name="googlemapkey" method="post" action="'.self().'">';
		$out .= '<br/>';
		$out .= '<label>Google Map API Key <a href="http://www.google.com/apis/maps/signup.html" target="_blank" >'._T('geomap:conseguir').'</a></label> <input type="text" name="key" value="'.$apikey.'" size="30" />';
		$out .= '<input type="submit" name="ok" value="ok" />';
		
		if(_request('ok')){
			$out .= '<div align="center" style="margin:20px auto">';
			$out .= ''._T('geomap:clave_engadida').'<code>'._request('key').'</code>';
			$out .= '</div>';
		}
		if (strlen($apikey)){
			if(_request('choisir')){
				include_spip('inc/meta');
				$lat= _request('form_lat');
				if (strlen($lat) AND is_numeric($lat))
					ecrire_meta('geomap_default_lat',$lat);
				$lonx= _request('form_lonx');
				if (strlen($lonx) AND is_numeric($lonx))
					ecrire_meta('geomap_default_lonx',$lonx);
				$zoom= _request('form_zoom');
				if (strlen($zoom) AND is_numeric($zoom))
					ecrire_meta('geomap_default_zoom',$zoom);
				ecrire_metas();
			}
			$glat = isset($GLOBALS['meta']['geomap_default_lat'])?$GLOBALS['meta']['geomap_default_lat']:'42.7631';
			$glonx = isset($GLOBALS['meta']['geomap_default_lonx'])?$GLOBALS['meta']['geomap_default_lonx']:'-7.9321';
			$gzoom = isset($GLOBALS['meta']['geomap_default_zoom'])?$GLOBALS['meta']['geomap_default_zoom']:'7';

			$geomap_append_clicable_map = charger_fonction('geomap_append_clicable_map','inc');
			$out .= 
			"<div id='cadroFormulario' style='border:1px solid #000;margin-top:30px;padding:10px;tex-align:center;'>\n"
			. "<p>"._T('geomap:default_geoloc')."</p>"
			. "<div id='formMap' name='formMap' style='width: 470px; height: 350px;margin:10px auto;'></div>"
			. $geomap_append_clicable_map('formMap','form_lat','form_lonx',$glat,$glonx,'form_zoom',$gzoom,false);
		
			$out .= '<input type="text" name="form_lat" id="form_lat" value="'.$glat.'" />
			<input type="text" name="form_lonx" id="form_lonx" value="'.$glonx.'" />
			<input type="text" name="form_zoom" id="form_zoom" value="'.$gzoom.'" />';
		}
		$out .= "<div style='text-align:$spip_lang_right'>";
		$out .= '<input type="submit" name="choisir" value="'._T('bouton_choisir').'" />';
		$out .= "</div>";

		$out .= '</form>';
		$out .= fin_cadre('r');
		return $out;
}

?>