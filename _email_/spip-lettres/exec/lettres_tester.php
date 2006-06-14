<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Atypik Créations
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/distant');


	/**
	 * exec_lettres_tester
	 *
	 * fenêtre popup pour tester l'envoi des lettres
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_tester() {

		lettres_verifier_droits();

		if (empty($_GET['id_lettre'])) {
			echo '<script language="javascript" type="text/javascript">window.close();</script>';
			die();
		}
		
		$id_lettre = intval($_GET['id_lettre']);
		
		$requete_titre = 'SELECT titre FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
		list($titre) = spip_fetch_array(spip_query($requete_titre));
		$titre = _T('lettres:email_test').' '.$titre;
		
		$fond_message_html	= $GLOBALS['meta']['fond_message_html'];
		$fond_message_texte	= $GLOBALS['meta']['fond_message_texte'];
		$url_message_html	= generer_url_public($fond_message_html, 'id_lettre='.$id_lettre, '&');
		$url_message_texte	= generer_url_public($fond_message_texte, 'id_lettre='.$id_lettre, '&');
		$message_html	= recuperer_page($url_message_html);
		$message_texte	= recuperer_page($url_message_texte);

		$requete_auteurs = 'SELECT A.email
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$id_lettre.'"';
		$resultat_auteurs = spip_query($requete_auteurs);
		while ($arr = @spip_fetch_array($resultat_auteurs)) 
			lettres_envoyer_test($arr['email'], $titre, $message_html, $message_texte, $id_lettre);
		
		echo '<html><head><title>'._T('lettres:fenetre_envoi').'</title>';
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.$GLOBALS['meta']['charset'].'" />';
		echo '</head><body>';
		echo '<h1 style="position: absolute; top: 30px; left: 50px; width: 300px;">'._T('lettres:fenetre_envoi_termine').'</h1>';
		echo '<div style="position: absolute; top: 100px; left: 50px; background-color: green; width: 300px; height: 24px;"></div>';
		echo '<div style="position: absolute; top: 140px; left: 130px;"><a href="javascript: window.close();">'._T('lettres:fermer').'</a></div>';
		echo '</body></html>';

	}


?>