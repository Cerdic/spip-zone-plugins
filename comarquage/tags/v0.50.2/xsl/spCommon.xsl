<?xml version="1.0" encoding="ISO-8859-15"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    exclude-result-prefixes="xsl dc">

    <xsl:output method="html" encoding="ISO-8859-15" cdata-section-elements="script" indent="yes"/>

    <!-- Affiche la barre des thèmes principaux -->
    <xsl:template name="getBarreThemes">
        <xsl:if test="$AFF_BARRE_THEME = 'true'">
            <xsl:choose>
                <xsl:when test="$CATEGORIE = 'part'">
                    <xsl:call-template name="createBarreThemes"/>
                </xsl:when>
                <xsl:when test="$CATEGORIE = 'pro'">
                    <xsl:call-template name="createBarreThemes"/>
                </xsl:when>
                <xsl:when test="$CATEGORIE = 'pivots'">
                    <xsl:call-template name="createBarreThemes"/>
                </xsl:when>
            </xsl:choose>
        </xsl:if>
    </xsl:template>

    <xsl:template name="createBarreThemes">
        <xsl:variable name="file">
            <xsl:value-of select="$XML_COURANT"/>
            <xsl:value-of select="$THEME_ARBORESCENCE"/>
            <xsl:text>.xml</xsl:text>
        </xsl:variable>
        <xsl:variable name="noeud">
            <xsl:value-of select="$ACCUEIL_CFS"/>
        </xsl:variable>
        <div class="spBarreThemes">
            <span class="spFilDArianeIci">
                <xsl:text>Thématiques principales :</xsl:text>
            </span>
            <br/>
            <xsl:for-each select="document($file)/Arborescence/Item[@type='Theme']">
                <div class="spBarreThemesFils">
                    <a xml:lang="fr" lang="fr">
                        <xsl:variable name="type_xsl" select="substring(@ID, 1, 1)"/>
                        <xsl:variable name="file_xsl">
                            <xsl:choose>
                                <xsl:when test="$type_xsl = 'N'">
                                    <xsl:value-of select="'spMainNoeud.xsl'" />
                                </xsl:when>
                                <xsl:when test="$type_xsl = 'F'">
                                    <xsl:value-of select="'spMainFiche.xsl'" />
                                </xsl:when>
                                <xsl:when test="$type_xsl = 'R'">
                                    <xsl:value-of select="'spMainRessource.xsl'" />
                                </xsl:when>
                            </xsl:choose>
                        </xsl:variable>
                        <xsl:attribute name="href">
                            <xsl:value-of select="$HYPERLIEN_COURANT"/>
                            <xsl:text>xml=</xsl:text>
                            <xsl:value-of select="@ID"/>
                            <xsl:text>.xml</xsl:text>
                            <xsl:text>&#x26;xsl=</xsl:text>
                            <xsl:value-of select="$file_xsl"/>
                        </xsl:attribute>
                        <xsl:attribute name="title">
                            <xsl:value-of select="normalize-space(Titre)"/>
                        </xsl:attribute>
                        <xsl:call-template name="imageOfATheme">
                            <xsl:with-param name="id" select="@ID"/>
                        </xsl:call-template>
                        <xsl:value-of select="normalize-space(Titre)"/>
                    </a>
                </div>
            </xsl:for-each>
        </div>
    </xsl:template>

    <!-- Création du lien vers une autre publication -->
    <xsl:template name="getPublicationLink">
        <xsl:param name="href"/>
        <xsl:param name="title"/>
        <xsl:param name="text"/>
        <xsl:param name="lang" select="'fr'"/>
        <xsl:variable name="hyperlien">
            <xsl:choose>
                <xsl:when test="@audience='Particuliers'">
                    <xsl:value-of select="$HYPERLIEN_PART"/>
                </xsl:when>
                <xsl:when test="Audience='Particuliers'">
                    <xsl:value-of select="$HYPERLIEN_PART"/>
                </xsl:when>
                <xsl:when test="@audience='Associations'">
                    <xsl:value-of select="$HYPERLIEN_ASSO"/>
                </xsl:when>
                <xsl:when test="Audience='Associations'">
                    <xsl:value-of select="$HYPERLIEN_ASSO"/>
                </xsl:when>
                <xsl:when test="@audience='Professionnels'">
                    <xsl:value-of select="$HYPERLIEN_PRO"/>
                </xsl:when>
                <xsl:when test="Audience='Professionnels'">
                    <xsl:value-of select="$HYPERLIEN_PRO"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$HYPERLIEN_COURANT"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$href = 'Theme'">
                <a href="{$hyperlien}" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:copy-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'Dossiers'">
                <a href="{$hyperlien}xml=dossiers.xml&#x26;xsl=spMainDossiers.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'Glossaire'">
                <a href="{$hyperlien}xml=glossaire.xml&#x26;xsl=spMainGlossaire.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'Ressources'">
                <a href="{$hyperlien}xml=ressources.xml&#x26;xsl=spMainRessource.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'CentresDeContact'">
                <a href="{$hyperlien}xml=centresDeContact.xml&#x26;xsl=spMainCentresDeContact.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'RessourcesEnLigne'">
                <a href="{$hyperlien}xml=ressourcesEnLigne.xml&#x26;xsl=spMainRessourcesEnLigne.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="$href = 'Annuaire'">
                <a href="{$hyperlien}xml=annuaire.xml&#x26;xsl=spMainAnnuaire.xsl" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:when test="contains($href,'Annuaire-')">
                <a href="{$hyperlien}-{$href}" class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                    </xsl:attribute>
                    <xsl:value-of select="$text"/>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <xsl:variable name="linkTitle">
                    <xsl:value-of select="normalize-space($text)"/>
                </xsl:variable>
                <xsl:variable name="audienceTitle">
                    <xsl:choose>
                        <xsl:when test="@audience">
                            <xsl:call-template name="lowerCase">
                                <xsl:with-param name="string" select="@audience"/>
                            </xsl:call-template>
                        </xsl:when>
                        <xsl:when test="Audience">
                            <xsl:call-template name="lowerCase">
                                <xsl:with-param name="string" select="Audience"/>
                            </xsl:call-template>
                        </xsl:when>
                        <xsl:otherwise><xsl:text></xsl:text></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <xsl:variable name="type_xsl" select="substring($href, 1, 1)"/>
                <xsl:variable name="file_xsl">
                    <xsl:choose>
                        <xsl:when test="$type_xsl = 'N'">
                            <xsl:value-of select="'spMainNoeud.xsl'" />
                        </xsl:when>
                        <xsl:when test="$type_xsl = 'F'">
                            <xsl:value-of select="'spMainFiche.xsl'" />
                        </xsl:when>
                        <xsl:when test="$type_xsl = 'R'">
                            <xsl:value-of select="'spMainRessource.xsl'" />
                        </xsl:when>
                    </xsl:choose>
                </xsl:variable>
                <a class="spLienInterne" xml:lang="{$lang}" lang="{$lang}">
                    <xsl:attribute name="title">
                        <xsl:value-of select="normalize-space($title)"/>
                        <xsl:if test="$audienceTitle != ''">
                            <xsl:text> (guide des droits et démarches des </xsl:text>
                            <xsl:value-of select="$audienceTitle"/>
                            <xsl:text>)</xsl:text>
                        </xsl:if>
                    </xsl:attribute>
                    <xsl:attribute name="href">
                        <xsl:value-of select="$hyperlien"/>
                        <xsl:text>xml=</xsl:text>
                        <xsl:value-of select="$href"/>
                        <xsl:text>.xml</xsl:text>
                        <xsl:text>&#x26;xsl=</xsl:text>
                        <xsl:value-of select="$file_xsl"/>
                    </xsl:attribute>
                    <xsl:copy-of select="$text"/>
                </a>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Création du lien vers un autre site -->
    <xsl:template name="getSiteLink">
        <xsl:param name="href"/>
        <xsl:param name="title"/>
        <xsl:param name="text"/>
        <xsl:param name="lang" select="'fr'"/>
        <a rel="nofollow" class="spLienExterne" xml:lang="{$lang}" lang="{$lang}">
            <xsl:attribute name="title">
                <xsl:value-of select="normalize-space($title)"/>
            </xsl:attribute>
            <xsl:attribute name="href">
                <xsl:value-of select="normalize-space($href)"/>
            </xsl:attribute>
            <xsl:value-of select="normalize-space($text)"/>
        </a>
    </xsl:template>

    <!-- Renvoie la description d'une publication -->
    <xsl:template name="getDescription">
        <xsl:param name="id"/>
        <xsl:variable name="file">
            <xsl:value-of select="$XML_COURANT"/>
            <xsl:value-of select="$id"/>
            <xsl:text>.xml</xsl:text>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="boolean(document($file))">
                <xsl:variable name="desc">
                    <xsl:value-of select="normalize-space(document($file)/*/dc:description)"/>
                </xsl:variable>
                <xsl:choose>
                    <xsl:when test="substring($desc,string-length($desc)-1) = '.'">
                        <xsl:value-of select="$desc"/>
                        <xsl:text>..</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="$desc"/>
                        <xsl:text>...</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text>...</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Renvoie le titre d'une publication -->
    <xsl:template name="getTitle">
        <xsl:param name="id"/>
        <xsl:variable name="file">
            <xsl:value-of select="$XML_COURANT"/>
            <xsl:value-of select="$id"/>
            <xsl:text>.xml</xsl:text>
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="boolean(document($file))">
                <xsl:value-of select="document($file)/*/dc:title"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text>...</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Renvoie la date d'une publication -->
    <xsl:template name="getDate">
        <xsl:variable name="date">
            <xsl:value-of select="substring(/Publication/dc:date,10,10)"/>
        </xsl:variable>
        <xsl:variable name="year">
            <xsl:value-of select="substring($date,0,5)"/>
        </xsl:variable>
        <xsl:variable name="monthInt">
            <xsl:value-of select="substring($date,6,2)"/>
        </xsl:variable>
        <xsl:variable name="dayInt">
            <xsl:value-of select="substring($date,9,2)"/>
        </xsl:variable>
        <div class="spPublicationDate">
            <xsl:text>Mis à jour le </xsl:text>
            <xsl:choose>
                <xsl:when test="contains($dayInt,'0')">
                    <xsl:value-of select="substring($dayInt,2)"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="$dayInt"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:call-template name="frenchMonth">
                <xsl:with-param name="month" select="$monthInt"/>
            </xsl:call-template>
            <xsl:value-of select="$year"/>
            <br/>
            <xsl:text>par « </xsl:text>
            <xsl:call-template name="lowerCase">
                <xsl:with-param name="string">
                    <xsl:value-of select="/Publication/dc:publisher"/>
                </xsl:with-param>
            </xsl:call-template>
            <xsl:text> »</xsl:text>
        </div>
    </xsl:template>

    <!-- Transforme une date au format RSS sous forme d'une chaîne de caractères -->
    <xsl:template name="transformRssDate">
        <xsl:param name="date"/>
        <xsl:choose>
            <xsl:when test="contains($date,'Z')">
                <xsl:variable name="onlyDate">
                    <xsl:value-of select="substring-before($date,'T')"/>
                </xsl:variable>
                <xsl:variable name="year">
                    <xsl:value-of select="substring-before($onlyDate,'-')"/>
                </xsl:variable>
                <xsl:variable name="month">
                    <xsl:value-of select="substring-before(substring-after($onlyDate,'-'),'-')"/>
                </xsl:variable>
                <xsl:variable name="day">
                    <xsl:value-of select="substring-after(substring-after($onlyDate,'-'),'-')"/>
                </xsl:variable>
                <xsl:value-of select="$day"/>
                <xsl:choose>
                    <xsl:when test="$month ='01'"><xsl:text> janvier </xsl:text></xsl:when>
                    <xsl:when test="$month ='02'"><xsl:text> février </xsl:text></xsl:when>
                    <xsl:when test="$month ='03'"><xsl:text> mars </xsl:text></xsl:when>
                    <xsl:when test="$month ='04'"><xsl:text> avril </xsl:text></xsl:when>
                    <xsl:when test="$month ='05'"><xsl:text> mai </xsl:text></xsl:when>
                    <xsl:when test="$month ='06'"><xsl:text> juin </xsl:text></xsl:when>
                    <xsl:when test="$month ='07'"><xsl:text> juillet </xsl:text></xsl:when>
                    <xsl:when test="$month ='08'"><xsl:text> août </xsl:text></xsl:when>
                    <xsl:when test="$month ='09'"><xsl:text> septembre </xsl:text></xsl:when>
                    <xsl:when test="$month ='10'"><xsl:text> octobre </xsl:text></xsl:when>
                    <xsl:when test="$month ='11'"><xsl:text> novembre </xsl:text></xsl:when>
                    <xsl:when test="$month ='12'"><xsl:text> décembre </xsl:text></xsl:when>
                </xsl:choose>
                <xsl:value-of select="$year"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:variable name="onlyDate">
                    <xsl:value-of select="substring-after($date,', ')"/>
                </xsl:variable>
                <xsl:variable name="day">
                    <xsl:value-of select="substring-before($onlyDate,' ')"/>
                </xsl:variable>
                <xsl:variable name="month">
                    <xsl:value-of select="substring-before(substring-after($onlyDate,' '),' ')"/>
                </xsl:variable>
                <xsl:variable name="year">
                    <xsl:value-of select="substring-before(substring-after(substring-after($onlyDate,' '),' '),' ')"/>
                </xsl:variable>
                <xsl:value-of select="$day"/>
                <xsl:choose>
                    <xsl:when test="$month ='Jan'"><xsl:text> janvier </xsl:text></xsl:when>
                    <xsl:when test="$month ='Feb'"><xsl:text> février </xsl:text></xsl:when>
                    <xsl:when test="$month ='Mar'"><xsl:text> mars </xsl:text></xsl:when>
                    <xsl:when test="$month ='Apr'"><xsl:text> avril </xsl:text></xsl:when>
                    <xsl:when test="$month ='May'"><xsl:text> mai </xsl:text></xsl:when>
                    <xsl:when test="$month ='Jun'"><xsl:text> juin </xsl:text></xsl:when>
                    <xsl:when test="$month ='Jul'"><xsl:text> juillet </xsl:text></xsl:when>
                    <xsl:when test="$month ='Aug'"><xsl:text> août </xsl:text></xsl:when>
                    <xsl:when test="$month ='Sep'"><xsl:text> septembre </xsl:text></xsl:when>
                    <xsl:when test="$month ='Oct'"><xsl:text> octobre </xsl:text></xsl:when>
                    <xsl:when test="$month ='Nov'"><xsl:text> novembre </xsl:text></xsl:when>
                    <xsl:when test="$month ='Dec'"><xsl:text> décembre </xsl:text></xsl:when>
                </xsl:choose>
                <xsl:value-of select="$year"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Renvoie l'image associée à un thème principal -->
    <xsl:template name="imageOfATheme">
        <xsl:param name="id"/>
        <xsl:param name="class"/>
        <xsl:if test="$AFF_IMAGES = 'true'">

            <xsl:choose>
                <xsl:when test="$CATEGORIE = 'part'">
                    <img width="50" height="50" alt="">
                        <xsl:if test="$class">
                            <xsl:attribute name="class">
                                <xsl:value-of select="$class"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:attribute name="src">
                            <xsl:value-of select="$IMAGES"/>
                            <xsl:choose>

                                <xsl:when test="$id = 'N19803'"><xsl:text>vdd/N19803.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19804'"><xsl:text>vdd/N19804.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19805'"><xsl:text>vdd/N19805.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19806'"><xsl:text>vdd/N19806.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19807'"><xsl:text>vdd/N19807.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19808'"><xsl:text>vdd/N19808.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19809'"><xsl:text>vdd/N19809.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19810'"><xsl:text>vdd/N19810.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19811'"><xsl:text>vdd/N19811.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N19812'"><xsl:text>vdd/N19812.png</xsl:text></xsl:when>

                                <xsl:when test="$id = 'F14128'"><xsl:text>comment-faire-si/F14128.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F16225'"><xsl:text>comment-faire-si/F16225.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F17556'"><xsl:text>comment-faire-si/F17556.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F14485'"><xsl:text>comment-faire-si/F14485.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F16507'"><xsl:text>comment-faire-si/F16507.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F1700'"><xsl:text>comment-faire-si/F1700.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F15913'"><xsl:text>comment-faire-si/F15913.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F17904'"><xsl:text>comment-faire-si/F17904.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F17649'"><xsl:text>comment-faire-si/F17649.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F3109'"><xsl:text>comment-faire-si/F3109.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F21829'"><xsl:text>comment-faire-si/F21829.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F601'"><xsl:text>comment-faire-si/F601.png</xsl:text></xsl:when>
                                <xsl:otherwise><xsl:text>comment-faire-si/comment-faire-si.jpg</xsl:text></xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:when test="$CATEGORIE = 'asso'">
                    <img width="50" height="50" alt="">
                        <xsl:if test="$class">
                            <xsl:attribute name="class">
                                <xsl:value-of select="$class"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:attribute name="src">
                            <xsl:value-of select="$IMAGES"/>
                            <xsl:text>vdd/N20.png</xsl:text>
                        </xsl:attribute>
                    </img>
                </xsl:when>
                <xsl:when test="$CATEGORIE = 'pro'">
                    <img width="50" height="50" alt="">
                        <xsl:if test="$class">
                            <xsl:attribute name="class">
                                <xsl:value-of select="$class"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:attribute name="src">
                            <xsl:value-of select="$IMAGES"/>
                            <xsl:choose>

                                <xsl:when test="$id = 'N24264'"><xsl:text>vdd/N24264.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N24265'"><xsl:text>vdd/N24265.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N24266'"><xsl:text>vdd/N24266.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N24267'"><xsl:text>vdd/N24267.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N24268'"><xsl:text>vdd/N24268.png</xsl:text></xsl:when>
                                <xsl:when test="$id = 'N24269'"><xsl:text>vdd/N24269.png</xsl:text></xsl:when>

                                <xsl:when test="$id = 'F23961'"><xsl:text>comment-faire-si/comment-faire-si.jpg</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F23697'"><xsl:text>comment-faire-si/comment-faire-si.jpg</xsl:text></xsl:when>
                                <xsl:when test="$id = 'F23571'"><xsl:text>comment-faire-si/comment-faire-si.jpg</xsl:text></xsl:when>
                                <xsl:otherwise><xsl:text>agri-peche.png</xsl:text></xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                    </img>
                </xsl:when>
            </xsl:choose>

        </xsl:if>
        <xsl:text> </xsl:text>
    </xsl:template>

    <!-- Renvoie l'image associée à une partie de la publication -->
    <xsl:template name="imageOfAPartie">
        <xsl:param name="nom"/>
        <xsl:param name="class" select="'entiteImageFloatLeft'"/>
        <xsl:if test="$AFF_IMAGES = 'true'">
            <img width="50" height="50" class="{$class}">
                <xsl:attribute name="alt">
                    <xsl:value-of select="$nom"/>
                </xsl:attribute>
                <xsl:attribute name="src">
                    <xsl:value-of select="$IMAGES"/>
                    <xsl:value-of select="$nom"/>
                    <xsl:text>.jpg</xsl:text>
                </xsl:attribute>
            </img>
        </xsl:if>
        <xsl:text> </xsl:text>
    </xsl:template>

    <xsl:template name="createSommaire">
        <xsl:variable name="nbSomItems">
            <xsl:value-of select="count(Texte/Chapitre)+count(CommentFaireSi)
                +count(SousTheme)+count(SousDossier)+count(Dossier)+count(Fiche)
                +count(InformationComplementaire)+count(LienExterneCommente)
                +count(Montant)+count(Partenaire)+count(PourEnSavoirPlus)
                +count(QuestionReponse)+count(Reference)
                +count(ServiceEnLigne)+count(SiteInternetPublic)
                +count(VoirAussi)+count(Actualite)+count(OuSAdresser)"/>
        </xsl:variable>
        <xsl:if test="$nbSomItems > 0">
            <div class="spPublicationSommaire">
