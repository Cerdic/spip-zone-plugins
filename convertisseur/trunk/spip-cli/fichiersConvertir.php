<?php

/***

Convertir des fichiers par lots.

Mettre les fichiers dans le repertoire /conversion_source/%COLLECTION%/%NUMERO%/[fichiers] du SPIP par défaut, ou dans un autre répertoire.

Lancer la commande spip-cli : spip convert

Les fichiers convertis sont placés dans le repertoire /conversion_spip/%COLLECTION%/%NUMERO% du SPIP

Si un repertoire git est trouvé dans /dest alors on prend le repertoire */ // /*.git/*/collections comme répertoire dest (ce qui permet de faire un suivi de révision du contenu). 


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Convert extends Command {
	protected function configure() {
		$this
			->setName('fichiers:convertir')
			->setDescription('Conversion des fichiers divers en fichiers d\'import SPIP.')
			->setAliases(array(
				'convert' // abbréviation commune pour "conversion"
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
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
		$extracteur = $input->getOption('extracteur') ;
		
		include_spip("iterateur/data");
		include_spip("inc/utils");
		
		include_spip("inc/convertisseur");
		global $conv_formats ;
		foreach($conv_formats as $v)
			if(!is_array($v))
				$extracteurs_disponibles[] = $v ;
		$extracteurs_dispos = join(", ",$extracteurs_disponibles);		
						
		if($extracteur == "" || !in_array($extracteur, $extracteurs_disponibles)){
			$output->writeln("<error>Définir un extracteur `spip convert -e %extracteur%`. Extracteurs disponibles : $extracteurs_dispos</error>");
			exit ;
		}
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			
			// Répertoire de destination ou l'on enregistre les fichiers spip (/convertion_spip par défaut).
			if(!is_dir($source)){
				$output->writeln("<error>Préciser où sont les fichiers à convertir `spip convert -s %repertoire%` ou créer un repertoire conversion_source/</error>");
				exit ;
			}
	
			// Repertoire source
			if($dest != "" AND !is_dir($dest))
				mkdir($dest);
			
			if(!is_dir($dest)){
				$output->writeln("<error>Préciser où placer les fichiers convertis `spip convert -d %repertoire%` ou créer un repertoire conversion_spip/</error>");
				exit ;
			}
			
			// Si c'est bon on continue
			else{
				$output->writeln("<info>C'est parti pour la convertion `$extracteur` des fichiers de $source/ dans $dest/ !</info>");

				// trouve t'on un repertoire trunk/collections dans $dest ?
				if($ls_depot = inc_ls_to_array_dist($dest ."/trunk/collections")){
					$dest = $ls_depot[0]['dirname'] . "/" .  $ls_depot[0]['basename'] ;
					$output->writeln("<info>GIT : dest = $dest</info>");
				}
								
				// plugin convertisseur
				include_spip("extract/$extracteur");				
				$fonction_extraction = $GLOBALS['extracteur'][$extracteur] ;
													
				// chopper des fichiers xml mais pas xxx.metatada.xml
				$fichiers = preg_files($source, "(?:(?<!\.metadata\.)xml$)");
									
				// ou a défaut n'importe quel fichier trouvé
				if(sizeof($fichiers) == 0)
					$fichiers = preg_files($source, ".*");
																	
				foreach($fichiers as $f){
					
					$fn = str_replace("$source/","", $f);
				
					// Répertoires publication et numero ?
					$classement = explode("/", $fn);
					
					if(sizeof($classement) >= 3){
						$collection = $classement[0] ;
						$numero = $classement[1] ;
					}elseif(sizeof($classement) == 2){
						$collection = "";
						$numero=$classement[0] ;
					}else{
						$collection = "";
						$numero="" ;
					}
					
					if($collection != "")
						$collection = "/" . $collection ;

					if($numero != "")
						$numero = "/" . $numero ;

								
					if(!is_dir("$dest" .  $collection)){
						mkdir("$dest"  . $collection) ;
					}
					if(!is_dir("$dest"  . $collection  . $numero)){
						mkdir("$dest"  . $collection  . $numero) ;
					}
					
					$article = basename($f);

					// pour le chemin des documents.
					set_request('fichier', "$collection/$numero/fichier.xml");
				
					$contenu = $fonction_extraction($f,$charset);
										
					include_spip("inc/convertisseur");
					$contenu = nettoyer_format($contenu);
					
					// nettoyer les noms de fichiers
					include_spip("inc/charsets");
					$article = translitteration($article);
					$article = preg_replace(',[^\w-]+,', '_', $article);
					$article = preg_replace(',_xml$,', '.xml', $article);
					
					include_spip("inc/flock");
					ecrire_fichier($dest . $collection  . $numero . "/" . $article, $contenu);
								
					$output->writeln("Nouvelle conversion : $dest" . $collection  . $numero . "/" . $article);
				}
				
			}

		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
