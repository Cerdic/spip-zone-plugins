<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');


	/**
	 * exec_lettres_envoyer
	 *
	 * fenêtre popup pour l'envoi des lettres
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_envoyer() {

		lettres_verifier_droits();

		if (empty($_GET['id_archive'])) {
			echo '<script language="javascript" type="text/javascript">window.close();</script>';
			die();
		}
		
		$id_archive = intval($_GET['id_archive']);
		$requete_archive	= 'SELECT id_lettre,
		 							titre,
									message_html,
									message_texte
								FROM spip_archives 
								WHERE id_archive="'.$id_archive.'"';
		$resultat_archive = spip_query($requete_archive);
		list($id_lettre, $titre, $message_html, $message_texte) = spip_fetch_array($resultat_archive);
		
		$requete_a_envoyer	= 'SELECT id_abonne 
								FROM spip_abonnes_archives 
								WHERE id_archive="'.$id_archive.'"
									AND statut="a_envoyer"';
		$resultat_a_envoyer = spip_query($requete_a_envoyer);
		$a_envoyer = @spip_num_rows($resultat_a_envoyer);
		
		$requete_total 	= 'SELECT id_abonne 
							FROM spip_abonnes_archives 
							WHERE id_archive="'.$id_archive.'"';
		$resultat_total = spip_query($requete_total);
		$total = @spip_num_rows($resultat_total);
		
		$fait = $total - $a_envoyer;
		
		echo '<html><head><title>'._T('lettres:fenetre_envoi').'</title>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.$GLOBALS['meta']['charset'].'" />';
		echo '</head><body>';
		if ($total)
			$largeur = intval(($fait / $total) * 300);
		if ($a_envoyer == 0) {
			echo '<h1 style="position: absolute; top: 30px; left: 50px; width: 300px;">'._T('lettres:fenetre_envoi_termine').'</h1>';
			echo '<div style="position: absolute; top: 100px; left: 50px; background-color: green; width: 300px; height: 24px;"></div>';
			echo '<div style="position: absolute; top: 140px; left: 130px;"><a href="javascript: window.close(); window.opener.location.reload(true);">'._T('lettres:fermer').'</a></div>';
		} else {
			echo '<h1 style="position: absolute; top: 30px; left: 50px; width: 300px;">'._T('lettres:fenetre_envoi_en_cours').'</h1>';
			echo '<div style="position: absolute; top: 100px; left: 50px; background-color: red; width: 300px; height: 24px;"></div>';
			echo '<div style="position: absolute; top: 100px; left: 50px; background-color: green; width: '.$largeur.'px; height: 24px;"></div>';
		}
		echo '</body></html>';

		if ($a_envoyer != 0) {
			$requete_a_envoyer	= 'SELECT AR.id_abonne,
										A.format
									FROM spip_abonnes_archives AS AR
									INNER JOIN spip_abonnes AS A ON A.id_abonne=AR.id_abonne
									WHERE AR.id_archive="'.$id_archive.'"
										AND AR.statut="a_envoyer"
									LIMIT 5';
			$resultat_a_envoyer = spip_query($requete_a_envoyer);
			while ($arr = @spip_fetch_array($resultat_a_envoyer)) {
				$id_abonne	= $arr['id_abonne'];
				$format		= $arr['format'];
		 		$resultat_envoi = lettres_envoyer_lettre($id_abonne, $titre, $message_html, $message_texte, $id_lettre, $id_archive);
				if ($resultat_envoi)
					$statut = 'envoye';
				else
					$statut = 'echec';
				$modification = 'UPDATE spip_abonnes_archives SET statut="'.$statut.'", format="'.$format.'", maj=NOW() WHERE id_abonne="'.$id_abonne.'" AND id_archive="'.$id_archive.'"';
				spip_query($modification);
			}
			$url = generer_url_ecrire('lettres_envoyer', 'id_archive='.$id_archive, '&');
			lettres_rediriger_javascript($url);
		}
	}


?>