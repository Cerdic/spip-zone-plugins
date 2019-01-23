<?php

use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class IndexerObjet extends Command {
	protected function configure() {
		$this
			->setName('indexer:objet')
			->setDescription('Indexer un objet en particulier')
			->addOption(
				'objet',
				null,
				InputOption::VALUE_REQUIRED,
				'objet',
				null
			)
			->addOption(
				'id_objet',
				null,
				InputOption::VALUE_REQUIRED,
				'id_objet ou liste d\'id_objet séparés par des virgules',
				''
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		include_spip('inc/indexer');
		global $spip_racine;
		global $spip_loaded;

		$objet = $input->getOption('objet');
		if (!$objet) {
			$output->writeln("<error>Indiquez un objet a indexer</error>");
			exit(1);
		}


		if ($id_objets = $input->getOption('id_objet')) {
			$id_objets = explode(',', $id_objets);
			$id_objets = array_map('intval', $id_objets);
			$id_objets = array_filter($id_objets);
		}
		if (!$id_objets) {
			$output->writeln("<error>Indiquez un ou des id_objet a indexer (séparés par des virgules)</error>");
			exit(1);
		}


		foreach ($id_objets as $id_objet) {
			$this->io->care("Indexer $objet #$id_objet");
			indexer_redindex_objet($objet,$id_objet, false);
		}

	}
}
