<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Fonctions de validite des url des sites references.
+--------------------------------------------+
| code d'origine : Matthieu ONFRAY
| code source des fonctions seek_redirect_location & check_connect 
| appartiennent à  categorizator (http://www.categorizator.org/).
| Modifiees pour plugin tabbord (spip 1.9.1) .. 08/04/07 .. scoty.koakidi.com
+--------------------------------------------+
*/

function seek_redirect_location($header) {
	# recherche la location de la redirection
	# si l'erreur HTTP renvoyee commence par 3
	$location = "";
	$tab_header = explode("\n",$header);
	for ($i=0;$tab_header[$i];$i++) {
		$line = split(":",$tab_header[$i],2);
		if(eregi("location",$line[0])) {
			$location = trim($line[1]);
			break;
		}
	}
	return $location;
}


function check_local_host($host){
	if($host == "localhost" || $host == "127.0.0.1" || $host==$_SERVER[HTTP_HOST]) {
		return true;
	}
	else return false;
}


function check_connect($url) {
# verifie la validite de l'adresse,
# c'est a dire on regarde si le site existe bien...

	//creation du tableau avec les valeurs a rendre
	$data["connect"] = '0'; //la page est OK ou KO (200 => OK sinon KO)
	$data["code"] = '0'; //code HTTP renvoye
	$data['remove'] = '';

		
	//on verifie si c'est bien le bon type de site
	$cmp_h = strcmp(substr($url,0,7),"http://");
	$cmp_n = strcmp(substr($url,0,7),"news://");
	$cmp_f = strcmp(substr($url,0,6),"ftp://");
	$cmp_m = strcmp(substr($url,0,7),"mailto:");
	if ($cmp_h != 0 && $cmp_n != 0 && $cmp_f != 0 && $cmp_m != 0) {
		$data["code"] = 'invalide';
		return $data;
	}
	
	# parse URL
	$url_parsee = @parse_url($url);
	$host = trim($url_parsee["host"]);
	$path = trim($url_parsee["path"]);
	
	# champ spip preremplis 'http://''
	if($host=='') {
		$data["code"]='vide';
		return $data;
	}
	
	# url d une page du site
	if (check_local_host($host)) {
		// adresse local
		$data["code"] = 'local';
		return $data;
	}		
	
	
	// ok url http://.....
	if ($cmp_h==0) {
		
		//connexion par socket
		$fp = @fsockopen($host,80);
		if (!$fp) {
			$data["code"]='delais';
			return $data;
		}
		else {
			//traitement du path
			if(substr($path,strlen($path)-1) != '/') {
				if(!ereg("\.",$path)) {
					$path .= "/";
				}
			}
	
			//envoi de la requete HTTP
			fputs($fp,"GET ".$path." HTTP/1.1\r\n"); 
			fputs($fp,"Host: ".$host."\r\n");
			fputs($fp,"Connection: close\r\n\r\n");
	
			//on lit le fichier
			$line = fread($fp,255);
			$en_tete = $line;
			//on lit tant qu'on n'est pas la fin du fichier ou qu'on trouve le debut du code html...
			while (!feof($fp) && !ereg("<",$line) ) {
				$en_tete .= $line;
				$line = fread($fp,255);
			}
			fclose($fp);
			
			// code HTTP renvoye
			$code = substr($en_tete,9,3);
			
			// url de redirection si 301||302
			if($code=='301' || $code=='302') {
				$data["remove"]= seek_redirect_location($en_tete);
			}
			$data["code"] = $code;
			$data["connect"] = 1;
			return $data;
		}
	}
}


function trad_check_connect($ret) {

	if($ret['connect']==0 && $ret['code']=='0') {
		$message = _T('tabbord:msg_pas connection');
		$color = "#ff0000";
	}
	else {
		switch ($ret['code']) {
			
			case 'invalide' :
					$message = _T('tabbord:msg_url_non_conforme');
					$color = "#ff0000";
					break;
			case 'local' :
					$message = _T('tabbord:msg_page_site');
					$color = "#dfdfdf";
					break;
			case 'vide' :
					$message = _T('tabbord:msg_champ_incomplet');
					$color = "#868686";
					break;
			case 'delais' :
					$message = _T('tabbord:msg_delais_depasse');
					$color = "#ff9966";
					break;
			
			// 2** la page a été trouvée
			case 200 :		
						$message = _T('tabbord:msg_ok');
						$color = "#33cc00";
						break;
			case 204 :	
						$message = _T('tabbord:msg_page_vide');
						#Cette page ne contient rien
						$color = "#ff9966";
						break;
			case 206 :	
						$message = _T('tabbord:msg_contenu_partiel');
						$color = "#ff9966";
						break;
			// 3** il y a une redirection
			case 301 :	
						$message = _T('tabbord:msg_deplacee_definitif'); 
						$color = "#ff9966";
						break;
			case 302 :	
						$message = _T('tabbord:msg_deplacee_tempo'); 
						$color = "#ff9966";
						break;
			// 4** erreur du coté du client
			case 400 :	
						$message = _T('tabbord:msg_erreur_requete_http');
						$color = "#ff0000";
						break;
			case 401 :	
						$message = _T('tabbord:msg_authentif_requise');
						$color = "#ff0000";
						break;
			case 402 :	
						$message = _T('tabbord:msg_acces_payant');
						$color = "#ff0000";
						break;
			case 403 :	
						$message = _T('tabbord:msg_acces_refuse');
						$color = "#ff0000";
						break;
			case 404 :	
						$message = _T('tabbord:msg_inexistante');
						$color = "#ff0000";
						break;
			// 5** erreur du coté du serveur
			case 500 :	
						$message = _T('tabbord:msg_erreur_interne_serveur');
						$color = "#ff0000";
						break;
			case 502 :	
						$message = _T('tabbord:msg_erreur_passerelle');
						#Erreur à cause de la passerelle du serveur
						$color = "#ff0000";
						break;
			// cas restant
			default :	
						$message = _T('tabbord:msg_erreur_code', array('code'=>$ret['code']));
						$color = "#000000";
						break;
		}
	}
	$return['code'] = $ret['code']; //code HTTP renvoye
	$return['message'] = $message;
	$return['remove'] = $ret['remove'];
	$return['color'] = $color;
	return $return;
}

?>
