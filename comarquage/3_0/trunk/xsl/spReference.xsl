<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template name="affReference" mode="Publication">
		<xsl:if test="count(Reference) > 0">
			<div class="spPublicationReference" id="sp-reference">
			<!-- 
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">references</xsl:with-param>
				</xsl:call-template>
			-->
				<h4 class="spip"><xsl:text>Références</xsl:text></h4>
				<xsl:apply-templates select="Reference" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="Reference" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:text>Référence</xsl:text>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="Titre"/>
		</xsl:variable>
		<ul class="spPublicationReference">
			<li class="spPublicationReference">
		 		<xsl:call-template name="getSiteLink">
		 			<xsl:with-param name="href"><xsl:value-of select="@URL"/></xsl:with-param>
		 			<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
		 			<xsl:with-param name="text">
		 				<xsl:value-of select="Titre"/>
						<xsl:if test="@commentaireLien">
							<xsl:text> - </xsl:text>
							<xsl:value-of select="@commentaireLien"/>
						</xsl:if>
		 			</xsl:with-param>
				</xsl:call-template>
				<xsl:if test="Complement">
					<xsl:text> - </xsl:text>
					<xsl:value-of select="Complement"/>
				</xsl:if>
			</li>
		</ul>
	</xsl:template>
	
</xsl:stylesheet>
