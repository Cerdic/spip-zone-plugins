<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  		
	<xsl:template name="affCommentFaireSi" mode="Publication">
		<xsl:if test="count(CommentFaireSi) > 0">
			<div class="spPublicationCFS" id="sp-comment-faire-si">
<!--
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">comment-faire</xsl:with-param>
				</xsl:call-template>
-->
				<h4 class="spip"><span><xsl:text>Comment faire si</xsl:text></span></h4>
				<ul class="spPublicationCFS">
					<xsl:for-each select="CommentFaireSi">
						<xsl:apply-templates select="." mode="Publication"/>
					</xsl:for-each>
					<xsl:call-template name="lienVersAccueilCommentFaireSi"/>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="CommentFaireSi" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:text>Comment faire si</xsl:text>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="text()"/>
		</xsl:variable>
		<li class="spPublicationCFS">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@ID"/><xsl:text>.xml</xsl:text>&#x26;xsl=spFichePrincipale.xsl</xsl:attribute>
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
	</xsl:template>
	
	<xsl:template name="lienVersAccueilCommentFaireSi">
		<xsl:variable name="file">
			<xsl:value-of select="$XMLURL"/>
			<xsl:text>N13042.xml</xsl:text>
		</xsl:variable>
		<li class="spPublicationCFS">
			<a>
				<xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=N13042.xml&#x26;xsl=spNoeud.xsl</xsl:attribute>
				<xsl:value-of select="text()"/>
			</a>
			<!-- 
    			<xsl:call-template name="getPublicationLink">
    				<xsl:with-param name="href"><xsl:text>N13042</xsl:text></xsl:with-param>
    				<xsl:with-param name="title">
						<xsl:call-template name="getDescription">
							<xsl:with-param name="id">N13042</xsl:with-param>
						</xsl:call-template>
    				</xsl:with-param>
    				<xsl:with-param name="text"><xsl:text>Accueil comment faire si...</xsl:text></xsl:with-param>
				</xsl:call-template>
			-->
		</li>
	</xsl:template>

</xsl:stylesheet>
