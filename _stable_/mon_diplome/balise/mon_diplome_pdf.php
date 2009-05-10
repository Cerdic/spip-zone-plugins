<?php

// balise/mon_diplome_pdf.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function plom_alert_and_die ($msg) {
	header('Content-Type: text/html; charset=utf-8');
	die(utf8_encode(html_entity_decode($msg)));
}

/**
 *Donne le chemin (path) du calque
 *@return string
 */
function plom_chemin_calque ($calque, $ext = "html", $options = null) {
	
	global $plom_options;
	
	$calque =
		($options && ($f = $options[$calque]))
		? $f
		: $plom_options[$calque]
		;
	$calque = "modeles/" . $calque . $ext;
	
	$result = find_in_path($calque);
	
	return ($result);
}


function balise_MON_DIPLOME_PDF ($p)
{
	return(calculer_balise_dynamique($p, 'MON_DIPLOME_PDF', array()));
}

function balise_MON_DIPLOME_PDF_stat ($args, $filtres)
{
	// la balise ne gère pas de filtre
	// si filtre présent, les $args ne sont pas reçus
	return (array(rawurlencode(serialize($args))));
}

function balise_MON_DIPLOME_PDF_dyn ($args)
{
	global $plom_options;
	
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	
	
	if($connect_id_auteur)
	{
		
		$queries = unserialize(rawurldecode($args));
		
		$result = array();
		
		// prendre les paramètres transmis dans le squelette
		foreach($queries as $query)
		{
			if(strpos($query, "=")) {
				list($key, $val) = explode("=", $query);
				if(array_key_exists($key, $plom_options))
				{
					$val = trim($val, "\"'");
					$result[$key] = $val;
				}
			}
		}
		$queries = $result;

		// complète par les valeurs par défaut
		foreach($plom_options as $key => $val)
		{
			if(!isset($queries[$key])) {
				$queries[$key] = $val;
			}
		}
		
		// sauf listing des diplômes,
		// c'est uniquement celui du connecté qui est demandé
		if(!$queries['id_auteur']) {
			$queries['id_auteur'] = $connect_id_auteur;
		}

		$queries['nom'] =
			($ii = $GLOBALS['auteur_session']['nom'])
			? ucwords($ii)
			: _T('plom:inconnu')
			;

		// calcul des chemins (paths)
		if($queries['appliquer_fond'] == 'oui')
		{
			// le modele de fond est (doit être) un png
			$calque = 'modele_fond';
			$ext = ".png";
			
			$queries[$calque] =
				($f = plom_chemin_calque ($calque, $ext, $queries))
				? $f
				: plom_alert_and_die (_T('plom:erreur_fichier_s_manquant', array('s' => $queries[$calque].$ext)))
				;
		}
		
		$calque = 'modele_texte';
		$ext = ".html";
			
		$queries[$calque] =
				($f = plom_chemin_calque ($calque, $ext, $queries))
				? $f
				: plom_alert_and_die (_T('plom:erreur_fichier_s_manquant', array('s' => $queries[$calque].$ext)))
				;
		
		
		$contexte = array(
			'msg' => "Un message a faire passer "
			, 'fond' => "modeles/mon_diplome"
			, 'nom' => $queries['nom']
			, 'diplome_ligne_1' => _T('plom:diplome_ligne_1')
			, 'diplome_etudes_sup' => _T('plom:diplome_etudes_sup')
			, 'diplome_ligne_2' => _T('plom:diplome_ligne_2')
			, 'diplome_ligne_3' => _T('plom:diplome_ligne_3')
			, 'diplome_ligne_4' => _T('plom:diplome_ligne_4')
		);
				
		// construction à partir du squelette
		$content = recuperer_fond("modeles/mon_diplome", $contexte);
		
		include_spip("html2pdf/html2pdf.class");
		
		$html2pdf = new HTML2PDF(
								 (($queries['orientation'] == "paysage") ? 'P' : 'L')
								 , $queries['format']
								 , $GLOBALS['auteur_session']['lang']
								 , array(0, 0, 0, 0) // marges
								 );
		
		
		// afficher la page en entier
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->pdf->SetAutoPageBreak(false, 0);
		$html2pdf->pdf->SetAuthor($queries['author'], true); // titre du site
		$html2pdf->pdf->SetCreator($queries['creator'], true);
		$html2pdf->pdf->SetMargins(0, 0);
		$html2pdf->pdf->SetSubject("Votre diplôme", true);
		$html2pdf->pdf->SetTitle("Votre diplôme", true);
		
		$fond = find_in_path('modeles/mon_diplome.png');
		
		// fond de page 
		$html2pdf->background = array('img' => $fond, 'posX' => 0, 'posY' => 0
									, 'width' => 297 // en mm !
									);  
	
		
		// conversion
		$html2pdf->WriteHTML($content, false);
	
		// Récupérer le résultat
		$result = $html2pdf->Output();
		
		// Envoyer
		header("Content-type: application/pdf");
		header('Content-Disposition: attachment; filename=mon_diplome.pdf');
		echo ($result);
		
		// Fin
		exit;
	}
	return (null);
}



?>