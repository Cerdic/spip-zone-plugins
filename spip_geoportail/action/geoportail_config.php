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
* Configuration des parametres
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/auth');
include_spip("inc/lang");
include_spip('inc/compat_192');
include_spip('inc/config');
// Repertoire geoportail
include_spip('geoportail');

function action_geoportail_config_dist() 
{	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// Qui est la ?
  /*
	$qui = $GLOBALS['auteur_session'];
	$qui['restreint'] = auth_rubrique($qui['id_auteur'], $qui['statut']);
  */

	// Admin total ?
	//if ( $qui['statut'] == '0minirezo' AND !$qui['restreint'] )
	if (autoriser('configurer','geoportail',0)) 
	{
		// Modif de la cle
 		if (isset($_POST['modifier']))
		{	ecrire_meta('geoportail_key',$_POST['geoportail_key']);
 			// Services
 			ecrire_meta('geoportail_service',isset($_POST['service']));
 			ecrire_metas();
 		}
 		// Modif des objets a georef
 		if (isset($_POST['objet']))
		{	ecrire_meta('geoportail_geoarticle',isset($_POST['article']));
			ecrire_meta('geoportail_geoauteur',isset($_POST['auteur']));
			ecrire_meta('geoportail_geodocument',isset($_POST['document']));
			ecrire_meta('geoportail_geodocument_auto',isset($_POST['docauto']));
			ecrire_meta('geoportail_georubrique',isset($_POST['rubrique']));
			ecrire_meta('geoportail_geomot',isset($_POST['mot']));
			ecrire_meta('geoportail_geobreve',isset($_POST['breve']));
			ecrire_meta('geoportail_geosyndic',isset($_POST['syndic']));
 			ecrire_metas();
 		}
 		
 		// Modif des objets a georef
 		if (isset($_POST['sysref']))
 		{	ecrire_meta('geoportail_sysref',$_POST['syscode']);
 			ecrire_metas();
 		}
 		
 		// RGC
 		if (isset($_POST['geoportail_norgc']))
 		{	spip_query_db ("TRUNCATE TABLE spip_georgc");
			ecrire_meta('geoportail_rgc',null);
 			ecrire_metas();
		}
		else
		{	// Rechercher quel RGC charger
			$count=0;
			while (isset($_POST["rgc_$count"]))
			{	$rgc = $_POST["rgc_$count"];
				if (isset($_POST["geoportail_".$rgc]) && $GLOBALS['meta']['geoportail_rgc'] != $rgc)
 				{	$rgc_file = "rgc/rgc.$rgc.txt";
 					ecrire_meta('geoportail_rgc',$rgc);
 					ecrire_metas();
 				}
 				$count++;
			}
		}
		if ($rgc_file)
		{	spip_query_db ("TRUNCATE TABLE spip_georgc");
			// Charger
			$query = "LOAD DATA LOCAL INFILE '"._FULLDIR_PLUGIN_GEOPORTAIL.$rgc_file."' INTO TABLE spip_georgc";
			spip_query_db ($query);
			$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
			// Essayer autrement...
			if (!$row) 
			{	$query = "LOAD DATA INFILE '"._FULLDIR_PLUGIN_GEOPORTAIL.$rgc_file."' INTO TABLE spip_georgc";
				spip_query_db ($query);
				$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
			}
/*
			// Essayons autre chose...
			if (!$row)
			{	// Le fichier a charger 
				$fichier = fopen(_FULLDIR_PLUGIN_GEOPORTAIL.$rgc_file, "r"); 

				// Import ligne par ligne...
				while (!feof($fichier)) 
				{ 	// On recupere toute la ligne
					$uneLigne = fgets($fichier, 1024);
					// On explise dans un tableau 
					$tableauValeurs = explode("\t", $uneLigne); 
					// Requete pour inserer les donnees (12 en tout)
					$sql="INSERT INTO spip_georgc VALUES ('"
					.$tableauValeurs[0]."', '".$tableauValeurs[1]."', '".$tableauValeurs[2]."', '"
					.$tableauValeurs[3]."', '".$tableauValeurs[4]."', '".$tableauValeurs[5]."', '"
					.$tableauValeurs[6]."', '".$tableauValeurs[7]."', '".$tableauValeurs[8]."', '"
					.$tableauValeurs[9]."', '".$tableauValeurs[10]."', '".$tableauValeurs[11]."')"; 
					$req = spip_query_db($sql); 
				}
				// Ca marche ?
				$row = spip_fetch_array(spip_query("SELECT * FROM spip_georgc LIMIT 0,1"));
			}
			// Zut rate...
			if (!$row) 
			{	$rgc_error = true;			
				ecrire_meta('geoportail_rgc',null);
 				ecrire_metas();
			}
*/			
			// Tenter un import ligne par ligne...
			if (!$row) redirige_par_entete (_DIR_RESTREINT.'./?exec=geoportail_importrgc');//generer_url_ecrire('geoportail_importrgc'));
		}
	}
	
	redirige_par_entete(_DIR_RESTREINT.urldecode(_request('redirect')));

}

?>