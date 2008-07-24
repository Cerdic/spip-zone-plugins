<?php

/*
 *  Plugin Bouquinerie pour SPIP
 *  Copyright (C) 2008  Polez Kévin
 *
 *    This program is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    This program is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* la fonction ods_xml_load permet de parser un fichier xml open-office
 * elle reçoit en paramètre le nom du fichier, le booléen $strict, le booléen $clean
 * la taille maximum et $datas ?
 *
 * Si tout ce passe bien elle renvoi une structure de type :
 * ['sheets'][num_feuille]['rows'][num_row]['cells'][num_cell]['value'] => le contenu de la cellule
 * ['sheets'][num_feuille]['rows'][num_row]['cells'][num_cell]['value-type'] => le type de contenu (string, float)
 * ['sheets'][num_feuille]['rows'][num_row]['cells'][num_cell] => une cellule
 * ['sheets'][num_feuille]['rows'][num_row]['style-name'] => le nom du style de la colone
 * ['sheets'][num_feuille]['name'] => nom de la feuille
 * ['sheets'][num_feuille]['style-name'] => nom du style de la feuille
 * ['sheets'][num_feuille]['print'] => parametre print de la feuille
 * ['head'] => les attributs d'en-tête du document
 *
 * Exemple :
 * ['sheets'][0]['rows'][4]['cells'][5]
 *       permet d'acceder à la cellule de la ligne 5 colonne 4 de la Feuille 0
 *
 * ['sheets'][0]['name']
 *       renvoi le nom de la feuille 0
 *
 * Si la cellule est vide, son value-type sera egale à la chaine "null"
 */

$ods = array();
$i_sheets = 0;
$i_rows = 0;
$i_cells = 0;
$value = "";

// lance le parse du fichier
function ods_xml_load($fichier,$strict = true, $clean = true, $taille_max = 1048576, $datas='') {
	$contenu = "";
	if (preg_match(",^(http|ftp)://,",$fichier)){
		include_spip('inc/distant');
		$contenu = recuperer_page($fichier,false,false,$taille_max, $datas);
	}
	else lire_fichier ($fichier, $contenu);

	$arbre = array();

	if ($contenu) $arbre = ods_xml_parse($contenu, $strict, $clean);
		
	return count($arbre)?$arbre:false;
}

function ods_array_xml($array) {

	$string = '<?xml version="1.0" encoding="UTF-8"?><office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" office:version="1.0">';

	// ToDo: scripts
	$string .= '<office:scripts/>';
		
	// ToDo: Fonts
	$string .= '<office:font-face-decls>';
	$string .= '</office:font-face-decls>';
		
	// ToDo: Styles
	$string .= '<office:automatic-styles>';
	$string .= '</office:automatic-styles>';
		
	// Body
	$string .= '<office:body>';
	$string .= '<office:spreadsheet>';

	foreach ($array['sheets'] as $sheet) {
		$string .= '<table:table table:name="' . $sheet['name'] . '" table:print="'.$sheet['print'].'">';
		foreach ($sheet['rows'] as $row) {
			$string .= '<table:table-row>';
			foreach($row['cells'] as $cell) {
				$string .= '<table:table-cell ';

				if ($cell['type'] != 'null') {
					$attrs = '';
					$attrs .= 'office:value-type ="' . $cell['type'] . '" ';
					//$attrs .= 'office:value ="' . $cell['value'] . '"';
					$string .= $attrs . '>';
					$string .= '<text:p>'.$cell['value'].'</text:p>';
					$string .= '</table:table-cell>';
				}
				else {
					$string .= '/>';
				}
			}
			$string .= '</table:table-row>';
		}
		$string .= '</table:table>';
	}

	$string .= '</office:spreadsheet>';
	$string .= '</office:body>';

	$string .= '</office:document-content>';

	//include_spip ('inc/filtres');
//	echo entites_html($string);
	return $string;
}

function ods_get_meta($lang) {
	$myDate = date('Y-m-j\TH:i:s');
	$meta = '<?xml version="1.0" encoding="UTF-8"?>
	<office:document-meta xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0">
		<office:meta>
			<meta:generator>ods-php</meta:generator>
			<meta:creation-date>'.$myDate.'</meta:creation-date>
			<dc:date>'.$myDate.'</dc:date>
			<dc:language>'.$lang.'</dc:language>
			<meta:editing-cycles>2</meta:editing-cycles>
			<meta:editing-duration>PT15S</meta:editing-duration>
			<meta:user-defined meta:name="Info 1"/>
			<meta:user-defined meta:name="Info 2"/>
			<meta:user-defined meta:name="Info 3"/>
			<meta:user-defined meta:name="Info 4"/>
		</office:meta>
	</office:document-meta>';
	return $meta;
}

