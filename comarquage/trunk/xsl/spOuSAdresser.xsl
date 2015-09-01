<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    exclude-result-prefixes="xsl dc">

    <xsl:import href="spPivotLocal.xsl"/>

    <xsl:output method="html" encoding="ISO-8859-15" cdata-section-elements="script" indent="yes"/>

    <xsl:template name="affOuSAdresser">
        <xsl:if test="count(OuSAdresser) > 0">
            <div class="spPublicationOSA" id="sp-ou-sadresser">
                <h2>
<!--
                    <xsl:call-template name="imageOfAPartie">
                        <xsl:with-param name="nom">sadresser</xsl:with-param>
                    </xsl:call-template>
-->
                    <xsl:text>O� s'adresser ?</xsl:text>
                </h2>
                <xsl:apply-templates select="OuSAdresser[@type='Centre de contact']" mode="web"/>
                <xsl:choose>
                    <xsl:when test="$MODE_PIVOT = 'pivot'">
                        <xsl:apply-templates select="OuSAdresser[@type!='Centre de contact']" mode="pivot"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="OuSAdresser[@type!='Centre de contact']" mode="web"/>
                    </xsl:otherwise>
                </xsl:choose>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template match="OuSAdresser" mode="web">
        <div class="spPublicationPivotOSA">
            <div class="spPublicationPivotOSATitle">
                <xsl:choose>
                    <xsl:when test="RessourceWeb">
                        <h3>
                            <xsl:call-template name="getSiteLink">
                                <xsl:with-param name="href"><xsl:value-of select="RessourceWeb/@URL"/></xsl:with-param>
                                <xsl:with-param name="title"><xsl:value-of select="RessourceWeb/@URL"/></xsl:with-param>
                                <xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
                            </xsl:call-template>
                        </h3>
                    </xsl:when>
                    <xsl:otherwise>
                        <h3><xsl:value-of select="Titre"/></h3>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:if test="Complement">
                    <xsl:text> - </xsl:text>
                    <xsl:value-of select="Complement"/>
                </xsl:if>
            </div>
            <xsl:if test="Texte">
                <xsl:apply-templates select="Texte" mode="OuSAdresser"/>
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template match="OuSAdresser" mode="pivot">
        <div class="spPublicationPivotOSA">
            <xsl:variable name="pivot">
                <xsl:text>,</xsl:text>
                <xsl:value-of select="PivotLocal"/>
                <xsl:text>,</xsl:text>
            </xsl:variable>
            <xsl:variable name="file">
                <xsl:value-of select="$PIVOTS"/>
                <xsl:value-of select="PivotLocal"/>
                <xsl:text>.xml</xsl:text>
            </xsl:variable>
            <xsl:choose>
                <xsl:when test="PivotLocal and contains($PIVOTS_ACTIFS,$pivot)">
                    <xsl:variable name="nb">
                        <xsl:value-of select="count(document($file)/PivotLocal/PTA-PL-Adresse) + count(document($file)/PivotLocal/PTA-PL-Communication) + count(document($file)/PivotLocal/PTA-PL-Horaires)"/>
                    </xsl:variable>
                    <div class="spPublicationPivotOSATitle">
                        <h3><xsl:value-of select="Titre"/></h3>
                        <xsl:if test="Complement">
                            <xsl:text> - </xsl:text>
                            <xsl:value-of select="Complement"/>
                        </xsl:if>
                    </div>
                    <xsl:apply-templates select="document($file)/PivotLocal/node()[not(self::PTA-PL-Titre)]">
                        <xsl:with-param name="cssWidth">
                            <xsl:value-of select="floor(100 div $nb)"/>
                        </xsl:with-param>
                    </xsl:apply-templates>
                </xsl:when>
                <xsl:otherwise>
                    <div class="spPublicationPivotOSATitle">
                        <xsl:choose>
                            <xsl:when test="RessourceWeb">
                                <h3>
                                    <xsl:call-template name="getSiteLink">
                                        <xsl:with-param name="href"><xsl:value-of select="RessourceWeb/@URL"/></xsl:with-param>
                                        <xsl:with-param name="title"><xsl:value-of select="RessourceWeb/@URL"/></xsl:with-param>
                                        <xsl:with-param name="text"><xsl:value-of select="Titre"/></xsl:with-param>
                                    </xsl:call-template>
                                </h3>
                            </xsl:when>
                            <xsl:otherwise>
                                <h3><xsl:value-of select="Titre"/></h3>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:if test="Complement">
                            <xsl:text> - </xsl:text>
                            <xsl:value-of select="Complement"/>
                        </xsl:if>
                    </div>
                    <xsl:if test="Texte">
                        <xsl:apply-templates select="Texte" mode="OuSAdresser"/>
                    </xsl:if>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>

    <xsl:template name="affOuSAdresserChapitre">
        <xsl:apply-templates mode="OuSAdresser"/>
    </xsl:template>

</xsl:stylesheet>
