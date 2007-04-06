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

function lienscontenus_referencer_liens($type_objet_contenant, $id_objet_contenant, $contenu)
{
    spip_log('Referencer liens contenus dans '.$type_objet_contenant.' '.$id_objet_contenant.' :', 'liens_contenus');

    $liens_trouves = array();

	// Types et aliases
	$liens_contenus_types = array('article', 'breve', 'rubrique', 'auteur', 'document', 'mot', 'syndic');
	$liens_contenus_aliases = array('art' => 'article', 'br' => 'breve', 'brËve' => 'breve', 'rub' => 'rubrique', 'aut' => 'auteur', 'doc' => 'document', 'im' => 'document', 'img' => 'document', 'image' => 'document', 'emb' => 'document', 'mot' => 'mot', 'site' => 'syndic');

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
                case 'syndic':
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
		$col_id = primary_index_table($table);
        if ($res = spip_query("SELECT * FROM $table")) {
            while ($row = spip_fetch_array($res)) {
                $id_objet_contenant = $row[$col_id];
                // implode() n'est pas forcement le plus propre conceptuellement, mais ca doit convenir et c'est rapide
                lienscontenus_referencer_liens($type_objet_contenant, $id_objet_contenant, implode(' ', $row));
            }
        }
	}
}

function lienscontenus_boite_liste($type_objet, $id_objet)
{
    $data = "\n";
    $data .= debut_cadre_relief('../'._DIR_PLUGIN_LIENSCONTENUS.'/images/liens_contenus-24.gif', true);
    include_spip('public/assembler');
	$contexte = array('type_objet' => $type_objet, 'id_objet' => $id_objet);
	$data .= recuperer_fond('exec/lienscontenus_liste', $contexte);
    $data .= fin_cadre_relief(true);
    return $data;
}

function lienscontenus_verification()
{
    $data = '<script language="javascript" type="text/javascript">' .
                'var messageConfirmationChangementStatut="'._T('lienscontenus:confirmation_depublication').'";' .
                'var messageConfirmationSuppression="'._T('lienscontenus:confirmation_suppression').'";' .
                'var messageInformationElementContenu="'._T('lienscontenus:information_element_contenu').'";' .
                'var baseUrlPlugin="../'._DIR_PLUGIN_LIENSCONTENUS.'";' .
                '</script>';
    $data .= '<style>a.lienscontenus_oui { color: red; text-decoration: line-through; }</style>';
    return $data;
}

