/*
 Jaxe - Editeur XML en Java

 Copyright (C) 2002 Observatoire de Paris-Meudon

 Ce programme est un logiciel libre ; vous pouvez le redistribuer et/ou le modifier conformément aux dispositions de la Licence Publique Générale GNU, telle que publiée par la Free Software Foundation ; version 2 de la licence, ou encore (à votre choix) toute version ultérieure.
  
 Ce programme est distribué dans l'espoir qu'il sera utile, mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de COMMERCIALISATION ou D'ADAPTATION A UN OBJET PARTICULIER. Pour plus de détail, voir la Licence Publique Générale GNU .

 Vous devez avoir reçu un exemplaire de la Licence Publique Générale GNU en même temps que ce programme ; si ce n'est pas le cas, écrivez à la Free Software Foundation Inc., 675 Mass Ave, Cambridge, MA 02139, Etats-Unis.
 */

package jaxe;

import java.awt.Color;
import java.awt.Graphics;
import java.awt.Rectangle;
import java.awt.Shape;

import javax.swing.SizeRequirements;
import javax.swing.text.AttributeSet;
import javax.swing.text.BoxView;
import javax.swing.text.Element;
import javax.swing.text.ParagraphView;
import javax.swing.text.TableView;
import javax.swing.text.View;
import javax.swing.text.ViewFactory;
import javax.swing.text.html.HTML;

// attention, ici Element = javax.swing.text.Element

/**
 * Vue pour les tables de JETableTexte
 */
public class JaxeTableView extends TableView implements ViewFactory {

    String TRtag = "tr";

    String TDtag = "td";

    String THtag = "th";

    String CaptionTag = "caption";

    public JaxeTableView(Element elem) {
        super(elem);
        setInsets((short) 2, (short) 2, (short) 2, (short) 2);
    }

    public void paint(Graphics g, Shape allocation) {
        // paint interior
        int n = getViewCount();
        for (int i = 0; i < n; i++) {
            View v = getView(i);
            v.paint(g, getChildAllocation(i, allocation));
        }

        Rectangle alloc = (allocation instanceof Rectangle) ? (Rectangle) allocation
                : allocation.getBounds();

        g.setColor(Color.gray);
        g.draw3DRect(alloc.x, alloc.y, alloc.width - 1, alloc.height - 1, true);
        g.setColor(Color.black);
    }

    public ViewFactory getViewFactory() {
        return this;
    }

    protected View getViewAtPoint(int x, int y, Rectangle alloc) {
        int n = getViewCount();
        View v = null;
        Rectangle allocation = new Rectangle();
        for (int i = 0; i < n; i++) {
            allocation.setBounds(alloc);
            childAllocation(i, allocation);
            v = getView(i);
            if (v instanceof JaxeTableRow) {
                v = ((JaxeTableRow) v).findViewAtPoint(x, y, allocation);
                if (v != null) {
                    alloc.setBounds(allocation);
                    return v;
                }
            }
        }
        return super.getViewAtPoint(x, y, alloc);
    }

    /**
     * The table itself acts as a factory for the various views that actually
     * represent pieces of the table. All other factory activity is delegated to
     * the factory returned by the parent of the table.
     */
    public View create(Element elem) {
        String kind = elem.getName();
        if (kind != null) {
            if (kind.equals(TRtag)) {
                return new JaxeTableRow(elem);
            } else if ((kind.equals(TDtag)) || (kind.equals(THtag))) {
                return new JaxeTableCell(elem);
            } else if (kind.equals(CaptionTag)) { return new ParagraphView(elem); }
        }

        View p = getParent();
        if (p != null) {
            ViewFactory f = p.getViewFactory();
            if (f != null) { return f.create(elem); }
        }
        return null;
    }

    private int getColsOccupied(View v) {
        AttributeSet a = v.getElement().getAttributes();
        String s = (String) a.getAttribute(HTML.Attribute.COLSPAN);
        if (s != null) {
            try {
                return Integer.parseInt(s);
            } catch (NumberFormatException nfe) {
            }
        }
        return 1;
    }

    private int getRowsOccupied(View v) {
        AttributeSet a = v.getElement().getAttributes();
        String s = (String) a.getAttribute(HTML.Attribute.ROWSPAN);
        if (s != null) {
            try {
                return Math.max(Integer.parseInt(s), 1);
            } catch (NumberFormatException nfe) {
            }
        }
        return 1;
    }

