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
		$dict_tmp = $dossier_tmp . $index . '.dict.txt';
		
		// On se fait un dossier final
		$dossier_autocomplete = sous_repertoire(_DIR_IMG . 'indexer_autocomplete');
		
		$sphinxql = new \Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);
		
		// On fait produire les bons fichiers pour le RT
		$sphinxql->query("FLUSH RAMCHUNK $index");
		
		// On récupère le chemin du premier fichier SPI trouvé dans le dossier Sphinx
		if ($spis = glob($dossier_data . $index . '.*.spi') and $spi = $spis[0]) {
			if (function_exists('passthru')) {
				passthru("indextool --dumpdict {$spi} > {$dict_tmp}");
				
				// Si on a bien le dictionnaire voulu à la fin
				if (file_exists($dict_tmp)) {
					$output->writeln("<info>Dictionnaire correctement créé dans {$dict_tmp}</info>");
					
					$this->analyser_dictionnaire(
						$dossier_autocomplete,
						$dict_tmp,
						find_in_path('autocomplete/exceptions.txt')
					);
				}
			}
			else {
				$output->writeln('<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>');
			}
		}
		else {
			$output->writeln("<error>Pas trouvé de fichier data/$index.*.spi</error>");
		}
	}
	
	protected function analyser_dictionnaire($rep, $dict, $exceptions) {
		include_spip('inc/charsets');
		$ab = '';
		
		$exceptions = array_map('trim', file($exceptions));
		
		foreach(file($dict) as $k => $l) {
			list($keyword,$docs,$hits,$offset) = explode(',', $l);
			
			// Quand le tableau mots-fréquences commence, on démarre
			if ($keyword === 'keyword' and $docs === 'docs') {
				$start = true;
			}
			
			if ($start AND ord($l) != 2) {
				if (!in_array($keyword, $exceptions)) {
					$_ab = strtolower(translitteration(mb_substr($keyword, 0, 2)));
					if ($_ab !== $ab) {
						$this->save($ab, $mots, $rep);
						$ab = $_ab;
						$mots = [];
					}
					// conserver les mots ayant un nombre suffisant d'occurrences
					if ($hits >= 3) {
						$mots[$keyword] = $hits;
					}
				}
			}
		}
		
		$this->save($ab, $mots, $rep);
	}
	
	// enregistrer la liste ab - triée par hits décroissants
	protected function save($ab, $mots, $rep) {
		if (empty($mots)) return;
		if (!preg_match(',[a-z][a-z],S', $ab)) return;
		
		arsort($mots);
		$dump = join("\n", array_keys($mots));
		$a = mb_substr($ab, 0,1);
		if (!is_dir($rep.'/'.$a)) mkdir ($rep.'/'.$a);
		$res = ($fp = fopen($rep.'/'.$a.'/'.$ab.'.txt', 'w'))
		&& fwrite ($fp, $dump)
		&& fclose ($fp);
		spip_log("autocomplete dict $ab " . count($mots) . " ($res)");
		return $res;
	}
}
