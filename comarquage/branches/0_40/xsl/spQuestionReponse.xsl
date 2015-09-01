<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 

	<xsl:template name="affQuestionReponse" mode="Publication">  	
		<xsl:if test="count(QuestionReponse) > 0">
			<div class="spPublicationQR" id="sp-question-reponse">
				<!-- 
				<xsl:call-template name="imageOfAPartie">
					<xsl:with-param name="nom">questions-reponses</xsl:with-param>
				</xsl:call-template>
				 -->
				<h4 class="spip"><span><xsl:text>Questions - Réponses</xsl:text></span></h4>
				<xsl:apply-templates select="QuestionReponse" mode="Publication"/>
			</div>
		</xsl:if>
	</xsl:template>

	<xsl:template match="QuestionReponse" mode="Publication">
		<xsl:variable name="title">
			<xsl:value-of select="../dc:title"/>
			<xsl:value-of select="$sepFilDAriane"/>
			<xsl:value-of select="text()"/>
		</xsl:variable>
		<ul class="spPublicationQR">
			<li class="spPublicationQR">	
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

				<xsl:text> : </xsl:text>
				<xsl:call-template name="getDescription">
					<xsl:with-param name="id" select="@ID"/>
				</xsl:call-template>
			</li>
		</ul>
	</xsl:template>
	
</xsl:stylesheet>
