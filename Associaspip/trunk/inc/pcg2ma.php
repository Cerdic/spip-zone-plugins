<?php
/***************************************************************************\
 *  ComptaSPIP, extension comptable
 *
 * @read (licence, copyrigth, authors, credits)
 *  ../plugin.xml
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$pc_norme = array(
	'[0-9]', //0: classes
	'[0-9]', //1: sections (en fait, debuts : [1235][1-9], 0[0-5], 4[4-7], 6[1-7], 7[1-5], 8[1-8], 9[0-9], sans entrer dans les details)
	'[0-9]', //2: groupes (mais pas de : 010 016 017 018 019 020 026 027 028 029 030 032 034 037 039 040 042 044 047 049 050 052 053 054 057 058 059 110 117 120 121 122 123 124 125 126 127 128 129 130 132 133 134 136 137 138 139 140 142 143 144 145 146 147 149 150 152 153 154 156 157 159 161 162 163 164 164 165 166 167 168 169 170 173 174 175 176 177 178 179 214 215 216 217 218 219 220 224 225 226 227 229 230 236 237 240 242 243 244 245 246 247 249 250 252 253 254 255 256 257 259 270 273 274 275 276 277 278 279 280 284 285 286 287 288 289 290 291 296 297 298 299 310 316 317 318 319 340 344 347 351 352 353 354 355 356 357 358 359 371 372 373 374 375 376 377 378 379 390 392 393 396 397 398 399 440 512 513 515 517 518 519 550 551 555 556 557 558 559 591 592 593 594 595 596 597 598 599 610 615 630 632 634 635 636 637 650 652 653 654 655 657 671 672 673 674 675 676 677 678 679 710 712 714 715 717 730 731 734 735 736 737 750 752 753 754 755 812 813 815 816 818 819 831 832 833 834 835 836 837 838 839 841 842 843 844 845 846 847 848 849 851 852 853 854 855 856 857 858 859 861 862 863 864 865 866 867 868 869 900 901 902 904 905 909 920 922 924 926 929 930 931 932 934 936 937 939 940 946 947 948 950 951 952 956 957 959 960 961 963 965 966 967 968 969 970 979 980 984 985 989 990 992 993 994 996 997 998 999 00x 18x 19x 20x 26x 30x 32x 33x 36x 38x 40x 41x 42x 43x 47x 50x 52x 53x 54x 56x 57x 58x 60x 62x 64x 66x 68x 69x 70x 71x 74x 76x 77x 78x 79x 80x 82x 87x 89x 91x)
	'A' => array(6,7), // classes de gestion
	'B' => array(1,2,3,4,5,9), // classes de bilan
	'C' => '(035)|(15)|3|7|(907)', // comptes au credit
	'D' => '(o36)|(21)|4|6|(906)', // comptes au debit
);

// http://fr.wikipedia.org/wiki/Plan_comptable_marocain
// http://www.becompta.be/modules/mydownloads/downloads-8-209.html

?>