function ods_get_style() {
		return '<?xml version="1.0" encoding="UTF-8"?>
			<office:document-styles xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:dom="http://www.w3.org/2001/xml-events" office:version="1.0"><office:font-face-decls><style:font-face style:name="Liberation Sans" svg:font-family="&apos;Liberation Sans&apos;" style:font-family-generic="swiss" style:font-pitch="variable"/><style:font-face style:name="DejaVu Sans" svg:font-family="&apos;DejaVu Sans&apos;" style:font-family-generic="system" style:font-pitch="variable"/></office:font-face-decls><office:styles><style:default-style style:family="table-cell"><style:table-cell-properties style:decimal-places="2"/><style:paragraph-properties style:tab-stop-distance="1.25cm"/><style:text-properties style:font-name="Liberation Sans" fo:language="es" fo:country="ES" style:font-name-asian="DejaVu Sans" style:language-asian="zxx" style:country-asian="none" style:font-name-complex="DejaVu Sans" style:language-complex="zxx" style:country-complex="none"/></style:default-style><number:number-style style:name="N0"><number:number number:min-integer-digits="1"/>
			</number:number-style><number:currency-style style:name="N103P0" style:volatile="true"><number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true"/><number:text> </number:text><number:currency-symbol number:language="es" number:country="ES">€</number:currency-symbol></number:currency-style><number:currency-style style:name="N103"><style:text-properties fo:color="#ff0000"/><number:text>-</number:text><number:number number:decimal-places="2" number:min-integer-digits="1" number:grouping="true"/><number:text> </number:text><number:currency-symbol number:language="es" number:country="ES">€</number:currency-symbol><style:map style:condition="value()&gt;=0" style:apply-style-name="N103P0"/></number:currency-style><style:style style:name="Default" style:family="table-cell"/><style:style style:name="Result" style:family="table-cell" style:parent-style-name="Default"><style:text-properties fo:font-style="italic" style:text-underline-style="solid" style:text-underline-width="auto" style:text-underline-color="font-color" fo:font-weight="bold"/></style:style><style:style style:name="Result2" style:family="table-cell" style:parent-style-name="Result" style:data-style-name="N103"/><style:style style:name="Heading" style:family="table-cell" style:parent-style-name="Default"><style:table-cell-properties style:text-align-source="fix" style:repeat-content="false"/><style:paragraph-properties fo:text-align="center"/><style:text-properties fo:font-size="16pt" fo:font-style="italic" fo:font-weight="bold"/></style:style><style:style style:name="Heading1" style:family="table-cell" style:parent-style-name="Heading"><style:table-cell-properties style:rotation-angle="90"/></style:style></office:styles><office:automatic-styles><style:page-layout style:name="pm1"><style:page-layout-properties style:writing-mode="lr-tb"/><style:header-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm"/></style:header-style><style:footer-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm"/>
			</style:footer-style></style:page-layout><style:page-layout style:name="pm2"><style:page-layout-properties style:writing-mode="lr-tb"/><style:header-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-bottom="0.25cm" fo:border="0.088cm solid #000000" fo:padding="0.018cm" fo:background-color="#c0c0c0"><style:background-image/></style:header-footer-properties></style:header-style><style:footer-style><style:header-footer-properties fo:min-height="0.751cm" fo:margin-left="0cm" fo:margin-right="0cm" fo:margin-top="0.25cm" fo:border="0.088cm solid #000000" fo:padding="0.018cm" fo:background-color="#c0c0c0"><style:background-image/></style:header-footer-properties></style:footer-style></style:page-layout></office:automatic-styles><office:master-styles><style:master-page style:name="Default" style:page-layout-name="pm1"><style:header><text:p><text:sheet-name>???</text:sheet-name></text:p></style:header><style:header-left style:display="false"/><style:footer><text:p>Página <text:page-number>1</text:page-number></text:p></style:footer><style:footer-left style:display="false"/></style:master-page><style:master-page style:name="Report" style:page-layout-name="pm2"><style:header><style:region-left><text:p><text:sheet-name>???</text:sheet-name> (<text:title>???</text:title>)</text:p></style:region-left><style:region-right><text:p><text:date style:data-style-name="N2" text:date-value="2008-02-18">18/02/2008</text:date>, <text:time>00:17:06</text:time></text:p></style:region-right></style:header><style:header-left style:display="false"/><style:footer><text:p>Página <text:page-number>1</text:page-number> / <text:page-count>99</text:page-count></text:p></style:footer><style:footer-left style:display="false"/></style:master-page></office:master-styles></office:document-styles>';
}

