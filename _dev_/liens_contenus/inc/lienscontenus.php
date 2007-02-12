<?php
/*
 * liens_contenus
 * Gestion des liens inter-contenus
 *
 * Auteur :
 * Nicolas Hoizey
 * © 2007 - Distribue sous licence GNU/GPL
 *
 */

function lienscontenus_verifier_version_base()
{
	static $version_base_active;

	// Version de la base pour cette version du code
	$version_base_code = 1;

	if (!isset($version_base_active)) {
		$version_base_active = isset($GLOBALS['meta']['liens_contenus_version_base']) ? intval($GLOBALS['meta']['liens_contenus_version_base']) : 0;
	}

	if ($version_base_active != $version_base_code) {
		if ($version_base_active == 0) {
			// Premiere installation
			include_spip('base/create');
            include_spip('base/abstract_sql');
            spip_log('Creation de la base', 'liens_contenus');
			creer_base();
    		$version_base_active = $version_base_code;
    		ecrire_meta('liens_contenus_version_base', $version_base_active);
    		ecrire_metas();
			lienscontenus_initialiser();
		} else {
			// Mise a jour
            spip_log('Mise a jour de la base', 'liens_contenus');
		}
	}
}

function lienscontenus_referencer_liens($type_objet_contenant, $id_objet_contenant, $contenu)
{
    spip_log('Referencer liens contenus dans '.$type_objet_contenant.' '.$id_objet_contenant.' :', 'liens_contenus');

	lienscontenus_verifier_version_base();

    $liens_trouves = array();

	// Types et aliases
	$liens_contenus_types = array('article', 'breve', 'rubrique', 'auteur', 'document', 'mot', 'site');
	$liens_contenus_aliases = array('art' => 'article', 'br' => 'breve', 'brËve' => 'breve', 'rub' => 'rubrique', 'aut' => 'auteur', 'doc' => 'document', 'im' => 'document', 'img' => 'document', 'image' => 'document', 'emb' => 'document', 'mot' => 'mot', 'syndic' => 'site');

	// Effacer les liens connus
	spip_query("DELETE FROM spip_liens_contenus WHERE type_objet_contenant="._q($type_objet_contenant)." AND id_objet_contenant="._q($id_objet_contenant));
	
	// Echapper les <a href>, <html>...< /html>, <code>...< /code>
	include_spip('inc/texte');
	$contenu = echappe_html($contenu);

	// Raccourcis de liens [xxx->url]
	$regexp = ',\[([^][]*)->(>?)([^]]*)\],msS';
	if (preg_match_all($regexp, $contenu, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			$lien = trim($match[3]);
			if (preg_match(',^(\S*?)\s*(\d+)(\?.*?)?(#[^\s]*)?$,S', $lien, $match)) {
				list(, $type_objet_contenu, $id_objet_contenu, $params, $ancre) = $match;
				// article par defaut
				if (!$type_objet_contenu) $type_objet_contenu = 'article';
				$type_objet_contenu = isset($liens_contenus_aliases[$type_objet_contenu]) ? $liens_contenus_aliases[$type_objet_contenu] : $type_objet_contenu;
				if (in_array($type_objet_contenu, $liens_contenus_types)) {
				    $liens_trouves[$type_objet_contenu.' '.$id_objet_contenu] = array('type' => $type_objet_contenu, 'id' =>$id_objet_contenu);
				}
			}
		}
	}

	// Raccourcis d'insertion de modeles
	$regexp = '/<([a-z_-]{3,})\s*([0-9]+)?([|][^>]*)*>/iS';
    // La regex de inc/texte n'est pas exploitable directement
    // $regexp = '/'._RACCOURCI_MODELE.'/iS';
	if (preg_match_all($regexp, $contenu, $matches, PREG_SET_ORDER)) {
		foreach ($matches as $match) {
			list($chaine_modele ,$type_objet_contenu, $id_objet_contenu, $params) = $match;
			$type_objet_contenu = isset($liens_contenus_aliases[$type_objet_contenu]) ? $liens_contenus_aliases[$type_objet_contenu] : $type_objet_contenu;
            $nouveau_lien = true;
            switch ($type_objet_contenu) {
                case 'article':
                case 'rubrique':
                case 'breve':
                case 'site':
                case 'auteur':
                case 'mot':
                    // Les elements de base de SPIP ont des pseudo modeles automatiques
                    break;
                case 'document':
                    if ($type_objet_contenant == 'article' || $type_objet_contenant == 'rubrique') {
                        $query = 'SELECT COUNT(*) AS nb FROM spip_documents_'.$type_objet_contenant.'s WHERE id_document='.$id_objet_contenu.' AND id_'.$type_objet_contenant.'='.$id_objet_contenant;
                        $res = spip_query($query);
                        $row = spip_fetch_array($res);
                        if ($row['nb'] == 1) {
                            // Si le doc est rattache a l'article ou la rubrique courant, on ne doit pas le comptabiliser
                            $nouveau_lien = false;
                        }
                    }
                    break;
            	case 'form':
                    // Soyons gentil avec le plugin Forms s'il est activé
                    if (defined('_DIR_PLUGIN_FORMS')) {
                        break;
                    }
                default:
                    if ($id_objet_contenu != '' || $params != '') {
                        // C'est a priori un modele
                        $params = array_filter(explode('|', strtolower($params)));
                        if ($params) {
                            list(, $soustype) = each($params);
                            if (in_array($soustype, array('left', 'right', 'center'))) {
                                list(, $soustype) = each($params);
                            }
                            if (preg_match(',^[a-z0-9_]+$,', $soustype)) {
                                if (find_in_path('modeles/'.$type_objet_contenu.'_'.$soustype.'.html')) {
                                    // C'est un modele compose
                                    $id_objet_contenu = $type_objet_contenu.'_'.$soustype;
                                    $type_objet_contenu = 'modele';
                                } elseif (find_in_path('modeles/'.$type_objet_contenu.'.html')) {
                                    // C'est un modele simple
                                    $id_objet_contenu = $type_objet_contenu;
                                    $type_objet_contenu = 'modele';
                                } else {
                                    // C'est cense etre un modele, mais on ne le trouve pas
                                    $id_objet_contenu = $type_objet_contenu;
                                    $type_objet_contenu = 'modele';
                                }
                            }
                        } elseif (find_in_path('modeles/'.$type_objet_contenu.'.html')) {
                            // C'est un modele simple
                            $id_objet_contenu = $type_objet_contenu;
                            $type_objet_contenu = 'modele';
                        } else {
                            // C'est cense etre un modele, mais on ne le trouve pas
                            $id_objet_contenu = $type_objet_contenu;
                            $type_objet_contenu = 'modele';
                        }
                    } else {
                        // Ce n'est pas un modele, sans doute un de <quote>, <poesie>, <html>, <code>, <cadre>, etc.
                        $nouveau_lien = false;
                    }
            }
            if ($nouveau_lien) {
                $liens_trouves[$type_objet_contenu.' '.$id_objet_contenu] = array('type' => $type_objet_contenu, 'id' =>$id_objet_contenu);
            }
		}
	}
	if (count($liens_trouves) > 0) {
	   foreach ($liens_trouves as $lien) {
            //spip_log('- lien '.$type_objet_contenant.' '.$id_objet_contenant.' vers '.$lien['type'].' '.$lien['id'], 'liens_contenus');
            include_spip('base/abstract_sql');
            spip_abstract_insert(
                'spip_liens_contenus',
                '(type_objet_contenant, id_objet_contenant, type_objet_contenu, id_objet_contenu)',
                '('._q($type_objet_contenant).','._q($id_objet_contenant).','._q($lien['type']).','._q($lien['id']).')');
	   }
	} else {
        //spip_log('- aucun lien', 'liens_contenus');
	}
}

