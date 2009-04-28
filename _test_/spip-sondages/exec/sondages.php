<?php


	/**
	 * SPIP-Sondages
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	if (!defined("_ECRIRE_INC_VERSION")) return;
 	include_spip('inc/presentation');
	include_spip('sondages_fonctions');


	function exec_sondages() {

		if (!autoriser('editer', 'sondages')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$id_sondage = $_GET['id_sondage'];
		$sondage = new sondage($id_sondage);
		
		pipeline('exec_init',array('args'=>array('exec'=>'sondages','id_sondage'=>$id_sondage),'data'=>''));

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($sondage->titre, "naviguer", "sondages");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($sondage->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('sondagesprive:sondage_numero').' :';
		echo '<p>'.$sondage->id_sondage.'</p>';
		echo '</div>';

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('sondagesprive:ce_sondage').'</strong>';
		echo '<ul>';
		if ($sondage->statut == 'prepa') {
			echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('sondagesprive:en_cours_de_redaction').'</li>';
			echo '<li class="publie"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=publie', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('sondagesprive:a_publier').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('sondagesprive:a_supprimer').'</a></li>';
		}
		if ($sondage->statut == 'publie') {
			echo '<li class="prepa"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=prepa', false, true).'">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('sondagesprive:en_cours_de_redaction').'</a></li>';
			echo '<li class="publie selected">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('sondagesprive:publie').'</li>';
			echo '<li class="prop"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=purge', false, true).'">'.http_img_pack('puce-orange.gif', 'puce-orange', '')._T('sondagesprive:a_purger').'</a></li>';
			echo '<li class="refuse"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=termine', false, true).'">'.http_img_pack('puce-rouge.gif', 'puce-rouge', '')._T('sondagesprive:a_terminer').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('sondagesprive:a_supprimer').'</a></li>';
		}
		if ($sondage->statut == 'termine') {
			echo '<li class="prepa"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=prepa', false, true).'">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('sondagesprive:en_cours_de_redaction').'</a></li>';
			echo '<li class="publie"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=publie', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('sondagesprive:a_publier').'</a></li>';
			echo '<li class="refuse selected">'.http_img_pack('puce-rouge.gif', 'puce-rouge', '')._T('sondagesprive:termine').'</li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_sondage', 'id_sondage='.$sondage->id_sondage.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('sondagesprive:a_supprimer').'</a></li>';
		}
		echo '</ul>';
		echo '</li>';
		echo '</ul>';

		if ($sondage->statut == 'publie' or $sondage->statut == 'termine') {
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_sondage($sondage->id_sondage).'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('sondagesprive:voir_en_ligne').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_sondage($sondage->id_sondage).'" class="cellule-h" target="_blank">'._T('sondagesprive:voir_en_ligne').'</a></td></tr></table>';
		}

		echo '</div>';
		echo '</div>';
		echo '</div>';

		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_sondage', $sondage->id_sondage, 'sondages');

		echo afficher_objets('sondages_mini', _T('info_meme_rubrique'), array('FROM' => 'spip_sondages', 'WHERE' => 'id_rubrique='.intval($sondage->id_rubrique).' AND id_sondage!='.intval($sondage->id_sondage), 'ORDER BY' => 'maj DESC'));

		echo bloc_des_raccourcis(
				icone_horizontale(_T('sondagesprive:creer_nouveau_sondage'), generer_url_ecrire("sondages_edit"), _DIR_PLUGIN_SONDAGES."/prive/images/sondage-24.png", 'creer.gif', false).
				icone_horizontale(_T('sondagesprive:aller_liste_sondages'), generer_url_ecrire("sondages_tous"), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png', 'rien.gif', false)
			);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'sondages','id_sondage'=>$sondage->id_sondage),'data'=>''));

		echo creer_colonne_droite('', true);
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'sondages','id_sondage'=>$sondage->id_sondage),'data'=>''));

   		echo debut_droite('', true);

		$editer_mots = charger_fonction('editer_mots', 'inc');
		$dater = charger_fonction('dater', 'inc');

		$onglet_proprietes = $dater($sondage->id_sondage, true, $sondage->statut, 'sondage', 'sondages', $sondage->date);
		$onglet_proprietes.= $editer_mots('sondage', $sondage->id_sondage, $cherche_mot, $select_groupe, ($sondage->statut == 'publie'), '', 'sondages');
		$onglet_proprietes.= '<div id="choix_tous" style="position: relative;">';
		$onglet_proprietes.= afficher_objets('choix_sondage', _T('sondagesprive:choix'), array('FROM' => 'spip_choix', 'WHERE' => 'id_sondage='.intval($sondage->id_sondage), 'ORDER BY' => 'ordre'));
		$onglet_proprietes.= http_img_pack("searching.gif", ' ', ' id="searching-choix" style="position: absolute; top: 3px; right: 3px; visibility: hidden;"');
		$onglet_proprietes.= '</div>';

		if ($sondage->statut != 'termine') {
			$onglet_proprietes.= '<div style="float: right;">';
			$onglet_proprietes.= icone_inline(_T('sondagesprive:ajouter_choix'), generer_url_ecrire('choix_edit', 'id_sondage='.$sondage->id_sondage.'&id_choix=-1'), _DIR_PLUGIN_SONDAGES.'/prive/images/radio.png', "creer.gif", $GLOBALS['spip_lang_left']);
			$onglet_proprietes.= '</div>'."\n";
		}

		$contexte = array('id' => $sondage->id_sondage);
		$fond = recuperer_fond("prive/contenu/sondage", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'sondage', 'id_objet' => $sondage->id_sondage, 'contexte' => $contexte), 'data' => $fond));
		$onglet_contenu = "<div id='wysiwyg'>$fond</div>";

		if ($sondage->statut != 'termine')
			$onglet_documents = sondages_documents('sondage', intval($sondage->id_sondage));
	
		echo '<div class="fiche_objet">';

		if ($sondage->statut != 'termine') {
			echo '<div class="bandeau_actions">';
			echo '<div style="float: right;">';
			echo icone_inline(_T('sondagesprive:modifier_sondage'), generer_url_ecrire("sondages_edit", "id_sondage=".$sondage->id_sondage), _DIR_PLUGIN_SONDAGES.'/prive/images/sondage-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
			echo '</div>';
			echo '</div>';
		}
		echo '<h1>'.$sondage->titre.'</h1>';
		
		echo '<br class="nettoyeur" />';

	  	echo afficher_onglets_pages(
			  	array(
				  	'props' => _T('onglet_proprietes'),
				  	'voir' => _T('onglet_contenu'),
				  	'docs' => _T('onglet_documents')
				),
			  	array(
				    'props' => $onglet_proprietes,
				    'voir' => $onglet_contenu,
				    'docs' => $onglet_documents
				)
			);

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'sondages','id_sondage'=>$sondage->id_sondage),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

		echo fin_gauche();

		echo fin_page();

	}


	function sondages_documents($type, $id) {
		global $spip_lang_left, $spip_lang_right;

		// Joindre ?
		if  ($GLOBALS['meta']["documents_$type"]=='non'
		OR !autoriser('joindre', $type, $id))
			$res = '';
		else {
			$joindre = charger_fonction('joindre', 'inc');

			$res = $joindre(array(
				'cadre' => 'relief',
				'icone' => 'image-24.gif',
				'fonction' => 'creer.gif',
				'titre' => _T('titre_joindre_document'),
				'script' => 'sondages',
				'args' => "id_sondage=$id",
				'id' => $id,
				'intitule' => _T('info_telecharger_ordinateur'),
				'mode' => 'document',
				'type' => 'sondage',
				'ancre' => '',
				'id_document' => 0,
				'iframe_script' => generer_url_ecrire("documenter","id_sondage=$id&type=$type",true)
			));

			// eviter le formulaire upload qui se promene sur la page
			// a cause des position:relative incompris de MSIE
			if ($GLOBALS['browser_name']!='MSIE') {
				$res = "\n<table style='float: $spip_lang_right' width='50%' cellpadding='0' cellspacing='0' border='0'>\n<tr><td style='text-align: $spip_lang_left;'>\n$res</td></tr></table>";
			}

			$res .= http_script('',"async_upload.js")
			  . http_script('$("form.form_upload").async_upload(async_upload_portfolio_documents);');
		}

		$documenter = charger_fonction('documenter', 'inc');

		return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
		. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>"
		. $res;
	}


?>