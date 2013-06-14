<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

  	<xsl:import href="spCommon.xsl"/>
  	<xsl:import href="spTitre.xsl"/>
  	<xsl:import href="spFilDAriane.xsl"/>
  	<xsl:import href="spOuSAdresser.xsl"/>

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
   	<xsl:template match="/">
 		<xsl:call-template name="getFilDArianeOfPivotLocal"/>
		<div class="center"><h1><xsl:value-of select="PivotLocal/Titre"/></h1></div>
		<xsl:variable name="nb">
			<xsl:value-of select="count(PivotLocal/Adresse) + count(PivotLocal/Communication) + count(PivotLocal/Horaires)"/>
		</xsl:variable>
		<xsl:apply-templates select="PivotLocal" mode="PivotLocal">
			<xsl:with-param name="cssWidth">
				<xsl:value-of select="floor(100 div $nb)"/>
			</xsl:with-param>
		</xsl:apply-templates>
		<xsl:if test="not(PivotLocal/Gps)">
			<br class="clearall"/>
		</xsl:if>
  	</xsl:template>
	
</xsl:stylesheet>
