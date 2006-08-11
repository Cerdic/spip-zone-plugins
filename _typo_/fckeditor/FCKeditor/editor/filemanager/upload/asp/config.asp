﻿<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: config.asp
 * 	Configuration file for the File Manager Connector for ASP.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<%

' SECURITY: You must explicitelly enable this "uploader" (set it to "True"). 
Dim ConfigIsEnabled
ConfigIsEnabled = False

' Path to user files relative to the document root.
Dim ConfigUserFilesPath
ConfigUserFilesPath = "/UserFiles/"

' Allowed and Denied extensions configurations.
Dim ConfigAllowedExtensions, ConfigDeniedExtensions
Set ConfigAllowedExtensions	= CreateObject( "Scripting.Dictionary" )
Set ConfigDeniedExtensions	= CreateObject( "Scripting.Dictionary" )

ConfigAllowedExtensions.Add	"File", ""
ConfigDeniedExtensions.Add	"File", "php|php3|php5|phtml|asp|aspx|ascx|jsp|cfm|cfc|pl|bat|exe|dll|reg|cgi"

ConfigAllowedExtensions.Add	"Image", "jpg|gif|jpeg|png|bmp"
ConfigDeniedExtensions.Add	"Image", ""

ConfigAllowedExtensions.Add	"Flash", "swf|fla"
ConfigDeniedExtensions.Add	"Flash", ""

%>