<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="UTF-8" indent="yes"/> 
  <xsl:include href="Parametres.xsl"/>
  <!-- Tags ignorés pour Fiche-->
  <xsl:template match="TitreCourt" mode="Fiche"/>
  <xsl:template match="Description" mode="Fiche"/>
  <xsl:template match="Coproducteur" mode="Fiche"/>
  <xsl:template match="Type" mode="Fiche"/>
  <xsl:template match="Catégorie" mode="Fiche"/>
  <xsl:template match="CouvertureGéographique" mode="Fiche"/>
  <xsl:template match="Indexation" mode="Fiche"/>
  <xsl:template match="Ascendance" mode="Fiche"/>
  <!-- Tags ignorés pour RessourceRattachée-->
  <xsl:template match="TitreCourt" mode="Ressource"/>
  <xsl:template match="Description" mode="Ressource"/>
  <xsl:template match="Coproducteur" mode="Ressource"/>
  <xsl:template match="DateValidité" mode="Ressource"/>
  <xsl:template match="EditeurSource" mode="Ressource"/>
  <xsl:template match="Source" mode="Ressource"/>
  <xsl:template match="Type" mode="Ressource"/>
  <xsl:template match="Catégorie" mode="Ressource"/>
  <xsl:template match="CouvertureGéographique" mode="Ressource"/>
  <xsl:template match="Indexation" mode="Ressource"/>
  <!-- Tags ignorés pour RessourceInterne-->
  <xsl:template match="TitreCourt" mode="RessourceInterne"/>
  <xsl:template match="Description" mode="RessourceInterne"/>
  <xsl:template match="Coproducteur" mode="RessourceInterne"/>
  <xsl:template match="DateValidité" mode="RessourceInterne"/>
  <xsl:template match="EditeurSource" mode="RessourceInterne"/>
  <xsl:template match="Source" mode="RessourceInterne"/>
  <xsl:template match="Type" mode="RessourceInterne"/>
  <xsl:template match="Catégorie" mode="RessourceInterne"/>
  <xsl:template match="CouvertureGéographique" mode="RessourceInterne"/>
  <xsl:template match="Indexation" mode="RessourceInterne"/>
  <!-- Tags ignorés pour Noeud-->
  <xsl:template match="TitreCourt" mode="Noeud"/>
  <xsl:template match="Description" mode="Noeud"/>
  <xsl:template match="Coproducteur" mode="Noeud"/>
  <xsl:template match="Source" mode="Noeud"/>
  <xsl:template match="Type" mode="Noeud"/>
  <xsl:template match="Catégorie" mode="Noeud"/>
  <xsl:template match="CouvertureGéographique" mode="Noeud"/>
  <xsl:template match="Indexation" mode="Noeud"/>
  <!-- Tags communs-->
  <!-- Motclé -->
  <xsl:template match="MotClé">
    <xsl:if test="position()!=1">
      <xsl:text>, </xsl:text>
    </xsl:if>
    <xsl:value-of select="."/>
  </xsl:template>
  <!-- Montant -->
  <xsl:template match="Montant">
    <xsl:value-of select="."/>
    <xsl:text> EUR</xsl:text>
  </xsl:template>
  <!-- LienExterne -->
  <xsl:template match="LienExterne">
    <a class="lien" target="_blank">
      <xsl:attribute name="href"><xsl:value-of select="@URL"/></xsl:attribute>
      <xsl:apply-templates/>
    </a>
  </xsl:template>
  <!-- LienInterne -->
  <xsl:template match="LienInterne">
    <xsl:variable name="publication">
      <xsl:value-of select="@LienPublication"/>
    </xsl:variable>
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="$publication"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="starts-with($publication, 'N')">
        <a class="lien">
          <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@LienPublication"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
          <xsl:apply-templates/>
        </a>
      </xsl:when>
      <xsl:when test="starts-with($publication, 'F')">
        <a class="lien">
          <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@LienPublication"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text></xsl:attribute>
          <xsl:apply-templates/>
        </a>
      </xsl:when>
      <xsl:otherwise>
        <xsl:choose>
          <xsl:when test="document($lien)">
            <xsl:apply-templates select="document($lien)/Ressource" mode="RessourceInterne">
              <xsl:with-param name="libelle" select="."/>
            </xsl:apply-templates>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="."/>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <!-- MiseEnEvidence -->
  <xsl:template match="MiseEnEvidence">
    <b>
      <xsl:apply-templates/>
    </b>
  </xsl:template>
  <!-- Citation -->
  <xsl:template match="Citation">
    <i>
      <xsl:apply-templates/>
    </i>
  </xsl:template>
  <xsl:template match="DateValidité">
    <xsl:variable name="date">
      <xsl:value-of select="."/>
    </xsl:variable>
    <xsl:variable name="ddate">
      <xsl:choose>
        <xsl:when test="substring($date, 3, 1) = '-' or substring($date, 3, 1) = '/'">
          <xsl:choose>
            <xsl:when test="substring($date, 4, 2)='01'">Janvier</xsl:when>
            <xsl:when test="substring($date, 4, 2)='02'">Février</xsl:when>
            <xsl:when test="substring($date, 4, 2)='03'">Mars</xsl:when>
            <xsl:when test="substring($date, 4, 2)='04'">Avril</xsl:when>
            <xsl:when test="substring($date, 4, 2)='05'">Mai</xsl:when>
            <xsl:when test="substring($date, 4, 2)='06'">Juin</xsl:when>
            <xsl:when test="substring($date, 4, 2)='07'">Juillet</xsl:when>
            <xsl:when test="substring($date, 4, 2)='08'">Août</xsl:when>
            <xsl:when test="substring($date, 4, 2)='09'">Septembre</xsl:when>
            <xsl:when test="substring($date, 4, 2)='10'">Octobre</xsl:when>
            <xsl:when test="substring($date, 4, 2)='11'">Novembre</xsl:when>
            <xsl:when test="substring($date, 4, 2)='12'">Décembre</xsl:when>
          </xsl:choose>
          <xsl:text> </xsl:text>
          <xsl:value-of select="substring($date, 7, 4)"/>
        </xsl:when>
        <xsl:when test="substring($date, 5, 1) = '-' or substring($date, 5, 1) = '/'">
          <xsl:choose>
            <xsl:when test="substring($date, 6, 2)='01'">Janvier</xsl:when>
            <xsl:when test="substring($date, 6, 2)='02'">Février</xsl:when>
            <xsl:when test="substring($date, 6, 2)='03'">Mars</xsl:when>
            <xsl:when test="substring($date, 6, 2)='04'">Avril</xsl:when>
            <xsl:when test="substring($date, 6, 2)='05'">Mai</xsl:when>
            <xsl:when test="substring($date, 6, 2)='06'">Juin</xsl:when>
            <xsl:when test="substring($date, 6, 2)='07'">Juillet</xsl:when>
            <xsl:when test="substring($date, 6, 2)='08'">Août</xsl:when>
            <xsl:when test="substring($date, 6, 2)='09'">Septembre</xsl:when>
            <xsl:when test="substring($date, 6, 2)='10'">Octobre</xsl:when>
            <xsl:when test="substring($date, 6, 2)='11'">Novembre</xsl:when>
            <xsl:when test="substring($date, 6, 2)='12'">Décembre</xsl:when>
          </xsl:choose>
          <xsl:text> </xsl:text>
          <xsl:value-of select="substring($date, 1, 4)"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$date"/>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:variable>
    <p>&#xA0;</p>
    <p>
      <span class="texte1">
        <i><xsl:text>Dernière mise à jour : </xsl:text><xsl:value-of select="$ddate"/></i>
      </span>
    </p>
    <p>&#xA0;</p>
  </xsl:template>
</xsl:stylesheet>
