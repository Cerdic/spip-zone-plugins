<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template match="ServiceComplementaire">
		<xsl:call-template name="getBarre10Themes"/>

		<xsl:call-template name="getTitreOfRessource"/>
		<xsl:apply-templates select="Description"/>
		<xsl:apply-templates select="Texte"/>
		<xsl:call-template name="affServiceEnLigneOfRessource" mode="ServiceComplementaire"/>		
		<xsl:call-template name="affServiceNoticeOfRessource" mode="ServiceComplementaire"/>
	</xsl:template>
	
</xsl:stylesheet>
