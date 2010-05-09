TCPDF - README
============================================================

Name:
	TCPDF

Version:
	2.1.000
	
Release date:
	2008-01-09

Author:
	Nicola Asuni 
	
Copyright (c) 2001-2008:
	Nicola Asuni
	Tecnick.com s.r.l.
	Via Della Pace, 11
	09044 Quartucciu (CA)
	ITALY
	www.tecnick.com
	
URLs:
	http://tcpdf.sourceforge.net/
	http://tcpdf.sf.net/

Description:
	TCPDF is a PHP5 class for generating PDF files on-the-fly without requiring external extensions.
	TCPDF has been originally derived from the Public Domain FPDF class by Olivier Plathey (http://www.fpdf.org).
	
	Main Features:
	
	 - supports all ISO page formats;
	 - supports UTF-8 Unicode;
	 - supports Right-To-left (RTL) languages;
	 - includes methods to publish some xhtml code, supporting the following elements: h1, h2, h3, h4, h5, h6, b, u, i, a, img, p, br, strong, em, font, blockquote, li, ul, ol, hr, td, th, tr, table, sup, sub, small;
	 - includes a method to print various barcode formats using an improved version of "Generic Barcode Render Class" by Karim Mribti (http://www.mribti.com/barcode/) (require GD library: http://www.boutell.com/gd/)
	 - supports TrueTypeUnicode, TrueType, Type1 and encoding; 
	 - supports custom page formats, margins and units of measure;
	 - includes methods for page header and footer management;
	 - supports automatic page break;
	 - supports automatic page numbering;
	 - supports automatic line break and text justification;
	 - supports JPEG, PNG and GIF images;
	 - supports colors;
	 - supports links;
	 - support page compression (require zlib extension: http://www.gzip.org/zlib/);
	 - the source code is full documented in PhpDocumentor Style (http://www.phpdoc.org).

Installation:
	1. copy the folder on your Web server
	2. set your installation path on the config/tcpdf_config.php
	3. call the test_unicode.php page with your browser to see an example
	NOTE: the test_old.php require proper font setup on tcpdf_config.

Source Code Documentation:
	doc/index.html

License
	GNU LESSER GENERAL PUBLIC LICENSE v.2.1
	http://www.gnu.org/copyleft/lesser.html
============================================================