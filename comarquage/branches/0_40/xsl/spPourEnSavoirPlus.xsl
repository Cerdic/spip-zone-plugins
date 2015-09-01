<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affPourEnSavoirPlus" mode="Publication">
		<xsl:if test="count(PourEnSavoirPlus) > 0">
			<div class="spPublicationPESP" id="sp-pour-en-savoir-plus">
				<!--
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">savoir-plus</xsl:with-param>
				</xsl:call-template>
				-->
				<h4 class="spip"><span><xsl:text>Pour en savoir plus</xsl:text></span></h4>
				<xsl:apply-templates select="PourEnSavoirPlus" mode="Publication"/>
			</div>
		</xsl:if>	
	</xsl:template>

	<xsl:template match="PourEnSavoirPlus" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="Titre"/>
		</xsl:variable>
		<ul class="spPublicationPESP">
			<li class="spPublicationPESP">
		 		<xsl:call-template name="getSiteLink">
		 			<xsl:with-param name="href"><xsl:value-of select="@URL"/></xsl:with-param>
		 			<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
		 			<xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
				</xsl:call-template>
				<xsl:text> - </xsl:text>
				<xsl:value-of select="@type"/>
				<xsl:if test="Source">
				    <xsl:variable name="file">
				    	<xsl:value-of select="$XMLURL"/>
				    	<xsl:value-of select="Source/@ID"/>
				    	<xsl:text>.xml</xsl:text>
				    </xsl:variable>
					<xsl:text> - </xsl:text>
					<xsl:value-of select="Source/text()"/>
				</xsl:if>
			</li>
		</ul>
	</xsl:template>
	
</xsl:stylesheet>