    /**
     * Calculates the height of all tablecells
     * @return height of the table
     */
    private float calculateAllCells() {

        int n = getViewCount();
        int maxcols = 0;
        int maxrows = 0;

        //How many colums are in the table?
        for (int i = 0; i < n; i++) {
            View v = getView(i);
            if (v instanceof JaxeTableRow) {
                JaxeTableRow jtv = (JaxeTableRow) v;

                int cols = 0;

                int m = jtv.getViewCount();
                for (int j = 0; j < m; j++) {
                    JaxeTableCell t = (JaxeTableCell) jtv.getView(j);

                    cols += getColsOccupied(t);
                }

                maxcols = Math.max(cols, maxcols);

            }
        }

        // How may rows are in the table?
        maxrows = n;
        
        
        float[][] sizes = new float[maxrows][maxcols];
        float[] maxrowsize = new float[maxrows];

        //Now read the sizes of all cells which have no rowspan
        for (int i = 0; i < maxrows; i++) {
            View v = getView(i);
            maxrowsize[i] = 0;
            if (v instanceof JaxeTableRow) {
                JaxeTableRow jtv = (JaxeTableRow) v;

                for (int j = 0; j < jtv.getViewCount(); j++) {
                    JaxeTableCell t = (JaxeTableCell) jtv.getView(j);
                    int rows = getRowsOccupied(t);
                    if (rows == 1) {
                        sizes[i][j] = t.getRealSpan(Y_AXIS);
                        maxrowsize[i] = Math.max(maxrowsize[i], sizes[i][j]);
                    } else {
                        sizes[i][j] = -1;
                    }

                }
            }
        }

        //The rowsize ist the highest cellsize
        for (int row = 0; row < maxrows; row++) {
            float rowpref = 0;
            for (int col = 0; col < maxcols; col++) {
                rowpref = Math.max(rowpref, sizes[row][col]);
            }
            JaxeTableRow jtr = (JaxeTableRow) getView(row);
            jtr.setPreferredSize((int) rowpref);
            for (int cellc = 0; cellc < jtr.getViewCount(); cellc++) {
                JaxeTableCell cell = (JaxeTableCell) jtr.getView(cellc);
                int rows = getRowsOccupied(cell);
                if (rows == 1) {
                    cell.setPreferredHeight(rowpref);
                }
            }
        }

        boolean retry = false;
        do {
            retry = false;
        //Now multirowcells get their sizes
        for (int i = 0; i < maxrows && !retry; i++) {
            View v = getView(i);
            if ((v instanceof JaxeTableRow) && (!retry)){
                JaxeTableRow jtv = (JaxeTableRow) v;

                for (int j = 0; j < jtv.getViewCount() && !retry; j++) {
                    JaxeTableCell t = (JaxeTableCell) jtv.getView(j);
                    int rows = getRowsOccupied(t);

                    if ((rows > 1) && (!retry)){
                        float maxspan = 0;

                        for (int anz = 0; anz < rows; anz++) {
                            if (i+anz < maxrowsize.length) {
                                maxspan += maxrowsize[i + anz];
                            }
                        }
                        
                        if (maxspan > t.getRealSpan(Y_AXIS)) {
                            t.setPreferredHeight(maxspan);
                        } else if (maxspan < t.getRealSpan(Y_AXIS)) {
                            t.setPreferredHeight(t.getRealSpan(Y_AXIS));
                            float diff = (t.getRealSpan(Y_AXIS) - maxspan)
                                    / rows;
                            for (int anz = 0; anz < rows; anz++) {
                                
                                if (i+anz < maxrowsize.length) {
                                    JaxeTableRow jtr = (JaxeTableRow) getView(i + anz);
                                    jtr.setPreferredSize((int) (jtr
                                            .getPreferredSpan(Y_AXIS) + diff));
                                    maxrowsize[i + anz] = maxrowsize[i + anz] + diff;
                                }
                                
                            }
                            retry = true; // Repeat resize because 1 cell was altered
                        }
                    }

                }
            }
        }
        } while (retry);

        //Normalize all cellsizes and calculate the tableheight
        float tableheight = 0;
        for (int row = 0; row < maxrows; row++) {
            JaxeTableRow jtr = (JaxeTableRow) getView(row);
            for (int cellc = 0; cellc < jtr.getViewCount(); cellc++) {
                JaxeTableCell cell = (JaxeTableCell) jtr.getView(cellc);
                if (getRowsOccupied(cell) == 1) {
                    cell.setPreferredHeight((int) maxrowsize[row]);
                }
            }
            tableheight += maxrowsize[row];
        }

        return tableheight;
    }

    protected SizeRequirements calculateMajorAxisRequirements(int axis,
            SizeRequirements r) {

        if (axis == Y_AXIS) {
            super.calculateMajorAxisRequirements(axis, r);
            r = new SizeRequirements();
            
            r.preferred = r.maximum = r.minimum = (int) calculateAllCells();
        } else {
            r = super.calculateMajorAxisRequirements(axis, r);
            r.maximum = r.preferred;
        }

        return (r);
    }

    public void setParent(View parent) {
        // a useless replace is causing a setParent(null). As a result, child
        // views
        // forget about their parents and are unable to change it back when
        // setParent(v) is called afterwards.
        if (parent != null) super.setParent(parent);
    }

    class JaxeTableRow extends TableRow {

        public JaxeTableRow(Element elem) {
            super(elem);
       }

        /**
         * This is called by a child to indicate its preferred span has changed.
         * This is implemented to execute the superclass behavior and well as
         * try to determine if a row with a multi-row cell hangs across this
         * row. If a multi-row cell covers this row it also needs to propagate a
         * preferenceChanged so that it will recalculate the multi-row cell.
         * 
         * @param child
         *            the child view
         * @param width
         *            true if the width preference should change
         * @param height
         *            true if the height preference should change
         */

