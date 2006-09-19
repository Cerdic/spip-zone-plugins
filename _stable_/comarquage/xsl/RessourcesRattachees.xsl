<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl">
<xsl:output method="html" encoding="UTF-8" cdata-section-elements="script" indent="yes"/> 
  <!-- RessourcesRattachées -->
  <xsl:template match="RessourcesRattachées" mode="RessourcesRattachees">
    <xsl:comment><xsl:value-of select="RessourceRattachée/@lien"/></xsl:comment>
    <xsl:if test="count(RessourceRattachée[@type='Définition de glossaire'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_b">Définitions</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_b">
              <xsl:apply-templates select="RessourceRattachée[@type='Définition de glossaire']" mode="Ressource_Definition_glossaire">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
    <xsl:if test="count(RessourceRattachée[@type='Site Web'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_o">Pour en savoir plus</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_o">
              <tr>
                <td colspan="2" align="left" valign="baseline">
                  <b>
                    <font color="#000000">Sites internet publics</font>
                  </b>
                </td>
              </tr>
              <xsl:apply-templates select="RessourceRattachée[@type='Site Web']" mode="Ressource_Web">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
    <xsl:if test="count(RessourceRattachée[@type='Téléservice'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_b">Démarches en ligne</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_b">
              <xsl:apply-templates select="RessourceRattachée[@type='Téléservice']" mode="Ressource_Teleservice">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
    <xsl:if test="count(RessourceRattachée[@type='Formulaire'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_b">Formulaires</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_b">
              <xsl:apply-templates select="RessourceRattachée[@type='Formulaire']" mode="Ressource_Formulaire">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
    <xsl:if test="count(RessourceRattachée[@type='Texte de référence'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_b">Textes de référence</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_b">
              <xsl:apply-templates select="RessourceRattachée[@type='Texte de référence']" mode="Ressource_Texte_Reference">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
    <xsl:if test="count(RessourceRattachée[@type='Référent de calcul'])>0">
      <table cellSpacing="0" cellPadding="0" width="100%" hspace="0" vspace="0">
        <tr valign="top">
          <td>
            <div class="box_sp_titre_b">Montants</div>
          </td>
        </tr>
        <tr valign="top">
          <td>
            <table class="box_sp_b">
              <xsl:apply-templates select="RessourceRattachée[@type='Référent de calcul']" mode="Ressource_Referent_de_calcul">
                <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
              </xsl:apply-templates>
            </table>
          </td>
        </tr>
      </table>
      <br/>
    </xsl:if>
  </xsl:template>
  <xsl:template match="RessourcesRattachées" mode="OrganismesLies">
    <xsl:if test="count(RessourceRattachée[@type='Organisme national'])>0 or count(RessourceRattachée[@type='Organisme local Web'])>0 or count(RessourceRattachée[@type='Organisme local SPL'])>0">
      <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <xsl:if test="count(RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'démarche')])>0">
        <tr valign="bottom"> 
          <td>
             
          </td>
        </tr>
        <tr valign="bottom"> 
          <td>
            <b>Pour accomplir la démarche, les coordonnées utiles : </b>
          </td>
        </tr>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'démarche') and not(boolean(CommentaireDiffusion))]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'démarche') and boolean(CommentaireDiffusion)]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        </xsl:if>
        <xsl:if test="count(RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'informer')])>0">
        <tr valign="bottom"> 
          <td>
             
          </td>
        </tr>
        <tr valign="bottom"> 
          <td>
            <b>Pour plus d'information, les services à contacter : </b>
          </td>
        </tr>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'informer') and not(boolean(CommentaireDiffusion))]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'informer') and boolean(CommentaireDiffusion)]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        </xsl:if>
        <xsl:if test="count(RessourceRattachée[starts-with(@type, 'Organisme') and not(boolean(@typologieDémarche))])>0">
          <xsl:if test="count(RessourceRattachée[starts-with(@type, 'Organisme') and contains(@typologieDémarche, 'informer')])=0">
            <tr valign="bottom"> 
              <td>
                 
              </td>
            </tr>
            <tr valign="bottom"> 
              <td>
                <b>Pour plus d'information, les services à contacter : </b>
              </td>
            </tr>
          </xsl:if>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and not(boolean(@typologieDémarche)) and not(boolean(CommentaireDiffusion))]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        <xsl:apply-templates select="RessourceRattachée[starts-with(@type, 'Organisme') and not(boolean(@typologieDémarche)) and boolean(CommentaireDiffusion)]" mode="Ressource_Organismes">
          <xsl:sort select="@type" order="descending"/>
          <xsl:sort select="@positionPrésentation" data-type="number" order="ascending"/>
        </xsl:apply-templates>
        </xsl:if>
      </table>
      <br/>
    </xsl:if>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Avertissement">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Avertissement"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Web">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Web"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Teleservice">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Teleservice"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Formulaire">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Formulaire"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Texte_Reference">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Texte_Reference"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Referent_de_calcul">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Organismes">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <tr>
      <td width="552">
        <img width="1" height="2" alt="">
          <xsl:attribute name="src"><xsl:value-of select="$IMAGESURL"/><xsl:text>/trsp.gif</xsl:text></xsl:attribute>
        </img>
      </td>
    </tr>
    <tr bgcolor="#EEF0FB">
      <td>
        <table border="0" cellpadding="0" cellspacing="6" width="100%">
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <xsl:choose>
                  <xsl:when test="@type='Organisme local SPL'">
                    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Organisme_local_SPL">
                      <xsl:with-param name="commentaire" select="CommentaireDiffusion"></xsl:with-param>
                    </xsl:apply-templates>
                  </xsl:when>
                  <xsl:when test="@type='Organisme local Web'">
                    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Organisme_local_Web">
                      <xsl:with-param name="commentaire" select="CommentaireDiffusion"></xsl:with-param>
                    </xsl:apply-templates>
                  </xsl:when>
                  <xsl:when test="@type='Organisme national'">
                    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Organisme_national">
                      <xsl:with-param name="commentaire" select="CommentaireDiffusion"></xsl:with-param>
                    </xsl:apply-templates>
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource">
                      <xsl:with-param name="commentaire" select="CommentaireDiffusion"></xsl:with-param>
                    </xsl:apply-templates>
                  </xsl:otherwise>
                </xsl:choose>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Lettre_type">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <table cellSpacing="0" cellPadding="0" width="98%" border="0" hspace="0" vspace="0">
      <tr align="Left" bgColor="#fbf2dd">
        <td vAlign="baseline">
          <h4>
            <font color="#000000">
              <xsl:value-of select="@type"/>
            </font>
          </h4>
        </td>
      </tr>
      <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource_Lettre_type"/>
      <br/>
    </table>
    <br/>
    <br/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource_Definition_glossaire">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Ressource">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <xsl:apply-templates select="document($lien)/Ressource" mode="Ressource"/>
  </xsl:template>
  <!-- RessourceRattachée-->
  <xsl:template match="RessourceRattachée" mode="Fiche">
    <xsl:variable name="lien">
      <xsl:text>../xml/</xsl:text>
      <xsl:value-of select="@lien"/>
      <xsl:text>.xml</xsl:text>
    </xsl:variable>
    <table cellSpacing="0" cellPadding="4" width="98%" border="0" hspace="0" vspace="0">
      <tr align="Left" bgColor="#fbf2dd">
        <td vAlign="baseline">
          <h4>
            <font color="#000000">
              <xsl:value-of select="@type"/>
            </font>
          </h4>
        </td>
      </tr>
      <tr align="left">
        <td vAlign="baseline">
          <xsl:apply-templates select="document($lien)/Ressource/Texte" mode="Fiche"/>
        </td>
      </tr>
      <br/>
    </table>
    <br/>
    <br/>
  </xsl:template>
</xsl:stylesheet>
