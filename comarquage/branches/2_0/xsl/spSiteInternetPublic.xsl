<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affSiteInternetPublic" mode="Publication">
		<xsl:if test="count(SiteInternetPublic) > 0">
			<div class="spPublicationSIP" id="sp-site-internet-public">
				<!-- 
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">sites-internet-publics</xsl:with-param>
				</xsl:call-template>
				 -->
				<h4 class="spip"><span><xsl:text>Sites internet publics</xsl:text></span></h4>
				<xsl:apply-templates select="SiteInternetPublic" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="SiteInternetPublic" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:text>Site internet public</xsl:text>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="@complementLien"/>
		</xsl:variable>
		<ul class="spPublicationSIP">
			<li class="spPublicationSIP">
		 		<xsl:call-template name="getSiteLink">
		 			<xsl:with-param name="href"><xsl:value-of select="@URL"/></xsl:with-param>
		 			<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
		 			<xsl:with-param name="text">
						<xsl:value-of select="@complementLien"/>
						<xsl:text> - </xsl:text>
						<xsl:value-of select="Titre"/>
		 			</xsl:with-param>
				</xsl:call-template>
				<br /><xsl:text> [</xsl:text>
					<xsl:choose>
						<xsl:when test="Source/text()">
			    			<xsl:apply-templates select="Source"/>
						</xsl:when>
						<xsl:otherwise>
			    			<xsl:call-template name="getPublicationLink">
			    				<xsl:with-param name="href"><xsl:value-of select="Source/@ID"/></xsl:with-param>
			    				<xsl:with-param name="title"><xsl:text>En savoir plus sur </xsl:text><xsl:value-of select="Titre"/></xsl:with-param>
			    				<xsl:with-param name="text"><xsl:text>Pour en savoir plus sur </xsl:text><xsl:value-of select="Titre"/></xsl:with-param>
							</xsl:call-template>
						</xsl:otherwise>
					</xsl:choose>
				<xsl:text>]</xsl:text>
			</li>
		</ul>
	</xsl:template>
	
</xsl:stylesheet>
