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
*/

function action_geoportail_importrgc_dist()
{	header("Content-Type: text/json; charset=UTF-8; ");
	#HTTP_HEADER{Content-Type: text/json; charset=UTF-8; }
	//-- Protection du script (interdir l'acces hors du site)
	include_spip ('inc/geoportail_protect');
	if (!geoportail_good_referer('importrgc')) { echo "[{ error:'Bad Referer '}]"; return; }
	
	include_spip('inc/compat_192');
	include_spip('inc/geoupload');
	// Repertoire geoportail
	include_spip('geoportail');

	$pos = _request('pos');
	$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));

	// Ne pas recommencer !
	if ($pos==0 && $row) echo 'reload intempestif';
	else
	{	// Le fichier a charger 
 		$rgc_file = _FULLDIR_PLUGIN_GEOPORTAIL."rgc/rgc.".$GLOBALS['meta']['geoportail_rgc'].".txt";
		$fichier = fopen($rgc_file, "r"); 
		if (!$fichier) 
		{	echo 'Impossible de charger le fichier';
			return;
		}
		// Import ligne par ligne...
		for ($i=0; $i<$pos; $i++) fgets($fichier, 1024);
		$pos += 2000;
		for ( ; $i<$pos; $i++)
		{	// On recupere toute la ligne
			$uneLigne = fgets($fichier, 1024);
			// On explise dans un tableau 
			$tableauValeurs = explode("\t", addSlashes($uneLigne));
			
			// Requete pour inserer les donnees (12 en tout)
			$sql="INSERT INTO spip_georgc VALUES ('"
			.$tableauValeurs[0]."', '".$tableauValeurs[1]."', '".$tableauValeurs[2]."', '"
			.$tableauValeurs[3]."', '".$tableauValeurs[4]."', '".$tableauValeurs[5]."', '"
			.$tableauValeurs[6]."', '".$tableauValeurs[7]."', '".$tableauValeurs[8]."', '"
			.$tableauValeurs[9]."', '".$tableauValeurs[10]."', '".$tableauValeurs[11]."')"; 
			$req = spip_query_db($sql); 
			
			/*
			if (!$req)
			{	echo "ERREUR - $i : "
			.$tableauValeurs[0]."', '".$tableauValeurs[1]."', '".$tableauValeurs[2]."', '"
			.$tableauValeurs[3]."', '".$tableauValeurs[4]."', '".$tableauValeurs[5]."', '"
			.$tableauValeurs[6]."', '".$tableauValeurs[7]."', '".$tableauValeurs[8]."', '"
			.$tableauValeurs[9]."', '".$tableauValeurs[10]."', '".$tableauValeurs[11]."')"; 
				return;
			}
			*/
						
			if (feof($fichier)) 
			{	echo "OK"; 
				return;
			}
		}
		echo $pos." ".round(ftell($fichier)*100/filesize($rgc_file));
	}
}
?>