function ods_get_settings() {
	return '<?xml version="1.0" encoding="UTF-8"?>
		<office:document-settings xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:ooo="http://openoffice.org/2004/office" office:version="1.0"><office:settings><config:config-item-set config:name="ooo:view-settings"><config:config-item config:name="VisibleAreaTop" config:type="int">0</config:config-item><config:config-item config:name="VisibleAreaLeft" config:type="int">0</config:config-item><config:config-item config:name="VisibleAreaWidth" config:type="int">2258</config:config-item><config:config-item config:name="VisibleAreaHeight" config:type="int">903</config:config-item><config:config-item-map-indexed config:name="Views"><config:config-item-map-entry><config:config-item config:name="ViewId" config:type="string">View1</config:config-item><config:config-item-map-named config:name="Tables"><config:config-item-map-entry config:name="Hoja1"><config:config-item config:name="CursorPositionX" config:type="int">0</config:config-item><config:config-item config:name="CursorPositionY" config:type="int">1</config:config-item><config:config-item config:name="HorizontalSplitMode" config:type="short">0</config:config-item><config:config-item config:name="VerticalSplitMode" config:type="short">0</config:config-item><config:config-item config:name="HorizontalSplitPosition" config:type="int">0</config:config-item><config:config-item config:name="VerticalSplitPosition" config:type="int">0</config:config-item><config:config-item config:name="ActiveSplitRange" config:type="short">2</config:config-item><config:config-item config:name="PositionLeft" config:type="int">0</config:config-item><config:config-item config:name="PositionRight" config:type="int">0</config:config-item><config:config-item config:name="PositionTop" config:type="int">0</config:config-item><config:config-item config:name="PositionBottom" config:type="int">0</config:config-item></config:config-item-map-entry></config:config-item-map-named><config:config-item config:name="ActiveTable" config:type="string">Hoja1</config:config-item><config:config-item config:name="HorizontalScrollbarWidth" config:type="int">270</config:config-item><config:config-item config:name="ZoomType" config:type="short">0</config:config-item><config:config-item config:name="ZoomValue" config:type="int">100</config:config-item><config:config-item config:name="PageViewZoomValue" config:type="int">60</config:config-item><config:config-item config:name="ShowPageBreakPreview" config:type="boolean">false</config:config-item><config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item><config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item><config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item><config:config-item config:name="GridColor" config:type="long">12632256</config:config-item><config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item><config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item><config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item><config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item><config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item><config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item><config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item><config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item><config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item>
		<config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item><config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item></config:config-item-map-entry></config:config-item-map-indexed></config:config-item-set><config:config-item-set config:name="ooo:configuration-settings"><config:config-item config:name="ShowZeroValues" config:type="boolean">true</config:config-item><config:config-item config:name="ShowNotes" config:type="boolean">true</config:config-item><config:config-item config:name="ShowGrid" config:type="boolean">true</config:config-item><config:config-item config:name="GridColor" config:type="long">12632256</config:config-item><config:config-item config:name="ShowPageBreaks" config:type="boolean">true</config:config-item><config:config-item config:name="LinkUpdateMode" config:type="short">3</config:config-item><config:config-item config:name="HasColumnRowHeaders" config:type="boolean">true</config:config-item><config:config-item config:name="HasSheetTabs" config:type="boolean">true</config:config-item><config:config-item config:name="IsOutlineSymbolsSet" config:type="boolean">true</config:config-item><config:config-item config:name="IsSnapToRaster" config:type="boolean">false</config:config-item><config:config-item config:name="RasterIsVisible" config:type="boolean">false</config:config-item><config:config-item config:name="RasterResolutionX" config:type="int">1000</config:config-item><config:config-item config:name="RasterResolutionY" config:type="int">1000</config:config-item><config:config-item config:name="RasterSubdivisionX" config:type="int">1</config:config-item><config:config-item config:name="RasterSubdivisionY" config:type="int">1</config:config-item><config:config-item config:name="IsRasterAxisSynchronized" config:type="boolean">true</config:config-item><config:config-item config:name="AutoCalculate" config:type="boolean">true</config:config-item><config:config-item config:name="PrinterName" config:type="string">Generic Printer</config:config-item><config:config-item config:name="PrinterSetup" config:type="base64Binary">WAH+/0dlbmVyaWMgUHJpbnRlcgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU0dFTlBSVAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAWAAMAngAAAAAAAAAFAFZUAAAkbQAASm9iRGF0YSAxCnByaW50ZXI9R2VuZXJpYyBQcmludGVyCm9yaWVudGF0aW9uPVBvcnRyYWl0CmNvcGllcz0xCm1hcmdpbmRhanVzdG1lbnQ9MCwwLDAsMApjb2xvcmRlcHRoPTI0CnBzbGV2ZWw9MApjb2xvcmRldmljZT0wClBQRENvbnRleERhdGEKUGFnZVNpemU6TGV0dGVyAAA=</config:config-item><config:config-item config:name="ApplyUserData" config:type="boolean">true</config:config-item><config:config-item config:name="CharacterCompressionType" config:type="short">0</config:config-item><config:config-item config:name="IsKernAsianPunctuation" config:type="boolean">false</config:config-item><config:config-item config:name="SaveVersionOnClose" config:type="boolean">false</config:config-item><config:config-item config:name="UpdateFromTemplate" config:type="boolean">false</config:config-item><config:config-item config:name="AllowPrintJobCancel" config:type="boolean">true</config:config-item><config:config-item config:name="LoadReadonly" config:type="boolean">false</config:config-item></config:config-item-set></office:settings></office:document-settings>';
}

