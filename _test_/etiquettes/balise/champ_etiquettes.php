<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes                                       #
#                                                                                                      #
#  Définition de la balise #CHAMP_ETIQUETTES                                                           #
#  Rappel d'utilisation :                                                                              #
#  #CHAMP_ETIQUETTES{groupe_de_mots?, forcer_name?, type-id?, forcer_aide?}                            #
#------------------------------------------------------------------------------------------------------#

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_CHAMP_ETIQUETTES($p) {
	
    return calculer_balise_dynamique(
    	$p,
    	'CHAMP_ETIQUETTES',
    	array(
    		'type_boucle',
    		$p->boucles[$p->id_boucle]->primary
    	)
    );
    
}

function balise_CHAMP_ETIQUETTES_stat($args, $filtres) {
	
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
	
	
	// initialisation du nom du champ
		if (!($nom_champ = $args[3]))
			$nom_champ = false;
	
	
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
			if ($args[4] == "rien" OR $args[4] == "aucun"){
				// si on a explicitement dit qu'on voulait pas d'objet
				// par exemple pour mettre le champ dans un formulaire de création
				$type_objet = $id_objet = $remplacer = false;
			}else{
				$remplacer = true;
				// ici on a mis explicitement un objet
				$objet = preg_replace("/^(.*)-([0-9]+)$/","$1@$2",$args[4]);
				// si c'est mal formé on s'arrête
				if ($objet == $args[4])
					return erreur_squelette(
						_T('etiquettes:zbug_objet_mal_forme',
							array (
								'champ' => '#CHAMP_ETIQUETTES',
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
								'champ' => '#CHAMP_ETIQUETTES',
								'type' => $type_objet,
								'id' => $id_objet
							)
						), '');
			}
		}else{
			// sinon on prend du contexte
			$remplacer = true;
			include_spip('base/connect_sql');
			$type_objet = $args[0];
			$id_objet = intval($args[1]);
			$cle_objet = id_table_objet($type_objet);
			
			// mais on vérifie si la balise est effectivement dans un contexte
			if(!$type_objet OR $id_objet <= 0)
				return erreur_squelette(
					_T('zbug_champ_hors_boucle',
						array (
							'champ' => '#CHAMP_ETIQUETTES',
						)
					)._T('etiquettes:zbug_et_parametre_manquant'), '');
		}
	
		// on ne peut pas continuer si le type choisi n'est pas relié à des mots-clés
		// autrement dit, s'il n'y a pas de table mots_machin
		if($type_objet AND !in_array("mots_$type_objet", $tables_jointures['spip_mots']))
			return erreur_squelette(
				_T('etiquettes:zbug_pas_de_table_mots',
					array (
						'type' => $type_objet,
					)
				), '');
    
    return $args = array($groupe, $id_groupe, $nom_champ, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet);
	
}

function balise_CHAMP_ETIQUETTES_dyn($groupe, $id_groupe, $nom_champ, $aide_nuage, $aide_ajax, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet) {
	
	global $tables_jointures;
	
	include_spip('inc/filtres');
	
	// Ici aucune autorisation puisque c'est juste un champ à utiliser autre part
	
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
	
	// On provoque enfin l'affichage
    return array(
        'formulaires/etiquettes',
        0,
        array(
        	'uniquement_champ' => true, // on précise qu'on ne veut que le champ
        	'nom_champ' => $nom_champ,
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
