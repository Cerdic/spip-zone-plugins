/*
Jaxe - Editeur XML en Java

Copyright (C) 2002 Observatoire de Paris-Meudon

Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conform�ment aux dispositions de la Licence Publique G�n�rale GNU, telle que publi�e par la Free Software Foundation ; version 2 de la licence, ou encore (� votre choix) toute version ult�rieure.

Ce programme est distribu� dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans m�me la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de d�tail, voir la Licence Publique G�n�rale GNU .

Vous devez avoir re�u un exemplaire de la Licence Publique G�n�rale GNU en m�me temps que ce programme ; si ce n'est pas le cas, �crivez � la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
*/

package jaxe;

import java.lang.reflect.Constructor;

import jaxe.elements.JEDivision;
import jaxe.elements.JEInconnu;
import jaxe.elements.JEItem;
import jaxe.elements.JEListe;
import jaxe.elements.JEListeChamps;
import jaxe.elements.JESauf;
import jaxe.elements.JEString;
import jaxe.elements.JEStyle;
import jaxe.elements.JETable;
import jaxe.elements.JETableTexte;
import jaxe.elements.JEVide;
import jaxe.elements.JEZone;

import org.w3c.dom.Element;
import org.w3c.dom.ProcessingInstruction;

/**
 * Usine � �l�ments Jaxe
 */
public class JEFactory {
    
    /**
     * Cr�ation d'un JaxeElement � partir du type de balise, du document Jaxe,
     * de la d�finition de l'�l�ment, et (pour une cr�ation � partir d'un �l�ment DOM existant)
     * de l'�l�ment DOM. el doit �tre null pour la cr�ation d'un nouvel �l�ment.
     */
    public static JaxeElement createJE(String typebalise, JaxeDocument doc, Element eldef, Element el) {
        JaxeElement newje;
        if (typebalise.equals("division"))
            newje = new JEDivision(doc);
        else if (typebalise.equals("liste"))
            newje = new JEListe(doc);
        else if (typebalise.equals("listechamps"))
            newje = new JEListeChamps(doc);
        else if (typebalise.equals("item"))
            newje = new JEItem(doc);
        else if (typebalise.equals("tableau")) {
            if (el != null && JETable.preferreZone(doc, el))
                newje = new JEZone(doc);
            else
                newje = new JETable(doc);
        } else if (typebalise.equals("zone"))
            newje = new JEZone(doc);
        else if (typebalise.equals("string"))
            newje = new JEString(doc);
        else if (typebalise.equals("vide"))
            newje = new JEVide(doc);
        else if (typebalise.equals("style"))
            newje = new JEStyle(doc);
        else if (typebalise.equals("tabletexte"))
            newje = new JETableTexte(doc);
        else if (typebalise.equals("plugin")) {
            String classid = doc.cfg.getParamFromDefinition(eldef, "classe",
                eldef.getAttribute("param"));

            try {
                Class c = Class.forName(classid);
                Constructor cons = null;
                try {
                    Class[] parameterTypes = new Class[1];
                    parameterTypes[0] = JaxeDocument.class;
                    cons = c.getConstructor(parameterTypes);
                } catch (NoSuchMethodException ex) {
                    // cons sera null
                }
                if (cons != null) {
                    Object[] initargs = new Object[1];
                    initargs[0] = doc;
                    newje = (JaxeElement) cons.newInstance(initargs);
                } else {
                    newje = (JaxeElement) c.newInstance();
                    newje.doc = doc;
                }
            } catch (Exception ex) {
                newje = new JEInconnu(doc);
                System.out.println("Plugin not found : " + classid);
                ex.printStackTrace();
            }

        } else
            newje = new JEInconnu(doc);
        return(newje);
    }

    public static JaxeElement createJE(String typebalise, JaxeDocument doc, Element eldef, ProcessingInstruction el) {
        JaxeElement newje;
        if (typebalise.equals("plugin")) {
            String classid = doc.cfg.getParamFromDefinition(eldef, "classe",
                eldef.getAttribute("param"));

            try {
                Class c = Class.forName(classid);
                newje = (JaxeElement) c.newInstance();
                newje.doc = doc;
            } catch (Exception ex) {
                newje = new JESauf(doc);
                System.out.println("Plugin not found : " + classid);
                ex.printStackTrace();
            }

        } else
            newje = new JESauf(doc);
        return(newje);
    }
}
