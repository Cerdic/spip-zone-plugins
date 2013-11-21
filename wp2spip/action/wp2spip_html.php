<?php
/**
 * Plugin wp2spip
 * 
 * GNU/GPL v3
 * 
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Fonction de post clean du HTML dans les articles
 * 
 */
function action_wp2spip_html_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!autoriser('configurer', 'plugins'))
		die('erreur');
	
	if($arg == 'catlist'){
		$modifies = array();
		
		/**
		 * On cherche toute présence de [catlist .*] dans les textes des articles
		 * 
		 * cf : CF : http://wordpress.org/plugins/list-category-posts/other_notes/
		 * 
		 * Attention : le sql_select produit du php qui va être interprété. 
		 * Donc il faut bien slasher les \\ qu'on lui envoie (@denisb), donc 3 fois \ plutôt que 2
		 *
		 */
		$articles = sql_select('texte,id_article','spip_articles',"texte REGEXP '\\\[catlist .*\\\]'");
		spip_log(sql_count($articles).' articles trouvés ayant des [catlist ...]');
		
		/**
		 * Tableau des attributs possibles
		 */
		$catlist_tags = array('id=\d*[\s\]]','name=\w*[\s\]]','orderby=\w*[\s\]]','order=\w*[\s\]]','numberposts=.*[\s\]]','date=.*[\s\]]','date_tag=.*[\s\]]','date_class=.*[\s\]]','dateformat=.*[\s\]]',
								'author=\d*[\s\]]','author_tag=.*[\s\]]','author_class=.*[\s\]]','template=.*[\s\]]','excerpt=\w*[\s\]]','excerpt_size=.*[\s\]]','excerpt_strip=.*[\s\]]',
								'excerpt_overwrite=.*[\s\]]','excerpt_tag=.*[\s\]]','excerpt_class=.*[\s\]]','exclude=.*[\s\]]','excludeposts=.*[\s\]]','offset=.*[\s\]]','tags=.*[\s\]]','exclude_tags=.*[\s\]]','content=.*[\s\]]',
								'content_tag=.*[\s\]]','content_class=.*[\s\]]','catlink=.*[\s\]]','catlink_string=.*[\s\]]','catlink_tag=.*[\s\]]','catlink_class=.*[\s\]]','comments=.*[\s\]]',
								'comments_tag=.*[\s\]]','comments_class=.*[\s\]]','thumbnail=.*[\s\]]','thumbnail_size=\d*[\s\]]','thumbnail_class=.*[\s\]]',
								'title_tag=.*[\s\]]','title_class=.*[\s\]]','post_type=.*[\s\]]','post_status=.*[\s\]]','post_parent=.*[\s\]]','class=.*[\s\]]',
								'customfield_value=.*[\s\]]','customfield_display=.*[\s\]]','customfield_orderby=.*[\s\]]','taxonomy=.*[\s\]]','categorypage=.*[\s\]]','category_count=\d*[\s\]]',
								'morelink=.*[\s\]]','morelink_class=.*[\s\]]','morelink_tag','posts_morelink=.*[\s\]]','posts_morelink_class=.*[\s\]]',
								'year=.*[\s\]]','monthnum=.*[\s\]]','search=.*[\s\]]','link_target=.*[\s\]]');
		/**
		 * Tableau de remplacement de l'orderby par des champs SPIP
		 */
		$order_by = array('title'=>'titre','author'=>'id_auteur','category'=>'id_rubrique','date'=>'date','ID'=>'id_article','modified' => 'maj','parent'=>'id_rubrique');
		/**
		 * En SPIP en général on cause français, c'est pour ça que les techos hype de l'aventure anglo saxonne ne nous aiment pas trop
		 * C'est pas grave on le revendique et on met oui à la place de yes
		 */
		$yes_no = array('yes'=>'oui','no'=>'non');
		while($article = sql_fetch($articles)){
			$texte = $article['texte'];
			/**
			 * On cherche les [catlist ...] dans le texte retourné
			 */
			preg_match_all("|\[catlist .*?\]|",$texte,$matches);
			if(is_array($matches)){
				/**
				 * On cherche les attributs spécifiques
				 */
				foreach($matches[0] as $text){
					preg_match_all('#'.join('|',$catlist_tags).'#',$text,$matches_attr);
					/**
					 * On enleve le dernier caractère ' ' ou ']' inutile
					 */ 
					array_walk($matches_attr[0], function (&$v, $k) { $v = substr($v, 0, -1); });
					$attrs = array();
					/**
					 * On crée un table clé/valeur des attributs pour les filtrer ensuite
					 */
					foreach($matches_attr[0] as $attr){
						$attr = explode('=',$attr);
						$attrs[$attr[0]] = $attr[1];
					}
					/**
					 * On teste chaque attribut que l'on utilise et on le modifie si besoin
					 */
					foreach($attrs as $type => $value){
						switch($type){
							case 'id':
								$id = $value;
								unset($attrs['id']);
								break;
							case 'orderby':
								$attrs['tri'] = $order_by[$value];
								unset($attrs['orderby']);
								break;
							case 'numberposts':
								$attrs['pagination'] = $value;
								unset($attrs['numberposts']);
								break;
							case 'excerpt':
								$attrs['intro'] = $yes_no[$value];
								unset($attrs['excerpt']);
								break;
							case 'order':
								$attrs['ordre'] = ($value == 'ASC') ? 1:'-1';
								unset($attrs['order']);
								break;
						}
					}
					
					/**
					 * On génère le code de notre modèle <wp_catlistXX...>
					 */
					$replace = "<wp_catlist$id|";
					foreach($attrs as $type =>$value){
						$replace .= "$type=$value|";
					}
					$replace = substr($replace,0,-1).'>';
					
					/**
					 * On remplace [catlist..] par <wp_catlistXX|..>
					 */
					$texte = str_replace($text,$replace,$texte);
				}
			}
			/**
			 * Le texte est modifié ? on le met à jour en base de donnée
			 */
			if($texte != $article['texte']){
				sql_updateq('spip_articles',array('texte'=>$texte),'id_article='.intval($article['id_article']));
				$modifies[] = $article['id_article'];
			}
		}
		if(count($modifies) > 1){
			spip_log(count($modifies).' articles modifiés après correction des [catlist ...]','wp2spip');
			spip_log($modifies,'wp2spip');
		}
	}
}
?>