<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
  <xsl:include href="Entete.xsl"/>
  <xsl:include href="Publication.xsl"/>
  <xsl:include href="RessourcesRattachees.xsl"/>
  <xsl:include href="RessourceRattachee.xsl"/>
  <xsl:include href="Pied.xsl"/>

<xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN" indent="yes"/> 
  <xsl:template match="/">
        <xsl:call-template name="entete"/>
        <br/>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" vspace="0" hspace="0">
          <tr>
            <td width="60%" align="left" valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2">
                      <b> VOS DROITS ET DÉMARCHES : 
                       <xsl:choose>
                          <xsl:when test="/Noeud/Navigation/*[1]">
                            <xsl:value-of select="/Noeud/Navigation/*[1]"/>
                          </xsl:when>
                          <xsl:when test="//ListeRacines/RacinePréf">
                            <xsl:apply-templates select="//ListeRacines/RacinePréf" mode="Noeud"/>
                          </xsl:when>
                          <xsl:when test="/Noeud/CheminPréf/*[1]">
                            <xsl:value-of select="/Noeud/CheminPréf/*[1]"/>
                          </xsl:when>
                          <xsl:otherwise>
                            <xsl:value-of select="//TitreLong"/>
                          </xsl:otherwise>
                        </xsl:choose>
                      </b>
                  </td>
                </tr>
                <tr>
                  <td rowspan="2" width="16" valign="bottom" align="right">
                  </td>
                </tr>
                <tr>
                  <td width="100%" valign="top">
                    <table width="100%" border="0" cellspacing="0" cellpadding="4" vspace="0" hspace="0">
                      <tr align="left" valign="top">
                        <td width="60%">
                         <xsl:apply-templates select="Noeud/TitreLong" mode="Noeud"/>
                         <xsl:apply-templates select="Noeud/RessourcesRattachées/RessourceRattachée[@type='Avertissement']" mode="Ressource_Avertissement"/>
                         <table border="0" cellspacing="0" cellpadding="1" vspace="0" hspace="0" width="90%">
                            <xsl:apply-templates select="Noeud/Descendance/Fils[@type!='Fiche Question-réponse']" mode="Noeud_VD">
                              <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
                            </xsl:apply-templates>
                          </table>
                          <xsl:apply-templates select="Noeud/Descendance" mode="Noeud_QR">
                            <xsl:sort select="Fils/@positionPrésentation" data-type="number" order="ascending"/>
                          </xsl:apply-templates>
                        </td>
                        <!-- Deuxième colonne : renvois -->
                        <td width="40%">
                          <xsl:apply-templates select="Noeud/RessourcesRattachées" mode="RessourcesRattachees"/>
                        </td>
                      </tr>
                      <!-- Deuxieme ligne : Fonctionnalité de recherche par mots-clés-->
                      <tr>
                        <td colspan="2">
                          <xsl:apply-templates select="Noeud/DateValidité"/>
                          <br/>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
        <br/>
        <xsl:call-template name="pied"/>
  </xsl:template>
  <!-- TitreLong pour Noeud-->
  <xsl:template match="TitreLong" mode="Noeud">
    <xsl:if test="../@racine='0'">
    <p>
      <xsl:choose>
        <xsl:when test="//Navigation">
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Noeud/Navigation/*[last()]/@href"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="navigationparams"><xsl:with-param name="pos" select="count(/Noeud/Navigation/*)"/></xsl:call-template></xsl:attribute>
            <img align="texttop" width="18" height="18" border="0" alt="Retour au menu précédent">
              <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ouvert.gif</xsl:text></xsl:attribute>
            </img>
          </a>
          <xsl:text> </xsl:text>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Noeud/Navigation/*[last()]/@href"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="navigationparams"><xsl:with-param name="pos" select="count(/Noeud/Navigation/*)"/></xsl:call-template></xsl:attribute>
            <b>
              <xsl:value-of select="."/>
            </b>
          </a>
        </xsl:when>
        <xsl:otherwise>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Noeud/CheminPréf/*[last()]/@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            <img align="texttop" width="18" height="18" border="0" alt="Retour au menu précédent">
              <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ouvert.gif</xsl:text></xsl:attribute>
            </img>
          </a>
          <xsl:text> </xsl:text>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Noeud/CheminPréf/*[last()]/@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            <b>
              <xsl:value-of select="."/>
            </b>
          </a>
        </xsl:otherwise>
      </xsl:choose>
    </p>
    </xsl:if>
  </xsl:template>
  <!-- Fils -->
  <xsl:template match="Fils" mode="Noeud_VD">
    <tr align="left">
       <td valign="baseline" width="8" align="right">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <img width="18" height="18" border="0" alt="Développer">
            <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ferme.gif</xsl:text></xsl:attribute>
          </img>
        </a>
      </td>
      <td valign="top" width="100%">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:value-of select="TitreContextuel"/>
        </a>
      </td>
    </tr>
  </xsl:template>
  <!-- FichesLiées QR -->
  <xsl:template match="Descendance" mode="Noeud_QR">
    <br/>
    <xsl:if test="Fils[@type='Fiche Question-réponse']">
      <h4>
        <img width="15" height="16" align="texttop" alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_rouge2.gif</xsl:text></xsl:attribute>
        </img>
        <font color="#CC3333">Questions-réponses</font>
      </h4>
      <table border="0" cellspacing="0" cellpadding="4" vspace="0" hspace="0" width="90%">
        <xsl:apply-templates select="Fils[@type='Fiche Question-réponse']" mode="Noeud_QR"/>
      </table>
    </xsl:if>
  </xsl:template>
  <!-- LienFiche QR-->
  <xsl:template match="Fils" mode="Noeud_QR">
    <tr align="left">
      <td valign="baseline" width="8" align="right">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <img border="0" alt="Développer">
            <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_r.gif</xsl:text></xsl:attribute>
          </img>
        </a>
      </td>
      <td valign="TOP" width="100%">
        <a>
          <xsl:choose>
            <xsl:when test="@nature='Noeud'">
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text><xsl:call-template name="parametres"/></xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:value-of select="TitreContextuel"/>
        </a>
      </td>
    </tr>
  </xsl:template>
  <xsl:template match="ListeRacines/RacinePréf" mode="Noeud">
    <xsl:variable name="theme">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="."/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:value-of select="document($theme)/Noeud/TitreLong"/>
  </xsl:template>
  <xsl:template match="Commune">
    <xsl:value-of select="."/>
  </xsl:template>
</xsl:stylesheet>
