<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class SphinxGenererAutocomplete extends Command {
	protected function configure() {
		$this
			->setName('sphinx:generer_autocomplete')
			->setDescription('Générer le dictionnaire nécessaire à l’autocomplétion. DOIT être lancée en sudo pour lire les dossiers de Sphinx.')
			->addOption(
				'index',
				'i',
				InputOption::VALUE_OPTIONAL,
				'Nom de l’index Sphinx dont on veut générer le dictionnaire',
				'spip'
			)
			->addOption(
				'dossier-data',
				null,
				InputOption::VALUE_OPTIONAL,
				'Nom de l’index Sphinx dont on veut générer le dictionnaire',
				'/var/lib/sphinxsearch/data/'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/flock');
		include_spip('inc/indexer');
		
		// On récupère les options
		$index = $input->getOption('index');
		$dossier_data = $input->getOption('dossier-data');
		
		// On se fait un dossier temporaire pour enregistrer le dictionnaire complet
		$dossier_tmp = sous_repertoire(_DIR_TMP . 'sphinx/');
		
		$sphinxql = new \Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);
		
		// On fait produire les bons fichiers pour le RT
		$sphinxql->query("FLUSH RAMCHUNK $index");
		
		// On récupère le chemin du premier fichier SPI trouvé dans le dossier Sphinx
		if ($spis = glob($dossier_data . $index . '.*.spi') and $spi = $spis[0]) {
			if (function_exists('passthru')) {
				passthru("indextool --dumpdict $spi > {$dossier_tmp}{$index}.dict.txt");
			}
			else {
				$output->writeln('<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>');
			}
		}
		else {
			$output->writeln("<error>Pas trouvé de fichier data/$index.*.spi</error>");
		}
	}
}