function ods_get_manifest() {
	return '<?xml version="1.0" encoding="UTF-8"?>
<manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
 <manifest:file-entry manifest:media-type="application/vnd.oasis.opendocument.spreadsheet" manifest:full-path="/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/statusbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/accelerator/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/floater/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/popupmenu/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/progressbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/menubar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/toolbar/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/Bitmaps/"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Configurations2/images/"/>
 <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>
 <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>
 <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>
</manifest:manifest>';
}

function ods_debut_element($parser,$name,$attrs) {

	global $ods;
	global $i_sheets;
	global $i_rows;
	global $i_cells;
	global $value;

	switch ($name) {
		case 'OFFICE:DOCUMENT-CONTENT' :
			$ods['head'] = $attrs;
			$ods['sheets'] = array();
			$i_sheets = 0;
			break;
		case 'TABLE:TABLE' : // on entamme une nouvelle feuille
			$ods['sheets'][$i_sheets]['name'] = $attrs['TABLE:NAME'];
			$ods['sheets'][$i_sheets]['style-name'] = $attrs['TABLE:STYLE-NAME'];
			$ods['sheets'][$i_sheets]['print'] = $attrs['TABLE:PRINT'];
			$ods['sheets'][$i_sheets]['rows'] = array();
			$i_rows = 0;
			break;

		case 'TABLE:TABLE-ROW' : // on entamme une nouvelle ligne
			$ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'] = array();
			$i_cells = 0;
			break;
		case 'TABLE:TABLE-CELL' : // une nouvelle cellule
			$ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['repeted'] = $attrs['TABLE:NUMBER-COLUMNS-REPEATED'];
			$ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['type'] = $attrs['OFFICE:VALUE-TYPE'];
			$ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['value'] = $attrs['OFFICE:VALUE'];
			break;
	}
}

// le parseur coupe les éléments en plusieurs partie.
// les caractères "'", et accents semble provoquer cette coupure
// donc, tant que l'on ne change pas de cellule, il faut réunir
// ces éléments sinon on se retrouve avec une chaine tronquée
function ods_element($parser, $data) {
	global $value;
	$value .= $data;
}

function ods_fin_element($parser, $name) {

	global $ods;
	global $i_sheets;
	global $i_rows;
	global $i_cells;
	global $value;

	switch ($name) {
		case 'OFFICE:DOCUMENT-CONTENT' :
			break;
		case 'TABLE:TABLE' : // on termine une feuille
			$i_sheets++;
			break;
		case 'TABLE:TABLE-ROW' : // on termine une ligne
			$i_rows++;
			break;
		case 'TABLE:TABLE-CELL' : // on termine une cellule
			$type = $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['type'];
			if (!$type) $type = "null";
			if ($value) $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['value'] = $value;
			$value = $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['value'];
			$repeted = $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['repeted'];

			if (!intval($repeted)) $repeted = 1;
			while ($repeted > 0) {
				if ($value) $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['value'] = $value;
				if ($type) $ods['sheets'][$i_sheets]['rows'][$i_rows]['cells'][$i_cells]['type'] = $type;
				$repeted--;$i_cells++;			
			}
			$value = "";
			break;
	}
}



// transforme le fichier xml en structure de type array
function ods_xml_parse($texte, $strict=true, $clean=true){
	global $ods;

	if ($clean){ 	// enleve les commentaires
		$charset = 'AUTO';
	  	if (preg_match(",<\?xml\s(.*?)encoding=['\"]?(.*?)['\"]?(\s(.*))?\?>,im",$texte,$regs))
			$charset = $regs[2];
		$texte = preg_replace(',<!--(.*?)-->,is','',$texte);
		$texte = preg_replace(',<\?(.*?)\?>,is','',$texte);
		include_spip('inc/charsets');
		$texte = importer_charset($texte,$charset);
	}

	$xml_parse = xml_parser_create("UTF-8");
	xml_set_element_handler($xml_parse,"ods_debut_element","ods_fin_element");
	xml_set_character_data_handler($xml_parse,"ods_element");

	if (!xml_parse($xml_parse,$texte,false)) {
			die(sprintf("erreur XML : %s à la ligne %d",
        		xml_error_string(xml_get_error_code($xml_parser)),
        		xml_get_current_line_number($xml_parser)));
	}
	xml_parser_free($xml_parse);
	return $ods;
}

?>
