<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Classe SimplePPTX v0.1 pour SPIP.
// Tres inspire de Zend Search Lucene (http://www.chisimba.com/apidocs/fwdocs/Zend_Search_Lucene/Document/Zend_Search_Lucene_Document_Pptx.html) et de la classe SimpleXLSX de Sergey Schuchkin 
 
class SimplePPTX {
	// scheme
	const SCHEMA_OFFICEDOCUMENT  =  'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument';
	const SCHEMA_PRESENTATIONML = 'http://schemas.openxmlformats.org/presentationml/2006/main';
	const SCHEMA_DRAWINGML = 'http://schemas.openxmlformats.org/drawingml/2006/main';
	const SCHEMA_SLIDERELATION = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide';
	const SCHEMA_SLIDENOTESRELATION = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/notesSlide';

    function __construct($fileName, $storeContent){
        // Document data holders
        $slides = array();
        $slideNotes = array();
        $documentBody = array();
        $coreProperties = array();		
		
		// Open OpenXML package
        $package = new ZipArchive();
        $package->open($fileName);

		 // Read relations and search for officeDocument
        $relationsXml = $package->getFromName('_rels/.rels');
		$relations = simplexml_load_string($relationsXml);
        foreach ($relations->Relationship as $rel) {
			if ($rel["Type"] == SimplePPTX::SCHEMA_OFFICEDOCUMENT) {
			// Found office document! Search for slides...
                $slideRelations = simplexml_load_string($package->getFromName( $this->absoluteZipPath(dirname($rel["Target"]) . "/_rels/" . basename($rel["Target"]) . ".rels")) );
				foreach ($slideRelations->Relationship as $slideRel) {
					 if ($slideRel["Type"] == SimplePPTX::SCHEMA_SLIDERELATION) {
					    // Found slide!
                        $slides[ str_replace( 'rId', '', (string)$slideRel["Id"] ) ] = simplexml_load_string(
                            $package->getFromName( $this->absoluteZipPath(dirname($rel["Target"]) . "/" . dirname($slideRel["Target"]) . "/" . basename($slideRel["Target"])) )
                        );
                        // Search for slide notes
                        $slideNotesRelations = simplexml_load_string($package->getFromName( $this->absoluteZipPath(dirname($rel["Target"]) . "/" . dirname($slideRel["Target"]) . "/_rels/" . basename($slideRel["Target"]) . ".rels")) );						
                        foreach ($slideNotesRelations->Relationship as $slideNoteRel) {
                            if ($slideNoteRel["Type"] == SimplePPTX::SCHEMA_SLIDENOTESRELATION) {
                                // Found slide notes!
                                $slideNotes[ str_replace( 'rId', '', (string)$slideRel["Id"] ) ] = simplexml_load_string(
                                    $package->getFromName( $this->absoluteZipPath(dirname($rel["Target"]) . "/" . dirname($slideRel["Target"]) . "/" . dirname($slideNoteRel["Target"]) . "/" . basename($slideNoteRel["Target"])) )
                                );

                                break;
                            }
                        }
						
					 }
				}				
			 break;
			}
		}
		
		// Sort slides
        ksort($slides);
        ksort($slideNotes);
		
        // Extract contents from slides
        foreach ($slides as $slideKey => $slide) {
            // Register namespaces
            $slide->registerXPathNamespace("p", SimplePPTX::SCHEMA_PRESENTATIONML);
            $slide->registerXPathNamespace("a", SimplePPTX::SCHEMA_DRAWINGML);

            // Fetch all text
            $textElements = $slide->xpath('//a:t');
            foreach ($textElements as $textElement) {
                $documentBody[] = (string)$textElement;
            }

            // Extract contents from slide notes
            if (isset($slideNotes[$slideKey])) {
                // Fetch slide note
                $slideNote = $slideNotes[$slideKey];

                // Register namespaces
                $slideNote->registerXPathNamespace("p", SimplePPTX::SCHEMA_PRESENTATIONML);
                $slideNote->registerXPathNamespace("a", SimplePPTX::SCHEMA_DRAWINGML);

                // Fetch all text
                $textElements = $slideNote->xpath('//a:t');
                foreach ($textElements as $textElement) {
                    $documentBody[] = (string)$textElement;
                }
            }
        }
		
        // Close file
        $package->close();
        // Store filename
        $this->nom_fichier = $fileName;

            // Store contents
        if ($storeContent) {
		   $this->contenu = implode(' ', $documentBody);
        } 

		
	}
	
	function absoluteZipPath($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode('/', $absolutes);
    }
}
?>