<?php

/***

	Installer ImageMagick pour que cela fonctionne. (brew install imagemagick sous Mac)

*/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class optimg extends Command {
	protected function configure() {
		$this
			->setName('images:optimiser')
			->setDescription('Compression et redimensionnement en option d\'une ou plusieurs images')
			->setAliases(array(
				'optimg' // abbréviation pas ouf pour ca
			))
			->addArgument(
                'image',
                InputArgument::OPTIONAL,
                'Image à optimiser.'
            )			
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Répertoire source. Optimiser toutes les images d\'un répertoire. Exemple : `spip optimg -s IMG`.',
				'0'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire de destination. Exemple : `spip optimg -d mon_repertoire mon_image.jpg`. Si ce répertoire n\'est pas précisé, on écrase l\'image avec sa version optimisée.',
				'0'
			)
			->addOption(
				'resize',
				'r',
				InputOption::VALUE_OPTIONAL,
				'Redimensionner la largeur à n px, en conservant les proportions pour calculer la hauteur. Exemple : `spip optimg -r 900 mon_image.jpg`',
				'0'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		global $spip_racine;
		global $spip_loaded;
		
		include_spip("iterateur/data");
		
		$source = $input->getOption('source') ;
		$dest = $input->getOption('dest') ;
		$resize = $input->getOption('resize') ;
		$image = $input->getArgument('image');
		
		
		
		if ($spip_loaded) {
			chdir($spip_racine);

			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{				
				if(strlen($dest) > 1){
					 $label_d=" dans $dest " ;
					 $param_d=" $dest" ;
					 if(!is_dir($dest))
					 	mkdir($dest);
				}	 
				if($resize > 0){
					$label_r=" en redimensionnant la largeur à $resize px " ;
				}
				$param_r=" $resize" ;
				
				// optimisation imagemagick
				if($image){
					$output->writeln("<info>C'est parti pour une petite optimisation d'image(s) ${label_r}${label_d} !</info>");
					passthru('plugins/convertisseur/scripts/optimg.sh ' . $image . $param_r . $param_d);
				}	
				elseif($source){
					$param_s = " $source" ;
					$output->writeln("<info>C'est parti pour une petite optimisation des images de $source/ ${label_r}${label_d} !</info>");

					$fichiers_jpg = preg_files($source . "/", "\.jpg$");
	
					foreach($fichiers_jpg as $image){
						
						$path = explode("/", preg_replace(",^/,", "", dirname(str_replace($source, "", $image)))) ;
						
						# var_dump($path);
						$dpt = $dest ;
						foreach($path as $r){
							if(!is_dir("$dpt/$r")){
								mkdir("$dpt/$r");
								$dpt="$dpt/$r";
							}else
								$dpt="$dpt/$r";
						}
						
						$param_d=" $dpt";

						// Conversion imagemagick
						passthru('plugins/convertisseur/scripts/optimg.sh ' . $image . $param_r . $param_d);
					}
				}	
			}
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}
