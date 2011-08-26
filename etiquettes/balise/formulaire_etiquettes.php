<?php
#---------------------------------------------------#
#  Plugin  : Étiquettes                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Plugin-Etiquettes                                       #
#                                                                                                      #
#  Définition de la balise #FORMULAIRE_ETIQUETTES                                                      #
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
	
    include_spip('base/connect_sql');
    global $tables_jointures;
    
	// Liste des paramètres autorisés
	$params_ok = array('groupe', 'name', 'aide', 'proposer_login', 'remplacer', 'objet', 'uniquement_champ', 'explication', 'nuage');
	
	// On enlève de la liste des arguments ce qui a été récupéré
	$type_objet = array_shift($args);
	$id_objet = array_shift($args);
	
	// On considère que tout le reste doit être de la forme : param=valeur
	$variables = array();
	foreach ($args as $couple){
		if (($pos = strpos($couple, "=")) !== FALSE and in_array(($param = substr($couple, 0, $pos)), $params_ok)){
			$valeur = substr($couple, $pos+1);
			$variables["$param"] = $valeur;
		}
	}
	extract($variables);
	
	// initialisation du mode généré : tout le formulaire ou que le champ
		// si on met rien ou n'importe quoi, ça donne false
		// donc le formulaire complet
		$uniquement_champ = (strtolower($uniquement_champ) == "true");
	
	// initialisation de la petite explication
		if (!isset($explication))
			$explication = _T('etiquettes:explication');
		elseif ($explication == 'false')
			$explication = false;
	
	// initialisation du squelette d'aide pour le nuage
		if (!isset($nuage))
			$squelette_nuage = 'etiquettes_aide_nuage';
		else
			$squelette_nuage = 'etiquettes_aide_nuage_'.$nuage;
	
	// initialisation de l'objet à lier
		if (isset($objet)){
			// on peut mettre "aucun" si c'est uniquement le champ
			if ($uniquement_champ and $objet == 'aucun'){
				$type_objet = $id_objet = false;
			}
			// ici on a mis explicitement un objet
			// si c'est mal formé on s'arrête
			elseif (preg_match("/^(.*)-([0-9]+)$/", $objet, $captures) == 0){
				return erreur_squelette(
					_T('etiquettes:zbug_objet_mal_forme',
						array (
							'champ' => '#FORMULAIRE_ETIQUETTES',
							'objet' => $objet
						)
					), '');
			}
			else{
				$type_objet = $captures[1];
				$id_objet = $captures[2];
			}
			
			// on précise si ça vaut le coup
			if ($type_objet and $id_objet){
			
				$id_objet = intval($id_objet);
				$type_objet = strtolower($type_objet);
				$cle_objet = id_table_objet($type_objet);
			
				// il faut vérifier s'il existe bien cet objet
				$reponse = sql_fetsel(
					$cle_objet,
					table_objet_sql($type_objet),
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
			
			}
		}else{
			// sinon on prend du contexte
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
		if (
            $type_objet and (
                !in_array('mots', $tables_jointures[table_objet_sql($type_objet)]) and
                !in_array('mots', $tables_jointures[$type_objet])
             )
        )
			return erreur_squelette(
				_T('etiquettes:zbug_pas_de_table_mots',
					array (
						'type' => $type_objet,
					)
				), '');
	
	// initialisation du groupe de mots-clés
		if (!isset($groupe))
			$groupe = 'tags';
	
		// On récupère l'id du groupe de mots
		$reponse = sql_fetsel(
			'id_groupe',
			'spip_groupes_mots',
			'titre='.sql_quote($groupe)
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
					'unseul' => 'non',
					'obligatoire' => 'non',
					'table_liees' => 'articles'
				)
			);
	
	// initialisation du mode d'ajout des mots-clés
		// si on met rien ou n'importe quoi ça donne true
		// donc en mode "remplacer"
		$remplacer = !(strtolower($remplacer) == "false");
	
	// initialisation de la proposition de login en cas de mauvaise autorisation
		// si on met rien ou n'importe quoi, ça donne false
		// donc renvoie du vide si pas autorisé
		$proposer_login = (strtolower($proposer_login) == "true");
	
	// initialisation du type d'aide
		$aide = strtolower($aide);
		if (!strlen($aide) OR !in_array($aide, array("nuage", "autocompletion", "liste", "aucun", "aucune", "rien"))){
			$aide_nuage = true;
			$aide_autocompletion = true;
			$aide_liste = false;
		}
		else{
			$aide_nuage = ($aide == "nuage");
			$aide_autocompletion = ($aide == "autocompletion");
			$aide_liste = ($aide == "liste");
		}
		// on teste ensuite si les plugins sont bien présents
		$aide_nuage &= defined('_DIR_PLUGIN_NUAGE');
		$aide_autocompletion &= defined('_DIR_PLUGIN_SELECTEURGENERIQUE');
	
	// initialisation du nom du champ
		// pour le formulaire complet c'est automatique
		// sinon on peut le choisir (sinon c'est aussi automatique)
		if (!$uniquement_champ or !isset($name)) $name = 'etiquettes_'.etiquettes_produire_id($groupe, $type_objet, $id_objet);
    
    return array($groupe, $id_groupe, $name, $aide_nuage, $aide_autocompletion, $aide_liste, $remplacer, $type_objet, $cle_objet, $id_objet, $proposer_login, $uniquement_champ, $explication, $squelette_nuage);
	
}

?>
