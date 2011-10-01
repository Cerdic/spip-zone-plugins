<?php
/*
 * GMap plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 * Fonctions utilitaires pour la saisie des points dans l'espace privé
 *
 */
 
include_spip('inc/gmap_presentation');

// Ajout du formulaire de choix par recherche sur le geocoder
function gmap_sous_bloc_geocoder($mapId, $cbSetMarkerPosition, $bSousBloc = true, $bSimpleSearch = false)
{
	$out = "";

	$out .= '
<div class="geoedit_subform">
	<input type="text" class="text empty-edit" size="50" name="'.$mapId.'_address" id="'.$mapId.'_address" value="'._T('gmap:address_explic').'" style="width:360px; margin-right:10px;" /><input type="button" name="'.$mapId.'_geocode" id="'.$mapId.'_geocode" value="'._T('gmap:address_btn_find').'" disabled="disabled" />
	<div id="'.$mapId.'_address_position" class="geocoder-results hidden">
		<table class="address_list edit_markers" align="right">
			<tbody>
			</tbody>
		</table>
		<table class="address_template" style="display:none;">
			<tbody>
				<tr class="header"><th>'._T('gmap:geocoder_name').'</th><th>'._T('gmap:latitude').'</th><th>'._T('gmap:longitude').'</th><th>&nbsp;</th></tr>
				<tr class="geocoder">
					<td class="addr_location"></td>
					<td class="addr_latitude" nowrap></td>
					<td class="addr_longitude" nowrap></td>
					<td><span class="addr_copy btn_marker_copy" nowrap></span></td>
				</tr>
				<tr class="no-results">
					<td class="addr_location" colspan="4">'._T('gmap:geocoder_no_results').'</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>';
	
	$out .= '
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function()
{
	// Action sur le bouton de la recherche par adresse
	jQuery("#'.$mapId.'_geocode").click(function()
	{
		var map = gMap("'.$mapId.'");
		var address = jQuery("#'.$mapId.'_address").val();
		if (isObject(map) && isObject(address) && (address !== ""))
		{';
	if ($bSimpleSearch === true)
		$out .= '
			map.searchGeocoder(address, function(latitude, longitude)
			{
				jQuery("#'.$mapId.'_address_position").updateGeocoderResults({ name: "", latitude: latitude, longitude: longitude });
				jQuery("#'.$mapId.'_address_position .addr_copy").click(function()
				{
					var row = jQuery(this).parents("tr.geocoder");
					var latitude = jQuery("td.addr_latitude", row).text();
					var longitude =	jQuery("td.addr_longitude", row).text();
					if ((isObject(latitude) && (latitude != "")) && (isObject(longitude) && (longitude != "")))
						'.$cbSetMarkerPosition.'("'.$mapId.'", Number(latitude), Number(longitude));
				});
			});';
	else
		$out .= '
			map.queryGeocoder(address, function(locations)
			{
				jQuery("#'.$mapId.'_address_position").updateGeocoderResults(locations);
				jQuery("#'.$mapId.'_address_position .addr_copy").click(function()
				{
					var row = jQuery(this).parents("tr.geocoder");
					var latitude = jQuery("td.addr_latitude", row).text();
					var longitude =	jQuery("td.addr_longitude", row).text();
					if ((isObject(latitude) && (latitude != "")) && (isObject(longitude) && (longitude != "")))
						'.$cbSetMarkerPosition.'("'.$mapId.'", Number(latitude), Number(longitude));
				});
			});';
		$out .= '
			return true;
		}
		return false;
	});
	
	// Gestion des edits
	var bEraseOnFocusIn = true;
	jQuery("#'.$mapId.'_address").focusin(function() {
		if (bEraseOnFocusIn === true)
		{
			bEraseOnFocusIn = false;
			jQuery(this).val("");
			jQuery(this).removeClass("empty-edit");
		}
	});
	jQuery("#'.$mapId.'_address").focusout(function() {
		if (jQuery(this).val() === "")
		{
			bEraseOnFocusIn = true;
			jQuery(this).addClass("empty-edit");
			jQuery(this).val("'._T('gmap:address_explic').'");
			jQuery("#'.$mapId.'_geocode").attr("disabled", "disabled");
		}
		else
			jQuery("#'.$mapId.'_geocode").removeAttr("disabled");
	});
	jQuery("#'.$mapId.'_address").keyup(function() {
		if (jQuery(this).val() === "")
			jQuery("#'.$mapId.'_geocode").attr("disabled", "disabled");
		else
			jQuery("#'.$mapId.'_geocode").removeAttr("disabled");
	});
});
//]]>
</script>'."\n";
	
	if ($bSousBloc === true)
		return gmap_sous_bloc_depliable("geocoder", _T('gmap:formulaire_geocoder'), $out);
	else
		return $out;
}

	
?>