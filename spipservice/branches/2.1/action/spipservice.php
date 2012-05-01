<?php

/*______________________________________________________________________________
 | Plugin SpipService 1.0 pour Spip 2.1                                           \
 | Copyright 2012 Sebastien Chandonay - Studio Lambda                            \
 |                                                                                |
 | SpipService est un logiciel libre : vous pouvez le redistribuer ou le          |
 | modifier selon les termes de la GNU General Public Licence tels que            |
 | publiés par la Free Software Foundation : à votre choix, soit la               |
 | version 3 de la licence, soit une version ultérieure quelle qu'elle            |
 | soit.                                                                          |
 |                                                                                |
 | SpipService est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE     |
 | GARANTIE ; sans même la garantie implicite de QUALITÉ MARCHANDE ou             |
 | D'ADÉQUATION À UNE UTILISATION PARTICULIÈRE. Pour plus de détails,             |
 | reportez-vous à la GNU General Public License.                                 |
 |                                                                                |
 | Vous devez avoir reçu une copie de la GNU General Public License               |
 | avec SpipService. Si ce n'est pas le cas, consultez                            |
 | <http://www.gnu.org/licenses/>                                                 |
 ________________________________________________________________________________*/

include_spip('inc/spip_service_core');
include_spip('inc/spip_service_utils');

function action_spipservice_dist(){

	if (_DEBUG_SPIPSERVICE){
		spip_log("getRequestParam('format') : ".getRequestParam('format'),"spipservice");
		spip_log("getRequestParam('service') : ".getRequestParam('service'),"spipservice");
		spip_log("getRequestParam('data') : ".getRequestParam('data'),"spipservice");
	}

	// recuperation du format
	$format = getRequestParam('format');

	// recuperation du nom du service demande
	$service = getRequestParam('service') ;

	// recuperation des donnees
	$data = getRESTParams(getRequestParam('data'), $format);

	$response = "";

	// la recherche de fonction permet à d'autres plugin de surcharger les méthodes natives du plugin spipservice
	// ou de definir ses propres services
	// pour se faire il faut créer un fichier spipservice_[servicename].php dans le dossier inc/ de votre plugin
	// et définir une méthode avec la signature inc_spipservice_[servicename]($format, $service, $data){}
	$fonc = charger_fonction("spipservice_".$service,"inc");
	if ($fonc){
		// on a trouver une fonction definie, on l'utilise
		$response = $fonc($format, $service, $data);
		if ($format == FORMAT_JSON){
			echo json_encode($response);
		}else if ($format == FORMAT_XML){
			echo arrayIntoXml($response);
		}else{// format inconnu, on n'y touche pas
			echo $response;
		}
	}else{
		// on n'a pas trouve de fonction
		// on ne fait pas d'echo car la fonction Spip charger_fonction() en fait déjà un!
		// on se contente de loger...
		spip_log("ERREUR - le service '".$service."' n'est pas defini - charger_fonction(\"spipservice_".$service,"inc\") -> fonction introuvable!", "spipservice");
	}

}

?>