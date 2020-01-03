<?php

/*
    This file is part of Salvatore, the translation robot of Trad-lang (SPIP)

    Salvatore is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Trad-Lang is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Trad-Lang; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    Copyright 2003-2018
        Florent Jugla <florent.jugla@eledo.com>,
        Philippe Riviere <fil@rezo.net>,
        Chryjs <chryjs!@!free!.!fr>,
 		kent1 <kent1@arscenic.info>
*/

/**
 * Ce script va chercher les fichiers définis dans le fichier traductions/traductions.txt
 *
 */


use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressHelper;


class SalvatoreUpgrade extends Command {
	protected function configure(){
		$this
			->setName('salvatore:upgrade')
			->setDescription('Mets a jour la base de donnees et migre depuis une ancienne version')
			->addOption(
				'traductions',
				null,
				InputOption::VALUE_REQUIRED,
				'Chemin vers le fichier traductions.txt a utiliser [salvatore/traductions/traductions.txt] pour mettre a jour les modules en base lors de l\'upgrade',
				null
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output){
		global $spip_racine;
		global $spip_loaded;

		include_spip('inc/salvatore');
		include_spip('inc/salvatore_lecteur');

		salvatore_init(array($output, 'writeln'));


		$output->writeln("<comment>=======================================</comment>");
		$output->writeln("<comment>UPGRADE [Mise à jour de la base]</comment>");
		$output->writeln("<comment>=======================================</comment>");

		define('_TIME_OUT', time() + 24*3600); // pas de timeout
		include_spip('tradlang_administrations');

		// commencer par la
		include_spip('inc/filtres');
		$schema_declare = filtre_info_plugin_dist('tradlang', 'schema');
		tradlang_upgrade('tradlang_base_version', $schema_declare);
		$output->writeln("-");

		$traductions = $input->getOption('traductions');
		$liste_trad = salvatore_charger_fichier_traductions($traductions);
		$n = count($liste_trad);
		$output->writeln("<info>$n modules dans le fichier traductions " . ($traductions ? $traductions : '') . "</info>");

		$modules_todo = sql_allfetsel('distinct module','spip_tradlang_modules', "dir_module='' OR dir_module=module");
		$modules_todo = array_column($modules_todo, 'module');
		$n = count($modules_todo);
		$output->writeln("$n modules en base sans dir_module");

		foreach ($liste_trad as $traduction) {
			if (in_array($traduction['module'], $modules_todo)) {
				sql_updateq('spip_tradlang_modules', array('dir_module' => $traduction['dir_module']), "module=".sql_quote($traduction['module'])." AND (dir_module='' OR dir_module=module)");
				$output->writeln("  Module " . $traduction['module'] . " -> " . $traduction['dir_module']);
			}
		}

		$modules_todo = sql_allfetsel('distinct module','spip_tradlang_modules', "dir_module='' OR dir_module=module");
		$modules_todo = array_column($modules_todo, 'module');
		if ($n = count($modules_todo)) {
			throw new Exception("Encore $n modules en base sans dir_module : \n" . implode(',', $modules_todo)."\n\nEssayez avec un fichier traductions complementaire");
		}


		// et ca doit finir nicely
		try {
			salvatore_verifier_base_upgradee();
		}
		catch (Exception $e) {
			throw new Exception("Upgrade incomplet : \n" . $e->getMessage()."\n\nEssayez avec un fichier traductions complementaire");
		}
		$output->writeln("<info>Upgrade Complet</info>");
	}
}

