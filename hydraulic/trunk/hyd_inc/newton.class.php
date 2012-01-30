<?php
/*
def deriv(f , c, dx = 0.0001):
   """
deriv(f,c,dx) --> float

Returns f'(c), computed as a symmetric difference quotient.  Make dx smaller for more precise results.
"""

   return (f(c+dx) - f(c - dx))/ (2*dx)


def fuzzyequals(a,b,tol=0.0001):
   """
fuzzyequals(a,b,tol) --> Bool

Returns True if a is within tol of b.
"""
   return abs(a-b) < tol


def Newton(f, c):

   """
Newton(f,c) --> float

Returns the x closest to c such that f(x) = 0.
"""

 if fuzzyequals(f(c),0,tol):
        return c

 else:
        # catch recursion limit errors, division by zero errors
        try:
            # x_n+1 = x_n - f(x_n)/f'(x_n)
            return newton(f, c - f(c)/deriv(f,c,tol), tol)
        except:
            # We've either hit a horizontal tangent or else
            # haven't been able to find one within the recursion depth.
            return None
*/
abstract class acNewton {
   protected $rTol;
   protected $rDx;
   private $iCpt=0;
   private $iCptMax=50;

   abstract public function CalcFn($rX);

   private function CalcDer($x) {
      return ($this->CalcFn($x+$this->rDx)-$this->CalcFn($x-$this->rDx))/(2*$this->rDx);
   }

   private function FuzzyEqual($rFn) {
      return (abs($rFn) < $this->rTol);
   }

   public function Newton($rX) {
      $this->iCpt++;
      $rFn=$this->CalcFn($rX);
      //echo $this->iCpt.' - f('.$rX.') = '.$rFn;
      if($this->FuzzyEqual($rFn) || $this->iCpt >= $this->iCptMax) {
         return $rX;
      }
      else {
         $rDer=$this->CalcDer($rX);
         //echo ' - f\\\' = '.$rDer.'<br/>';
         if($rDer!=0) {
            return $this->Newton($rX-$rFn/$rDer);
         }
      }
   }
}

?>
