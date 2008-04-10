<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes                                       #
#                                                                                                      #
#  Définition de la balise #FORMULAIRE_ETIQUETTES                                                      #
#  Rappel d'utilisation :                                                                              #
#  #FORMULAIRE_ETIQUETTES{groupe_de_mots?, aide?, remplacer?, type_objet?, id_objet?, proposer_login?} #
#------------------------------------------------------------------------------------------------------#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_ETIQUETTES($p) {
	
    global $pilpil;
    $pilpil = $p;
    return calculer_balise_dynamique($p, 'FORMULAIRE_ETIQUETTES', array($p->boucles[$p->id_boucle]->primary));
    
}

function balise_FORMULAIRE_ETIQUETTES_stat($args, $filtres) {
	
    global $pilpil, $tables_jointures;
	
	// initialisation du groupe de mots-clés
		$groupe = $args[1] ? $args[1] : "tags";
	
		// On récupère l'id du groupe de mots
		$reponse = sql_fetsel(
			'id_groupe',
			'spip_groupes_mots',
			'titre='._q($groupe)
		);
	
		// Si c'est un bon numéro on le garde
		if (intval($reponse['id_groupe']) > 0)
			$id_groupe = intval($reponse['id_groupe']);
		// Sinon il faut créer le groupe
		else
			$id_groupe = sql_insertq('spip_groupes_mots', array('titre' => $groupe));
	
	// initialisation du type d'aide
		$aide = strtolower($args[2]);
		if (!strlen($aide) OR !in_array($aide, array("normal", "nuage", "ajax", "aucun", "aucune", "rien")))
			$aide = "normal";
		if ($aide = "nuage" AND !defined('_DIR_PLUGIN_NUAGE'))
			$aide = "normal";
		if ($aide = "ajax" AND !defined('_DIR_PLUGIN_SELECTEURGENERIQUE'))
			$aide = "normal";
	
	// initialisation du mode d'ajout des mots-clés
		// si on met rien ou n'importe quoi ça donne true
		$remplacer = !(strtolower($args[3]) == "false");
	
	// initialisation de la proposition de login en cas de mauvaise autorisation
		// si on met rien ou n'importe quoi, ça donne false
		$proposer_login = (strtolower($args[6]) == "true");
	
	// initialisation de l'objet à lier
		if ($args[4] AND $args[5]){
			// ici on a précisé explicitement un objet
			// il faut donc vérifier s'il existe bien
			include_spip('base/connect_sql');
			$type_objet = preg_replace(',^spip_|s$,', '', $args[4]);
			$type_objet = table_objet_sql($type_objet);
			$type_objet = preg_replace(',^spip_,', '', $type_objet);
			$cle_objet = id_table_objet($type_objet);
			$id_objet = intval($args[5]);
			
			$reponse = sql_fetsel(
				'*',
				'spip_'.$type_objet,
				$cle_objet.'='.$id_objet
			);
			if(!$reponse)
				return erreur_squelette(
					_T('etiquettes:zbug_objet_existe_pas',
						array (
							'champ' => '#FORMULAIRE_ETIQUETTES',
							'type' => $type_objet,
							'id' => $id_objet
						)
					), '');
		}else{
			// sinon on prend du contexte
			$id_objet = intval($args[0]);
			$type_objet = $pilpil->boucles[$pilpil->id_boucle]->id_table;
			$cle_objet = id_table_objet($type_objet);
			
			// mais on vérifie si la balise est effectivement dans un contexte
			if(!$type_objet OR $id_objet <= 0)
				return erreur_squelette(
					_T('zbug_champ_hors_boucle',
						array (
							'champ' => '#FORMULAIRE_ETIQUETTES',
						)
					)._T('etiquettes:zbug_et_parametre_manquant'), '');
		}
	
		// on ne peut pas continuer si le type choisi n'est pas relié à des mots-clés
		// autrement dit, s'il n'y a pas de table mots_machin
		if(!in_array("mots_$type_objet", $tables_jointures['spip_mots']))
			return erreur_squelette(
				_T('etiquettes:zbug_pas_de_table_mots',
					array (
						'type' => $type_objet,
					)
				), '');
    
    return $args = array($groupe, $id_groupe, $aide, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login);
	
}

