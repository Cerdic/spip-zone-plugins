/*
 * Created on 22.07.2005
 */
package jaxe.elements;

import org.w3c.dom.Element;
import org.w3c.dom.Node;

public class TableHelper {
    int nblignes;

    int nbcolonnes;

    String TRtag;

    String TDtag;

    String THtag;

    String colspanAttr;

    String rowspanAttr;

    Element[][] grille;

    public TableHelper(Node noeud, String TRtag, String TDtag, String THtag, String colspanAttr, String rowspanAttr) {
        this.TRtag = TRtag;
        this.TDtag = TDtag;
        this.THtag = THtag;
        this.colspanAttr = colspanAttr;
        this.rowspanAttr = rowspanAttr;
        grille = creerGrille(noeud);
    }

    public Element[][] getGrille() {
        return grille;
    }
    
    public Element[][] updateGrille(Node n) {
        return creerGrille(n);
    }
    
    private Element[][] creerGrille(Node noeud) {
        nblignes = calculerNbLignes(noeud);
        nbcolonnes = calculerNbColonnes(noeud);
        grille = new Element[nblignes][nbcolonnes];
        for (int iil = 0; iil < nblignes; iil++)
            for (int iic = 0; iic < nbcolonnes; iic++)
                grille[iil][iic] = null;
        int il = 0;
        int ic = 0;
        for (Element tr = premiereLigne((Element) noeud); tr != null; tr = ligneSuivante(tr)) {
            for (Node n2 = tr.getFirstChild(); n2 != null; n2 = n2.getNextSibling()) {
                if (n2.getNodeType() == Node.ELEMENT_NODE) {
                    String bal2 = n2.getNodeName();
                    if (bal2.equals(TDtag) || bal2.equals(THtag)) {
                        while (ic < nbcolonnes && grille[il][ic] != null)
                            ic++;
                        String colspan = ((Element) n2).getAttribute(colspanAttr);
                        String rowspan = ((Element) n2).getAttribute(rowspanAttr);
                        int icolspan = 1;
                        int irowspan = 1;
                        if (!"".equals(colspan)) {
                            try {
                                icolspan = Math.max(Integer.parseInt(colspan), 1);
                            } catch (NumberFormatException e) {
                            }
                        }
                        if (!"".equals(rowspan)) {
                            try {
                                irowspan = Math.max(Integer.parseInt(rowspan), 1);
                            } catch (NumberFormatException e) {
                            }
                        }
                        // System.out.println(il+" "+ic+" "+irowspan+" "+icolspan);
                        for (int iil = 0; iil < irowspan; iil++)
                            for (int iic = 0; iic < icolspan; iic++) {
                                if (ic + iic >= nbcolonnes || il + iil >= nblignes)
                                    System.err.println("Erreur: nombre de cellules " + "dans la ligne " + il
                                            + " du tableau");
                                else {
                                    grille[il + iil][ic + iic] = (Element) n2;
                                    /*
                                     * if (n2.getFirstChild() != null) System.out.println((il + iil)+","+(ic + iic)+" = " +
                                     * n2.getFirstChild().getNodeValue()); else System.out.println((il + iil)+","+(ic +
                                     * iic)+" = null");
                                     */
                                }
                            }
                        ic += icolspan;
                    }
                }
            }
            il++;
            ic = 0;
        }
        return grille;
    }

    public int calculerNbLignes(Node noeud) {
        int nb = 0;
        Element el = (Element) noeud;
        for (Element tr = premiereLigne(el); tr != null; tr = ligneSuivante(tr)) {
            String rowspan = tr.getAttribute(rowspanAttr);
            if ("".equals(rowspan))
                nb++;
            else {
                int irowspan;
                try {
                    irowspan = Math.max(Integer.parseInt(rowspan), 1);
                } catch (NumberFormatException e) {
                    irowspan = 1;
                }
                nb += irowspan;
            }
        }
        return (nb);
    }

    public int calculerNbColonnes(Node noeud) {
        Element tr = trouverLigne(0, noeud);
        if (tr == null)
            return (0);
        int nb = 0;
        for (Node n = tr.getFirstChild(); n != null; n = n.getNextSibling())
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TDtag) || bal.equals(THtag)) {
                    String colspan = ((Element) n).getAttribute(colspanAttr);
                    if ("".equals(colspan))
                        nb++;
                    else {
                        int icolspan;
                        try {
                            icolspan = Math.max(Integer.parseInt(colspan), 1);
                        } catch (NumberFormatException e) {
                            icolspan = 1;
                        }
                        nb += icolspan;
                    }
                }
            }
        return (nb);
    }

    public Element premiereLigne(Element table) {
        Node n = table.getFirstChild();
        if (n == null)
            return (null);
        if (n.getNodeType() == Node.ELEMENT_NODE && TRtag.equals(n.getNodeName()))
            return ((Element) n);
        else
            return (ligneSuivante(n));
    }

    public Element ligneSuivante(Node n) {
        n = n.getNextSibling();
        while (n != null) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag))
                    return ((Element) n);
            }
            n = n.getNextSibling();
        }
        return (null);
    }

    public Element trouverLigne(int lsel, Node noeud) {
        Element el = (Element) noeud;
        int l = 0;
        for (Element tr = premiereLigne(el); tr != null; tr = ligneSuivante(tr)) {
            if (l == lsel)
                return (tr);
            l++;
        }
        return (null);
    }

    public int numeroLigne(Element tr) {
        int nb = 0;
        for (Node n = tr.getPreviousSibling(); n != null; n = n.getPreviousSibling()) {
            if (n.getNodeType() == Node.ELEMENT_NODE) {
                String bal = n.getNodeName();
                if (bal.equals(TRtag))
                    nb++;
            }
        }
        return (nb);
    }

    public Element trouverCellule(Element tr, int csel) {
        int il = numeroLigne(tr);
        if (il >= nblignes || csel >= nbcolonnes)
            return (null);
        return (grille[il][csel]);
    }

    /**
     * Find out if the Table is within another Table
     * @param node Node
     * @return True if Node is in another Table
     */
    public boolean inTable(Node node) {

        while (node != null) {
            if (node.getNodeName().equals(TDtag)) {
                return true;
            }
            node = node.getParentNode();
        }

        return false;
    }
}