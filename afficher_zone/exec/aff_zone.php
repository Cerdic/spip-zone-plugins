<?  
/* aff_zone est un plugin pour trier/classer/afficher les plugins à partir du flux RSS des paquets de la zone
*	 VERSION : 0.1
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
  if (!defined("_ECRIRE_INC_VERSION")) return;
     
  include_spip('public/assembler');

  function exec_aff_zone() {
    // vérifier les droits
      global $connect_statut, $connect_toutes_rubriques;
      if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {    
        // si on est pas en retour ajax d'enregistrement d'une modif
          if (!_request("id_mot")) {
              debut_page(_T('titre'), "aff_zone", "plugin");
              echo _T('avis_non_acces_page');
              fin_page();
          }
          else echo _T('avis_non_acces_page');
          exit;
      }
      
  // si CFG n'est pas actif arreter tout
    if (!function_exists('lire_config')) {
        // si on est pas en retour ajax d'enregistrement d'une modif
          if (!_request("id_mot")) {
              debut_page(_T('titre'), "aff_zone", "plugin");
              echo _T('aff_zone:activez_cfg');
              fin_page();
          }
          else echo _T('aff_zone:activez_cfg');
          exit;
    }
    
  // si la config du plugin n'a pas encore été faite, basculer automatiquement sur l'interface cfg
    if (!lire_meta('aff_zone')) {
        include_spip('inc/headers');
        redirige_url_ecrire('cfg','cfg=aff_zone');
    }
  
  // récupérer les paramètres de CFG
    $id_groupes_categories = implode(',', lire_config('aff_zone/categorie'));
    $id_groupe_mots_statut = lire_config('aff_zone/id_groupe_statuts');

    // récupérer le numéro de version et passer le chemin du plugin en constante
      include_spip('inc/plugin');
      $Tplugins_actifs = liste_plugin_actifs();
      $version_script = $Tplugins_actifs['AFF_ZONE']['version'];
      define('_DIR_PLUGIN_AFF_ZONE',$Tplugins_actifs['AFF_ZONE']['dir']);
/* 
	// définir comme constante le chemin du répertoire du plugin
      $p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
      $pp = explode("/", end($p));
      define('_DIR_PLUGIN_AFF_ZONE',(_DIR_PLUGINS.$pp[0]));
*/

// TRAITEMENT DONNEES par ajax à la validation d'une série de checkbox de plugin pour un mot-clé
// => mettre à jour la table spip_mots_syndic_articles: créer/modifier/effacer 
      if (_request('id_mot') ) {    // AND _request('id_plug')) {
          if (preg_match('/[^0-9]/is', _request('id_mot')) != 0
              OR preg_match('/[^0-9,]/is', _request('id_plug')) != 0 ) {
              echo _T('aff_zone:erreur_parametres_ajax');
              exit;
          }
          $Terreurs = array();

        // effacer les plugins attachés à ce mot qui ne font pas partie de la liste de _request(id_plug)
          $Tarray_where = array('id_groupe IN ('.$id_groupes_categories.')');
        // ne traiter que les plugins ayant le statut en cours
          if (_request('statut') != 'tout') $Tarray_where[] = "tags LIKE '%>"._request('statut')."<%'"; 
          $sql = sql_select('spip_mots_syndic_articles.*', 
                            'spip_mots_syndic_articles 
                              LEFT JOIN spip_syndic_articles
                              ON spip_mots_syndic_articles.id_syndic_article = spip_syndic_articles.id_syndic_article
                              LEFT JOIN spip_mots
                              ON spip_mots_syndic_articles.id_mot = spip_mots.id_mot
                              AND spip_mots.id_mot = '._request('id_mot'),
                            $Tarray_where
                           );
//echo '<br>err eff= '.mysql_error().'<br>nb a virer= '.sql_count($sql);       
                    
          if (sql_count($sql) > 0) {
              $Tplugs_selectionnes = explode(',',_request('id_plug'));
              while ($data = sql_fetch($sql)) {
                  if (in_array($data['id_syndic_article'], $Tplugs_selectionnes)) continue;
//echo '<br>id_plug a virer = '.$data['id_syndic_article'];
//continue;
                  sql_delete('spip_mots_syndic_articles', 
                             'id_syndic_article = '.$data['id_syndic_article'].' AND id_mot = '.$data['id_mot']
                            );
                  if (mysql_error() != '') $Terreurs[] = _T('aff_zone:erreur_suppression').' id_plugin = '.$data['id_syndic_article'].' id_mot = '.$data['id_mot'].': '.mysql_error();
              }
          }
          
        // si _request('id_plug') est vide, pas la peine d'aller plus loin
          if (_request('id_plug') == '') {
              if (count($Terreurs) == 0) echo 'OK';
              else echo implode('<br>',$Terreurs);
              exit;
          }
          
        // effacer les enregistrements des plugs de la liste de _request(id_plug) avec d'autres mots
          $sql = sql_select('spip_mots_syndic_articles.*', 
                            'spip_mots_syndic_articles 
                              LEFT JOIN spip_mots 
                              ON spip_mots_syndic_articles.id_mot = spip_mots.id_mot 
                              AND spip_mots.id_mot != '._request('id_mot'), 
                            array('id_syndic_article IN ('._request('id_plug').')',
                                  'id_groupe IN ('.$id_groupes_categories.')')
                           );
          if (sql_count($sql) > 0) {
              while ($data = sql_fetch($sql)) {
                  sql_delete('spip_mots_syndic_articles', 
                             'id_syndic_article = '.$data['id_syndic_article'].' AND id_mot = '.$data['id_mot']
                            );
                  if (mysql_error() != '') $Terreurs[] = _T('aff_zone:erreur_suppression').' id_plugin = '.$data['id_syndic_article'].' id_mot = '.$data['id_mot'].': '.mysql_error();
              }
          }
          
        // faire l'insertion des enregistrements par un REPLACE
          $Tplugs_a_enregistrer = explode(',', _request('id_plug'));
          foreach ($Tplugs_a_enregistrer as $id_plug) {
              sql_replace('spip_mots_syndic_articles', 
                          array('id_mot'=>_request('id_mot'), 'id_syndic_article'=>$id_plug)
                         );
              if (mysql_error() != '') $Terreurs[] = _T('aff_zone:erreur_enregistrement').' id_plugin = '.$id_plug.': '.mysql_error();
          }
          if (count($Terreurs) == 0) echo 'OK';
          else echo implode('<br>',$Terreurs);
          
          exit;
      } 
