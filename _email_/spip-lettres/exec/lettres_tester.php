<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');


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
		
		$fond_message_html	= lettres_recuperer_meta('fond_message_html');
		$fond_message_texte	= lettres_recuperer_meta('fond_message_texte');
		$f = charger_fonction('assembler', 'public');
		$page_html	= $f($fond_message_html, array('id_lettre' => $id_lettre));
		$page_texte	= $f($fond_message_texte, array('id_lettre' => $id_lettre));
		$message_html	= $page_html['texte'];
		$message_texte	= $page_texte['texte'];

		$requete_auteurs = 'SELECT A.email
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$id_lettre.'"';
		$resultat_auteurs = spip_query($requete_auteurs);
		while ($arr = @spip_fetch_array($resultat_auteurs)) 
			lettres_envoyer_test($arr['email'], $titre, $message_html, $message_texte, $id_lettre);
		
		echo '<html><head><title>'._T('lettres:fenetre_envoi').'</title></head><body>';
		echo '<h1 style="position: absolute; top: 30px; left: 50px; width: 300px;">'._T('lettres:fenetre_envoi_termine').'</h1>';
		echo '<div style="position: absolute; top: 100px; left: 50px; background-color: green; width: 300px; height: 24px;"></div>';
		echo '<div style="position: absolute; top: 140px; left: 130px;"><a href="javascript: window.close();">'._T('lettres:fermer').'</a></div>';
		echo '</body></html>';

	}


?>