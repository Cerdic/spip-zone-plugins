<?php

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class OembedRecupererData extends Command {
	protected function configure() {
		$this
			->setName('oembed:recupererdata')
			->setDescription('Recuperer les donnees oembed d\'une URL')
			->addOption(
				'url',
				null,
				InputOption::VALUE_REQUIRED,
				'URL de la ressource',
				null
			)
			->addOption(
				'maxwidth',
				null,
				InputOption::VALUE_OPTIONAL,
				'Largeur maxi',
				''
			)
			->addOption(
				'maxheight',
				null,
				InputOption::VALUE_OPTIONAL,
				'Hauteur maxi',
				null
			)
			->addOption(
				'format',
				null,
				InputOption::VALUE_OPTIONAL,
				'Format des données',
				'json'
			)
			->addOption(
				'detect',
				null,
				InputOption::VALUE_OPTIONAL,
				'Detecter automatiquement le provider oembed si il n\'est pas connu',
				null
			)
			->addOption(
				'force',
				null,
				InputOption::VALUE_OPTIONAL,
				'forcer une nouvelle récupération des données depuis le serveur source (ignorer le cache)',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		include_spip('inc/oembed');

		#global $spip_racine;
		#global $spip_loaded;

		$url = $input->getOption('url');
		if (!$url) {
			$output->writeln("<error>Indiquez une URL</error>");
			exit(1);
		}

		$maxwidth = $input->getOption('maxwidth');
		$maxheight = $input->getOption('maxheight');
		$format = $input->getOption('format');
		$detect = $input->getOption('detect');
		$force = $input->getOption('force');

		$data = oembed_recuperer_data($url, $maxwidth, $maxheight, $format, $detect ? 'oui': 'non', $force);

		$output->writeln(var_export($data, true));
	}
}
