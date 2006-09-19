<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
  <xsl:include href="Entete.xsl"/>
  <xsl:include href="Publication.xsl"/>
  <xsl:include href="RessourcesRattachees.xsl"/>
  <xsl:include href="RessourceRattachee.xsl"/>
  <xsl:include href="Pied.xsl"/>

<xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD HTML 4.01 Transitional//EN"  indent="yes"/> 
  <xsl:template match="/">
        <xsl:call-template name="entete"/>
        <br/>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" vspace="0" hspace="0">
          <tr>
            <td width="60%" align="left" valign="top">
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td colspan="2">
                    <a name="#DebutGroupe"/>
                      <b> VOS DROITS ET DÉMARCHES :
                       <xsl:choose>
                          <xsl:when test="/Fiche/Navigation/*[1]">
                            <xsl:value-of select="/Fiche/Navigation/*[1]"/>
                          </xsl:when>
                          <xsl:when test="//ListeRacines/RacinePréf">
                            <xsl:apply-templates select="//ListeRacines/RacinePréf" mode="Noeud"/>
                          </xsl:when>
                          <xsl:when test="/Fiche/CheminPréf/*[1]">
                            <xsl:value-of select="/Fiche/CheminPréf/*[1]"/>
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
                          <xsl:choose>
                            <xsl:when test="contains(Fiche/Type/Nom, 'actualité')">
                              <xsl:apply-templates select="Fiche/TitreLong" mode="Fiche"/>
                              <xsl:apply-templates select="Fiche/RessourcesRattachées/RessourceRattachée[@type='Avertissement']" mode="Ressource_Avertissement"/>
                              <xsl:apply-templates select="Fiche/Texte" mode="Fiche_Actualite"/>
                            </xsl:when>
                            <xsl:when test="contains(Fiche/Type/Nom, 'sommaire')">
                              <xsl:apply-templates select="Fiche/TitreLong" mode="Fiche"/>
                              <xsl:apply-templates select="Fiche/RessourcesRattachées/RessourceRattachée[@type='Avertissement']" mode="Ressource_Avertissement"/>
                              <table border="0" cellspacing="0" cellpadding="1" vspace="0" hspace="0" width="90%">
                                <xsl:apply-templates select="Fiche/Texte/Chapitre/Titre" mode="Sommaire"/>
                              </table>
                              <xsl:apply-templates select="Fiche/Texte" mode="Fiche"/>
                            </xsl:when>
                            <xsl:when test="contains(Fiche/Type/Nom, 'Question-réponse')">
                              <xsl:apply-templates select="Fiche/TitreLong" mode="FicheQR"/>
                              <xsl:apply-templates select="Fiche/RessourcesRattachées/RessourceRattachée[@type='Avertissement']" mode="Ressource_Avertissement"/>
                              <xsl:apply-templates select="Fiche/Texte" mode="Fiche"/>
                            </xsl:when>
                            <xsl:otherwise>
                              <xsl:apply-templates select="Fiche/TitreLong" mode="Fiche"/>
                              <xsl:apply-templates select="Fiche/RessourcesRattachées/RessourceRattachée[@type='Avertissement']" mode="Ressource_Avertissement"/>
                              <xsl:apply-templates select="Fiche/Texte" mode="Fiche"/>
                            </xsl:otherwise>
                          </xsl:choose>
                          <xsl:apply-templates select="Fiche/RessourcesRattachées" mode="OrganismesLies"/>
                          <xsl:apply-templates select="Fiche/RessourcesRattachées/RessourceRattachée[@type='Lettre type']" mode="Ressource_Lettre_type"/>
                        </td>
                        <!-- Deuxième colonne : renvois -->
                        <td width="40%">
                          <xsl:apply-templates select="Fiche/RessourcesRattachées" mode="RessourcesRattachees"/>
                        </td>
                      </tr>
                      <!-- Deuxieme ligne : Fonctionnalité de recherche par mots-clés-->
                      <tr>
                        <td colspan="2">
                          <xsl:apply-templates select="Fiche/DateValidité"/>
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
  <!-- Sommaire -->
  <xsl:template match="Titre" mode="Sommaire">
    <tr align="left">
      <td valign="baseline" width="8" align="right">
        <a>
          <xsl:attribute name="href"><xsl:text>#titre</xsl:text><xsl:value-of select="generate-id()"/></xsl:attribute>
          <img width="18" height="18" border="0" alt="Développer">
            <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ferme.gif</xsl:text></xsl:attribute>
          </img>
        </a>
      </td>
      <td valign="top" width="100%">
        <a>
          <xsl:attribute name="href"><xsl:text>#titre</xsl:text><xsl:value-of select="generate-id()"/></xsl:attribute>
          <xsl:apply-templates mode="Sommaire"/>
        </a>
      </td>
    </tr>
  </xsl:template>
  <xsl:template match="Paragraphe" mode="Sommaire">
    <xsl:apply-templates/>
  </xsl:template>
  <!-- Texte -->
  <xsl:template match="Texte" mode="Fiche_Actualite">
    <table width="90%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td bgcolor="#E9E9E9">
          <table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
              <td>
                <xsl:apply-templates  mode="Fiche"/>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <xsl:apply-templates select="/Fiche/FichesLiées" mode="FichesLiees"/>
  </xsl:template>
  <!-- TitreLong pour Fiche-->
  <xsl:template match="TitreLong" mode="Fiche">
    <p>
      <xsl:choose>
        <xsl:when test="//Navigation">
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Fiche/Navigation/*[last()]/@href"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="navigationparams"><xsl:with-param name="pos" select="count(/Fiche/Navigation/*)"/></xsl:call-template></xsl:attribute>
            <img align="texttop" width="18" height="18" border="0" alt="Retour au menu précédent">
              <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ouvert.gif</xsl:text></xsl:attribute>
            </img>
          </a>
          <xsl:text>&#160;</xsl:text>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Fiche/Navigation/*[last()]/@href"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text><xsl:call-template name="navigationparams"><xsl:with-param name="pos" select="count(/Fiche/Navigation/*)"/></xsl:call-template></xsl:attribute>
            <b>
              <xsl:value-of select="."/>
            </b>
          </a>
        </xsl:when>
        <xsl:otherwise>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Fiche/CheminPréf/*[last()]/@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            <img align="texttop" width="18" height="18" border="0" alt="Retour au menu précédent">
              <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ouvert.gif</xsl:text></xsl:attribute>
            </img>
          </a>
          <xsl:text>&#160;</xsl:text>
          <a>
            <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="/Fiche/CheminPréf/*[last()]/@ID"/><xsl:text>.xml&#x26;xsl=Noeud.xsl</xsl:text></xsl:attribute>
            <b>
              <xsl:value-of select="."/>
            </b>
          </a>
        </xsl:otherwise>
      </xsl:choose>
    </p>
  </xsl:template>
  <!-- TitreLong pour Fiche QR-->
  <xsl:template match="TitreLong" mode="FicheQR">
    <br/>
    <table cellSpacing="0" cellPadding="0" width="100%" border="0">
      <tr>
        <td vAlign="top" width="18">
          <img align="texttop" width="15" height="16" border="0" alt="">
            <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_rouge2.gif</xsl:text></xsl:attribute>
          </img>
        </td>
        <td>
          <h4>
            <font color="#cc3333">
              <xsl:value-of select="."/>
            </font>
          </h4>
        </td>
      </tr>
    </table>
    <br/>
  </xsl:template>
  <!-- Texte -->
  <xsl:template match="Texte" mode="Fiche">
    <xsl:apply-templates mode="Fiche"/>
    <xsl:apply-templates select="/Fiche/FichesLiées" mode="FichesLiees"/>
  </xsl:template>
  <!-- Chapitre -->
  <xsl:template match="Chapitre" mode="Fiche">
    <xsl:apply-templates mode="Fiche"/>
    <br/>
  </xsl:template>
  <!-- SousChapitre -->
  <xsl:template match="SousChapitre" mode="Fiche">
    <xsl:apply-templates mode="Fiche"/>
  </xsl:template>
  <!-- Titre de Chapitre-->
  <xsl:template match="Chapitre/Titre" mode="Fiche">
    <br/>
    <table cellSpacing="0" cellPadding="0" width="100%" border="0">
      <tr>
        <td width="94%">
          <a>
            <xsl:attribute name="name"><xsl:text>titre</xsl:text><xsl:value-of select="generate-id()"/></xsl:attribute>
            <font color="#000000">
              <b>
                <xsl:apply-templates mode="Fiche"/>
              </b>
            </font>
          </a>
        </td>
        <xsl:if test="contains(/Fiche/Type/Nom, 'sommaire')">
          <td width="6%">
	    <!--
            <a href="#DebutGroupe">
              <img height="16" alt="Début de page" width="27" border="0">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/fleche_haut.gif</xsl:text></xsl:attribute>
              </img>
            </a>
	    -->
          </td>
        </xsl:if>
      </tr>
    </table>
  </xsl:template>
  <!-- Titre de SousChapitre -->
  <xsl:template match="SousChapitre/Titre" mode="Fiche">
    <br/>
    <b>
      <xsl:apply-templates mode="Fiche"/>
    </b>
  </xsl:template>
  <!-- Paragraphe -->
  <xsl:template match="Paragraphe" mode="Fiche">
    <p class="p_justify">
      <xsl:choose>
        <xsl:when test="(name(..) = 'Chapitre' or name(..) = 'SousChapitre') and position()=2">
          <xsl:choose>
            <xsl:when test="../@type='note'">
              <img width="26" height="26" align="absbottom" alt="Note">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/l_stylo1.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../@type='attention'">
              <img width="26" height="26" align="absbottom" alt="Attention !">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/l_excla.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../@type='info'">
              <img width="26" height="26" align="absbottom" alt="Information">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/ampoule.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../@type='savoir'">
              <img width="26" height="26" align="absbottom" alt="Bon à savoir">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/fleche-orange.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
          </xsl:choose>
        </xsl:when>
        <xsl:when test="name(..) = 'Titre' and position()=2">
          <xsl:choose>
            <xsl:when test="../../@type='note'">
              <img width="26" height="26" align="absbottom" alt="Note">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/l_stylo1.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../../@type='attention'">
              <img width="26" height="26" align="absbottom" alt="Attention !">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/l_excla.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../../@type='info'">
              <img width="26" height="26" align="absbottom" alt="Information">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/ampoule.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
            <xsl:when test="../../@type='savoir'">
              <img width="26" height="26" align="absbottom" alt="Bon à savoir">
                <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/fleche-orange.gif</xsl:text></xsl:attribute>
              </img>
            </xsl:when>
          </xsl:choose>
        </xsl:when>
      </xsl:choose>
      <xsl:apply-templates/>
      <xsl:text>&#xA0;</xsl:text>
    </p>
  </xsl:template>
  <!-- Liste -->
  <xsl:template match="Liste" mode="Fiche">
    <ul>
      <xsl:apply-templates mode="Fiche"/>
    </ul>
  </xsl:template>
  <!-- Item -->
  <xsl:template match="Item" mode="Fiche">
    <li>
      <xsl:apply-templates mode="Fiche"/>
    </li>
  </xsl:template>
  <xsl:template match="Tableau" mode="Fiche">
    <table border="1" cellspacing="0" bordercolor="black">
      <tbody>
        <xsl:apply-templates mode="Fiche"/>
      </tbody>
    </table>
  </xsl:template>
  <!-- Rangée -->
  <xsl:template match="Rangée" mode="Fiche">
    <tr>
      <xsl:apply-templates mode="Fiche"/>
    </tr>
  </xsl:template>
  <!-- Cellule -->
  <xsl:template match="Cellule" mode="Fiche">
    <xsl:choose>
      <xsl:when test="../@type='Entete'">
        <th>
          <xsl:apply-templates mode="Fiche"/>
        </th>
      </xsl:when>
      <xsl:otherwise>
        <td>
          <xsl:apply-templates mode="Fiche"/>
        </td>
      </xsl:otherwise>
    </xsl:choose>  
  </xsl:template>
  <!-- FichesLiées-->
  <xsl:template match="FichesLiées" mode="FichesLiees">
    <p>&#xA0;</p>
    <p>
      <b>
        Voir aussi :
      </b>
    </p>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
       <td width="4%">&#xA0;</td>
       <td width="96%">&#xA0;</td>
      </tr>
      <xsl:apply-templates mode="FichesLiees"/>
   </table>
   <p>&#xA0;</p>
 </xsl:template>
 <xsl:template match="LienFiche" mode="FichesLiees">
   <tr>
     <td width="4%">
       <xsl:choose>
        <xsl:when test="contains(@type, 'Question-réponse')">
       <img width="15" height="16" align="texttop" alt="">
         <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/puce_rouge2.gif</xsl:text></xsl:attribute>
       </img>
       </xsl:when>
       <xsl:otherwise>
       <img width="15" height="16" align="texttop" alt="">
         <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/chapitre_ferme.gif</xsl:text></xsl:attribute>
       </img>
       </xsl:otherwise>
      </xsl:choose>
     </td>
     <td width="96%">
       <a class="lien">
         <xsl:attribute name="href"><xsl:value-of select="$REFERER"/>xml=<xsl:value-of select="@lien"/><xsl:text>.xml&#x26;xsl=Fiche.xsl</xsl:text></xsl:attribute>
         <xsl:value-of select="Titre"/>
       </a>
     </td>
   </tr>
 </xsl:template>
</xsl:stylesheet>
