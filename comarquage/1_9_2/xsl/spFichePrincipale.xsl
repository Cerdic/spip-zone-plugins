<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">
  	
  	<xsl:import href="spCommon.xsl"/>
  	<xsl:import href="spFilDAriane.xsl"/>
  	<xsl:import href="spTitre.xsl"/>
  	<xsl:import href="spTexte.xsl"/>
  	<xsl:import href="spVoirAussi.xsl"/>
  	<xsl:import href="spAvertissement.xsl"/>
  	<xsl:import href="spIntroduction.xsl"/>
  	<xsl:import href="spOuSAdresser.xsl"/>
  	<xsl:import href="spReference.xsl"/>
  	<xsl:import href="spActualite.xsl"/>
  	<xsl:import href="spInformationComplementaire.xsl"/>
  	<xsl:import href="spMontant.xsl"/>
  	<xsl:import href="spServiceEnLigne.xsl"/>
  	<xsl:import href="spQuestionReponse.xsl"/>
  	<xsl:import href="spPourEnSavoirPlus.xsl"/>
  	<xsl:import href="spSiteInternetPublic.xsl"/>
  	
	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 

  	<!-- Publication -->
   	<xsl:template match="/Publication">
   		<xsl:if test="(dc:type !='Comment faire si')">
			<xsl:call-template name="getBarre10Themes"/>
		</xsl:if>
		
		<xsl:value-of select="@dc:type"/>
		
		<xsl:apply-templates select="FilDAriane"/>
		<xsl:call-template name="getTitre"/>
		<xsl:call-template name="affAvertissement" mode="Publication"/>
		 
		<xsl:if test="(dc:type ='Fiche') or (dc:type ='Comment faire si') or (dc:type ='Question-réponse')">
			<xsl:choose>
				<xsl:when test="$CATEGORIE = 'entreprises'">
					<xsl:if test="(count(Texte/Chapitre/Titre)+count(OuSAdresser)+count(Reference)+count(Actualite)+count(InformationComplementaire)+count(Montant)+count(ServiceEnLigne)+count(QuestionReponse)+count(EnSavoirPlus)+count(SiteInternetPublic)) > 0">
						<div class="spPublicationMenuGauche">
							<xsl:call-template name="createSommaireFiche" mode="Fiche"/>
						</div>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<div class="spPublicationMenuGauche">
						<xsl:call-template name="createSommaireFiche" mode="Fiche"/>
<!--
						<xsl:call-template name="affDossiersAZ"/>
-->
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="(count(Actualite)+ count(InformationComplementaire) + count(ServiceEnLigne)) > 0">
			<div class="spPublicationMenuDroite">
				<xsl:call-template name="affActualite" mode="Publication"/>		
				<xsl:call-template name="affInformationComplementaire" mode="Publication"/>		
				<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
			</div>
		</xsl:if>
		
		
		<xsl:apply-templates select="Introduction"/>
		<xsl:apply-templates select="Texte"/>
		<div class="clearall">
			<xsl:call-template name="affOuSAdresser" mode="Publication"/>		
			<xsl:call-template name="affReference" mode="Publication"/>		
			<xsl:call-template name="affMontant" mode="Publication"/>		
			<xsl:call-template name="affQuestionReponse" mode="Publication"/>		
			<xsl:call-template name="affPourEnSavoirPlus" mode="Publication"/>		
			<xsl:call-template name="affSiteInternetPublic" mode="Publication"/>
		</div>
  		<xsl:call-template name="ancreTop"/>
  		<xsl:call-template name="affiche_tag_xiti"/>
	 </xsl:template>
		
</xsl:stylesheet>
