<?php
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Aiguilleur de sortie :
| Generer le lien de telechargement
| Incrementation compteur
+--------------------------------------------+
| Merci a Greg pour le grand coup de menage (2.12) !
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

// requis spip .h.26/12 charger le bon format url 
charger_generer_url();


/*
| $origine ainsi que la distinction "admin" "redact" et "visteur",
| pour coordination avec plugin acces_restreint_groupe (plus tard !!)
| return '1' or '0'
*/
function droits_telecharge_auteur($id_auteur, $origine, $statut_doc) {
	$qaut=spip_query("SELECT statut FROM spip_auteurs WHERE id_auteur=$id_auteur");
	
	$raut=spip_fetch_array($qaut);
	$statut_aut=$raut['statut'];
	
	switch ($statut_aut) {
		case '0minirezo':
			$diffusion = '1';
		break;
		case '1comite':
			$diffusion = ($statut_doc<='2')? '1':'0';
		break;
		case '6forum':
			$diffusion = ($statut_doc<='1')? '1':'0';
		break;
	}
	return $diffusion;
}

//
// moteur ... action ! ... je le dis si je veux !
//

function action_dw2_out() {

	// arrive ici avec $id -> id_document
	$id = intval(_request('id'));
	
	// requis
	// 1 - lire globales config
	include_spip("inc/dw2_lireconfig");
	lire_dw2_config();
	//
	include_spip('inc/dw2_inc_ajouts'); // -> origine_doc()
	include_spip("inc/dw2_inc_hierarchie"); // -> hierarchie ; dependance_restriction()
	$adr_site=lire_meta("adresse_site");

	
	// qq variables ...
	$ip = $_SERVER["REMOTE_ADDR"];	// ip du visiteur 
	$tconn=3600*24;					// duree avant eraze : 24 heures 
	$timee = time() + $tconn;		// maintenant + 24h : date d'eraze
	$timevire = time();				// maintenant !
	

	// Faire un peu de menage ... Si time ip trop vieux .. on efface l'enreg !
	spip_query("DELETE FROM spip_dw2_triche WHERE time < $timevire");
	


	//
	// recherche le doc ($id)
	//
	// ## h.18/12 .. ajout de 'dw.restreint' ##
	$req_dw = spip_query("SELECT dw.id_document, dw.url, dw.heberge ".
					"FROM spip_dw2_doc dw, spip_documents sd ".
					"WHERE dw.id_document='$id' AND sd.id_document='$id' AND dw.statut='actif'");

	if(spip_num_rows($req_dw)) {
		$rec = spip_fetch_array($req_dw);
		$tabdoc = array('dw', $rec['url'], $rec['heberge']);
	}
	else {
		$req_spip = spip_query("SELECT id_document, fichier, distant FROM spip_documents WHERE id_document='$id'");
		$res = spip_fetch_array($req_spip);
		$tabdoc = array('sp', $res['fichier'], $res['distant']);
	}


	//
	// origine du doc, le doc est-il dans un art/rub 'publie'
	//
	$origine=origine_doc($id);
		// pour info :
		// $origine[0] => type origine (.. article, rubrique)
		// $origine[1] => id_ du doctype
		// $origine[2] (statut article...)	=> produit '1' donc doc trouve et un des 2 $tabdoc est rempli
		// 									=> produit '0' doc absent de spip	
	
	
	//
	// acces restreint ? #h.16/12
	//
	if($GLOBALS['dw2_param']['mode_restreint']=='oui' && !empty($origine[2])) {
		
		// test if Doc have restriction (if '0' no restriction so .. continue like mode restreint = no)
		# function controle : in spip_dw2_acces_restreint for document, article or (root)section
		# product : array s
		$hierarchie = hierarchie_doc($id);
		$restriction = dependance_restriction($id, 'document', $hierarchie, true);
		
		$statut_restrict = $restriction[0];
		
		if($statut_restrict>='1') {
			// verifier la session
			include_spip("inc/session");
			$auteur_session=verifier_session();
			if($auteur_session) {
				if($auteur_session > '1') {// id_auteur '1' ==> webmaster site
				$diffusion = droits_telecharge_auteur($auteur_session, $origine, $statut_restrict);
				// analyse return :
					if($diffusion=='0') {
						$origine[2]='0'; // stop "traitement" (see below)
						$flag_dwacces = '2'; // visitor without permission to download
					}
				}
			}
			else {
				$origine[2]='0'; // stop "traitement" (see below)
				$flag_dwacces = '1'; // visitor non recognize on the site
			}
		}
	}	
	
	
	//
	// traitement
	//
	// statut origine = 'publie' ('1') OK ..
	if($origine[2]=='1') {
		
		// doc commun dw et spip
		if($tabdoc[0]=='dw') {
		
			// incrementation compteur oui/non ... initialise :
			$increm = "";
						
			// mode anti triche activé ?
			if ($GLOBALS['dw2_param']['anti_triche']=="oui") {
				
				//On recherche si couple ip/id existe déjà dans la base
				$req_ipid = spip_query("SELECT * FROM spip_dw2_triche WHERE idsite LIKE '$id' AND ip LIKE '%$ip%'");
			
				//Si l'ip/id n'existe pas dans la table, on l'ajoute
				if(!spip_num_rows($req_ipid)) {
					spip_query("INSERT INTO spip_dw2_triche VALUES('','$ip','$id', '$timee')");
					#$nouv_insert = spip_insert_id();		
					$increm = "oui";
				}
			}
			else {
				$increm = "oui";
			}
			
			
			// incremente compteur doc, stats
			//
			if ($increm == "oui") {
				
				$date = date("Y-m-d");
								
				spip_query("UPDATE spip_dw2_doc SET total=total+1, dateur=NOW() WHERE id_document='$id'");
				
				//h.28/12 .. restreint .. crea||increm ligne sur table stats_auteurs
				
				if($auteur_session && $statut_restrict >= '1') {
					$rq = spip_query("SELECT * FROM spip_dw2_stats_auteurs WHERE id_doc='$id' AND id_auteur='$auteur_session'");
					
					if(!spip_num_rows($rq)) {
						spip_query("INSERT IGNORE INTO spip_dw2_stats_auteurs (date, id_auteur, id_doc, date_enreg) VALUES (CURDATE(), '$auteur_session', '$id', NOW())");
					spip_log('enreg stat auteur');
					}
				}
				
				// créa ligne || incrementation ligne .. dw2_stats
				$rst = spip_query("SELECT * FROM spip_dw2_stats WHERE date=CURDATE() AND id_doc='$id'");
				if(!spip_num_rows($rst)) {
					spip_query("INSERT IGNORE INTO spip_dw2_stats (date, id_doc, telech) VALUES ('$date', '$id', '1')");
				}
				else {
					spip_query("UPDATE spip_dw2_stats SET telech=telech+1 WHERE id_doc='$id' AND date='$date'");
				}
			}
			
			
			// et hop envois du Doc au visiteur
			//
			$url = $tabdoc[1]; // url
			$heberge = $tabdoc[2]; // heberge
			
			if ($heberge == "local")
				{ @header("Location: $adr_site$url"); }
			else if ($heberge == "distant")
				{ @header("Location: $url"); }
			else
				{ @header("Location: $heberge$url"); }
		
		}
		else {
			// doc spip uniquement, on envois
			$url = $tabdoc[1]; // fichier
			$distant = $tabdoc[2]; // distant
				
			if($distant=='oui')
				{ @header("Location: $url");}
			else
				{ @header("Location: $adr_site/$url"); }
		}

	}
	else {
		// non distribution du fichier
		// reconstitue page referer (sans referer, yep !)
		$function_generer = 'generer_url_'.$origine[0];
		
		//  auteur identifié mais pas les bon droits ('2') || auteur non identifié ('1')
		if(isset($flag_dwacces)) {
			redirige_par_entete($function_generer($origine[1])."&dwacces=".$flag_dwacces);
		}
		// doc invalide ou article non publié
		else {
		#	redirige_par_entete(generer_url_public('dw2_erreur_out',"cata=".$GLOBALS['dw2_param']['squelette_cata_public'],true));
			// renvois sur 'sommaire'
			redirige_par_entete(generer_url_public('',"dwacces=3",true));
		}
	}
			

} // fin function
?>
