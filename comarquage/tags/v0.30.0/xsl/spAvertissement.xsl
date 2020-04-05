<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affAvertissement" mode="Publication">
		<xsl:if test="count(Avertissement) > 0">
			<div class="spPublicationAvertissement" id="sp-avertissement">
				<xsl:apply-templates select="Avertissement" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="Avertissement" mode="Publication">
		<h4 class="spip">
			<xsl:call-template name="imageOfAPartie">
				<xsl:with-param name="nom">avertissement</xsl:with-param>
			</xsl:call-template>
				
			<xsl:value-of select="Titre"/>
			<xsl:text> - </xsl:text>
			<xsl:call-template name="transformRssDate">
				<xsl:with-param name="date">
					<xsl:value-of select="@date"/>
					<xsl:text>TZ</xsl:text>
				</xsl:with-param>
			</xsl:call-template>
		</h4>
		<xsl:apply-templates select="Texte"/>
	</xsl:template>
	
</xsl:stylesheet>
