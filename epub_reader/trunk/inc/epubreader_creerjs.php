<?php
/**
 * Plugin Epub reader
 * © 2011-2012 - kent1
 * Licence GPL v3
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Créer le js qui sera utilisé par monocle
 * 
 * @param $id_document int l'identifiant numérique du document
 */
function inc_epubreader_creerjs_dist($id_document,$id=false,$hauteur=600) {
	if($repertoire = epubreader_unzip($id_document)){
		include_spip('inc/xml');
		$base_root = $repertoire;
		if(file_exists($repertoire.'META-INF/container.xml')){
			$arbre_container = spip_xml_load($repertoire.'/META-INF/container.xml');
			spip_xml_match_nodes(",^rootfile ,",$arbre_container, $rootfiles);
			$items_manifest = array();
			if(is_array($rootfiles)){
				foreach($rootfiles as $rootfile => $info){
					$file = extraire_attribut("<$rootfile>",'full-path');
					if($file){
						$base_root = dirname($repertoire.$file);
						$arbre_root = spip_xml_load($repertoire.$file);
						spip_xml_match_nodes(",^manifest,",$arbre_root, $manifest);
						/**
						 * On parcourt la partie manifest
						 * et on remplit $items_manifest
						 */
						if(is_array($manifest)){
							foreach($manifest['manifest'][0] as $item => $info_item){
								if(preg_match('/^item.*href=["\'](.*)["\'].*id=["\'](.*)["\'].*/Uims',$item,$item_match))
									$items_manifest[$item_match[2]] = array('content'=>url_absolue($base_root.'/'.$item_match[1]),'id'=>$item_match[2]);
								else if(preg_match('/^item.*id=["\'](.*)["\'].*href=["\'](.*)["\'].*/Uims',$item,$item_match))
									$items_manifest[$item_match[1]] = array('content'=>url_absolue($base_root.'/'.$item_match[2]),'id'=>$item_match[1]);
							}
						}
						/**
						 * On parcourt la partie <spine></spine> et on range nos items comme dans cette partie
						 */
						if(count($items_manifest) > 0){
							spip_xml_match_nodes(",^spine,",$arbre_root, $spines);
							if(is_array($spines)){
								$items_spine = array();
								foreach($spines as $spine){
									foreach($spine[0] as $itemref => $info_itemref){
										if(preg_match('/^itemref.*idref=["\'](.*)["\'].*/Uims',$itemref,$itemref_match))
											$items_spine[$itemref_match[1]] = $items_manifest[$itemref_match[1]];
									}
								}
							}
						}
					}
				}
			}
			if(is_array($items_spine)){
				$items_manifest = $items_spine;
			}
		}
		if($ncx = preg_files($repertoire, '[.]ncx$')){
			foreach($ncx as $toc){
				$base = dirname($toc);
				lire_fichier($toc,$contenu);
				$contenu = preg_replace(',<!DOCTYPE(.*)>,Uims','',$contenu);
				$arbre_toc = spip_xml_parse($contenu);
				spip_xml_match_nodes(",^navMap,",$arbre_toc, $navmaps);
				foreach($navmaps as $navmap => $nav){
					foreach($nav[0] as $navpoints => $navpoint){
						$new_navpoint = array();
						foreach($navpoint[0] as $info => $data){
							if(preg_match(',navLabel,',$info))
								$new_navpoint['label'] = $data[0]['text'][0];
							else if(preg_match(',content src="(.*)",',$info,$matches))
								$new_navpoint['content'] = url_absolue($base.'/'.$matches[1]);
						}
						$navpoint_final[$new_navpoint['content']] = $new_navpoint;
					}
				}
				foreach($navpoint_final as $navpoint_final_content => $navpoint_final_content_infos){
					foreach($items_manifest as $item_manifest => $item_manifest_info){
						if($navpoint_final_content == $item_manifest_info['content']){
							$items_manifest[$item_manifest]['label'] = $navpoint_final_content_infos['label'];
							break;
						}
					}
				}
			}
		}
		if(count($items_manifest) > 0){
			$components_done = array();
			$components = $contents = $component = '';
			foreach($items_manifest as $item_manifest => $item){
				$component_normal = preg_replace(',#.*,','',$item['content']);
				if(!in_array($component_normal,$components_done))
					$components_done[] = "\"$component_normal\"";
				$contents .= "{
					title: '".texte_script($item['label'])."',
					src: '".$item['content']."',
				},";
				$component .= '\''.$item['content'].'\':\'<h3>'.texte_script($item['label']).'</h3>\',';
			}
			$components = implode(',',$components_done);

			$js = "<script type='text/javascript'>
var base = '".$base_root."';
var bookData = {
	getComponents: function () {
		return [
			".$components."
		];
	},
	getContents: function () {
		return [
			".$contents."
		]
	},
	getComponent: function (componentId) {
		 return this.getViaAjax(componentId);
	},
	getMetaData: function(key) {
		return {
			title: 'A book',
			creator: 'Inventive Labs'
		}[key];
	},
	getViaAjax: function (path) {
		var url = '".generer_url_public('epub_reader_get')."';
		var ajReq = new XMLHttpRequest();
		var url_fin = url+'&base='+base+'&content='+path;
		ajReq.open('GET', url_fin, false);
		ajReq.send(null);
		return ajReq.responseText;
	}
}
//Resize height
$('#".$id."').height(parseInt(".$hauteur."));

// Initialize the reader element.
Monocle.Reader('".$id."', bookData, { panels: Monocle.Panels.IMode });
</script>";	
			return $js;
		}
		return false;
	}else
		return false;
}

