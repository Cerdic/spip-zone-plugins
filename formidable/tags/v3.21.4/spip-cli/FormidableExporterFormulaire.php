<?php

namespace Spip\Formidable\Command;

use Spip\Cli\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FormidableExporterFormulaire extends Command {

	protected $formats = ['csv', 'xls'];

	protected function configure() {
		$this
			->setName('formidable:exporter:formulaire')
			->setDescription('Exporter un formulaire Formidable.')
			->addArgument(
				'identifiant',
				InputArgument::REQUIRED,
				'Numéro ou identifiant du formulaire'
			)
			->addOption(
				'format',
				'f',
				InputOption::VALUE_OPTIONAL,
				'Format d’export (' . implode(', ', $this->formats) . ')',
				'csv'
			)
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		/** @var Spip $spip */
		$this->demarrerSpip();

		$identifiant = $input->getArgument('identifiant');
		if (!$id_formulaire = $this->identifierFormulaireFormidable($identifiant)) {
			$this->io->error("Identifiant $identifiant introuvable");
			return;
		}

		$format = $input->getOption('format');
		if (!in_array($format, $this->formats)) {
			$this->io->error("Format d’export $format inconnu");
			return;
		}

		$this->io->text("Exporter le formulaire n°<info>$id_formulaire</info> en <info>$format</info>");
		$fichier = $this->exporterFormulaireFormidable($id_formulaire, $format);
		if ($fichier) {
			$this->io->check("Export réussi");

			$this->io->text(["", "<info>Chemin physique</info>"]);
			$this->io->text(realpath($fichier));

			$this->io->text(["", "<info>Lien de téléchargement</info>"]);
			include_spip('inc/securiser_action');
			$args = "$id_formulaire:" . md5_file($fichier) . ":" . basename($fichier);
			$cle = calculer_cle_action($args);
			$action = generer_url_action('formidable_recuperer_export', "args=$args&cle=$cle", true);
			$this->io->text(url_absolue($action));
		} else {
			$this->io->fail("Echec de l’export");
		}
		$this->io->text("");
	}

	/**
	 * Retourne l’identifiant numérique d’un identifiant (numérique ou texte)
	 * d’un formulaire formidable,
	 *
	 * @param string|int $identifiant
	 * @return int
	 */
	protected function identifierFormulaireFormidable($identifiant) {
		if (is_numeric($identifiant)) {
			$where = 'id_formulaire = ' . intval($identifiant);
		} elseif (is_string($identifiant)) {
			$where = 'identifiant = ' . sql_quote($identifiant);
		} else {
			return 0;
		}

		return intval(sql_getfetsel('id_formulaire', 'spip_formulaires', $where));
	}

	/**
	 * Génère un export de formulaire Formidable
	 * @param int $id_formulaire
	 * @param string $format
	 * @return string|false Chemin du fichier csv, xls ou zip généré
	 */
	protected function exporterFormulaireFormidable($id_formulaire, $format = 'csv') {
		include_spip('inc/formidable');
		include_spip('formulaires/exporter_formulaire_reponses');
		$fichier = false;
		switch ($format) {
			case 'csv':
				$fichier = exporter_formulaires_reponses($id_formulaire, ',');
				break;
			case 'xls':
				$fichier = exporter_formulaires_reponses($id_formulaire, 'TAB');
				break;
		}
		return $fichier;

	}
}
