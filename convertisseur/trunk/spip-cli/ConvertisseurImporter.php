<?php

/***

Importer en masse des fichiers txt dans spip_articles.
Les articles sont ajoutés avec leurs info sauf id_article dans des rubriques créées si besoin. 
Les documents <docxxx> sont gérés
Les raccourcis de liens [xxx->12345] sont gérés

*/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ConvertisseurImporter extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:importer')
			->setDescription('Importer des fichiers SPIP txt dans la table spip_articles (ou autre).')
			->setAliases(array(
				'import' // abbréviation commune pour "import"
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source',
				'conversion_spip'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'id_rubrique de la rubrique où importer la hierarchie de rubriques et les articles défini dans les fichiers txt (en général l\'id_secteur ou on veut importer)',
				'0'
			)
			->addOption(
				'auteur_defaut',
				'a',
				InputOption::VALUE_OPTIONAL,
				'Auteur par défaut (id_auteur)',
				'1'
			)
			->addOption(
				'racine_documents',
				'r',
				InputOption::VALUE_OPTIONAL,
				'path ajouté devant le `fichier` des documents joints importés',
				''
			)
			->addOption(
				'conserver_id_article',
				'c',
				InputOption::VALUE_OPTIONAL,
				'option -c oui pour conserver les id_article',
				''
			)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		$spip_version_branche = $GLOBALS['spip_version_branche'] ;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$id_parent = $input->getOption('dest') ;
		$racine_documents = $input->getOption('racine_documents') ;
		$conserver_id_article = $input->getOption('conserver_id_article') ;
		$auteur_defaut = $input->getOption('auteur_defaut') ;
		
		// Répertoire source
		if(!is_dir($source)){
			$output->writeln("<error>Préciser le répertoire avec les fichiers à importer. spip import -s repertoire </error>\n");
			exit ;
		}
		
		if ($spip_loaded) {
			chdir($spip_racine);
			
			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				
				// Champs d'un article
				include_spip("base/abstract_sql");
				$show = sql_showtable("spip_articles");
				$champs = array_keys($show['field']);
				
				$show = sql_showtable("spip_documents");
				$champs_documents = array_keys($show['field']);
				
				// Ajout d'un champ la premiere fois pour stocker les éventuelles <ins> qui n'ont pas de champs (peut-être long).
				if(!in_array('metadonnees', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add metadonnees MEDIUMTEXT NOT NULL DEFAULT ''");
					sql_query("alter table spip_articles add metadonnees MEDIUMTEXT NOT NULL DEFAULT ''");
				}
				// Ajout d'un champ la premiere fois pour stocker l'id_article original (pour ensuite remapper les liens [->123]).
				if(!in_array('id_source', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add id_source BIGINT(21)");
					sql_query("alter table spip_articles add id_source BIGINT(21)");
				}
				// Ajout d'un champ la premiere fois pour stocker le nom du fichier source, pour reconnaitre un article déjà importé.
				if(!in_array('fichier_source', $champs)){
					$output->writeln("MAJ BDD : alter table spip_articles add fichier_source MEDIUMTEXT NOT NULL DEFAULT ''");
					sql_query("alter table spip_articles add fichier_source MEDIUMTEXT NOT NULL DEFAULT ''");
				}
				
				// on prend tous les fichiers txt dans la source, sauf si metadata.txt a la fin.
				$fichiers = preg_files($source . "/", "(?:(?<!\.metadata\.)txt$)", 100000);
				if(sizeof($fichiers)>0){
					// start and displays the progress bar
					$progress = new ProgressBar($output, sizeof($fichiers));
					$progress->setBarWidth(100);
					$progress->setRedrawFrequency(1);
					$progress->setMessage(" Import de $source/*.txt en cours dans la rubrique $id_parent ... ", 'message'); /**/
					$progress->setMessage("", 'inforub');
					$progress->start();
					
					foreach($fichiers as $f){
						// date d'apres le nom du fichier
						$fichier = basename($f);
						preg_match("/^(\d{4})-\d{2}/", $fichier, $m);
						$mois = $m[0];
						$annee = $m[1] ;
						
						// chopper l'id_parent dans le fichier ?
						include_spip("inc/flock");
						lire_fichier($f, $texte);
						
						// menage
						//@@COLLECTION:esRetour ligne automatique
						//@@SOURCE:article914237.html
						
						$texte = preg_replace("/@@COLLECTION.*/", "", $texte);
						$texte = preg_replace("/@@SOURCE.*/", "", $texte);
						
						// Si des <ins> correspondent à des champs metadonnees connus, on les ajoute.
						$champs_metadonnees = array("mots_cles", "auteurs", "hierarchie", "documents", "descriptif_rubrique", "texte_rubrique");
						$hierarchie = "" ;
						$auteurs = "" ;
						$mots_cles = "" ;
						$documents = "" ;
						$texte_rubrique = "" ;
						$descriptif_rubrique = "" ;
						
						if (preg_match_all(",<ins[^>]+class='(.*?)'[^>]*?>(.*?)</ins>,ims", $texte, $z, PREG_SET_ORDER)){ 
							foreach($z as $d){ 
								if(in_array($d[1], $champs_metadonnees)){ 
									// class="truc" => $truc 
									$$d[1] = split("@@", $d[2]); 
									// virer du texte 
									$texte = substr_replace($texte, '', strpos($texte, $d[0]), strlen($d[0])); 
								}
							}
						}
						
						if (preg_match(",<ins class='id_article'>(.*?)</ins>,ims", $texte, $z))
							$id_source = $z[1];
						
						// dans quelle rubrique importer ?
						// La hierarchie est-elle précisée dans le fichier ? (en principe oui)
						if($hierarchie){
							$hierarchie = implode("/", $hierarchie);
						}else{ // sinon on genere des rubriques annees / mois
							$hierarchie = "$annee/$annee-$mois";
						}
						
						include_spip("inc/rubriques");
						
						// Les éventuels / sont échappés \/ ; exemple : 1999/1999\/06
						$hierarchie = preg_replace("`\\\/`","`--**--`", $hierarchie);
						
						// rétablir les échapemments
						// ajout  des échapemments à creer_rubrique_nommee($titre, $id_parent = 0, $serveur = '')
						
						$arbo = explode("/", $hierarchie);
						
						$id_parent_rubrique = $id_parent ;
						
						foreach ($arbo as $titre) {
							// retablir les </multi> et autres balises fermantes html
							$titre = preg_replace(",<@([a-z][^>]*)>,ims", "</\\1>", $titre);
							// rétablir les échapemments \/
							$titre = preg_replace(",`--\*\*--`,","/", $titre);
							
							$r = sql_getfetsel("id_rubrique", "spip_rubriques",
								"titre = " . sql_quote($titre) . " AND id_parent=" . intval($id_parent_rubrique),
								$groupby = array(), $orderby = array(), $limit = '', $having = array(), $serveur);
							
							if ($r !== null) {
								$id_parent_rubrique = $r;
							} else {
								$id_rubrique = sql_insertq('spip_rubriques', array(
										'titre' => $titre,
										'id_parent' => $id_parent_rubrique,
										'statut' => 'prepa'
									), $desc = array(), $serveur);
								if ($id_parent_rubrique > 0) {
									$data = sql_fetsel("id_secteur,lang", "spip_rubriques", "id_rubrique=$id_parent_rubrique",
										$groupby = array(), $orderby = array(), $limit = '', $having = array(), $serveur);
									$id_secteur = $data['id_secteur'];
									$lang = $data['lang'];
								} else {
									$id_secteur = $id_rubrique;
									$lang = $GLOBALS['meta']['langue_site'];
								}
								
								sql_updateq('spip_rubriques', array('id_secteur' => $id_secteur, "lang" => $lang),
									"id_rubrique=" . intval($id_rubrique), $desc = '', $serveur);
								
								// pour la recursion
								$id_parent_rubrique = $id_rubrique;
							}
						}
						
						$hierarchie = preg_replace(",`--\*\*--`,","/", $hierarchie);
						
						$id_rubrique = intval($id_parent_rubrique);
						
						if($descriptif_rubrique OR $texte_rubrique){
							$up = sql_update('spip_rubriques', array('statut' => sql_quote("publie"), 'texte' => sql_quote($texte_rubrique), 'descriptif' => sql_quote($descriptif_rubrique)), "id_rubrique=$id_rubrique");
						}
						else
							$up = sql_updateq('spip_rubriques', array('statut' => 'publie'), "id_rubrique=$id_rubrique");
						
						$hierarchie_rub = sql_allfetsel("id_secteur,id_parent","spip_rubriques","id_rubrique=$id_rubrique");
						
						if($hierarchie_rub[0]["id_secteur"] == 0){
							die("Erreur de création de rubrique $id_rubrique : id_secteur=0");
						}
						
						if($up)
							$progress->setMessage(" Rubrique $hierarchie => (" . $hierarchie_rub[0]["id_secteur"] ." > " . $hierarchie_rub[0]["id_parent"] ." > ) $id_rubrique ", 'inforub');
						
						$progress->setMessage("", 'docs');
						$progress->setMessage("", 'mot');
						$progress->setMessage("", 'auteur');
						
						// inserer l'article
						include_spip("inc/convertisseur");
						
						// auteur par défaut (admin)
						$id_admin = sql_getfetsel("id_auteur", "spip_auteurs", "id_auteur=" . $auteur_defaut);
						$id_admin = ($id_admin)? $id_admin : 12166 ;
						
						$GLOBALS['auteur_session']['id_auteur'] = $id_admin ;
						
						if($id_article = inserer_conversion($texte, $id_rubrique, $f)){
							
							// doit-on conserver l'id_article (option) ?
							// sql_update spip_articles id_article=$id_source
							if($conserver_id_article == "oui" and $id_source > 0){
								sql_update("spip_articles", array("id_article" => $id_source), "id_article=$id_article") ;
								// maj le lien auteur admin
								if($spip_version_branche > "3")
									sql_update('spip_auteurs_liens',
										array(
										'id_objet' => $id_source
										),
										"objet='article' and id_objet=$id_article"
									);
								else
									sql_update('spip_auteurs_articles',
										array(
										'id_article' => $id_source
										),
										"id_article=$id_article"
									);
								$id_article = $id_source ;
							}
							// Créer l'auteur ?
							if($auteurs){
								// on efface les auteurs, puis on remet les nouveaux
								if($spip_version_branche > "3")
									sql_delete('spip_auteurs_liens', 'id_objet = ' . intval($id_article) . ' and objet="article" and id_auteur !=' . $id_admin);
								else // spip2
									sql_delete('spip_auteurs_articles', 'id_article = ' . intval($id_article) . 'and id_auteur !=' . $id_admin);
								
								foreach($auteurs as $auteur){
									
									list($nom_auteur,$bio_auteur) = explode("::", $auteur);
									// On essaie de trouver un nom*prénom dans les auteurs
									$a_nom = explode(" ", $nom_auteur);
									$prenom_nom = array_pop($a_nom) . "*" . join(" ", $a_nom);
									
									// echo "\n$prenom_nom\n" ;
									
									if($id_auteur = sql_getfetsel("id_auteur", "spip_auteurs", "nom=" . sql_quote($prenom_nom))){
										
									}else
										$id_auteur = sql_getfetsel("id_auteur", "spip_auteurs", "nom=" . sql_quote($nom_auteur));
									
									if(!$id_auteur){
										$id_auteur = sql_insertq("spip_auteurs", array(
												"nom" => $nom_auteur,
												"statut" => "1comite",
												"bio" => $bio_auteur
										));
										
										$auteur_m = substr("Création de l'auteur " . $auteur, 0, 100) ;
										$progress->setMessage($auteur_m, 'auteur');
									}
									
									if($spip_version_branche > "3"){
										if(!sql_getfetsel("id_auteur", "spip_auteurs_liens", "id_auteur=$id_auteur and id_objet=$id_article and objet='article'"))
											sql_insertq("spip_auteurs_liens", array(
												"id_auteur" => $id_auteur,
												"id_objet" => $id_article,
												"objet" => "article"
											));
									}else // spip 2
										if(!sql_getfetsel("id_auteur", "spip_auteurs_articles", "id_auteur=$id_auteur and id_article=$id_article"))
											sql_insertq("spip_auteurs_articles", array(
												"id_auteur" => $id_auteur,
												"id_article" => $id_article
											));
									
								}
							}
							
							// Créer des mots clés ?
							if($mots_cles){
								// on commence par effacer les mots déjà sur l'article, puis on remet les mots.
								if($spip_version_branche > "3")
									sql_delete('spip_mots_liens', 'id_objet = ' . intval($id_article) . ' and objet="article"');
								else // spip2
									sql_delete('spip_mots_articles', 'id_article = ' . intval($id_article));
								
								foreach($mots_cles as $mot){
									// groupe mot-clé
									list($type_mot,$titre_mot) = explode("::", $mot);
									$type_mot = ($type_mot)? $type_mot : "Mots importés" ;
									
									$id_groupe_mot = sql_getfetsel("id_groupe", "spip_groupes_mots", "titre=" . sql_quote($type_mot));
									if(!$id_groupe_mot)
										$id_groupe_mot = sql_insertq("spip_groupes_mots", array("titre" => $type_mot));
									
									$id_mot = sql_getfetsel("id_mot", "spip_mots", "titre=" . sql_quote($titre_mot));
									if(!$id_mot AND $titre_mot !=""){
										$id_mot = sql_insertq("spip_mots", array(
											"titre" => $titre_mot,
											"type" => $type_mot,
											"id_groupe" => $id_groupe_mot
										));
										$mot_m = substr("Création du mot " . $titre_mot . " (" . $type_mot .")", 0, 100) ;
										$progress->setMessage($mot_m, 'mot');
									}
									
									if($spip_version_branche > "3"){
										if(!sql_getfetsel("id_mot", "spip_mots_liens", "id_mot=$id_mot and id_objet=$id_article and objet='article'"))
											sql_insertq("spip_mots_liens", array(
												"id_mot" => $id_mot,
												"id_objet" => $id_article,
												"objet" => "article"
											));
									}else // spip 2
										if(!sql_getfetsel("id_mot", "spip_mots_articles", "id_mot=$id_mot and id_article=$id_article"))
											sql_insertq("spip_mots_articles", array(
												"id_mot" => $id_mot,
												"id_article" => $id_article
											));
								}
							}
							
							// Créer des documents ?
							if($documents){
								// on commence par effacer les docs déjà sur l'article, puis on remet les mots.
								sql_delete('spip_documents_liens', 'id_objet = ' . intval($id_article) . ' and objet="article"');
								
								foreach($documents as $doc){
									$d = json_decode($doc, true);
									$id_doc = $d['id_document'] ;
									unset($d['id_document']);
									if(strlen($racine_documents) > 0 AND !preg_match(",/$,",$racine_documents))
										$racine_documents = $racine_documents . "/" ;
									
									if(!preg_match(",^http://,",$d['fichier']))
										$d['fichier'] = $racine_documents . $d['fichier'] ;
									
									// champs ok dans les documents ?
									foreach($d as $k => $v)
										if(in_array($k, $champs_documents))
											$document_a_inserer[$k] = $v ;
									
									// insertion du doc
									$id_document = sql_getfetsel("id_document", "spip_documents", "fichier=" . sql_quote($d['fichier']));
									
									if(!$id_document){
										$id_document = sql_insertq("spip_documents", $document_a_inserer);
										$progress->setMessage("Création du document " . $d['titre'] . " (" . $d['fichier'] .")", 'docs');
									}else{
										unset($document_a_inserer["id_document"]);
										sql_updateq("spip_documents", $document_a_inserer, "id_document=$id_document") ;
									}
									
									if($id_document AND !sql_getfetsel("id_document", "spip_documents_liens", "id_document=$id_document and id_objet=$id_article and objet='article'"))
										sql_insertq("spip_documents_liens", array(
												"id_document" => $id_document,
												"id_objet" => $id_article,
												"objet" => "article"
									));
									
									// modifier le texte qui appelle peut etre un <doc123>
									if($id_document){
										// ressortir le texte propre...
										$texte_art = sql_getfetsel("texte", "spip_articles", "id_article=$id_article");
										$texte_art = preg_replace("/(<(doc|img|emb))". $id_doc . "/i", "\${1}$id_document", $texte_art);
										sql_update("spip_articles", array("texte" => sql_quote($texte_art)), "id_article=$id_article");
										
										// le chapo aussi
										$chapo_art = sql_getfetsel("chapo", "spip_articles", "id_article=$id_article");
										$chapo_art = preg_replace("/(<(doc|img|emb))". $id_doc . "/i", "\${1}$id_document", $chapo_art);
										sql_update("spip_articles", array("chapo" => sql_quote($chapo_art)), "id_article=$id_article");
										
										// le ps aussi
										$ps_art = sql_getfetsel("ps", "spip_articles", "id_article=$id_article");
										$ps_art = preg_replace("/(<(doc|img|emb))". $id_doc . "/i", "\${1}$id_document", $ps_art);
										sql_update("spip_articles", array("ps" => sql_quote($ps_art)), "id_article=$id_article");
									}
								}
							}
							
							// recaler des liens [->123456] dans les textes
							// si on ne conserve pas le meme id_article
							include_spip("inc/lien");
							if(preg_match(_RACCOURCI_LIEN, $texte) and $conserver_id_article == "")
								passthru("echo '$id_article	$id_source' >> liens_a_corriger.txt");
							
							$progress->setMessage("ajout de l'art $id_article", 'article');
							
							// on réindexe immédiatement avec avec le plugin indexer (Sphinx) le cas échéant.
							$progress->setMessage("", 'index');
							include_spip("indexer_pipelines");
							if (function_exists("indexer_redindex_objet")) {
								indexer_redindex_objet('article', $id_article, false);
								$progress->setMessage(" + indexation de $id_article", 'index');
							}
							
							// Si tout s'est bien passé, on avance la barre
							$progress->setMessage($f, 'filename');
							$progress->setFormat("<fg=white;bg=blue>%message% %article% %index%</>\n" . "<fg=white;bg=red>%inforub% %auteur% %mot%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n  %filename%\n%docs%\n\n");
							$progress->advance();
							
						}else{
							$output->writeln("<error>échec de l'import de $f</error>");
							exit ;
						}
					}
					
					// ensure that the progress bar is at 100%
					$progress->finish();
					
					// remapper les liens [->12345]
					lire_fichier("liens_a_corriger.txt", $articles);
					$corrections_liens = inc_file_to_array_dist($articles);
					
					if(is_array($corrections_liens))
						foreach($corrections_liens as $k => $v){
							if($v){
								list($id_article, $id_source) = explode("\t", $v);
								include_spip("action/corriger_liens_internes");
								convertisseur_corriger_liens_internes($id_article,$id_parent,'texte');
								convertisseur_corriger_liens_internes($id_article,$id_parent,'chapo');
							}
						}
					
					$output->writeln("");
					if(is_file("liens_a_corriger.txt"))
						unlink("liens_a_corriger.txt");
				}
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
