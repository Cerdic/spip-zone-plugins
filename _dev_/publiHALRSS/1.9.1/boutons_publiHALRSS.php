<?php

// ATTENTION SI IL Y A UNE ERREUR 
// TOUTE L'INTERFACE PRIVEE EST BLOQUEE

 
/**
 * Installation du bouton de gestion
 */
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_publiHAL',(_DIR_PLUGINS.end($p)));


function publiHAL_ajouterBoutons($boutons_admin) {
	  // on voit le bouton dans la barre "naviguer"
	  $boutons_admin['naviguer']->sousmenu['publihal']= new Bouton(
		"../"._DIR_PLUGIN_publiHAL."/img_pack/publiHAL.png",  // icone
		'publications'	// titre
		);
	return $boutons_admin;
}


//function publiHAL_ajouterOnglets($flux) {
//	$rubrique = $flux['args'];
//	return $flux;
//}

function publiHAL_afficheGauche($flux){
	global $connect_statut;
	$exec =  $flux['args']['exec'];
	if ($exec=='mots_edit' && $flux['args']['id_mot']){
		if(isset($GLOBALS['meta']['publiHAL_auteurs_publi'])){
			$id_mot = $flux['args']['id_mot'];
			$id_groupe=$GLOBALS['meta']['publiHAL_auteurs_publi'];
			$result = spip_query("SELECT titre, descriptif FROM spip_mots WHERE id_groupe=$id_groupe AND id_mot=$id_mot");
			// comme dans mots_edit.php ligne 60
			if ($row = spip_fetch_array($result)) {
				$titre=$row['titre'];
				$descriptif=$row['descriptif'];
				$flux['data'] .= debut_boite_info(true);
				$flux['data'] .= "<center>";
				$flux['data'] .= "<FONT FACE='Verdana,Arial,Sans,sans-serif' SIZE=2><B>Selection de l'auteur avec la chaîne :</B></FONT>";
				$flux['data'] .= "<br> <br><FONT FACE='Verdana,Arial,Sans,sans-serif'><B>".$descriptif."</B></FONT>";
				$flux['data'] .= " </center>";
//				$flux['data'] .= "<center><a href='" . generer_url_ecrire("publihal_publi","id_syndic_article=$id_syndic_article") . "'>";
//				$flux['data'] .= "<b>Ajout automatique aux publications ?</b></a></center>";
				if ($connect_statut == '0minirezo'){
					//$redirect=_request('redirect');
					$redirect = "id_mot=$id_mot";//self()
					$flux['data'] .=  "<br> <center><a href='". redirige_action_auteur('auteur_publi',"$id_mot", $GLOBALS['exec'], $redirect)
												 . "'>Ajout automatique aux publications</a></center> <br>";
				}
				$flux['data'] .= " <br> ".fin_boite_info(true);
			}
		}
	}elseif ($exec=='publihal_publi'){
		$id_syndic_article = $flux['args']['id_syndic_article'];
		$flux['data'] .= debut_boite_info(true);
		$flux['data'] .= "<center>";
		$flux['data'] .= "<font face='Verdana,Arial,Sans,sans-serif' size=1><b>PUBLICATION NUMÉRO :</b></font>";
		$flux['data'] .= "<br/><font face='Verdana,Arial,Sans,sans-serif' size=6><b>$id_syndic_article</b></font>";
		//$flux['data'] .= icone(_T('icone_retour'), generer_url_ecrire("publihal",""), "article-24.gif", "rien.gif",'',false);
		$flux['data'] .= "</center>";
		$flux['data'] .= fin_boite_info(true);
	}
	return $flux;
}
?>