// (re)initialisation de la table des liens
function lienscontenus_initialiser()
{
	// vider la table
	spip_query("DELETE FROM spip_liens_contenus");
	
	include_spip('inc/indexation');
	$liste_tables = liste_index_tables();

	// parcourir les tables et les champs
	foreach ($liste_tables as $table) {
		$type_objet_contenant = ereg_replace("^spip_(.*[^s])s?$", "\\1", $table);
        if ($type_objet_contenant == 'syndic') {
            $type_objet_contenant = 'site';
        }
		$col_id = primary_index_table($table);
		$res = spip_query("SELECT * FROM $table");
		while ($row = spip_fetch_array($res)) {
		    $id_objet_contenant = $row[$col_id];
			// implode() n'est pas forcement le plus propre conceptuellement, mais ca doit convenir et c'est rapide
			lienscontenus_referencer_liens($type_objet_contenant, $id_objet_contenant, implode(' ', $row));
		}
	}
}

function lienscontenus_boite_liste($type_objet, $id_objet)
{
    $data = "\n";

    $data .= debut_cadre_relief('../'._DIR_PLUGIN_LIENS_CONTENUS.'/images/liens_contenus-24.gif', true);
    include_spip('public/assembler');
	$contexte = array('type_objet' => $type_objet, 'id_objet' => $id_objet);
	$data .= recuperer_fond('exec/lienscontenus_liste', $contexte);
    $data .= '<script language="javascript" type="text/javascript">var messageConfirmation="'._T('lienscontenus:confirmation_depublication').'";</script>';
    // TODO : gerer les autres cas que les breves et articles
    switch ($type_objet) {
    	case 'article':
            $script = <<<EOS
                <script language="javascript" type="text/javascript">
                $(document).ready(function() {
                    // on recupere le statut actuel et le code par defaut du onchange
                    var initialStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
                    var currentStatut = initialStatut;
                    var currentOnChange = $('select[@name=statut_nouv]').attr('onchange').replace('/this/', '$(\'select[@name=statut_nouv]\')');
                
                    // on supprime le onchange par defaut
                    /*
                    Cela ne marche pas ???
                    $('select[@name=statut_nouv]').unbind('change');
                    */
                    $('select[@name=statut_nouv]').removeAttr('onchange');
                
                    // on gere un onchange specifique
                    $('select[@name=statut_nouv]').bind('change', function(event) {
                        // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                        if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                            if (confirm(messageConfirmation)) {
                                var newStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
                                currentStatut = newStatut; 
                                /*
                                Cela ne marche pas... :(
                                eval(currentOnChange);
                                */
                                $('select[@name=statut_nouv]').next("img").attr('src', '../dist/images/' + puce_statut(newStatut));
                                setvisibility('valider_statut', 'visible');
                            } else {
                                $('select[@name=statut_nouv] > option[@selected]').removeAttr('selected');
                                $('select[@name=statut_nouv] > option[@value=publie]').attr('selected', 'selected');
                            }
                        } else {
                            var newStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
                            currentStatut = newStatut;
                            /*
                            Cela ne marche pas... :(
                            eval(currentOnChange);
                            */
                            $('select[@name=statut_nouv]').next("img").attr('src', '../dist/images/' + puce_statut(newStatut));
                            setvisibility('valider_statut', 'visible');
                        }
                    });
                });
                </script>
EOS;
            $data .= $script;
            break;
        case 'breve':
            $script = <<<EOS
                <script language="javascript" type="text/javascript">
                $(document).ready(function() {
                    // on recupere le statut actuel
                    if ($('select[@name=statut]')) {
                        var initialStatut = $('select[@name=statut] > option[@selected]').attr('value');
                        var currentStatut = initialStatut;
                    
                        // on gere un onchange specifique
                        $('select[@name=statut]').bind('change', function(event) {
                            // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                            if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                                if (confirm(messageConfirmation)) {
                                    var newStatut = $('select[@name=statut] > option[@selected]').attr('value');
                                    currentStatut = newStatut; 
                                } else {
                                    $('select[@name=statut] > option[@selected]').removeAttr('selected');
                                    $('select[@name=statut] > option[@value=publie]').attr('selected', 'selected');
                                }
                            } else {
                                var newStatut = $('select[@name=statut] > option[@selected]').attr('value');
                                currentStatut = newStatut;
                            }
                        });
                    }
                });
                </script>
EOS;
            $data .= $script;
            break;
    }
    $data .= fin_cadre_relief(true);

    return $data;
}
?>