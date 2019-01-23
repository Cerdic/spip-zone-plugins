<?php

namespace Spip\Indexer\Sources;

defined('_INDEXER_PARTS_NUMBER') || define('_INDEXER_PARTS_NUMBER', 1000);

use \Indexer\Indexer;
use Indexer\Sources\SourceInterface;
use \Indexer\Sources\Sources;

class SpipSourcesIndexer {

    /** @var \Indexer\Indexer */
    private $indexer = null;

    /** @var \Indexer\Sources\Sources */
    private $sources = null;

    /** @var string clé de config */
    private $meta_stats = 'indexer/indexing/stats';

    /**
     *
     *
     * @param \Indexer\Indexer $indexer
     * @param \Indexer\Sources\Sources $sources
    **/
    public function __construct(Indexer $indexer, Sources $sources) {
        $this->indexer = $indexer;
        $this->sources = $sources;
    }

    public function isTimeout() {
        static $timeout = null;

        if (is_null($timeout)) {
            include_spip('base/upgrade');
            
            if (defined('_TIME_OUT')) {
                $timeout = _TIME_OUT;
            } else {
                $timeout = time() + _UPGRADE_TIME_OUT;
            }
        }

        return time() >= $timeout;
    }

    public function initTimeout() {
        $this->isTimeout(); // le premier lancement initialise les temps
    }


    public function loadIndexesStats() {
        include_spip('inc/config');
        
        $stats = lire_config($this->meta_stats, array());
        if (!is_array($stats)) {
            $stats = array();
        }
        
        return $stats + array(
            'last' => array(
                'sourceClass' => '',
                'source'      => 0,
                'part'        => array(),
                'documents'   => 0,
                'time' => array(
                    'documents'   => 0,
                    'indexing'    => 0,
                ),
            ),
            'sources' => array(),
        );
    }

    public function loadIndexesStatsClean() {
        $stats = $this->loadIndexesStats();
        $stats['last']['documents'] = 0;
        $stats['last']['time'] = array(
            'documents'   => 0,
            'indexing'    => 0,
        );
        
        return $stats;
    }

    public function saveIndexesStats($stats) {
        include_spip('inc/config');
        ecrire_config($this->meta_stats, $stats);
    }

    public function resetIndexesStats() {
        include_spip('inc/config');
        effacer_config($this->meta_stats);
    }


    /**
     * Purge toutes les sources de ce site
     */
    public function purgeData() {
        $sources = $this->sources->getIterator();
        while ($sources->valid()) {
            $skey    = $sources->key();
            $source  = $sources->current();
echo  "$skey, $source\n";
            $sources->next();

        }
    }


    /**
     * Indexe toutes les sources en prenant en compte le timeout
     */
    public function indexAll() {
        $this->initTimeout();
        ecrire_meta('indexer_derniere_reindexation', time());

        $stats = $this->loadIndexesStatsClean();
        // pas de reset, car ca met le brin en cas de processus concourants
        // $this->resetIndexesStats();

        $sources = $this->sources->getIterator();
        // se replacer à la dernière source renseignée (cas d'une indexation non terminée)
        // $sources->seek($stats['last']['source']) n'est pas bon visiblement, on le fait old school
        if ($stats['last']['source']) {
            while ($sources->valid()) {
                $skey    = $sources->key();
                if ($skey == $stats['last']['source']){
                    break;
                }
                $sources->next();
            }
        }

        while ($sources->valid()) {
            $skey    = $sources->key();
            $source  = $sources->current();

            $stats['last']['source'] = $skey;
            $stats['last']['sourceClass'] = (string)$source;

            if (!isset($stats['sources'][$skey])) {
                $stats['sources'][$skey] = array(
                    'sourceClass' => (string)$source,
                    'documents' => 0,
                    'time' => array(
                        'documents' => 0,
                        'indexing' => 0,
                        'total' => 0
                    )
                );
            }

            if ($this->isTimeout()) {
                break;
            }

            if (!$this->indexSource($source, $skey, $stats)) {
                // timeout, on reste sur cette source, qui n’a pas fini d’indexer
                break;
            }

            $sources->next();
        }

        // si ce n’est pas le dernier élément, sauver l’état pour continuer l’indexation au hit suivant.
        if ($sources->valid() and $this->isTimeout()) {
            $this->saveIndexesStats($stats);
            return false;
        }

        $this->resetIndexesStats();
        
        return $stats;
    }