function balise_FORMULAIRE_ETIQUETTES_dyn($groupe, $id_groupe, $aide, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login) {
	
	include_spip('inc/autoriser');
	include_spip('inc/filtres');
	
	// Les paramètres ont tous déjà été testés
	// Maintenant on teste si la personne a le droit d'ajouter des mots-clés au groupe choisi
	// ET si elle a le droit de modifier l'objet choisi
	// On ne va pas plus loin si pas d'autorisation
	if($aut1 = !autoriser('modifier', 'groupemots', $id_groupe, $GLOBALS['auteur_session'])
		OR $aut2 = !autoriser('modifier', preg_replace(',s$,','',$type_objet), $id_objet, $GLOBALS['auteur_session'])
	){
		$erreur_autorisation = true;
		$proposer_login &= true;
		$message_erreur = _T('etiquettes:pas_le_droit');
	}
	else{
		
		$proposer_login &= false;
		
		// si on vient du formulaire validé on le traite
		if (_request("valider_etiquettes-$groupe-$type_objet-$id_objet")){
			
			// On récupère les tags
			$etiquettes = trim(_request("etiquettes-$groupe-$type_objet-$id_objet"));
			// On utilise la tag-machine avec les millions de paramètres
			include_spip('inc/tag-machine');
			ajouter_liste_mots($etiquettes,$id_objet,$groupe,$type_objet,$cle_objet,$remplacer);
			
			// Si on a modifié, on renvoie la liste tel quel, ça évite une requête pour rien
			if ($remplacer)
				$etiquettes = entites_html($etiquettes);
			// Sinon c'est un formulaire d'ajout donc il apparaît toujours vide
			else $etiquettes = "";
			
		}
		else{
			
			// Pour l'ajout c'est vide
			$etiquettes = "";
			
			// Mais si on modifie, le champ est rempli avec les tags liés à l'objet
			if ($remplacer){
				
				$reponse = sql_select(
					'mots.titre',
					array('mots' => 'spip_mots', 'liaison' => 'spip_mots_'.$type_objet),
					array(
						array('=', 'mots.type', _q($groupe)),
						array('=', 'liaison.'.$cle_objet, $id_objet),
						array('=', 'mots.id_mot', 'liaison.id_mot')
					),
					"",
					"mots.titre"
				);
				while ($mot = sql_fetch($reponse)){
				
					// S'il y a des espaces ou virgules on entoure de guillemets
					if (strcspn($mot['titre'], ' ,"') != strlen($mot['titre']))
						$etiquettes .= " &quot;".entites_html($mot['titre'])."&quot;";
					// Sinon on renvoie tel quel
					else
						$etiquettes .= " ".entites_html($mot['titre']);
					// Enfin en vire les éventuels espaces en trop
					$etiquettes = trim($etiquettes);
				
				}
				
			}
			
		}
		
	}
	
	// On provoque enfin l'affichage
    return array(
        'formulaires/etiquettes', 
        0, 
        array(
        	'self' => str_replace('&amp;', '&', self()),
        	'redirect' => str_replace('&amp;', '&', $redirect),
        	'message_ok' => $message_ok,
			'message_erreur' => $message_erreur,
			'erreur_autorisation' => $erreur_autorisation,
			'proposer_login' => $proposer_login, 
			'groupe' => $groupe,
			'aide' => $aide,
			'remplacer' => $remplacer,
			'type_objet' => $type_objet,
			'cle_objet' => $cle_objet,
			'id_objet' => $id_objet,
			'proposer_login' => $proposer_login,
			'etiquettes' => $etiquettes
        )
    );
    
}

?>