<!--
                <xsl:call-template name="imageOfAPartie">
                    <xsl:with-param name="nom">sommaire</xsl:with-param>
                    <xsl:with-param name="class" select="'entiteImageFloatRight'"/>
                </xsl:call-template>
-->
                <h2>Sommaire</h2>
                <ul class="spPublicationSommaire">
                    <xsl:for-each select="Texte/Chapitre">
                        <xsl:if test="Titre">
                            <xsl:variable name="title">
                                <xsl:value-of select="../../dc:title"/>
                                <xsl:value-of select="$sepFilDAriane"/>
                                <xsl:value-of select="normalize-space(Titre)"/>
                            </xsl:variable>
                            <li class="spPublicationSommaire">
                                <h3>
                                    <a title="{$title}">
                                        <xsl:attribute name="href">
                                            <xsl:text>#</xsl:text>
                                            <xsl:call-template name="createChapitreId"/>
                                        </xsl:attribute>
                                        <xsl:call-template name="string-replace">
                                            <xsl:with-param name="string"><xsl:value-of select="translate(Titre,':','')"/></xsl:with-param>
                                        </xsl:call-template>
                                    </a>
                                </h3>
                            </li>
                        </xsl:if>
                    </xsl:for-each>
                    <xsl:if test="count(CommentFaireSi)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:value-of select="$TEXTE_CFS"/>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-comment-faire</xsl:text>
                                    </xsl:attribute>
                                    <xsl:value-of select="$TEXTE_CFS"/>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(SousTheme)">
                        <xsl:for-each select="SousTheme">
                            <xsl:variable name="title">
                                <xsl:value-of select="dc:title"/>
                                <xsl:value-of select="$sepFilDAriane"/>
                                <xsl:value-of select="Titre"/>
                            </xsl:variable>
                            <li class="spPublicationSommaire">
                                <h3>
                                    <a title="{$title}">
                                        <xsl:attribute name="href">
                                            <xsl:text>#</xsl:text>
                                            <xsl:call-template name="createSousThemeId"/>
                                        </xsl:attribute>
                                        <xsl:value-of select="Titre"/>
                                    </a>
                                </h3>
                            </li>
                        </xsl:for-each>
                    </xsl:if>
                    <xsl:if test="count(SousDossier)">
                        <xsl:for-each select="SousDossier">
                            <xsl:variable name="title">
                                <xsl:value-of select="dc:title"/>
                                <xsl:value-of select="$sepFilDAriane"/>
                                <xsl:value-of select="Titre"/>
                            </xsl:variable>
                            <li class="spPublicationSommaire">
                                <h3>
                                    <a title="{$title}">
                                        <xsl:attribute name="href">
                                            <xsl:text>#</xsl:text>
                                            <xsl:call-template name="createSousDossierId"/>
                                        </xsl:attribute>
                                        <xsl:value-of select="Titre"/>
                                    </a>
                                </h3>
                            </li>
                        </xsl:for-each>
                    </xsl:if>
                    <xsl:if test="count(Dossier)+count(Fiche)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>En lien</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-informations</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Articles connexes</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(InformationComplementaire)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Informations complémentaires</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-information-complementaire</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Informations complémentaires</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(LienExterneCommente)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Liens externes</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-lien-externe</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Liens externes</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(Montant)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Montants</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-montant</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Montants</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(Partenaire)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Partenaires</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-partenaire</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Partenaires</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(PourEnSavoirPlus)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Pour en savoir plus</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-pour-en-savoir-plus</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Pour en savoir plus</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(QuestionReponse)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Questions - réponses</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-question-reponse</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Questions - réponses</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(Reference)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Références</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-reference</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Références</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(ServiceEnLigne)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Services en ligne</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-service-en-ligne</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Services en ligne</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(SiteInternetPublic)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Sites internet publics</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-site-internet-public</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Sites internet publics</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(VoirAussi)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Voir aussi</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-voir-aussi</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Voir aussi</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(Actualite)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Actualités</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-actualite</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Actualités</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                    <xsl:if test="count(OuSAdresser)">
                        <xsl:variable name="title">
                            <xsl:value-of select="dc:title"/>
                            <xsl:value-of select="$sepFilDAriane"/>
                            <xsl:text>Ou s'adresser</xsl:text>
                        </xsl:variable>
                        <li class="spPublicationSommaire">
                            <h3>
                                <a title="{$title}">
                                    <xsl:attribute name="href">
                                        <xsl:text>#sp-ou-sadresser</xsl:text>
                                    </xsl:attribute>
                                    <xsl:text>Ou s'adresser</xsl:text>
                                </a>
                            </h3>
                        </li>
                    </xsl:if>
                </ul>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template name="createChapitreId">
        <xsl:text>sp-chapitre-</xsl:text>
        <xsl:call-template name="lowerCase">
            <xsl:with-param name="string">
                <xsl:call-template name="textWithoutAccent">
                    <xsl:with-param name="string">
                        <xsl:value-of select="Titre/Paragraphe"/>
                    </xsl:with-param>
                </xsl:call-template>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="createSousThemeId">
        <xsl:text>sp-sous-theme-</xsl:text>
        <xsl:call-template name="lowerCase">
            <xsl:with-param name="string">
                <xsl:call-template name="textWithoutAccent">
                    <xsl:with-param name="string">
                        <xsl:value-of select="Titre"/>
                    </xsl:with-param>
                </xsl:call-template>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="createSousDossierId">
        <xsl:text>sp-sous-dossier-</xsl:text>
        <xsl:call-template name="lowerCase">
            <xsl:with-param name="string">
                <xsl:call-template name="textWithoutAccent">
                    <xsl:with-param name="string">
                        <xsl:value-of select="Titre"/>
                    </xsl:with-param>
                </xsl:call-template>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="textWithoutAccent">
        <xsl:param name="string"/>
        <xsl:variable name="stringFrom"> ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ</xsl:variable>
        <xsl:variable name="stringTo">-aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn</xsl:variable>
        <xsl:variable name="stringDel">()"?',:%&#8217;&#8211;</xsl:variable>
        <xsl:variable name="twastring">
            <xsl:value-of select="translate(translate($string,$stringDel,''),$stringFrom,$stringTo)"/>
        </xsl:variable>
        <xsl:call-template name="string-replace">
            <xsl:with-param name="string"><xsl:value-of select="$twastring"/></xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="string-replace">
        <xsl:param name="string"/>
        <xsl:variable name="nstring"><xsl:value-of select="normalize-space($string)"/></xsl:variable>
        <xsl:value-of select="$nstring"/>
    </xsl:template>

    <xsl:template name="upperCase">
        <xsl:param name="string"/>
        <xsl:value-of select="translate($string,'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')"/>
    </xsl:template>

    <xsl:template name="lowerCase">
        <xsl:param name="string"/>
        <xsl:value-of select="translate($string,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>
    </xsl:template>

    <xsl:template name="getMAJDate">
        <xsl:variable name="date"><xsl:value-of select="//*/dc:date"/></xsl:variable>
        <xsl:text>Mis à jour le </xsl:text>
        <xsl:call-template name="transformRssDate">
            <xsl:with-param name="date">
                <xsl:value-of select="substring-after($date,' ')"/>
                <xsl:text>TZ</xsl:text>
            </xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="getMAJDateContributor">
        <xsl:call-template name="getMAJDate"/>
        <xsl:text> - </xsl:text>
        <xsl:value-of select="//*/dc:contributor"/>
    </xsl:template>

    <xsl:template name="ancreTop">
        <div class="clearall">
            <br class="clearall"/>
            <a href="#top" title="Retour vers le haut de la page">
                <img class="entiteImageFloatRight" alt="Retour vers le haut de la page" width="24" height="20">
                    <xsl:attribute name="src">
                        <xsl:value-of select="$IMAGES"/>
                        <xsl:text>pictos/pct-haut.png</xsl:text>
                    </xsl:attribute>
                </img>
            </a>
        </div>
    </xsl:template>

    <xsl:template name="createDossierAzId">
        <xsl:text>sp-dossieraz-</xsl:text>
        <xsl:call-template name="lowerCase">
            <xsl:with-param name="string"><xsl:value-of select="Titre"/></xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <xsl:template name="affDossiers">
        <div class="spTousDossiersAZ">
