<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affActualite" mode="Pulication">
		<xsl:if test="count(Actualite) > 0">
			<div class="spPublicationActualite" id="sp-actualite">
<!--
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">actualites</xsl:with-param>
				</xsl:call-template>
-->
				<h4 class="spip"><span><xsl:text>Actualités</xsl:text></span></h4>
				<xsl:apply-templates select="Actualite" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="Actualite" mode="Publication">
		<xsl:choose>
			<xsl:when test="contains(@type,'Fil')">
				<h5><xsl:value-of select="Titre"/></h5>
				<xsl:call-template name="filActualite" mode="Publication">
					<xsl:with-param name="channel">
						<xsl:value-of select="document(@URL)/rss/channel"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="articleActualite" mode="Publication"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="articleActualite" mode="Publication">	
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="Titre"/>
		</xsl:variable>
		<ul class="spPublicationActualite">
			<li class="spPublicationActualite">
				
	    			<xsl:call-template name="getSiteLink">
	    				<xsl:with-param name="href"><xsl:value-of select="@URL"/></xsl:with-param>
	    				<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
	    				<xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
					</xsl:call-template>
				
				<xsl:text> - </xsl:text>
				<xsl:value-of select="@type"/>
			</li>
		</ul>
	</xsl:template>

	<xsl:template name="filActualite" mode="Publication">
		<xsl:param name="channel"/>
		<ul class="spPublicationActualite">
			<xsl:for-each select="document(@URL)/rss/channel/item[position()&lt;4]">
				<li class="spPublicationActualite">
					
		    			<xsl:call-template name="getSiteLink">
		    				<xsl:with-param name="href"><xsl:value-of select="link"/></xsl:with-param>
		    				<xsl:with-param name="title"><xsl:value-of select="description"/></xsl:with-param>
		    				<xsl:with-param name="text"><xsl:value-of select="title"/></xsl:with-param>
						</xsl:call-template>
					
					<xsl:text> - </xsl:text>
					<xsl:call-template name="transformRssDate">
						<xsl:with-param name="date">
							<xsl:choose>
								<xsl:when test="dc:date"> 
									 <xsl:value-of select="dc:date"/>
								</xsl:when>
								<xsl:when test="pubDate"> 
									<xsl:value-of select="pubDate"/>
								</xsl:when>
							</xsl:choose>
						</xsl:with-param>
					</xsl:call-template>
				</li>	
			</xsl:for-each>
			<li class="spPublicationActualite">
				
	    			<xsl:call-template name="getSiteLink">
	    				<xsl:with-param name="href"><xsl:value-of select="@URL"/></xsl:with-param>
	    				<xsl:with-param name="title"><xsl:text>Lien vers le flux RSS d'actualités</xsl:text></xsl:with-param>
	    				<xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
					</xsl:call-template>
				
			</li>
		</ul>
	</xsl:template>
		
</xsl:stylesheet>
