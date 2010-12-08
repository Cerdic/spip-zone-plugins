<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  		
	<xsl:template name="affServiceEnLigne" mode="Publication">
		<xsl:if test="count(ServiceEnLigne) > 0">
			<div class="spPublicationSEL" id="sp-service-en-ligne">
				<!--
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">enligne</xsl:with-param>
				</xsl:call-template>
				-->
				<h4 class="spip"><span><xsl:text>Services et formulaires en ligne</xsl:text></span></h4>
				<xsl:apply-templates select="ServiceEnLigne" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template match="ServiceEnLigne" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="@type"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="text()"/>
		</xsl:variable>
		<ul class="spPublicationSEL">
			<li class="spPublicationSEL">
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
			
				<xsl:text> - </xsl:text>
				<xsl:value-of select="@type"/>
				<xsl:if test="@numerocerfa">
					<xsl:text> - Cerfa n°</xsl:text>
					<xsl:value-of select="@numerocerfa"/>
				</xsl:if>
			</li>
		</ul>
	</xsl:template>
	
	<xsl:template name="affServiceEnLigneOfRessource" mode="ServiceComplementaire">
		<xsl:if test="count(ServiceEnLigne) > 0">
			<div class="spServiceComplementaireSEL" id="sp-service-en-ligne">
				<h3 class="spip"><span><xsl:text>Voir aussi</xsl:text></span></h3>
				<xsl:apply-templates select="ServiceEnLigne" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template name="affServiceNoticeOfRessource" mode="ServiceComplementaire">
		<xsl:if test="(count(LienWeb) + count(LienMonServicePublic) + count(NoticeLiee)) > 0">
			<div class="spServiceComplementaireSN" id="sp-service-notice">
				<ul class="spServiceComplementaireSN">
					<xsl:apply-templates select="LienWeb" mode="ServiceComplementaire"/>
					<xsl:apply-templates select="LienMonServicePublic" mode="ServiceComplementaire"/>
					<xsl:apply-templates select="NoticeLiee" mode="ServiceComplementaire"/>
				</ul>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="LienWeb" mode="ServiceComplementaire">
		<li class="spServiceComplementaireSN">
			
				<a href="{@URL}" rel="noffolow" title="{@URL} (nouvelle fenêtre)" target="_blank">
					<xsl:choose>
						<xsl:when test="../dc:type = 'Lettre type'">
							<xsl:text>Accéder aux lettres types</xsl:text>
							<xsl:if test="@commentaireLien != ''">
								<xsl:text> - </xsl:text>
								<xsl:value-of select="@commentaireLien"/>
							</xsl:if>
						</xsl:when>
						<xsl:when test="../dc:type = 'Formulaire'">
							<xsl:text>Accéder au </xsl:text>
							<xsl:call-template name="lowerCase">
								<xsl:with-param name="string"><xsl:value-of select="../dc:type"/></xsl:with-param>
							</xsl:call-template>
							<xsl:if test="../NumeroCerfa">
								<xsl:text> Cerfa n°</xsl:text>
								<xsl:value-of select="../NumeroCerfa"/>
							</xsl:if>
							<xsl:if test="@commentaireLien != ''">
								<xsl:text> - </xsl:text>
								<xsl:value-of select="@commentaireLien"/>
							</xsl:if>
						</xsl:when>
						<xsl:otherwise>
							<xsl:text>Accéder au </xsl:text>
							<xsl:call-template name="lowerCase">
								<xsl:with-param name="string"><xsl:value-of select="../dc:type"/></xsl:with-param>
							</xsl:call-template>
							<xsl:text> "</xsl:text>
							<xsl:value-of select="../dc:title"/>
							<xsl:text>"</xsl:text>
							<xsl:if test="../NumeroCerfa">
								<xsl:text> Cerfa n°</xsl:text>
								<xsl:value-of select="../NumeroCerfa"/>
							</xsl:if>
							<xsl:if test="@commentaireLien != ''">
								<br />
								<xsl:value-of select="@commentaireLien"/>
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
				</a>
			
			<xsl:if test="Source">
				<xsl:text> - </xsl:text>
				<span class="italic"><xsl:value-of select="Source"/></span>
			</xsl:if>
		</li>
	</xsl:template>

	<xsl:template match="LienMonServicePublic" mode="ServiceComplementaire">
		<li class="spServiceComplementaireSN">
				<xsl:text>Vous pouvez aussi réaliser cette démarche sur mon.service-public.fr. </xsl:text>
				<a href="{@URL}" rel="noffolow" title="{@URL}">
					<xsl:apply-templates/>
					<xsl:if test="@commentaireLien != ''">
						<xsl:text> - </xsl:text>
						<xsl:value-of select="@commentaireLien"/>
					</xsl:if>
				</a>
		</li>
	</xsl:template>
			
	<xsl:template match="NoticeLiee" mode="ServiceComplementaire">
		<li class="spServiceComplementaireSN">
				<a href="{@URL}" rel="noffolow" title="{@URL}">
					<xsl:apply-templates/>
					<xsl:if test="@commentaireLien != ''">
						<xsl:text> - </xsl:text>
						<xsl:value-of select="@commentaireLien"/>
					</xsl:if>
				</a>
		</li>
	</xsl:template>

</xsl:stylesheet>
