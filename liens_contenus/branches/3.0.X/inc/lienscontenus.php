<?php
function lienscontenus_referencer_liens($type_objet_contenant, $id_objet_contenant, $contenu = false) {
  spip_log('Referencer liens contenus dans '.$type_objet_contenant.' '.$id_objet_contenant, 'liens_contenus');

  $liens_trouves = array();

  // Types et aliases
  $liens_contenus_types = array('article', 'breve', 'rubrique', 'auteur', 'document', 'mot', 'syndic');
  $liens_contenus_aliases = array('art' => 'article', 'br' => 'breve', 'brève' => 'breve', 'rub' => 'rubrique', 'aut' => 'auteur', 'doc' => 'document', 'im' => 'document', 'img' => 'document', 'image' => 'document', 'emb' => 'document', 'mot' => 'mot', 'site' => 'syndic');

  // Effacer les liens connus
  sql_delete("spip_liens_contenus", "type_objet_contenant="._q($type_objet_contenant)." AND id_objet_contenant="._q($id_objet_contenant));

  if ($contenu === false) {
    spip_log('- recuperation du contenu en base', 'liens_contenus');

    // Le contenu n'a pas été fourni, il faut le récupérer en base
    if (in_array($type_objet_contenant, array('syndic', 'forum'))) {
      $row = sql_fetsel("*", "spip_".$type_objet_contenant, "id_".$type_objet_contenant."="._q($id_objet_contenant));
    } else {
      // Marche aussi pour les formulaires (type = "form")
      $row = sql_fetsel("*", "spip_".$type_objet_contenant."s", "id_".$type_objet_contenant."="._q($id_objet_contenant));
    }
    if ($row) {
      // implode() n'est pas forcement le plus propre conceptuellement, mais ca doit convenir et c'est rapide
      $contenu = implode(' ', $row);
    } else {
      $contenu = '';
    }
  }

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
      $type_objet_contenu = strtolower($type_objet_contenu); // Pour tranformer les vieux <IMG...> en <img...>
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
            $nb = sql_countsel("spip_documents_liens", "objet='".$type_objet_contenant."' AND id_document=".$id_objet_contenu." AND id_objet=".$id_objet_contenant);
            if ($nb == 1) {
              // Si le doc est rattache a l'article ou la rubrique courant, on ne doit pas le comptabiliser
              // TODO: En fait si, non ?
              $nouveau_lien = false;
            }
          }
          break;
        case 'form':
          // Soyons gentil avec le plugin Forms s'il est actif
          if (defined('_DIR_PLUGIN_FORMS')) {
            break;
          }
        default:
          // C'est a priori un modele
          $params = array_filter(explode('|', strtolower($params)));
          if ($params) {
            list(, $soustype) = each($params);
            if (in_array($soustype, array('left', 'right', 'center'))) {
              // On ne prend pas en compte l'alignement pour les modèles de docs
              list(, $soustype) = each($params);
            }
            if (preg_match(',^[a-z0-9_]+$,', $soustype)
                && find_in_path('modeles/'.$type_objet_contenu.'_'.$soustype.'.html')) {
              // C'est un modele compose
              $id_objet_contenu = $type_objet_contenu.'_'.$soustype;
              $type_objet_contenu = 'modele';
            } elseif (find_in_path('modeles/'.$type_objet_contenu.'.html')) {
              // C'est un modele simple
              $id_objet_contenu = $type_objet_contenu;
              $type_objet_contenu = 'modele';
            } else {
              // Ce n'est pas un modele connu, sans doute un de <quote>, <poesie>, <html>, <code>, <cadre>, etc.
              $nouveau_lien = false;
            }
          } elseif (find_in_path('modeles/'.$type_objet_contenu.'.html')) {
            // TODO: supprimer cette duplication du code ci-dessus
            // C'est un modele simple
            $id_objet_contenu = $type_objet_contenu;
            $type_objet_contenu = 'modele';
          } else {
            // Ce n'est pas un modele connu, sans doute un de <quote>, <poesie>, <html>, <code>, <cadre>, etc.
            $nouveau_lien = false;
          }
      }
      if ($nouveau_lien && $type_objet_contenu != '' && $id_objet_contenu != '') {
        $liens_trouves[$type_objet_contenu.' '.$id_objet_contenu] = array('type' => $type_objet_contenu, 'id' =>$id_objet_contenu);
      }
    }
  }
  if (count($liens_trouves) > 0) {
    foreach ($liens_trouves as $lien) {
      spip_log('- lien '.$type_objet_contenant.' '.$id_objet_contenant.' vers '.$lien['type'].' '.$lien['id'], 'liens_contenus');
      include_spip('base/abstract_sql');
      sql_insertq(
          "spip_liens_contenus",
                array(
                    "type_objet_contenant" => $type_objet_contenant,
                    "id_objet_contenant" => $id_objet_contenant,
                    "type_objet_contenu" => $lien['type'],
                    "id_objet_contenu" => $lien['id']
                )
      );
    }
  } else {
    spip_log('- aucun lien', 'liens_contenus');
  }
}