        View findViewAtPoint(int x, int y, Rectangle alloc) {
            int n = getViewCount();
            for (int i = 0; i < n; i++) {
                Rectangle s = getChildAllocation(i, alloc).getBounds();
                
                JaxeTableCell cell  = (JaxeTableCell)getView(i);
                s.height = (int) cell.getPreferredHeight();
                
                if (s.contains(x, y)) {
                    childAllocation(i, alloc);
                    return getView(i);
                }
            }
            return null;
        }

        protected void layoutMinorAxis(int targetSpan, int axis, int[] offsets,
                int[] spans) {
            //switch
            calculateAllCells();
            int col = 0;
            int ncells = getViewCount();
            int max = 0;

            for (int cell = 0; cell < ncells; cell++, col++) {
                View cv = getView(cell);
                spans[cell] = (int) cv.getPreferredSpan(Y_AXIS);

                if (spans[cell] > max) {
                    max = spans[cell];
                }

            }
            super.layoutMinorAxis(targetSpan, axis, offsets, spans);
        }

        public void paint(Graphics g, Shape allocation) {
            Rectangle alloc = (allocation instanceof Rectangle) ? (Rectangle) allocation
                    : allocation.getBounds();

            /** used in paint. */
            Rectangle tempRect = new Rectangle();

            int n = getViewCount();
            int x = alloc.x + getLeftInset();
            int y = alloc.y + getTopInset();
            for (int i = 0; i < n; i++) {
                tempRect.x = x + getOffset(X_AXIS, i);
                tempRect.y = y + getOffset(Y_AXIS, i);
                tempRect.width = getSpan(X_AXIS, i);
                tempRect.height = getSpan(Y_AXIS, i);
                paintChild(g, tempRect, i);
            }
        }

        public float getPreferredSpan(int axis) {
            float f = super.getPreferredSpan(axis);
            if (axis == Y_AXIS) { return pref; }
            return f;
        }

        public float getMinimumSpan(int axis) {
            float f = super.getMinimumSpan(axis);
            if (axis == Y_AXIS) { return pref; }
            return f;
        }

        public float getMaximumSpan(int axis) {
            float f = super.getMaximumSpan(axis);

            if (axis == Y_AXIS) { return pref; }

            return f;
        }

        int pref = 0;

        public void setPreferredSize(int size) {
            pref = size;
        }

        protected SizeRequirements calculateMinorAxisRequirements(int axis,
                SizeRequirements r) {
            if (axis == Y_AXIS) {
                super.calculateMinorAxisRequirements(axis, r);
                r = new SizeRequirements();
                r.alignment = 0;
                r.preferred = (int) pref;
                r.minimum = (int) pref;
                r.maximum = (int) pref;
            } else {
                r = super.calculateMinorAxisRequirements(axis, r);
            }
            return r;
        }
    }

    class JaxeTableCell extends BoxView {

        public JaxeTableCell(Element elem) {
            super(elem, Y_AXIS);
            setInsets((short) 3, (short) 3, (short) 3, (short) 3);
        }

        protected SizeRequirements calculateMinorAxisRequirements(int axis, SizeRequirements r) {
            SizeRequirements r2 = super.calculateMinorAxisRequirements(axis, r);
            if (r2.maximum == 2147483647)
                r2.maximum = 32767; // beaucoup mieux que 2147483647, va comprendre...
            return(r2);
        }

        public void paint(Graphics g, Shape allocation) {
            Rectangle alloc = (allocation instanceof Rectangle) ? (Rectangle) allocation
                    : allocation.getBounds();

            alloc.height = (int) getPreferredHeight();
            Rectangle tempRect = new Rectangle();
            int n = getViewCount();
            int x = alloc.x + getLeftInset();
            int y = alloc.y + getTopInset();

            for (int i = 0; i < n; i++) {
                tempRect.x = x + getOffset(X_AXIS, i);
                tempRect.y = y + getOffset(Y_AXIS, i);
                tempRect.width = getSpan(X_AXIS, i);
                tempRect.height = (int) getPreferredHeight();
                paintChild(g, tempRect, i);
            }
            g.setColor(Color.black);
            g.drawRect(alloc.x, alloc.y, alloc.width - 1, alloc.height - 1);
        }

        float _height = -1;

        public void setPreferredHeight(float height) {
            _height = height;
        }

        public float getPreferredHeight() {
            return _height;
        }

        public float getMinimumSpan(int axis) {
            if (axis == Y_AXIS) {
                return _height;
            }
            return super.getMinimumSpan(axis);
        }

        public float getPreferredSpan(int axis) {
            if (axis == Y_AXIS) {
                return _height;
            }
            return super.getPreferredSpan(axis);
        }

        public float getMaximumSpan(int axis) {
            if (axis == Y_AXIS) {
                return _height;
            }
            return super.getMaximumSpan(axis);
        }

        public float getRealSpan(int axis) {
            return super.getPreferredSpan(axis);
        }

    }
}