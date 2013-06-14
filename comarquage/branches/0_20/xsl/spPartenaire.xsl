<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affPartenaire" mode="Publication">
		<xsl:if test="count(Partenaire) > 0">
			<div class="spPublicationPartenaire" id="sp-partenaire">
				<!--  
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">partenaires</xsl:with-param>
				</xsl:call-template>
				-->
				<h4 class="spip"><xsl:text>Réalisé en partenariat avec :</xsl:text></h4>
				<xsl:variable name="nbPart"><xsl:value-of select="count(Partenaire)+1"/></xsl:variable>
				<xsl:for-each select="Partenaire">
					<div class="spPublicationPartenaireLogo">
						<xsl:attribute name="style">
							<xsl:text>width:</xsl:text>
							<xsl:value-of select="floor(100 div $nbPart)"/>
							<xsl:text>%;</xsl:text>
						</xsl:attribute>
						<xsl:call-template name="affPartenaireLogo"/>
					</div>
				</xsl:for-each>
				<br class="clearall"/>			
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="affPartenaireLogo">
		<xsl:variable name="url">
			<xsl:value-of select="normalize-space(@URL)"/>
		</xsl:variable>
		<a href="{$url}" rel="nofollow" title="{$url}">
			<img src="http://www.service-public.fr/images2/partenaires/{@ID}.png" alt="{$url}"/>
		</a>
	</xsl:template>
	
</xsl:stylesheet>
