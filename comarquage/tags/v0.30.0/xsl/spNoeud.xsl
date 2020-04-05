<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">
  	
  	<xsl:import href="spCommon.xsl"/>
  	<xsl:import href="spFilDAriane.xsl"/>
  	<xsl:import href="spTitre.xsl"/>
  	<xsl:import href="spTexte.xsl"/>
  	<xsl:import href="spCommentFaireSi.xsl"/>
  	<xsl:import href="spFiche.xsl"/>
  	<xsl:import href="spDossier.xsl"/>
  	<xsl:import href="spSousDossier.xsl"/>
  	<xsl:import href="spVoirAussi.xsl"/>
  	<xsl:import href="spAvertissement.xsl"/>
  	<xsl:import href="spIntroduction.xsl"/>
  	<xsl:import href="spOuSAdresser.xsl"/>
  	<xsl:import href="spReference.xsl"/>
  	<xsl:import href="spPartenaire.xsl"/>
  	<xsl:import href="spActualite.xsl"/>
  	<xsl:import href="spInformationComplementaire.xsl"/>
  	<xsl:import href="spMontant.xsl"/>
  	<xsl:import href="spServiceEnLigne.xsl"/>
  	<xsl:import href="spQuestionReponse.xsl"/>
  	<xsl:import href="spCentreDeContact.xsl"/>
  	<xsl:import href="spPourEnSavoirPlus.xsl"/>
  	<xsl:import href="spSiteInternetPublic.xsl"/>
  	<xsl:import href="spTheme.xsl"/>
  	<xsl:import href="spSousTheme.xsl"/>
  	<xsl:import href="spNoeudDossier.xsl"/>
  	<xsl:import href="spAccueilCommentFaireSi.xsl"/>

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 

    	<xsl:template match="/">
   		<xsl:variable name="type">
   			<xsl:value-of select="/Publication/@type"/>
   		</xsl:variable>
        <xsl:choose>
   			<xsl:when test="$type = 'Theme'">
   				<xsl:apply-templates mode="Theme"/>
   			</xsl:when>
   			<xsl:when test="$type = 'Sous-theme'">
   				<xsl:apply-templates mode="Sous-theme"/>
   			</xsl:when>
   			<xsl:when test="$type = 'Noeud dossier'">
   				<xsl:apply-templates mode="Noeud-dossier"/>
   			</xsl:when>
   			<xsl:when test="$type = 'Dossier avec liens externes'">
   				<xsl:apply-templates mode="Noeud-dossier"/>
   			</xsl:when>
   			<xsl:when test="$type = 'Accueil Comment faire si'">
   				<xsl:apply-templates mode="Accueil-comment-faire-si"/>
   			</xsl:when>
   		</xsl:choose>
   		<xsl:call-template name="ancreTop"/>
 	</xsl:template>
		
</xsl:stylesheet>
