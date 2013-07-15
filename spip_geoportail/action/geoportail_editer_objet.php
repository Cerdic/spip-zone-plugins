<?php
/**
* Plugin SPIP Geoportail
*
* @author:
* Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2010
* Logiciel distribue sous licence GNU/GPL.
*
* Action securisee pour modifier une geoposition
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return "{error:'inc_version'}";
include_spip('base/abstract_sql');
include_spip('inc/geoupload');
include_spip('public/geoportail_boucles');
include_spip('inc/geoportail_autorisations');
		
function action_geoportail_editer_objet_dist()
{	//header("Content-Type: text/json; charset:UTF-8; ");
	#HTTP_HEADER{Content-Type: text/json; }

	include_spip('inc/compat_192');
	// Une modification ?
	if (isset($_POST['valider']) || isset($_POST['supprimer']))
	{	$lon = _request('lon');
		$lat = _request('lat');
		//$zone = _request('idzone');
		$zone = _request('zone_geo');
		$zoom = _request('zoom');
		$action = 'modifier';
		$alon = addslashes(geoportail_longitude($lon));
		$alat = addslashes(geoportail_latitude($lat));
		$verrou = _request('verrou');
		// Quoi modifier
		if ($lon && $lat && $zone)
		{	$objet = _request ('objet');
			$id = _request ('id_objet');
			if ($objet == 'auteur') $action = 'positionner';
		}
		else
		{	echo "{error:null, lon:'', lat:'', alon:'-', alat:'-', id_dep:'', id_com:'', nom_com:'' }";
			return;
		}
		// Rechercher la commune la plus proche
		if (!$verrou) geoportail_chercher_adm ($lon, $lat, $adm);
		if ($adm)
		{	$id_dep = $adm['id_dep'];
			$id_com = $adm['id_com'];
			$nom_com = $adm['name'];
		}
		else $nom_com = $id_dep = $id_com = "";
		
		// Verifier que l'action est securisee
		$securiser_action = charger_fonction('securiser_action','inc');
		$securiser_action();

		// Traiter
		if ($id)
		{	if (autoriser($action, $objet, $id))
			{	if (isset($_POST['valider']))
				{	$row = spip_fetch_array(spip_query("SELECT * FROM spip_geopositions WHERE id_objet='$id' AND objet='$objet'"));
					if ($row)
					{	$req = "";
						if (!$verrou) $req = ",id_dep='$id_dep',id_com='$id_com'";
						spip_query("UPDATE spip_geopositions SET "
							."lon=$lon,lat=$lat,zoom=$zoom,zone='$zone'".$req
							."WHERE id_geoposition=".$row['id_geoposition']." AND id_objet=$id AND objet='$objet'");
					}
					else
					{	$id_position = sql_insert("spip_geopositions",
							"(id_objet, objet, lon, lat, zoom, zone, id_dep, id_com)",
							"($id, '$objet', $lon, $lat, $zoom, '$zone', '$id_dep', '$id_com')"
						);
					}
				}
				else if (isset($_POST['supprimer']))
				{	spip_query("DELETE FROM spip_geopositions WHERE id_objet='$id' AND objet='$objet'");
					$alat=$alon='-';
					$id_dep = $id_com = $nom_com = '';
				}
				echo "{ error:null, lon:$lon, lat:$lat, alon:'$alon', alat:'$alat', id_dep:'$id_dep', id_com:'$id_com', nom_com:\"$nom_com\" }";
			}
			else echo "{ error:\"". _T('geoportail:pas_autoriser') ."\"}";
		}
		else echo "{error:'objet'}";
	}
	else echo "{error:'action'}";
}

?>