<!--
            <xsl:call-template name="imageOfAPartie">
                <xsl:with-param name="nom">dossiers</xsl:with-param>
                <xsl:with-param name="class" select="'entiteImageFloatRight'"/>
            </xsl:call-template>
-->
            <h2>De A à Z</h2>
            <ul class="spTousDossiersAZ">
                <li class="spTousDossiersAZ">
                    <xsl:call-template name="getFilDArianeCentresDeContact"/>
                </li>
                <li class="spTousDossiersAZ">
                    <xsl:call-template name="getFilDArianeDossiers"/>
                </li>
                <li class="spTousDossiersAZ">
                    <xsl:call-template name="getFilDArianeGlossaire"/>
                </li>
                <li class="spTousDossiersAZ">
                    <xsl:call-template name="getFilDArianeRessourcesEnLigne"/>
                </li>
            </ul>
        </div>
    </xsl:template>

    <xsl:template name="frenchMonth">
        <xsl:param name="month"/>
        <xsl:choose>
            <xsl:when test="$month = '01'">
                <xsl:text> janvier </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '02'">
                <xsl:text> février </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '03'">
                <xsl:text> mars </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '04'">
                <xsl:text> avril </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '05'">
                <xsl:text> mai </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '06'">
                <xsl:text> juin </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '07'">
                <xsl:text> juillet </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '08'">
                <xsl:text> août </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '09'">
                <xsl:text> septembre </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '10'">
                <xsl:text> octobre </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '11'">
                <xsl:text> novembre </xsl:text>
            </xsl:when>
            <xsl:when test="$month = '12'">
                <xsl:text> décembre </xsl:text>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="affDossierTexte" mode="Noeud-dossier">
        <div class="spNoeudDossierTexte">
            <xsl:apply-templates select="Texte"/>
        </div>
    </xsl:template>

    <xsl:template name="affTexteType">
        <img width="20" height="20" class="entiteImageFloatLeft">
            <xsl:choose>
                <xsl:when test="@type = 'note'">
                    <xsl:attribute name="src">
                        <xsl:value-of select="$IMAGES"/>
                        <xsl:text>note.jpg</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="alt">
                        <xsl:text>A noter</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:when test="@type = 'savoir'">
                    <xsl:attribute name="src">
                        <xsl:value-of select="$IMAGES"/>
                        <xsl:text>pictos/pct-a-savoir.png</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="alt">
                        <xsl:text>A savoir</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:when test="@type = 'attention'">
                    <xsl:attribute name="src">
                        <xsl:value-of select="$IMAGES"/>
                        <xsl:text>pictos/pct-attention.png</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="alt">
                        <xsl:text>Attention</xsl:text>
                    </xsl:attribute>
                </xsl:when>
                <xsl:when test="@type = 'info'">
                    <xsl:attribute name="src">
                        <xsl:value-of select="$IMAGES"/>
                        <xsl:text>pictos/pct-info.png</xsl:text>
                    </xsl:attribute>
                    <xsl:attribute name="alt">
                        <xsl:text>Sachez que</xsl:text>
                    </xsl:attribute>
                </xsl:when>
            </xsl:choose>
        </img>
    </xsl:template>

</xsl:stylesheet>
