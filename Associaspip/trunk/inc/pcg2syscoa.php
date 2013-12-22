<?php
/***************************************************************************\
 *  ComptaSPIP, extension comptable
 *
 * @read (licence, copyrigth, authors, credits)
 *  ../plugin.xml
\***************************************************************************/

$GLOBALS['ar'] = array(
	'[1-9]', //0: classes
	'[0-9]', //1: sections
	'[1-9]', //2: groupes (pas de : 107 108 114 115 116 117 119 122 123 124 125 126 127 128 129 122 123 124 125 126 127 128 143 144 145 146 147 148 149 159 169 171 174 175 177 179 189 203 204 205 207 208 236 253 254 255 256 257 258 259 264 267 269 279 285 286 287 288 289 298 299 313 314 315 316 317 324 325 326 327 328 329 336 337 339 345 346 347 348 349 353 354 355 356 357 358 359 363 364 365 366 367 368 369 373 374 375 376 377 378 379 384 385 389 399 403 404 405 406 407 413 417 429 434 435 436 437 438 439 453 454 455 456 457 459 464 468 469 473 487 489 507 509 516 517 519 525 527 528 529 534 535 539 546 547 548 549 562 563 574 575 576 577 578 579 583 584 586 587 589 595 596 597 598 599 606 607 609 615 617 629 636 639 642 643 644 649 655 656 657 665 669 682 683 684 685 686 688 689 692 693 694 696 698 699 708 709 714 715 716 717 719 723 724 725 727 728 729 731 732 733 736 738 739 741 742 743 744 745 746 747 748 749 751 755 756 757 775 761 762 763 764 765 766 767 768 769 782 783 784 785 786 788 789 792 793 794 795 796 799 801 802 803 804 805 806 807 808 809 813 814 815 817 818 819 823 824 825 827 828 829 832 833 837 838 842 843 844 847 855 856 857 859 866 867 869 872 873 875 876 877 879 882 883 885 889 893 894 896 897 898 909 91x 92x 93x 94x 95x 96x 97x 98x 99x !)
	'A' => array(6,7,8), // classes de gestion
	'B' => array(1,2,3,4,5), // classes de bilan
	'C' => '(4[0-9][1-8])|(6[0-9]9)|(7[0-9][0-8])', // comptes au credit
	'D' => '(4[0-9]9)|(6[0-9][0-8])|(7[0-9]9)', // comptes au debit
);

// http://www.izf.net/pages/uemoa-syscoa/2365/
// http://www.cpcc-rdc.org/Classification_Ohada.html
// N° 99-01 du 16 février 1999 relatif aux modalités d’établissement des comptes
// http://www.ohada.com/actes-uniformes/693/860/tableau-cadre-comptable-du-systeme-comptable-ohada.html
// http://fr.wikipedia.org/wiki/Plan_comptable_%28OHADA%29
// http://www.droit-afrique.com/images/textes/Ohada/Ohada%20-%20Acte%20uniforme%202000%20(Plan%20des%20comptes).pdf
// http://www.adoc-sn.com/doc/Livre-Vert%20du%20SYSCOA.pdf
// http://www.rag.sn/IMG/pdf/doc-35.pdf
// http://hal-auf.archives-ouvertes.fr/docs/00/58/46/10/PDF/BIGOU-LARE.pdf
// http://www.ohada.org/documentation.html

?>