/**
 * Dézip un document epub dans son répertoire de cache
 * Le répertoire de cache défini est local/cache-epub/id_document
 * 
 * @param $id_document int : l'identifiant numérique du document
 * @return $rep_dest : retourne le chemin du répertoire de cache ou false
 */
function epubreader_unzip($id_document){
	include_spip('inc/documents');
	include_spip('inc/flock');
	$document = sql_fetsel('*','spip_documents','id_document='.intval($id_document));
	
	$fichier = get_spip_doc($document['fichier']);
	if(!file_exists($fichier))
		return false;
	
	$rep_dest = sous_repertoire(_DIR_VAR, 'cache-epub/');
	$rep_dest = sous_repertoire(_DIR_VAR.'cache-epub/',$id_document);
	include_spip('inc/pclzip');
	$zip = new PclZip(get_spip_doc($fichier));
	
	$ok = $zip->extract(
		PCLZIP_OPT_PATH,$rep_dest,
		PCLZIP_OPT_SET_CHMOD, _SPIP_CHMOD,
		PCLZIP_OPT_REPLACE_NEWER,
		PCLZIP_OPT_REMOVE_PATH, ''
	);
	if ($zip->error_code < 0) {
		spip_log('Erreur de décompression ' . $zip->error_code .' pour le fichier: ' . $fichier,'epub_reader');
		return false;
	}
	else{
		return $rep_dest;
	}
}

/**
 * Récupération des métas d'un document epub
 * - Si le document n'est pas déjà dézipé, il le dézipe dans son répertoire de cache
 * - Si on a bien un fichier META-INF/container.xml, on l'analyse pour trouver le document root
 * - On analyse chaque document "root"
 * Renvoie toutes les métas dublin core contenue dans le document.
 * 
 * On change certains noms de métas (title devient titre, subject devient descriptif, rights devient credits)
 * pour être compatible avec la table spip_documents
 * 
 * TODO Déplacer cette fonction dans metadatas/epub.php
 * 
 * @param $id_document int : l'identifiant numérique du document
 * @return $infos array : un array des métas du document
 */