function lienscontenus_verification_articles()
{
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // ETAPE 1 : Gestion des changements de statut de l'article
            // on recupere le statut actuel et le code par defaut du onchange
            var initialStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
            var currentStatut = initialStatut;
            var select = $('select[@name=statut_nouv]')[0];
            var currentOnChange = select.onchange;
        
            // on supprime le onchange par defaut
            select.onchange = null;
        
            // on gere un onchange specifique
            $('select[@name=statut_nouv]').bind('change', function(event) {
                // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                    if (confirm(messageConfirmationChangementStatut)) {
                        // changement confirme
                        var newStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
                        currentStatut = newStatut; 
                        // on execute le onchange initial
                        currentOnChange.apply(this);
                    } else {
                        // on ne change pas, finalement
                        $('select[@name=statut_nouv] > option[@selected]').removeAttr('selected');
                        $('select[@name=statut_nouv] > option[@value=publie]').attr('selected', 'selected');
                    }
                } else {
                    // pas de probleme pour changer
                    var newStatut = $('select[@name=statut_nouv] > option[@selected]').attr('value');
                    currentStatut = newStatut;
                    // on execute le onchange initial
                    currentOnChange.apply(this);
                }
            });
            // ETAPE 2 : Gestion des changements de statut de l'article
            // on ajoute une classe specifique aux liens de suppression des docs
            $('div[@id^=legender-]').each(function() {
                var idDoc = $(this).attr('id').replace(/^legender-([0-9]+)$/g, '$1');
                // on recupere "oui" si un autre contenu pointe vers le doc, "non" sinon 
                var docContenu = $.ajax({
                    url: '?exec=lienscontenus_ajax_doc_contenu',
                    data: 'id_doc='+idDoc+'&var_ajaxcharset=utf-8',
                    async: false,
                    dataType: 'xml'
                    }).responseText;
                docContenu = $(docContenu).text();
                $(this).find('a.cellule-h').addClass('lienscontenus_' + docContenu);
            });
            // on ne s'interesse qu'aux mots vers lesquels pointent d'autres contenus
            $('a.lienscontenus_oui').each(function() {
                if (this.onclick) {
                    originalOnClick = this.onclick;
                    this.onclick = null;
                } else {
                    originalOnClick = null;
                }
                $(this).bind('click', {origclick: originalOnClick}, handleClick);
                function handleClick(event)
                {
                    if (confirm(messageConfirmationSuppression)) {
                        if(event.data.origclick) {
                            event.data.origclick.apply(this);
                            return false;
                        } else {
                            // Si on n'a pas de onclick a l'origine, c'est que le href doit etre suivi
                            return true;
                        }
                    } else {
                        return false;
                    }
                }
            });
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_articles_edit()
{
    // TODO : Quand on met a jour le doc, comment relancer cela ?
    // TODO : Y a t'il parfois de l'AjaxSqueeze pour la suppression de doc ? 
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on ajoute une classe specifique aux liens de suppression des docs
            $('div[@id^=legender-]').each(function() {
                var idDoc = $(this).attr('id').replace(/^legender-([0-9]+)$/g, '$1');
                // on recupere "oui" si un autre contenu pointe vers le doc, "non" sinon 
                var docContenu = $.ajax({
                    url: '?exec=lienscontenus_ajax_doc_contenu',
                    data: 'id_doc='+idDoc+'&var_ajaxcharset=utf-8',
                    async: false,
                    dataType: 'xml'
                    }).responseText;
                docContenu = $(docContenu).text();
                $(this).find('a.cellule-h').addClass('lienscontenus_' + docContenu);
            });
            // on ne s'interesse qu'aux docs vers lesquels pointent d'autres contenus
            $('a.lienscontenus_oui').each(function() {
                if (this.onclick) {
                    originalOnClick = this.onclick;
                    this.onclick = null;
                } else {
                    originalOnClick = null;
                }
                $(this).bind('click', {origclick: originalOnClick}, handleClick);
                function handleClick(event)
                {
                    if (confirm(messageConfirmationSuppression)) {
                        if(event.data.origclick) {
                            event.data.origclick.apply(this);
                            return false;
                        } else {
                            // Si on n'a pas de onclick a l'origine, c'est que le href doit etre suivi
                            return true;
                        }
                    } else {
                        return false;
                    }
                }
            });
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_breves_edit()
{
    $data = lienscontenus_verification();
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
                        if (confirm(messageConfirmationChangementStatut)) {
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
    return $data;
}

function lienscontenus_verification_sites()
{
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on recupere le statut actuel
            if ($('select[@name=nouveau_statut]')) {
                var initialStatut = $('select[@name=nouveau_statut] > option[@selected]').attr('value');
                var currentStatut = initialStatut;
            
                // on gere un onchange specifique
                $('select[@name=nouveau_statut]').bind('change', function(event) {
                    // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                    if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                        if (confirm(messageConfirmationChangementStatut)) {
                            var newStatut = $('select[@name=nouveau_statut] > option[@selected]').attr('value');
                            currentStatut = newStatut; 
                        } else {
                            $('select[@name=nouveau_statut] > option[@selected]').removeAttr('selected');
                            $('select[@name=nouveau_statut] > option[@value=publie]').attr('selected', 'selected');
                        }
                    } else {
                        var newStatut = $('select[@name=nouveau_statut] > option[@selected]').attr('value');
                        currentStatut = newStatut;
                    }
                });
            }
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_auteur_infos()
{
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on recupere le statut actuel
            if ($('select[@name=statut]')) {
                var initialStatut = $('select[@name=statut] > option[@selected]').attr('value');
                var currentStatut = initialStatut;
            
                // on gere un onchange specifique
                $('select[@name=statut]').bind('change', function(event) {
                    // Si le statut initial n'etait pas "5poubelle" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                    var newStatut = $('select[@name=statut] > option[@selected]').attr('value');
                    if ((initialStatut != '5poubelle') && (newStatut == '5poubelle') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                        if (confirm(messageConfirmationChangementStatut)) {
                            currentStatut = newStatut; 
                        } else {
                            $('select[@name=statut] > option[@selected]').removeAttr('selected');
                            $('select[@name=statut] > option[@value='+currentStatut+']').attr('selected', 'selected');
                        }
                    } else {
                        currentStatut = newStatut;
                    }
                });
            }
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_mots_tous()
{
    // TODO : A finir...
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            function gestionDesSuppressionsDeMots() {
                // on ajoute une classe specifique
                $('tr.tr_liste').each(function() {
                    var idMot = $(this).find('td:first-child > a').attr('href').replace(/^.*&id_mot=([0-9]+)&.*$/g, '$1');
                    // on recupere "oui" si un autre contenu pointe vers le mot, "non" sinon 
                    var motContenu = $.ajax({
                        url: '?exec=lienscontenus_ajax_mot_contenu',
                        data: 'id_mot='+idMot+'&var_ajaxcharset=utf-8',
                        async: false,
                        dataType: 'xml'
                        }).responseText;
                    motContenu = $(motContenu).text();
                    $(this).find('td:last-child > div > a').addClass('lienscontenus_' + motContenu);
                });
                // on ne s'interesse qu'aux mots vers lesquels pointent d'autres contenus
                $('tr.tr_liste > td > div > a.lienscontenus_oui').each(function() {
                    if (this.onclick) {
                        originalOnClick = this.onclick;
                        this.onclick = null;
                    } else {
                        originalOnClick = null;
                    }
                    $(this).bind('click', {origclick: originalOnClick}, handleClick);
                    function handleClick(event)
                    {
                        if (confirm(messageConfirmationSuppression)) {
                            if(event.data.origclick) {
                                event.data.origclick.apply(this);
                                return false;
                            } else {
                                // Si on n'a pas de onclick a l'origine, c'est que le href doit etre suivi
                                return true;
                            }
                        } else {
                            return false;
                        }
                    }
                });
            }
            // TODO : comment intercepter la modification de ces divs par AjaxSqueeze ?
            /*
            $('div[@id^=editer_mot-]').ajaxSuccess(gestionDesSuppressionsDeMotsApresMaj);
            function gestionDesSuppressionsDeMotsApresMaj()
            {
                $(this).unbind('ajaxSuccess');
                gestionDesSuppressionsDeMots();
                $(this).ajaxSuccess(gestionDesSuppressionsDeMotsApresMaj);
            }
            */
            gestionDesSuppressionsDeMots();
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_articles_page()
{
    // TODO : A finir...
    $data = lienscontenus_verification();
    $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on ajoute une classe specifique
            $('span[@id^=puce_statut_article]').each(function() {
                var idArticle = $(this).attr('id').replace(/^puce_statut_article([0-9]+)$/g, '$1');
                // on ne s'interesse qu'aux articles publies
                $(this).find('img[@src$=/puce-verte.gif]').each(function() {
                    // on recupere "oui" si un autre contenu pointe vers le mot, "non" sinon 
                    var articleContenu = $.ajax({
                        url: '?exec=lienscontenus_ajax_article_contenu',
                        data: 'id_article='+idArticle+'&var_ajaxcharset=utf-8',
                        async: false,
                        dataType: 'xml'
                        }).responseText;
                    articleContenu = $(articleContenu).text();
                    if (articleContenu == 'oui') {
                        $(this).parent().parent().next().prepend('<img src="' + baseUrlPlugin + '/images/alerte.png" style="float: left; padding: 0; margin: 0 3px;" title="' + messageInformationElementContenu + '" />');
                    }
                })
            });
            // on ne s'interesse qu'aux mots vers lesquels pointent d'autres contenus
            /*
            $('tr.tr_liste > td > div > a.lienscontenus_oui').each(function() {
                if (this.onclick) {
                    originalOnClick = this.onclick;
                    this.onclick = null;
                } else {
                    originalOnClick = null;
                }
                $(this).bind('click', {origclick: originalOnClick}, handleClick);
                function handleClick(event)
                {
                    if (confirm(messageConfirmationSuppression)) {
                        if(event.data.origclick) {
                            event.data.origclick.apply(this);
                            return false;
                        } else {
                            // Si on n'a pas de onclick a l'origine, c'est que le href doit etre suivi
                            return true;
                        }
                    } else {
                        return false;
                    }
                }
            });
            */
        });
        </script>
EOS;
    $data .= $script;
    return $data;
}

function lienscontenus_verification_naviguer()
{
    lienscontenus_verification_articles_page();	
}
?>