// (re)initialisation de la table des liens
function lienscontenus_initialiser()
{
    include_spip('base/abstract_sql');

  // vider la table
  sql_delete("spip_liens_contenus");
  spip_log('Initialisation des contenus', 'liens_contenus');

  // TODO: decouvrir un moyen automatique en SPIP 2 de récupérer la liste des tables
  $liste_tables = array(
        'spip_articles' => 'id_article',
        'spip_rubriques' => 'id_rubrique',
        'spip_breves' => 'id_breve',
        'spip_syndic' => 'id_syndic',
        'spip_forum' => 'id_forum'
    );
    // parcourir les tables et les champs
    foreach ($liste_tables as $table => $col_id) {
      $type_objet_contenant = ereg_replace("^spip_(.*[^s])s?$", "\\1", $table);
      if ($res = sql_select("*", $table)) {
        while ($row = sql_fetch($res)) {
          $id_objet_contenant = $row[$col_id];
                sql_insertq(
                    "spip_liens_contenus_todo",
                    array(
                        "type_objet_contenant" => $type_objet_contenant,
                        "id_objet_contenant" => $id_objet_contenant,
                        "date_added" => time()
                    )
                );
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
            'var messageConfirmationDepublication="'._T('lienscontenus:confirmation_depublication').'";' .
            'var messageConfirmationPublication="'._T('lienscontenus:confirmation_publication').'";' .
            'var messageConfirmationSuppression="'._T('lienscontenus:confirmation_suppression').'";' .
            'var messageInformationElementContenu="'._T('lienscontenus:information_element_contenu').'";' .
            'var messageAlertePublieContenant="'._T('lienscontenus:alerte_publie_contenant').'";' .
            'var messageAlertePublieContenantKo="'._T('lienscontenus:alerte_publie_contenant_ko').'";' .
            'var baseUrlPlugin="../'._DIR_PLUGIN_LIENSCONTENUS.'";' .
            '</script>';
  $data .= '<style>a.lienscontenus_oui { color: red; text-decoration: line-through; }</style>';
  return $data;
}

function lienscontenus_verification_article()
{
  spip_log('lienscontenus_verification_article');
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
	$(document).ready(function() {
            var statutActuel = $('select[name=statut] option:selected').val();
            var estPublie = statutActuel == 'publie';
            var estLie = $('#liens_contenus_contenants > li > a.publie').size() > 0;
            var estLiantOk = $('#liens_contenus_contenus > li > a.ok.publie').size() > 0;
            var estLiantOkNonPublie = $('#liens_contenus_contenus > li > a.ok:not(.publie)').size() > 0;
            var estLiantKo = $('#liens_contenus_contenus > li > span.ko').size() > 0;
            if (estPublie && estLiantOkNonPublie) {
              $('div.fiche_objet').prepend('<div class="alerte">' + messageAlertePublieContenant + '</div>');
            }
            if (estPublie && estLiantKo) {
              $('div.fiche_objet').prepend('<div class="alerte">' + messageAlertePublieContenantKo + '</div>');
	    }
	    $('select[name=statut]').bind("change", function() { 
              // Prévenir le double affichage de message en cas de cancel
	      var statutNouveau = $('select[name=statut] option:selected').val();
              if ( statutNouveau == statutActuel ) {
                return true;
              }
              /* Ancien ETAPE 1 : Alerte en cas de dépublication d'un article vers lequel pointent des contenus publies */
              if (estPublie && estLie) {
                if (!confirm(messageConfirmationDepublication)) {
                  // DONE : change le tit icône aussi
                  $('select[name=statut]').val(statutActuel);
                  $('select[name=statut]').trigger('change');
                }
	      }
              /* Ancien ETAPE 2 : Alerte en cas de publication d'un article qui pointe vers des contenus non publies */
              if (!estPublie && estLiantOK) {
                if (!confirm(messageConfirmationPublication)) {
                  // DONE : change le tit icône aussi
		  $('select[name=statut]').val(statutActuel);
                  $('select[name=statut]').trigger('change');
                }
              }

            });
            // ETAPE 3 : Gestion des changements de statut de l'article
            // on ajoute une classe specifique aux liens de suppression des docs
            $('div[id^=legender-]').each(function() {
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

function lienscontenus_verification_article_edit()
{
  spip_log('lienscontenus_verification_article');
  // TODO : Quand on met a jour le doc, comment relancer cela ?
  // TODO : Y a t'il parfois de l'AjaxSqueeze pour la suppression de doc ?
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on ajoute une classe specifique aux liens de suppression des docs
            $('div[id^=legender-]').each(function() {
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

function lienscontenus_verification_breve_edit()
{
  spiplog('lienscontenus_verification_breve_edit');
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on recupere le statut actuel
            if ($('select[name=statut]')) {
                var initialStatut = $('select[name=statut] > option:selected').attr('value');
                var currentStatut = initialStatut;
            
                // on gere un onchange specifique
                $('select[name=statut]').bind('change', function(event) {
                    // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                    if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                        if (confirm(messageConfirmationDepublication)) {
                            var newStatut = $('select[name=statut] > option:selected').attr('value');
                            currentStatut = newStatut; 
                        } else {
                            $('select[name=statut] > option:selected').removeAttr('selected');
                            $('select[name=statut] > option[value=publie]').attr('selected', 'selected');
                        }
                    } else {
                        var newStatut = $('select[name=statut] > option:selected').attr('value');
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

function lienscontenus_verification_site()
{
  spip_log('lienscontenus_verification_site');
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on recupere le statut actuel
            if ($('select[name=nouveau_statut]')) {
                var initialStatut = $('select[name=nouveau_statut] > option:selected').attr('value');
                var currentStatut = initialStatut;
            
                // on gere un onchange specifique
                $('select[name=nouveau_statut]').bind('change', function(event) {
                    // Si le statut initial etait "publie" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                    if ((initialStatut == 'publie') && (currentStatut == 'publie') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                        if (confirm(messageConfirmationDepublication)) {
                            var newStatut = $('select[name=nouveau_statut] > option:selected').attr('value');
                            currentStatut = newStatut; 
                        } else {
                            $('select[name=nouveau_statut] > option:selected').removeAttr('selected');
                            $('select[name=nouveau_statut] > option[value=publie]').attr('selected', 'selected');
                        }
                    } else {
                        var newStatut = $('select[name=nouveau_statut] > option:selected').attr('value');
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
  spip_log('lienscontenus_verification_auteur_infos');
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on recupere le statut actuel
            if ($('select[name=statut]')) {
                var initialStatut = $('select[name=statut] > option:selected').attr('value');
                var currentStatut = initialStatut;
            
                // on gere un onchange specifique
                $('select[name=statut]').bind('change', function(event) {
                    // Si le statut initial n'etait pas "5poubelle" et s'il y a au moins un contenu publie qui pointe vers lui, on demande confirmation
                    var newStatut = $('select[name=statut] > option:selected').attr('value');
                    if ((initialStatut != '5poubelle') && (newStatut == '5poubelle') && ($('#liens_contenus_contenants > li.publie').size() > 0)) {
                        if (confirm(messageConfirmationDepublication)) {
                            currentStatut = newStatut; 
                        } else {
                            $('select[name=statut] > option:selected').removeAttr('selected');
                            $('select[name=statut] > option[value='+currentStatut+']').attr('selected', 'selected');
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
  spip_log('lienscontenus_verification_mots_tous');
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
            $('div[id^=editer_mot-]').ajaxSuccess(gestionDesSuppressionsDeMotsApresMaj);
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

function lienscontenus_verification_article_page()
{
  spip_log('lienscontenus_verification_article_page');
  // TODO : A finir...
  $data = lienscontenus_verification();
  $script = <<<EOS
        <script language="javascript" type="text/javascript">
        $(document).ready(function() {
            // on ajoute une classe specifique
            $('span[id^=puce_statut_article]').each(function() {
                var idArticle = $(this).attr('id').replace(/^puce_statut_article([0-9]+)$/g, '$1');
                // on ne s'interesse qu'aux articles publies
                $(this).find('img[src$=/puce-verte.gif]').each(function() {
                    // on recupere "oui" si un autre contenu pointe vers l'article, "non" sinon 
                    var articleContenu = $.ajax({
                        url: '?exec=lienscontenus_ajax_article_contenu',
                        data: 'id_article='+idArticle+'&var_ajaxcharset=utf-8',
                        async: false,
                        dataType: 'xml'
                        }).responseText;
                    articleContenu = $(articleContenu).text();
                    // On ajoute un panneau d'alerte
                    if (articleContenu == 'oui') {
                        $(this).parent().parent().next().prepend('<img src="' + baseUrlPlugin + '/images/alerte.png" style="float: left; padding: 0; margin: 0 3px;" title="' + messageInformationElementContenu + '" />');
                    }
                })
            });
        });
        </script>
EOS;
  $data .= $script;
  return $data;
}

function lienscontenus_verification_rubrique()
{
  spip_log('lienscontenus_verification_rubrique');
  lienscontenus_verification_article_page();
}

