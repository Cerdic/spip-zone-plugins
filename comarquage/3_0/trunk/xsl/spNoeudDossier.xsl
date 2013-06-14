<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	exclude-result-prefixes="xsl dc">

	<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  	
	<xsl:template match="Publication" mode="Noeud-dossier">
		<xsl:call-template name="getBarre10Themes"/>
		<xsl:choose>
			<xsl:when test="$CATEGORIE = 'entreprises'">
				<xsl:call-template name="getFilDArianeOfPublicationEntreprise" select="FilDAriane"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates select="FilDAriane"/>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:call-template name="getTitre"/>
		<xsl:call-template name="affAvertissement" mode="Publication"/>
		 
		<div class="spPublicationMenuGauche">
			<xsl:call-template name="affVoirAussi" mode="Publication">
				<xsl:with-param name="titre"><xsl:text>dossiers</xsl:text></xsl:with-param>
			</xsl:call-template>
			<xsl:call-template name="createSommaireNoeud" mode="Noeud-dossier"/>
<!--
			<xsl:call-template name="affDossiersAZ"/>
-->
		</div>
		 
		<xsl:if test="(count(Actualite)+ count(InformationComplementaire) + count(Montant)) > 0">
			<div class="spPublicationMenuDroite">
				<xsl:call-template name="affActualite" mode="Publication"/>		
				<xsl:call-template name="affInformationComplementaire" mode="Publication"/>		
				<xsl:call-template name="affMontant" mode="Publication"/>		
			</div>
		</xsl:if>
		
		
		<xsl:if test="$CATEGORIE != 'entreprises'">
			<xsl:apply-templates select="Introduction"/>
		</xsl:if>
		<xsl:call-template name="affDossierTexte" mode="Noeud-dossier"/>
		<xsl:choose>
			<xsl:when test="count(SousDossier) > 0">
				<div class="spNoeudDossierSousDossierMain">
					<xsl:apply-templates select="SousDossier" mode="Noeud-dossier"/>
				</div>
			</xsl:when>
			<xsl:when test="count(Fiche) > 0">
				<div class="spNoeudDossierFiche">
					<ul class="spNoeudDossierFiche">
						<xsl:apply-templates select="Fiche" mode="Noeud-dossier"/>
					</ul>
				</div>
			</xsl:when>
		</xsl:choose>
 		<div class="clearall">
			<xsl:call-template name="affQuestionReponse" mode="Publication"/>		
			<xsl:call-template name="affServiceEnLigne" mode="Publication"/>		
			<xsl:call-template name="affPourEnSavoirPlus" mode="Publication"/>		
			<xsl:call-template name="affOuSAdresser" mode="Publication"/>		
			<xsl:call-template name="affReference" mode="Publication"/>		
			<xsl:call-template name="affPartenaire" mode="Publication"/>		
			<xsl:call-template name="affSiteInternetPublic" mode="Publication"/>
		</div>
	</xsl:template>
	
</xsl:stylesheet>
