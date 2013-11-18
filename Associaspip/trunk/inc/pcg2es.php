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
	'[1-7]', //0: classes
	'[0-9]', //1: sections (il manque : 10 11 12 13 17 18 19 20 21 22 26 27 30 31 32 33 34 35 36 37 38 39 40 42 43 44 45 47 48 49 55 57 58 61 63 65 66 67 68 70 71 72 76 77 78 79 !)
	'[0-9]', //2: groupes (mais pas de : 103 104 105 106 107 108 109 119 123 124 125 126 127 128 132 133 134 137 138 139 145 146 147 148 149 152 153 154 156 157 158 159 165 166 167 168 169 175 176 177 178 179 181 182 183 184 186 187 188 189 197 203 204 205 206 207 208 209 216 218 231 232 233 234 235 236 237 238 255 261 262 263 264 265 266 267 268 269 273 274 275 276 277 278 279 280 283 284 285 286 287 288 289 290 299 301 302 303 304 305 306 307 308 309 311 312 313 314 315 316 317 318 319 323 324 329 331 332 333 334 335 336 337 338 339 341 342 343 344 345 346 347 348 349 351 352 353 354 355 356 357 358 359 361 361 362 363 364 366 367 369 371 372 373 374 375 376 377 378 379 381 382 383 384 385 386 387 388 389 398 399 404 405 408 409 412 413 414 415 416 417 418 420 421 422 423 424 425 426 427 428 429 434 438 439 442 443 444 446 447 448 450 451 452 453 454 455 456 457 458 459 461 462 463 464 466 467 468 469 478 481 482 483 484 486 487 488 489 491 492 495 496 497 498 499 502 503 504 507 508 518 519 528 529 554 559 562 563 564 567 568 569 576 577 578 578 579 581 582 583 584 586 587 588 589 590 591 592 599 603 605 606 607 613 614 615 616 617 618 619 632 635 644 645 646 647 648 652 653 654 655 656 657 658 660 675 676 677 678 683 684 685 686 687 688 689 706 707 711 712 714 715 716 717 718 719 720 721 722 723 724 725 726 727 728 729 734 735 736 738 739 742 743 744 745 746 747 748 749 750 756 757 758 764 767 780 781 782 783 784 785 786 787 788 789 !)
	'A' => array(6,7), // classes de gestion
	'B' => array(1,2,3,4,5), // classes de bilan
	'C' => '6', // comptes au credit
	'D' => '7', // comptes au debit
);

// http://www.unexco-corral.com/spip.php?article56
// http://www.focusifrs.com/content/download/6110/31919/version/1/file/35_39.pdf

?>
