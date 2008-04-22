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
#  #FORMULAIRE_ETIQUETTES{groupe_de_mots?, remplacer?, type-id?, forcer_aide?, proposer_login?} #
#------------------------------------------------------------------------------------------------------#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_FORMULAIRE_ETIQUETTES($p) {
	
    return calculer_balise_dynamique(
    	$p,
    	'FORMULAIRE_ETIQUETTES',
    	array(
    		'type_boucle',
    		$p->boucles[$p->id_boucle]->primary
    	)
    );
    
}

function balise_FORMULAIRE_ETIQUETTES_stat($args, $filtres) {
	
    global $tables_jointures;
	
	// initialisation du groupe de mots-clés
		$groupe = $args[2] ? $args[2] : "tags";
	
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
			$id_groupe = sql_insertq(
				'spip_groupes_mots',
				array(
					'titre' => $groupe, 
					'minirezo' => 'oui',
				)
			);
	
	// initialisation du mode d'ajout des mots-clés
		// si on met rien ou n'importe quoi ça donne true
		// donc en mode "remplacer"
		$remplacer = !(strtolower($args[3]) == "false");
	
	// initialisation de la proposition de login en cas de mauvaise autorisation
		// si on met rien ou n'importe quoi, ça donne false
		// donc renvoie du vide si pas autorisé
		$proposer_login = (strtolower($args[6]) == "true");
	
	// initialisation du type d'aide
		$aide = strtolower($args[5]);
		if (!strlen($aide) OR !in_array($aide, array("nuage", "ajax", "liste", "aucun", "aucune", "rien"))){
			$aide_nuage = true;
			$aide_ajax = true;
			$aide_liste = false;
		}
		else{
			$aide_nuage = ($aide == "nuage");
			$aide_ajax = ($aide == "ajax");
			$aide_liste = ($aide == "liste");
		}
		// on teste ensuite si les plugins sont bien présents
		$aide_nuage &= defined('_DIR_PLUGIN_NUAGE');
		$aide_ajax &= defined('_DIR_PLUGIN_SELECTEURGENERIQUE');
	
	// initialisation de l'objet à lier
		if ($args[4]){
			// ici on a mis explicitement un objet
			$objet = preg_replace("/^(.*)-([0-9]+)$/","$1@$2",$args[4]);
			// si c'est mal formé on s'arrête
			if ($objet == $args[4])
				return erreur_squelette(
					_T('etiquettes:zbug_objet_mal_forme',
						array (
							'champ' => '#FORMULAIRE_ETIQUETTES',
							'objet' => $objet
						)
					), '');
			list($type_objet, $id_objet) = explode('@', $objet);
			
			// on précise
			$id_objet = intval($id_objet);
			$type_objet = strtolower($type_objet);
			include_spip('base/connect_sql');
			$type_objet = preg_replace(',^spip_|s$,', '', $type_objet);
			$type_objet = table_objet_sql($type_objet);
			$type_objet = preg_replace(',^spip_,', '', $type_objet);
			$cle_objet = id_table_objet($type_objet);
			
			// il faut vérifier s'il existe bien cet objet
			$reponse = sql_fetsel(
				$cle_objet,
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
			include_spip('base/connect_sql');
			$type_objet = $args[0];
			$id_objet = intval($args[1]);
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
    
    return $args = array($groupe, $id_groupe, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login);
	
}

function balise_FORMULAIRE_ETIQUETTES_dyn($groupe, $id_groupe, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login) {
	
	global $tables_jointures;
	
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
		
		$erreur_autorisation = false;
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
			
			// On dit qu'il faut recalculer tout vu qu'on a changé
			include_spip ("inc/invalideur");
			suivre_invalideur("1");
			
			// Relance la page
			include_spip('inc/headers');
			redirige_par_entete(
				self()
			);
			
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
        	'message_ok' => $message_ok,
			'message_erreur' => $message_erreur,
			'erreur_autorisation' => $erreur_autorisation,
			'proposer_login' => $proposer_login, 
			'groupe' => $groupe,
			'aide_nuage' => $aide_nuage,
			'aide_ajax' => $aide_ajax,
			'aide_liste' => $aide_liste,
			'remplacer' => $remplacer,
			'type_objet' => $type_objet,
			'cle_objet' => $cle_objet,
			'id_objet' => $id_objet,
			'etiquettes' => $etiquettes
        )
    );
    
}

?>