function epubreader_recuperer_metas($id_document){
	if($repertoire = epubreader_unzip($id_document)){
		if(file_exists($repertoire.'META-INF/container.xml')){
			include_spip('inc/xml');
			$arbre_container = spip_xml_load($repertoire.'META-INF/container.xml');
			spip_xml_match_nodes(",^rootfile ,",$arbre_container, $rootfiles);
			if(is_array($rootfiles)){
				foreach($rootfiles as $rootfile => $info){
					$file = extraire_attribut("<$rootfile>",'full-path');
					if($file){
						$file_dir = dirname($file);
						$arbre_root = spip_xml_load($repertoire.$file);
						spip_xml_match_nodes(",^dc:,",$arbre_root, $dublins);
						if(is_array($dublins)){
							foreach($dublins as $dublin => $info_dublin){
								if(preg_match('/dc:title/',$dublin) && (strlen($info_dublin[0]) > 0)){
									$infos['titre'] = trim(textebrut(translitteration($info_dublin[0])));
								}else if(preg_match('/dc:subject/',$dublin) && (strlen($info_dublin[0]) > 0)){
									$infos['descriptif'] = trim(textebrut(translitteration($info_dublin[0])));
								}else if(preg_match('/dc:rights/',$dublin) && (strlen($info_dublin[0]) > 0)){
									$infos['credits'] = trim(textebrut(translitteration($info_dublin[0])));
								}else if(preg_match('/dc:([a-z]*) /',$dublin,$matches) && (strlen($info_dublin[0]) > 0)){
									$infos[$matches[1]] = trim(textebrut(translitteration($info_dublin[0])));
								}else if(preg_match('/dc:([a-z]*)/',$dublin,$matches) && (strlen($info_dublin[0]) > 0)){
									if(($matches[1] == 'description') && !$infos['descriptif']){
										$infos['descriptif'] = trim(textebrut(translitteration($info_dublin[0])));
									}
									$infos[$matches[1]] = trim(textebrut(translitteration($info_dublin[0])));
								}else{
									spip_log($dublin,'epub_reader');
									spip_log($info_dublin[0],'epub_reader');
								}
							}
						}
						spip_xml_match_nodes(",^meta.*name,",$arbre_root, $metas);
						if(is_array($metas)){
							foreach($metas as $meta => $info_meta){
								if(preg_match('/.*name=["\']cover["\'].*content=["\'](.*)["\'].*/',$meta,$meta_match) OR preg_match('/.*content=["\'](.*)["\'].*name=["\']cover["\'].*/',$meta,$meta_match)){
									spip_xml_match_nodes(",^item.*href=['\"].*['\"].*id=['\"]".$meta_match[1]."['\"],",$arbre_root, $covers);
									/**
									 * On parcourt les items trouvés pour retrouver le chemin de la cover si elle existe
									 */
									if(is_array($covers)){
										foreach($covers as $cover => $info_cover){
											preg_match('/^item.*href=["\'](.*)["\'].*id=["\']'.$meta_match[1].'["\'].*/Uims',$cover,$cover_match);
											if(file_exists($repertoire.$cover_match[1])){
												$infos['cover'] = $repertoire.$cover_match[1];
												break;
											}
										}
									}
								}else{
									spip_log($meta,'epub_reader');
								}
							}
						}
						spip_xml_match_nodes(",^reference ,",$arbre_root, $references);
						if(is_array($references)){
							foreach($references as $reference => $info_reference){
								if(preg_match('/.*type=["\']cover["\'].*/s',$reference,$reference_match)){
									spip_log('On a une cover '.$reference,'epub_reader');
									if(preg_match('/ href=["\'](.*\.[a-z]{3})["\'] /',$reference,$cover_match)){
										spip_log($cover_match,'epub_reader');
										spip_log('la cover est : '.$repertoire.$file_dir.'/'.$cover_match[1],'epub_reader');
										if(file_exists($repertoire.$file_dir.'/'.$cover_match[1])){
											$infos['cover'] = $repertoire.$file_dir.'/'.$cover_match[1];
											break;
										}
									}
								}else{
									spip_log($reference,'epub_reader');
								}
							}
						}
						if(isset($infos['cover']) && file_exists($infos['cover'])){
							$ext = substr($infos['cover'],-3);
							if(in_array($ext,array('gif','png','jpg','jpeg')))
								$infos['cover'] = $infos['cover'];
							elseif(in_array($ext,array('xml','html'))){
								include_spip('inc/flock');
								$contenu = '';
								lire_fichier($infos['cover'],$contenu);
								if(strlen($contenu) > 1){
									$img = extraire_attribut(extraire_balise($contenu,'img'),'src');
									if(file_exists(dirname($infos['cover']).'/'.$img))
										$infos['cover'] = dirname($infos['cover']).'/'.$img;
									else
										unset($infos['cover']);
								}
							}
						}
					}
				}
			}
		}else{
			spip_log($repertoire.'META-INF/container.xml n existe pas','epub_reader');
		}
		
		/**
		 * Normaliser la date pour éviter des soucis d'édition
		 */
		if(isset($infos['date']) && $infos['date']){
			include_spip('inc/filtres');
			if (!$infos['date'] = recup_date($infos['date'])) {
				$erreur = "Impossible d'extraire la date de ".$infos['date'];
				unset($infos['date']);
			}
			else if (!($infos['date'] = mktime($infos['date'][3], $infos['date'][4], 0, (int)$infos['date'][1], (int)$infos['date'][2], (int)$infos['date'][0])))
				unset($infos['date']);
			else{
				$infos['date'] = date("Y-m-d H:i:s", $infos['date']);
				$infos['date'] = vider_date($infos['date']); // enlever les valeurs considerees comme nulles (1 1 1970, etc...)
			}
			if (!$infos['date'])
				unset($infos['date']);
		}
		spip_log($infos);
		return $infos;
	}else{
		return array();
	}
}

?>