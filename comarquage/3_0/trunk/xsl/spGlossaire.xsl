<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template match="ServiceComplementaire" mode="Glossaire">
		<xsl:call-template name="getBarre10Themes"/>
		<xsl:call-template name="getFilDArianeOfRessource"/>
		<xsl:call-template name="getTitreOfRessource"/>
		<xsl:apply-templates select="Texte" mode="Definition"/>
		<xsl:call-template name="affReference" mode="Publication"/>		
		<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
		<xsl:call-template name="affCentreDeContact" mode="Publication"/>		
		<xsl:call-template name="affPourEnSavoirPlus" mode="Publication"/>		
		<xsl:call-template name="affSiteInternetPublic" mode="Publication"/>		
	</xsl:template>
	
</xsl:stylesheet>
