<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affCentreDeContact" mode="Publication">
		<xsl:if test="count(CentreDeContact) > 0">
			<div class="spPublicationCDC" id="sp-centre-de-contact">
				<!--
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">appel-contact</xsl:with-param>
				</xsl:call-template>
				-->
				<h4 class="spip"><xsl:text>Centres d'appel et  de contact</xsl:text></h4>
				<xsl:apply-templates select="CentreDeContact" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="CentreDeContact" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:text>Centres de d'appel et de contact</xsl:text>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="text()"/>
		</xsl:variable>
		<ul class="spPublicationCDC">
			<li class="spPublicationCDC">
				<a>
				 	<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml</xsl:text>&#x26;xsl=spRessource.xsl</xsl:attribute>
	                <xsl:value-of select="text()"/> 
	             </a>
	             <!--  
    			<xsl:call-template name="getPublicationLink">
    				<xsl:with-param name="href"><xsl:value-of select="@ID"/></xsl:with-param>
    				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
    				<xsl:with-param name="text"><xsl:value-of select="text()"/></xsl:with-param>
				</xsl:call-template>
				 -->
			</li>
		</ul>
	</xsl:template>

	<xsl:template match="ServiceComplementaire" mode="Centre-de-contact">
		<xsl:call-template name="getBarre10Themes"/>
		<xsl:call-template name="getFilDArianeOfRessource" >
			<xsl:with-param name="typeRessource" >Teleservice</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="getTitreOfRessource"/>
		<xsl:apply-templates select="Texte" mode="OuSAdresser"/>
	</xsl:template>
	
</xsl:stylesheet>
