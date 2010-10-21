<?php


	/**
	 * SPIP-Formulaires
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
	include_spip('formulaires_fonctions');


	function exec_formulaires() {
		
		global $spip_lang_right;

		$id_formulaire = intval($_GET['id_formulaire']);
		if (!autoriser('voir', 'formulaires', $id_formulaire)) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}

		$formulaire = new formulaire($_GET['id_formulaire']);
		
		pipeline('exec_init',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$formulaire->id_formulaire),'data'=>''));

		$url = generer_url_ecrire('formulaires', 'id_formulaire='.$formulaire->id_formulaire, true);

		if (!empty($_GET['supprimer_auteur'])) {
			$formulaire->supprimer_auteur(intval($_GET['supprimer_auteur']));
			header('Location: ' . $url);
			exit();
		}

		if (!empty($_POST['ajouter_auteur'])) {
			$formulaire->ajouter_auteur(intval($_POST['id_auteur']));
			header('Location: ' . $url);
			exit();
		}


		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page($formulaire->titre, "naviguer", "lettres");

		echo debut_grand_cadre(true);
		echo afficher_hierarchie($formulaire->id_rubrique);
		echo fin_grand_cadre(true);

		echo debut_gauche('', true);
		echo '<div class="cadre cadre-info verdana1">';
		echo '<div class="cadre_padding">';
		echo '<div class="infos">';
		echo '<div class="numero">';
		echo _T('formulairesprive:formulaire_numero').' :';
		echo '<p>'.$formulaire->id_formulaire.'</p>';
		echo '</div>';

		echo '<ul class="instituer instituer_article">';
		echo '<li>';
		echo '<strong>'._T('formulairesprive:ce_formulaire_est').'</strong>';
		echo '<ul>';
		if ($formulaire->statut == 'hors_ligne') {
			echo '<li class="prepa selected">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('formulairesprive:hors_ligne').'</li>';
			if (autoriser('publierdans','rubrique',$formulaire->id_rubrique)) {
				echo '<li class="publie"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=en_ligne', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('formulairesprive:a_mettre_en_ligne').'</a></li>';
			}
			if ($formulaire->possede_applications())
				echo '<li class="publie"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=export', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('formulairesprive:exporter_resultats').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('formulairesprive:a_supprimer').'</a></li>';
		}
		if ($formulaire->statut == 'en_ligne') {
			echo '<li class="publie"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=hors_ligne', false, true).'">'.http_img_pack('puce-blanche.gif', 'puce-blanche', '')._T('formulairesprive:a_mettre_hors_ligne').'</a></li>';
			echo '<li class="publie selected">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('formulairesprive:a_mettre_en_ligne').'</li>';
			if ($formulaire->possede_applications())
				echo '<li class="publie"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=export', false, true).'">'.http_img_pack('puce-verte.gif', 'puce-verte', '')._T('formulairesprive:exporter_resultats').'</a></li>';
			echo '<li class="poubelle"><a href="'.generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=poubelle', false, true).'">'.http_img_pack('puce-poubelle.gif', 'puce-poubelle', '')._T('formulairesprive:a_supprimer').'</a></li>';
		}
		echo '</ul>';
		echo '</li>';
		echo '</ul>';

		if ($formulaire->statut == 'en_ligne') {
			echo '<table class="cellule-h-table" cellpadding="0" style="vertical-align: middle"><tr><td><a href="'.generer_url_formulaire($formulaire->id_formulaire).'" class="cellule-h" target="_blank"><span class="cell-i"><img src="../prive/images/rien.gif" alt="'._T('formulairesprive:voir_en_ligne').'"  style="background: url(../prive/images/racine-24.gif) center center no-repeat;" /></span></a></td><td class="cellule-h-lien"><a href="'.generer_url_formulaire($formulaire->id_formulaire).'" class="cellule-h" target="_blank">'._T('formulairesprive:voir_en_ligne').'</a></td></tr></table>';
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';

		$iconifier = charger_fonction('iconifier', 'inc');
		echo $iconifier('id_formulaire', $formulaire->id_formulaire, 'formulaires');

		echo afficher_objets('formulaires_mini', _T('info_meme_rubrique'), array('FROM' => 'spip_formulaires', 'WHERE' => 'id_rubrique='.intval($formulaire->id_rubrique).' AND id_formulaire!='.intval($formulaire->id_formulaire), 'ORDER BY' => 'maj DESC'));

		$raccourcis = icone_horizontale(_T('formulairesprive:creer_nouveau_formulaire'), generer_url_ecrire("formulaires_edit"), _DIR_PLUGIN_FORMULAIRES."/prive/images/formulaire-24.png", 'creer.gif', false);
		$raccourcis.= icone_horizontale(_T('formulairesprive:aller_liste_formulaires'), generer_url_ecrire("formulaires_tous"), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', 'rien.gif', false);
		$raccourcis.= icone_horizontale(_T('formulairesprive:copier_ce_formulaire'), generer_url_action('statut_formulaire', 'id_formulaire='.$formulaire->id_formulaire.'&statut=copie', false, true), _DIR_PLUGIN_FORMULAIRES.'/prive/images/copie.png', 'rien.gif', false);
		echo bloc_des_raccourcis($raccourcis);

		echo pipeline('affiche_gauche',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$id_formulaire),'data'=>''));

		creer_colonne_droite();
		echo pipeline('affiche_droite',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$id_formulaire),'data'=>''));

    	echo debut_droite('', true);

		$dater = charger_fonction('dater', 'inc');
		$editer_mots = charger_fonction('editer_mots', 'inc');

		$onglet_proprietes = $dater($formulaire->id_formulaire, true, $formulaire->statut, 'formulaire', 'formulaires', $formulaire->date);
		$onglet_proprietes.= $editer_mots('formulaire', $formulaire->id_formulaire, $cherche_mot, $select_groupe, true, '', 'formulaires');
		$onglet_proprietes.= formulaires_afficher_auteurs($formulaire->id_formulaire);

		$config.= debut_cadre_enfonce($icone='', true);
		$config.= '<table>';
		$config.= '<tr><td>'._T('formulairesprive:type_formulaire').'</td><td><strong>'._T('formulairesprive:'.$formulaire->type).'</strong></td></tr>';
		$config.= '<tr><td>'._T('formulairesprive:limiter_invitation').'</td><td><strong>'._T('formulairesprive:'.$formulaire->limiter_invitation).'</strong></td></tr>';
		if ($formulaire->limiter_invitation == 'non')
			$config.= '<tr><td>'._T('formulairesprive:limiter_applicant').'</td><td><strong>'._T('formulairesprive:'.$formulaire->limiter_applicant).'</strong></td></tr>';
		$config.= '<tr><td>'._T('formulairesprive:notifier_auteurs').'</td><td><strong>'._T('formulairesprive:'.$formulaire->notifier_auteurs).'</strong></td></tr>';
		$config.= '<tr><td>'._T('formulairesprive:notifier_applicant').'</td><td><strong>'._T('formulairesprive:'.$formulaire->notifier_applicant).'</strong></td></tr>';
		$config.= '</table>';
		$config.= fin_cadre_enfonce(true);

		$onglet_proprietes.= $config;
		
		$contexte = array('id' => $formulaire->id_formulaire);
		$fond = recuperer_fond("prive/contenu/formulaire", $contexte);
		$fond = pipeline('afficher_contenu_objet', array('args' => array('type' => 'formulaire', 'id_objet' => $formulaire->id_formulaire, 'contexte' => $contexte), 'data' => $fond));
		$onglet_contenu = '<div id="wysiwyg">'.$fond.'</div>';

		$onglet_documents = formulaires_documents('formulaire', intval($formulaire->id_formulaire));
	
		echo '<div class="fiche_objet">';

		echo '<div class="bandeau_actions">';
		echo '<div style="float: right;">';
		echo icone_inline(_T('formulairesprive:modifier_formulaire'), generer_url_ecrire("formulaires_edit", "id_formulaire=".$formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/formulaire-24.png', "edit.gif", $GLOBALS['spip_lang_left']);
		echo '</div>';
		echo '</div>';

		echo '<h1>'.$formulaire->titre.'</h1>';
		
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

		echo pipeline('affiche_milieu',array('args'=>array('exec'=>'formulaires','id_formulaire'=>$id_formulaire),'data'=>''));

		echo '</div><!-- fin fiche_objet -->';

		echo '<div id="blocs_tous" style="position: relative; padding-top: 10px;">';
		echo $formulaire->afficher();
		echo http_img_pack("searching.gif", ' ', ' id="searching-formulaire" style="position: absolute; top: 3px; right: 3px; visibility: hidden;"');
		echo '</div>';

		echo icone_inline(_T('formulairesprive:creer_nouveau_bloc'), generer_url_ecrire("blocs_edit","id_formulaire=".$formulaire->id_formulaire."&new=oui"), _DIR_PLUGIN_FORMULAIRES.'/prive/images/bloc.png', "creer.gif", $spip_lang_right);

		echo '<br class="nettoyeur" />';
		echo afficher_objets('application', _T('formulairesprive:liste_applications'), array('FROM' => 'spip_applications', 'WHERE' => 'id_formulaire='.intval($formulaire->id_formulaire), 'ORDER BY' => 'maj DESC'));
		if ($formulaire->limiter_invitation == 'oui') {	
			echo icone_inline(_T('formulairesprive:creer_invitation'), generer_url_ecrire("invitations_edit","id_formulaire=".$formulaire->id_formulaire), _DIR_PLUGIN_FORMULAIRES.'/prive/images/invitation.png', "creer.gif", $spip_lang_right);
		}

		echo fin_gauche();

		echo fin_page();


	}


	function formulaires_documents($type, $id)
	{
		global $spip_lang_left, $spip_lang_right;

		// Joindre ?
		if  ($GLOBALS['meta']["documents_$type"]=='non'
		OR !autoriser('joindredocument', $type, $id))
			$res = '';
		else {
			$joindre = charger_fonction('joindre', 'inc');

			$res = $joindre(array(
				'cadre' => 'relief',
				'icone' => 'image-24.gif',
				'fonction' => 'creer.gif',
				'titre' => _T('titre_joindre_document'),
				'script' => 'formulaires',
				'args' => "id_formulaire=$id",
				'id' => $id,
				'intitule' => _T('info_telecharger_ordinateur'),
				'mode' => 'document',
				'type' => 'formulaire',
				'ancre' => '',
				'id_document' => 0,
				'iframe_script' => generer_url_ecrire("documenter","id_formulaire=$id&type=$type",true)
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

		$flag_editable = autoriser('modifier', $type, $id);

		return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
		. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>"
		. $res;
	}

?>