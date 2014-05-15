<?php

namespace Spip\Indexer\Sources;

use \Indexer\Indexer;
use \Indexer\Sources\Sources;

class SpipSourcesIndexer {

    /** @var \Indexer\Indexer */
    private $indexer = null;

    /** @var \Indexer\Sources\Sources */
    private $sources = null;

    /** @var bool Tables de liens présentes (spip 3 ?) */
    private $tables_liens = true;

    /**
     *
     *
     * @param \Indexer\Indexer $indexer
     * @param \Indexer\Sources\Sourcesr $sources
    **/
    public function __construct(Indexer $indexer, Sources $sources) {
        $this->indexer = $indexer;
        $this->sources = $sources;
    }


    public function setTablesLiens($bool) {
        $this->tables_liens = (bool)$bool;
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


    public function loadIndexesInfos() {
        include_spip('inc/config');
        $data = lire_config('indexer/indexing/last', []);
        if (!is_array($data)) $data = [];
        return $data + [
            'source' => 0,
            'start'  => 0,
            'timeout' => 0
        ];
    }

    public function saveIndexesInfos($data) {
        include_spip('inc/config');
        return ecrire_config('indexer/indexing/last', $data);
    }


    /**
     * Indexe toutes les sources en prenant en compte le timeout
     */
    public function indexAll() {

        $this->initTimeout();

        $infos = $this->loadIndexesInfos();

        echo "<h1>Indexer tous les contenus :</h1>\n";
        echo "\n<pre>"; print_r($infos); echo "</pre>\n";


        foreach ($this->sources as $key => $source) {

            $source->setTablesLiens(false); // pour SPIP 2.1
            echo "<h2>Analyse de $source :</h2>\n";
            spip_timer('source');

            foreach ($source->getParts(1000) as $start => $end) {

                $documents = $source->getAllDocuments($start, $end);

                if (count($documents)) {
                    spip_timer('indexage');
                    $this->indexer->replaceDocuments($documents);

                    echo "<br /><strong>Temps pour indexer " . count($documents). "</strong>\n";
                    echo "<br /><i>ids entre $start et $end :</i><br />\n";
                    echo spip_timer('indexage');
                }

                if ($this->isTimeout()) {
                    $this->saveIndexesInfos([
                        'timeout' => true,
                        'source' => $key,
                        'sourceClass' => (string)$source,
                        'sourceTime'  => spip_timer('source'),
                        'start' => $start,
                    ]);
                    return false;
                }
            }



            echo "<hr /><strong>Temps pour $source :</strong><br />";
            echo $t = spip_timer('source');


            if ($this->isTimeout()) {
                $this->saveIndexesInfos([
                    'timeout' => true,
                    'source' => $key,
                    'sourceClass' => (string)$source,
                    'sourceTime'  => $t,
                    'start' => $start,
                ]);
                return false;
            }

        }

        return true;
    }
}
