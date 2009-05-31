<?php 

	// balise/formulaire_ecrire_livre_dor.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiDo.
	
	LiDo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiDo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiDo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiDo. 
	
	LiDo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiDo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
include_spip('inc/utils');
include_spip('inc/filtres');
include_spip('inc/plugin_globales_lib');
	
function balise_FORMULAIRE_ECRIRE_LIVRE_DOR ($p, $nom='FORMULAIRE_ECRIRE_LIVRE_DOR') {
	return calculer_balise_dynamique($p, $nom, array());
}

function balise_FORMULAIRE_ECRIRE_LIVRE_DOR_stat ($args, $filtres) {
	
	$config = __plugin_lire_key_in_serialized_meta('config', _LIDO_META_PREFERENCES);

	if(
		($config['lido_id_rubrique'] > 0)
		&& (
			($config['lido_table_destination'] == 'articles')
			|| ($config['lido_table_destination'] == 'breves')
			)
	) {
		return(
			array(
				$config['lido_id_rubrique']
				, "spip_".$config['lido_table_destination']
				, $config['lido_id_auteur']
				, $config['lido_prevenir_moderateur']
				, $config['lido_email_moderateur']
				, $config['lido_email_tag']
				, $config['lido_valider_auto']
			)
		);
	}
	lido_log(__LIDO_PRE_LOG." STOP! (please, configure)");
	return('');
}

function balise_FORMULAIRE_ECRIRE_LIVRE_DOR_dyn ($id_rubrique, $table, $id_auteur, $prevenir, $email, $email_tag, $valider_auto) {

	static $id_incr = 1;
	global $spip_lang_right;
	
	$texteko = $commentaire_envoye = "";
	$validable = false;

	$id = intval(_request('num_formulaire_livre_dor'));

	lido_log("num_formulaire_livre_dor: $id");
	
	if(($id > 0) && (_request('valide') || (_request('confirmer'.$id)))) {
	
		$texte = trim(_request('texte'.$id));
		$signature = ucwords(trim(_request('signature'.$id)));
		
		if(strlen($texte) > _LIDO_COMMENT_MAX_LEN) {
			$texteko = _T('lido:texte_trop_long');
		} else if(strlen($texte) < _LIDO_COMMENT_MIN_LEN) {
			$texteko = _T('lido:texte_trop_court');
		} else {
			if(_request('confirmer'.$id)) {
			
				$titre = couper($texte, 32).(strlen($signature) ? " ...".$signature : "");
				
				$texte = "<span class='commentaire'>".$texte."</span>\n<span class='signature'>".$signature."</span>";

				if ($GLOBALS['meta']['multi_articles'] == 'oui') {
					lang_select($GLOBALS['auteur_session']['lang']);
					if (in_array($GLOBALS['spip_lang'],
						explode(',', $GLOBALS['meta']['langues_multilingue']))) {
						$lang = $GLOBALS['spip_lang'];
						$choisie = 'oui';
					}
				}
				
				$row = spip_fetch_array(spip_query("SELECT lang, id_secteur FROM spip_rubriques WHERE id_rubrique=$id_rubrique"));
				$id_secteur = $row['id_secteur'];
				if (!$lang) {
					$lang = $GLOBALS['meta']['langue_site'];
					$choisie = 'non';
					$lang = $row['lang'];
				}
				
				$statut = ($valider_auto == 'oui') ? "publie" : "prop";
				
				if($table == 'spip_articles') {
					$sql_query = "INSERT INTO $table 
						(titre, texte, id_rubrique, id_secteur, statut, date, accepter_forum, lang, langue_choisie)
						VALUES
						("._q($titre).", "._q($texte).", $id_rubrique, $id_secteur, '$statut', NOW(), '" 
							. substr($GLOBALS['meta']['forums_publics'],0,3) . "', '$lang', '$choisie')
						";
				} else {
					$sql_query = "INSERT INTO spip_breves
						(titre, texte, id_rubrique, statut, date_heure, lang, langue_choisie)
						VALUES
						("._q($titre).", "._q($texte).", $id_rubrique, '$statut', NOW(), '$lang', '$choisie')
						";
				}
				$sql_result = spip_query($sql_query);
				if($sql_result) {
					$id_comment = spip_insert_id();
					if($id_comment && ($id_auteur > 0)) {
						// attribue l'article à un auteur
						spip_abstract_insert(
							'spip_auteurs_articles'
							, "(id_auteur,id_article)"
							, "($id_auteur, $id_comment)"
						);
					}
					lido_log(_LIDO_PRE_LOG." record comment #$id_comment into $table");
				}
				
				if(($prevenir == 'oui') && email_valide($email)) {
					include_spip('inc/urls');
					
					$texte .= ""
						. "\n\n-- "._T('envoi_via_le_site')." "
						. (supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site'])))
						. " (".$GLOBALS['meta']['adresse_site']."/) --\n";
					include_spip('inc/mail');
					$from = $GLOBALS['meta']['email_webmaster'];
					if(!email_valide($from)) {
						$from = $mail;
					}
					$hr = "\n".str_repeat('-', 40)."\n";
					$text_mail = _T('lido:commentaire_poste', array('nom_site'=>$GLOBALS['meta']['nom_site']))
						. (($valider_auto == 'oui') ? "" : _T('lido:commentaire_a_valider'))
						. "\n"
						. (
							($table == 'spip_articles')
							? generer_url_ecrire_article($id_comment)
							: generer_url_ecrire_breve($id_comment)
							)
						. "\n"
						. _T('lido:commentaire_contenu_')
						. $hr
						. strip_tags($texte)
						. $hr
						;
					envoyer_mail($email, $email_tag." ".$titre, $text_mail, $from, "X-Originating-IP: ".$GLOBALS['ip']);
					lido_log(_LIDO_PRE_LOG." send mail to $email");
				}
				$commentaire_envoye = _T('lido:commentaire_envoye')
					. (($valider_auto == 'oui') ? "" : _T('lido:commentaire_modere'))
					. _T('lido:commentaire_merci')
					;
			} else {
				$validable = true;
			}
		}
		if(!empty($texteko)) {
			$texteko = _T('lido:desole').$texteko._T('lido:merci_corriger');
		}
		if(strlen($signature) > _LIDO_SIGN_MAX_LEN) {
			$signature = substr($signature, 0, _LIDO_SIGN_MAX_LEN);
		}
	} else {
		// incrémente id du formulaire (pour en avoir plusieurs sur une meme page, on ne sait jamais 8-)
		$id = $id_incr++;
		$texte = $signature = "";
	}
	
	return array('formulaires/livre_dor', 0 
		, array(
			'id' => $id
			, 'bouton' => filtrer_entites(_T('lido:previsualiser_commentaire'))
			, 'bouton_align' => $spip_lang_right
			, 'texte' => $texte
			, 'texteko' => $texteko
			, 'signature' => $signature
			, 'bouton_confirmation' => $validable ? _T('form_prop_confirmer_envoi') : ''
			, 'commentaire_envoye' => $commentaire_envoye
		)
	);
}

?>