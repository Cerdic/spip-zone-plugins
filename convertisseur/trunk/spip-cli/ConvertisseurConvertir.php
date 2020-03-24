<?php

/***

Convertir des fichiers par lots.

Formats de conversion : docx, quark, html, quark_xml, indesign_xml, xml_ocr, xml_de.

Mettre les fichiers dans le repertoire /conversion_source/%COLLECTION%/%NUMERO%/[fichiers] du SPIP par défaut, ou dans un autre répertoire.

Lancer la commande spip-cli : spip conversion

Les fichiers convertis sont placés dans le repertoire /conversion_spip/%COLLECTION%/%NUMERO% du SPIP

Si un repertoire git est trouvé dans /dest alors on prend le repertoire */ // /*.git/*/collections comme répertoire dest (ce qui permet de faire un suivi de révision du contenu). 

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertisseurConvertir extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:convertir')
			->setDescription('Conversion de fichiers divers au format SPIP txt. `spip conversion -e %extracteur% -s %source% -d %dest%`.')
			->setAliases(array(
				'conversion' // abbréviation commune pour "conversion"
			))
			->addOption(
				'extracteur',
				'e',
				InputOption::VALUE_OPTIONAL,
				'Type d\'extracteur pour la conversion',
				''
			)
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source de la collection à convertir, structuré en collection/numero/[fichiers]',
				'conversion_source'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire de destination',
				'conversion_spip'
			)
			->addOption(
				'notes',
				'b',
				InputOption::VALUE_OPTIONAL,
				'Convertir les notes de bas de pages (mettre -b oui)',
				''
			)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
		$extracteur = $input->getOption('extracteur') ;
		$corriger_notes = $input->getOption('notes') ;
		
		include_spip("iterateur/data");
		include_spip("inc/utils");
		include_spip(_DIR_PLUGIN_CONVERTISSEUR . "convertisseur_fonctions");
		
		$extracteurs_dispos = join(", ",$GLOBALS['extracteurs_disponibles']);
		
		if($extracteur == "" || !in_array($extracteur, $GLOBALS['extracteurs_disponibles'])){
			$output->writeln("<error>Définir un extracteur `spip conversion -e %extracteur%`. Extracteurs disponibles : $extracteurs_dispos</error>");
			exit ;
		}
		
		if ($spip_loaded) {
			chdir($spip_racine);
			
			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			
			// Répertoire de destination ou l'on enregistre les fichiers spip (/convertion_spip par défaut).
			if(!is_dir($source)){
				$output->writeln("<error>Préciser où sont les fichiers à convertir `spip conversion -s %repertoire%` ou créer un repertoire conversion_source/</error>");
				exit ;
			}
			
			// Repertoire source
			if($dest != "" AND !is_dir($dest))
				mkdir($dest);
			
			if(!is_dir($dest)){
				$output->writeln("<error>Préciser où placer les fichiers convertis `spip conversion -d %repertoire%` ou créer un répertoire conversion_spip/</error>");
				exit ;
			}
			
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour la conversion `$extracteur` des fichiers de $source/ dans $dest/ !</info>");
				
				// trouve t'on un repertoire trunk/collections dans $dest ?
				if($ls_depot = inc_ls_to_array_dist($dest ."/trunk/collections")){
					$dest = $ls_depot[0]['dirname'] . "/" .  $ls_depot[0]['basename'] ;
					$output->writeln("<info>GIT : dest = $dest</info>");
				}
				
				// plugin convertisseur
				include_spip("extract/$extracteur");
				include_spip("convertisseur_fonctions");
				$fonction_extraction = "inc_${extracteur}_to_array_dist" ;
				
				// chopper des fichiers xml mais pas xxx_metatada.xml
				$fichiers = preg_files($source ."/", "(?:(?<!_metadata\.)xml$)");
				
				// ou a défaut n'importe quel fichier trouvé
				if(sizeof($fichiers) == 0)
					$fichiers = preg_files($source, ".*");
				
				foreach($fichiers as $f){
					
					//var_dump($f);
					
					$fn = str_replace("$source/","", $f);
					
					// Déterminer l'organisation des fichiers
					$classement = explode("/", $fn);
					// Répertoires publication et numero ?
					if(sizeof($classement) >= 3){
						$collection = $classement[0] . "/";
						$numero = $classement[1] . "/";
					}// Répertoires de numeros ?
					elseif(sizeof($classement) == 2){
						$collection = "";
						$numero=$classement[0] . "/" ;
					}else{
						# on recopie l'arbo de la source
						preg_match(",.*/([^/]+)/([^/]+)/[^/]+$,", $f, $m);
						$collection = $m[1] ."/ ";
						$numero = $m[2] . "/" ;
						// var_dump($m, $collection, $numero);
					}
					
					$file = basename($f);
					
					// pour le chemin des documents.
					set_request('fichier', $collection . $numero . "fichier.xml");
					
					lire_fichier($f,$c);
					$articles = $fonction_extraction($c);
					
					$nb_articles = sizeof($articles) ;
					
					include_spip("inc/charsets");
					foreach($articles as $contenu){
						
						// Générer des noms de fichiers valides
						if($nb_article == 1){
							// si un seul article, on grade le nom de fichier original
							$file = translitteration($file);
							$file = preg_replace(',[^\w-]+,', '_', $file);
							$file = preg_replace(',_xml$,', '.txt', $file);
						}else{
							// plusieurs articles, il faut creer des noms de fichiers
							$file = trim(annee($contenu['date']) . "-" . mois($contenu['date']) . "-" .translitteration($contenu['titre'])) ;
							$file = preg_replace(',[^\w-]+,', '_', $file) . ".txt" ;
							//var_dump($file);
						}
						
						// recaler les notes avec le plugin revision nbsp si dispo
						if(_DIR_PLUGIN_REVISIONNBSP AND $corriger_notes == "oui"){
							include_spip('revision_nbsp');
							if($n = notes_automatiques($contenu['insertion']))
								$contenu['insertion'] = $n ;
							//global $nb_notes;
							//echo "***** $f : $nb_notes notes ******\n\n" ;
						}
						
						$c = array(
							"fichier_source" => $f,
							"dest" => $dest,
							"collection" => ($contenu['collection']) ? trim($contenu['collection']) : trim($collection),
							"numero" => ($contenu['numero']) ? $contenu['numero'] : $numero,
							"contenu" => $contenu,
							"basename" => $file ,
							"fichier_dest" => $dest . "/" . $collection . $numero . $file
						);
						
						// traitements persos en pipeline maison sur $c avant d'écrire le fichier converti
						if(find_in_path('convertisseur_perso.php'))
							include_spip("convertisseur_perso");
						if (function_exists('nettoyer_conversion_cli')){
							$c = nettoyer_conversion_cli($c);
						}
						
						if(!is_dir($c["dest"] . "/" .  $c["collection"])){
							mkdir($c["dest"]  . "/" . $c["collection"]) ;
						}
						
						if(!is_dir($c["dest"]  . "/" . $c["collection"] . $c["numero"])){
							mkdir($c["dest"]  . "/" . $c["collection"] . $c["numero"]) ;
						}
						
						// var_dump($c["contenu"]["insertion"]);
						
						include_spip("inc/flock");
						ecrire_fichier($c["fichier_dest"], $c["contenu"]["insertion"]);
						
						$output->writeln("Nouvelle conversion : " . $c["fichier_dest"]);
					}
				
				}
				
			}

		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}

