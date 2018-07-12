<?php

/***

Exporter la table spip_articles en format txt

Lancer la commande spip-cli : spip export -d `repertoire destination`

Les fichiers txts sont placés dans le repertoire `repertoire destination` sur le disque dur.

Si un repertoire git est trouvé dans /dest alors on prend le repertoire. todo

Voir aussi fichiersImporter.

*/

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class ConvertisseurExporter extends Command {
	protected function configure() {
		$this
			->setName('convertisseur:exporter')
			->setDescription('Exporter la table spip_articles (ou autre) au format SPIP txt.')
			->setAliases(array(
				'export'
			))
			->addOption(
				'source',
				's',
				InputOption::VALUE_OPTIONAL,
				'Table à exporter',
				'spip_articles'
			)
			->addOption(
				'dest',
				'd',
				InputOption::VALUE_OPTIONAL,
				'Répertoire où exporter au format texte',
				'spip_articles'
			)
			->addOption(
				'branche',
				'b',
				InputOption::VALUE_OPTIONAL,
				'branche à exporter (id_secteur ou id_rubrique)',
				'0'
			)
			->addOption(
				'statuts',
				't',
				InputOption::VALUE_OPTIONAL,
				'statuts des articles a exporter (séparé par une virgule)',
				'prop,prepa,publie'
			)
			->addOption(
				'modif',
				'm',
				InputOption::VALUE_OPTIONAL,
				'date_modif après laquelle exporter',
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
		$dest = $input->getOption('dest') ;
		$branche = $input->getOption('branche') ;
		$date_modif = $input->getOption('modif') ;
		$statuts = explode(',', $input->getOption('statuts'));
		foreach($statuts as $s)
			$statuts_exportes[]= _q($s);
		
		// Secteur ou rubrique à exporter.
		if(!$branche OR !intval($branche)){
			$output->writeln("<error>Préciser l'id du secteur ou de la rubrique à exporter. spip export -b 123 </error>");
			exit();
		}
		
		// demande t'on un secteur ou une rubrique ?
		$parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=$branche");
		
		if($parent == 0)
			$critere_export = "where id_secteur=" . intval($branche) ;
		else{
			// y'a t'il des sous rubriques ?
			$sous_rubriques = sql_allfetsel("id_rubrique", "spip_rubriques", "id_parent=$branche");
			if($sous_rubriques AND sizeof($sous_rubriques) > 0){
				foreach($sous_rubriques as $k => $v)
					$ex[] = _q($v['id_rubrique']) ;
				$critere_export = "where id_rubrique in (" . implode(",", $ex) . ")" ;
			}
			else
				$critere_export = "where id_rubrique=" . intval($branche) ;
		}
		
		if($date_modif)
			$critere_date_modif = "and date_modif > '$date_modif'" ;
		
		$critere_statut = "and statut in(". implode(",", $statuts_exportes) .")" ;
		
		// Répertoire dest, ou arrivent les fichiers txt.
		if(!is_dir($dest)){
			$output->writeln("<error>Préciser le répertoire où exporter les fichiers de $source au format txt. spip export -d `repertoire` </error>");
			exit();
		}
		
		if ($spip_loaded) {
			chdir($spip_racine);
			
			if (!function_exists('passthru')){
				$output->writeln("<error>Votre installation de PHP doit pouvoir exécuter des commandes externes avec la fonction passthru().</error>");
			}
			// Si c'est bon on continue
			else{
				// chopper les articles en sql.
				$query = sql_query("select * from spip_articles $critere_export $critere_date_modif $critere_statut order by date_redac asc"); 
				
				if(sql_count($query) > 0){
					// start and displays the progress bar
					$progress = new ProgressBar($output, sql_count($query));
					$progress->setBarWidth(100);
					$progress->setRedrawFrequency(1);
					$progress->setMessage(" Export de `spip_articles` branche $branche en cours dans $dest ... ", 'message');
					$progress->start();
					
					while($f = sql_fetch($query)){
						
						$id_article = $f['id_article'] ;
						$id_rubrique = $f['id_rubrique'] ;
						
						// Exporter les champs spip_articles
						$fichier = "" ;
						$ins_auteurs = array();
						$ins_mc = array();
						$ins_doc = array();
						$progress->setMessage('', 'motscles');
						$progress->setMessage('', 'docs');
						$progress->setMessage('', 'auteurs');
						
						// mettre les champs dans un fichier texte balisé avec des <ins class="champ">.
						foreach($f as $k => $v){
							if($k == "texte" or $v == "" or $v == "0" or $v == "non" or $v == "0000-00-00 00:00:00")
								continue ;
							$fichier .= "<ins class='$k'>" . trim($v) ."</ins>\n" ;
						}
						$fichier .= "\n\n" . $f['texte'] . "\n\n" ;
						
						// Ajouter des métadonnées (hierarchie, auteurs, mots-clés...)
						
						// hierarchie
						$hierarchie = array();
						include_spip("inc/rubriques");
						$ariane = preg_replace("/^0,/","", calcul_hierarchie_in($id_rubrique));
						
						$ariane = sql_allfetsel("titre","spip_rubriques","id_rubrique in($ariane)");
						foreach($ariane as $a)
							$hierarchie[] = str_replace("/","",$a['titre']) ; // on ne veut pas de / car creer_rubrique_nommee pourrait se tromper à l'import.
						
						$hierarchie = implode("@@", $hierarchie);
						
						$rubrique = sql_fetsel("texte,descriptif", "spip_rubriques", "id_rubrique=$id_rubrique");
						
						if($texte_rubrique = $rubrique['texte'])
							$texte_rubrique = "<ins class='texte_rubrique'>$texte_rubrique</ins>\n" ;
						
						if($descriptif_rubrique = $rubrique['descriptif'])
							$descriptif_rubrique = "<ins class='descriptif_rubrique'>$descriptif_rubrique</ins>\n" ;
						
						// auteurs spip 3
						if($spip_version_branche > "3")
							$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_liens al, spip_auteurs a", "al.id_objet=$id_article and al.objet='article' and al.id_auteur=a.id_auteur");
						else // spip 2
							$auteurs = sql_allfetsel("a.nom, a.bio", "spip_auteurs_articles aa, spip_auteurs a", "aa.id_article=$id_article and aa.id_auteur=a.id_auteur");
						
						foreach($auteurs as $a)
							if($a['nom'])
								$ins_auteurs[] = $a ;
						
						$auteurs = "" ;
						foreach($ins_auteurs as $k => $a){
							if($k == 0)
								$sep = "" ;
							else
								$sep = "@@" ;
							$bio = ($a['bio'] != "") ? "::" . $a['bio'] : "" ;
							$auteurs .= $sep . $a['nom'] . $bio ;
						}
						
						$auteurs_m = substr($auteurs, 0, 100) ;
						$progress->setMessage($auteurs_m, 'auteurs');
						
						// mots-clés
						if($spip_version_branche > "3")
							$motscles = sql_allfetsel("*", "spip_mots_liens ml, spip_mots m", "ml.id_objet=$id_article and ml.objet='article' and ml.id_mot=m.id_mot");
						else // spip 2
							$motscles = sql_allfetsel("*", "spip_mots_articles ma, spip_mots m", "ma.id_article=$id_article and ma.id_mot=m.id_mot");
						
						foreach($motscles as $mc){
							if($mc['titre'])
								$ins_mc[] = $mc['type'] . "::" . $mc['titre'] ;
						}
						if(is_array($ins_mc)){
							$motscles = join("@@", $ins_mc) ;
							$motscles_m = substr($motscles, 0, 100) ;
							$progress->setMessage($motscles_m, 'motscles');
						}
						
						// documents joints
						$documents = sql_allfetsel("*", "spip_documents d, spip_documents_liens dl", "dl.id_objet=$id_article and dl.objet='article' and dl.id_document=d.id_document");
						foreach($documents as $doc)
								$ins_doc[] = json_encode($doc) ;
						if(is_array($ins_doc)){
							$documents = join("@@", $ins_doc) ;
							$docs_m = substr($documents, 0, 100) ;
							$progress->setMessage($docs_m, 'docs');
						}
						
						// Ajouter les métadonnées
						if($auteurs)
							$fichier = "<ins class='auteurs'>$auteurs</ins>\n" . $fichier ;
						if($motscles)
							$fichier = "<ins class='mots_cles'>$motscles</ins>\n" . $fichier ;
						if($documents)
							$fichier = "<ins class='documents'>$documents</ins>\n" . $fichier ;
						if($hierarchie){
							$fichier = "<ins class='hierarchie'>$hierarchie</ins>\n" .
							$descriptif_rubrique .
							$texte_rubrique .
							$fichier ;
						}
						
						// Créer un fichier txt
						$date = ($f['date_redac'] != "0000-00-00 00:00:00")? $f['date_redac'] : $f['date'] ;
						preg_match("/^(\d\d\d\d)-(\d\d)/", $date, $m);
						$annee = $m[1] ;
						$mois = $m[2] ;
						
						include_spip("inc/charsets");
						$nom_fichier = translitteration($f['titre']) ;
						$nom_fichier = preg_replace("/[^a-zA-Z0-9]/i", "-", $nom_fichier);
						$nom_fichier = preg_replace("/-{2,}/i", "-", $nom_fichier);
						$nom_fichier = preg_replace("/^-/i", "", $nom_fichier);
						$nom_fichier = preg_replace("/-$/i", "", $nom_fichier);
						
						$nom_fichier = "$dest/$annee/$annee-$mois/$annee-$mois"."_$nom_fichier.txt" ;
						
						// Créer les répertoires
						if(!is_dir("$dest/$annee"))
							mkdir("$dest/$annee");
						if(!is_dir("$dest/$annee/$annee-$mois"))
							mkdir("$dest/$annee/$annee-$mois");	
						
						if(ecrire_fichier("$nom_fichier", $fichier)){
							// Si tout s'est bien passé, on avance la barre
							$nom_fichier_m = substr($nom_fichier, 0, 100) ;
							$progress->setMessage($nom_fichier_m, 'filename');
							$progress->setFormat("<fg=white;bg=blue>%message%</>\n" . '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . "\n %auteurs% %motscles% \n %filename% \n\n");
							$progress->advance();
						
						}
						else{
							$output->writeln("<error>échec de l'export de $nom_fichier</error>");
							exit ;
						}
					}
					
					// ensure that the progress bar is at 100%
					$progress->finish();
					
				}else{
					$output->writeln("<error>Rien à exporter dans la branche $branche depuis $date_modif</error>");
				}
			}
			$output->writeln("\n");
		}
		else{
			$output->writeln('<error>Vous n’êtes pas dans une installation de SPIP. Impossible de convertir le texte.</error>');
		}
	}
}

// compat spip 2
if($spip_version_branche < 3){
	function calcul_hierarchie_in($id, $tout = true) {
		
		static $b = array();
		
		// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
		if (!is_array($id)) {
			$id = explode(',', $id);
		}
		$id = join(',', array_map('intval', $id));
		if (isset($b[$id])) {
			// Notre branche commence par la rubrique de depart si $tout=true
			return $tout ? (strlen($b[$id]) ? $b[$id] . ",$id" : $id) : $b[$id];
		}
		
		$hier = "";
		
		// On ajoute une generation (les filles de la generation precedente)
		// jusqu'a epuisement, en se protegeant des references circulaires
		
		$ids_nouveaux_parents = $id;
		$maxiter = 10000;
		while ($maxiter-- and $parents = sql_allfetsel(
			'id_parent',
			'spip_rubriques',
			sql_in('id_rubrique', $ids_nouveaux_parents) . " AND " . sql_in('id_parent', $hier, 'NOT')
		)) {
			$ids_nouveaux_parents = join(',', array_map('reset', $parents));
			$hier = $ids_nouveaux_parents . (strlen($hier) ? ',' . $hier : '');
		}
		
		# securite pour ne pas plomber la conso memoire sur les sites prolifiques
		
		if (strlen($hier) < 10000) {
			$b[$id] = $hier;
		}
		// Notre branche commence par la rubrique de depart si $tout=true
		$hier = $tout ? (strlen($hier) ? "$hier,$id" : $id) : $hier;
		return $hier;
	}
}