<?php

	include_spip('inc/lettres_fonctions');
	include_spip('inc/lettres_admin');
 	include_spip('inc/presentation');
	include_spip('inc/barre');
	include_spip('inc/documents');


	/**
	 * exec_lettres_edition
	 *
	 * Edition d'une nouvelle lettre
	 *
	 * @author Pierre Basson
	 **/
	function exec_lettres_edition() {

		lettres_verifier_droits();

		if (!empty($_GET['id_lettre'])) {
			$id_lettre = $_GET['id_lettre'];
			$requete_lettre = 'SELECT titre, descriptif, texte, lang, statut FROM spip_lettres WHERE id_lettre="'.$id_lettre.'" LIMIT 1';
			$resultat_lettre = spip_query($requete_lettre);
			list($titre, $descriptif, $texte, $lang, $statut) = spip_fetch_array($resultat_lettre);
			$titre		= entites_html($titre);
			$descriptif	= entites_html($descriptif);
			$texte		= entites_html($texte);
			$onfocus	= '';
			if ($statut == 'envoi_en_cours') {
				$url = generer_url_ecrire('lettres_envoi', 'id_lettre='.$id_lettre, '&');
				lettres_rediriger_javascript($url);
			}
		} else {
			$new		= true;
			$titre		= _T('lettres:nouvelle_lettre');
			$descriptif	= '';
			$texte		= '';
			$onfocus	= " onfocus=\"if(!antifocus){this.value='';antifocus=true;}\"";
			$id_lettre	= -1;
			$statut		= 'brouillon';
			$lang		= lire_meta('langue_site');
		}
		

		debut_page($titre, "lettres", "lettres");


		debut_gauche();
		if (!$new) {
			maj_documents($id_lettre, 'lettre');
			afficher_documents_colonne($id_lettre, "lettre", true);
		}
		
    	debut_droite();
		echo "<br />";
		debut_cadre_formulaire();
		echo "\n<table cellpadding=0 cellspacing=0 border=0 width='100%'>";
		echo "<tr width='100%'>";
		echo "<td>";
		if ($new)
			icone(_T('icone_retour'), generer_url_ecrire("lettres"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "rien.gif");
		else
			icone(_T('icone_retour'), generer_url_ecrire("lettres_visualisation", "id_lettre=$id_lettre"), '../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', "rien.gif");

		echo "</td>";
		echo "<td>". http_img_pack('rien.gif', " ", "width='10'") . "</td>\n";
		echo "<td width='100%'>";
		echo _T('lettres:modifier_lettre');
		gros_titre($titre);
		echo "</td></tr></table>";

		echo "<P><HR></P>";

		echo generer_url_post_ecrire("lettres_visualisation", ($id_lettre ? "id_lettre=$id_lettre" : ""), 'formulaire');

		echo "<INPUT TYPE='hidden' NAME='lang' VALUE='$lang'>";
		echo "<INPUT TYPE='hidden' NAME='statut' VALUE='$statut'>";

		echo _T('lettres:titre');
		echo "<BR><INPUT TYPE='text' NAME='titre' style='font-weight: bold; font-size: 13px;' CLASS='formo' VALUE=\"$titre\" SIZE='40' $onfocus><P>";

		echo "<P><B>"._T('lettres:descriptif')."</B>";
#		echo afficher_barre('document.formulaire.descriptif');
		echo "<TEXTAREA NAME='descriptif' CLASS='forml' ROWS='2' COLS='40' wrap=soft>";
		echo $descriptif;
		echo "</TEXTAREA></P>\n";

		echo "<p><B>"._T('lettres:texte')."</B>";
		echo "<br>"._T('texte_enrichir_mise_a_jour');
		echo aide("raccourcis");
		echo afficher_barre('document.formulaire.texte');
		echo "<TEXTAREA id='text_area' NAME='texte' ".$GLOBALS['browser_caret']." CLASS='formo' ROWS='20' COLS='40' wrap=soft>";
		echo $texte;
		echo "</TEXTAREA></p>\n";

		echo "<DIV ALIGN='right'>";
		echo "<INPUT CLASS='fondo' TYPE='submit' NAME='enregistrer' VALUE='"._T('lettres:enregistrer')."'>";
		echo "</DIV></FORM>";

		fin_cadre_formulaire();

		fin_page();

	}

?>