	/**
	 * Indexe une source, en le faisant par petits morceaux
	 *
	 * @param SourceInterface $source
	 * @param string $skey Nom / objet de la source
	 * @param array $stats Statistiques d’avancement de l’indexation
	 * @return bool
	 *     - true si l’indexation est finie
	 *     - false si timeout et indexation non finie.
	 */
    private function indexSource($source, $skey, &$stats) {
        echo "<h2>Analyse de $source :</h2>\n";
        spip_timer('source');

        // on découpe les documents de cette sources en parts d'un certain nombre
        // afin d'éviter un timeout et une surcharge mémoire
        $parts = new \ArrayIterator($source->getParts(_INDEXER_PARTS_NUMBER));

        // on se replace à la dernière part renseignée (cas d'une indexation non terminée)
        if (isset($stats['last']['part'][$skey]) && $stats['last']['part'][$skey] > 0) {
            $parts->seek($stats['last']['part'][$skey]);
        }

        while ($parts->valid()) {
            $part = $parts->current();
            $stats['last']['part'][$skey] = $parts->key();

            // on regarde s'il reste du temps AVANT d'indexer les 1000 suivants
            if ($this->isTimeout()) {
                $t = spip_timer('source', true);
                $stats['sources'][$skey]['time']['total'] += $t;
                return false;
            }

            $this->indexSourcePart($source, $skey, $part, $stats);
            $parts->next();
        }

        echo "<p class='success'><strong>Temps pour $source :</strong><br />";
        $t = spip_timer('source', true);
        $stats['sources'][$skey]['time']['total'] += $t;
        echo $this->getNiceTime( $stats['sources'][$skey]['time']['total'] );
        echo "</p><hr />";

        // arrivé là on a réussi à indexer toutes les parts sans timeout.
        return true;
    }

    private function indexSourcePart($source, $skey, $part, &$stats) {
        spip_timer('documents');
        $documents = $source->getDocuments($part['start'], $part['end']);
        $t = spip_timer('documents', true);
        $nb = count($documents);

        $stats['last']['documents'] += $nb;
        $stats['last']['time']['documents'] += $t;

        $stats['sources'][$skey]['documents'] += $nb;
        $stats['sources'][$skey]['time']['documents'] += $t;

        if ($nb) {
            echo "<p><strong>Temps pour indexer $nb $skey (ids $part[start] à $part[end])</strong>\n";
            echo "<br />Documents: " . $this->getNiceTime($t) . "\n";

            spip_timer('indexing');
            $ret = $this->indexer->replaceDocuments($documents);
            $t = spip_timer('indexing', true);

            if (!$ret) {
                fwrite(STDERR, "<h4>Erreur à l’enregistrement des documents.</h4>\n");
                exit(1);
            }
            
            $stats['last']['time']['indexing'] += $t;
            $stats['sources'][$skey]['time']['indexing'] += $t;
            echo "<br />Enregistrement dans l'index: " . $this->getNiceTime($t) . "\n";
            echo "</p>";
        }
    }

    /** Retourne un temps formaté pour une belle lecture */
    public function getNiceTime($p) {
        if ($p < 1000)
            $s = '';
        else {
            $s = sprintf("%d ", $x = floor($p/1000));
            $p -= ($x*1000);
        }
        
        return $s . sprintf($s?"%07.3f ms":"%.3f ms", $p);
    }
}