// FIN TRAITEMENT des données ajax

  
// INITIALISATION du mot clé de statut des plugins pour lesquels ça n'est pas encore fait
    // récupérer ss forme d'une chaîne (OK pour clause IN) les id_mots des mots clés utilisés comme statut
      $Tid_mots_statut = array();
      $sql = sql_select('spip_mots.id_mot, spip_mots.titre',
                        'spip_mots',
                        array('id_groupe = '.$id_groupe_mots_statut),
                        '', '',
                        '0,4'
                       );
      while ($data = sql_fetch($sql)) $Tid_mots_statut[$data['titre']] = $data['id_mot'];
      $Sid_mots_statut = implode(',', $Tid_mots_statut);
      
    // récupérer l'id_syndic du flux de la zone
      $sql = sql_select('spip_syndic.id_syndic',
                        'spip_syndic',
                        array("url_syndic = 'http://files.spip.org/spip-zone/ref.rss.xml.gz'"),
                        '', '',
                        '0,1'
                       );
      if ($data = sql_fetch($sql)) $id_syndic_zone = $data['id_syndic'];
      
    // récupérer ss forme d'une chaîne OK pour clause IN tous les syndic_articles de la zone ayant déja un mot clé de statut
      $Tid_syndic_zapper = array();
      $sql = sql_select('spip_syndic_articles.id_syndic_article', 
                        "spip_syndic_articles 
                          LEFT JOIN spip_mots_syndic_articles
                          ON spip_syndic_articles.id_syndic_article = spip_mots_syndic_articles.id_syndic_article",
                         array('url LIKE \'%/_plugins_/%\'',
                               "id_syndic = ".$id_syndic_zone,
                               "id_mot IN (".$Sid_mots_statut.")"
                              )
                       );
      if (sql_count($sql) > 0) {
          while ($data = sql_fetch($sql)) $Tid_syndic_zapper[] = $data['id_syndic_article'];
          $Sid_syndic_zapper = implode(',', $Tid_syndic_zapper);
      }
      
    // récupérer tous les syndic_articles à traiter et leur attribuer le mot clé correspondant à leur statut
      $array_where = array('url LIKE \'%/_plugins_/%\'', "id_syndic = ".$id_syndic_zone);
      if (isset($Sid_syndic_zapper)) $array_where[] = "id_syndic_article NOT IN (".$Sid_syndic_zapper.")";
      $sql = sql_select('spip_syndic_articles.id_syndic_article, spip_syndic_articles.tags',
                        "spip_syndic_articles",
                        $array_where
                       );                       
      if (sql_count($sql) > 0) {
          while ($data = sql_fetch($sql)) {
             preg_match("/#etat'\s*?>(.*?)<\/a>/is", $data['tags'], $res);
             if (!$res[1] OR !$Tid_mots_statut[$res[1]]) continue;
             sql_insertq('spip_mots_syndic_articles',
                         array('id_mot'=>$Tid_mots_statut[$res[1]], 'id_syndic_article'=>$data['id_syndic_article'])
                         );
          }
      }
// FIN INITIALISATION
      
// DEBUT AFFICHAGE
include_spip('inc/commencer_page');
      $htm = '';
      $commencer_page = charger_fonction('commencer_page', 'inc');
      echo $commencer_page(_T('aff_zone:attribution_mots_cles'), "", "", "");
      echo gros_titre(_T('aff_zone:titre_page'), '', false);
      
      echo debut_gauche('', true);
      
      $contexte = array();
      $contexte['statut'] = _request('statut');
      
      echo recuperer_fond('fonds/choix_statut', $contexte);

      echo creer_colonne_droite('', true);
      echo debut_boite_info(true);
      echo "<strong>"._T('aff_zone:plugin_info')."</strong><br />";
      echo '<br /><a href="?exec=cfg&cfg=aff_zone">'._T('aff_zone:lien_config').'</a><br />';
      echo "<br /><strong>"._T('aff_zone:version')."</strong>".$version_script;
      echo fin_boite_info(true);
            
      echo debut_droite('', true);
      echo debut_cadre_formulaire('', true);
      echo debut_cadre_couleur(_DIR_PLUGIN_AFF_ZONE.'img_pack/aff_zone.png', true);

      echo recuperer_fond('fonds/aff_zone', $contexte);
      
      echo fin_cadre_couleur(true);
      echo fin_cadre_formulaire(true);
      echo fin_gauche();
      
	    echo fin_page();
      
}		 		 

?>
