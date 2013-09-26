<?php
/******************************************************************
* Projectname:   PHP Pagination Class 
* Version:       1.1
* Author:        Radovan Janjic <rade@it-radionica.com>
* Last modified: 25 09 2012
* Copyright (C): 2011 IT-radionica.com, All Rights Reserved
*
*** GNU General Public License (Version 2, June 1991)
*
* This program is free software; you can redistribute
* it and/or modify it under the terms of the GNU
* General Public License as published by the Free
* Software Foundation; either version 2 of the License,
* or (at your option) any later version.
*
* This program is distributed in the hope that it will
* be useful, but WITHOUT ANY WARRANTY; without even the
* implied warranty of MERCHANTABILITY or FITNESS FOR A
* PARTICULAR PURPOSE. See the GNU General Public License
* for more details.
* 
*** Description
* 
* This class can generate links to browse MySQL query result pages.
* 
* It takes the total number of items in a query result and the limit of 
* items to display per page to generate HTML links to browse the different 
* pages of the results listing based on the current page number retrieved 
* from a request parameter.
* 
* The class can also generate the MySQL query limit clause to retrieve the 
* results for the current page.
* 
* The base URL for the links and the limit of links to appear before and 
* after the current page can be configured.
* 	
*** Example
* 
* [prev] [1] ... [9] [10] [11] [12] [13] [14] [15] ... [25] [next]
* 
**************************************************************************************************************

$p = new pagination;

// Items per page
$p->perPage = 9;

// Pagination left from current
$p->paginationLeft = 3; 

// Pagination right from current
$p->paginationRight = 3; 

// Link href
$p->path = '?example=%d'; // or $p->path = 'example/%d/';


// Paginaion appearance
$p->appearance = 
		array(
				'nav_prev' 			=> '<a href="%s" class="prev"><span>prev</span></a>',
				'nav_number_link' 	=> '<a href="%s"><span>%d</span></a>',
				'nav_number' 		=> '<a href="javascript:;" class="active"><span>%d</span></a>',
				'nav_more' 			=> '<a href="javascript:;" class="more"><span>...</span></a>',
				'nav_next' 			=> '<a href="%s" class="next"><span>next</span></a>'
		);

// Items count		
$p->setCount(500); 

// Current page
if(isset($_GET['example'])){
	$p->setStart($_GET['example']);
}

// Echo pagination
echo $p->paginate();

?>
<p>SELECT * FROM some_table LIMIT <?php echo $p->getLimit(); ?>, <?php echo $p->getLimitOffset(); ?><br />
<?php 

	if ($p->first) 
		echo "You are on first page.";
	
	if ($p->last) 
		echo "You are on last page.";

 ?>

******************************************************************/

class pagination
{	
	/** Start
	 * @var Integer
	 */
	var $limit = 0;
	
	/** Items count
	 * @var Integer
	 */
	var $count = 0;
	
	/** Items per page
	 * @var Integer
	 */
	var $perPage = 10;
	
	/** Navigation pages to left
	 * @var Integer
	 */
	var $paginationLeft = 3;
	
	/** Navigation pages to right
	 * @var Integer
	 */
	var $paginationRight = 3;
	
	/** HTML template of navigation located in associative array
	 * @var Array
	 */
	var $appearance =
		array(
				'nav_prev' 			=> '<a href="%s" class="prev"><span>prev</span></a>',
				'nav_number_link' 	=> '<a href="%s"><span>%d</span></a>',
				'nav_number' 		=> '<a href="javascript:;" class="active"><span>%d</span></a>',
				'nav_more' 			=> '<a href="javascript:;" class="more"><span>...</span></a>',
				'nav_next' 			=> '<a href="%s" class="next"><span>next</span></a>'
		);

	/** Navigation generated path
	 * @var String
	 */
	var $path = 'example-url/%d/';
	
	/** Indicator to know if you are on first page
	 * @var Boolean
	 */
	var $first = FALSE;
	
	/** Indicator to know if you are on last page
	 * @var Boolean
	 */
	var $last = FALSE;
	
	/** Constructor
	 * @param 	Integer		$count 		- Count items
	 * @param 	Integer		$start 		- Start from page (current page)
	 */
	function pagination($count = 0, $start = 0) {
		$this->setCount($count);
		$this->setStart($start);
	}
	
	/** Set start - Current page
	 * @param 	Integer		$start 		- Start from page (current page)
	 */
	function setStart($start = 0) {
		$this->limit = $start > 0 ? (int) ($start - 1) * $this->perPage : 0;
	}
	
	/** Total number of items
	 * @param 	Integer		$count 		- Count items
	 */
	function setCount($count = 0) {
		$this->count = $count > 0 ? (int) $count : 0;
	}
	
	/** Pagination appearance
	 * @param 	Array		$appearance - HTML template
	 */
	function setAppearance($appearance = array()) {
		$this->appearance = $appearance;	
	}
	
	/**
	 * @return 	Integer		- MySQL limit
	 */
	function getLimit() {
		return ($this->limit >= $this->count || $this->limit % $this->perPage != 0 ) ? 0 : $this->limit;
	}
	
	/**
	 * @return 	Integer		- MySQL limit offset 
	 */
	function getLimitOffset() {
		return $this->perPage;
	}
	
	/** Paginate HTML format
	 * @return	String 		- Returns paginated links
	 */
	function paginate() { 
		
		// Shorten vars
		$r = NULL;
		$c = $this->count;
		$p = $this->perPage;
		$l = $this->limit;
		$u = $this->path;
		$a = $this->appearance;
		$pl = $this->paginationLeft;
		$pr = $this->paginationRight;
		
		// Limit is bigger than count or limit is not dev by per page
		if ($l >= $c || $l % $p != 0 ) return NULL;
		
		// If all items can not be placed on the page
		if ($c > $p) { 
			
			// Show previous page link
			$this->first = ($l > 0) ? !($r .= sprintf($a['nav_prev'], sprintf($u, ($l - $p) / $p + 1))) : TRUE;
			
			// Dig.
			$k = $l / $p;
			
			// No more then ?? to left
			$min = $k - $pl;
			
			// Link to first page
			($min < 0) ? ($min = 0) : (($min >= 1) ? ($r .= sprintf($a['nav_number_link'], sprintf($u, 1), 1) . (($min != 1) ? $a['nav_more'] : NULL)) : FALSE);
			
			for ($i = $min; $i < $k; $i++) {
				(($m = $i * $p + $p) && $m > $c) ? ($m = $c) : FALSE;
				$r .= sprintf($a['nav_number_link'], sprintf($u, $i + 1), $i + 1);
			}
			
			// Current page
			$r .= (strcmp($l, "all")) ?
				(((($min = $l + $p) && $min > $c) ? ($min = $c) : TRUE) ? sprintf($a['nav_number'], $k + 1) : NULL) :
				(((($min = $p) && $min > $c) ? ($min = $c) : TRUE) ? sprintf($a['nav_number_link'], sprintf($u, 1), 1) : NULL);
			
			// No more then ?? on right
			(($min = $k + $pr + 1) && $min > $c / $p) ? ($min = $c / $p) : FALSE;
			
			for ($i = $k + 1; $i < $min; $i++) {
				(($m = $i * $p + $p) && $m > $c) ? ($m = $c) : FALSE;
				$r .= sprintf($a['nav_number_link'], sprintf($u, $i + 1), $i + 1);
			}
			
			// Last item
			if ($min * $p < $c) { 
				
				// ... not link
				($min * $p < $c - $p) ? ($r .= $a['nav_more']) : FALSE;
				
				// Count is dev by per-page
				$r .= (!($c % $p == 0)) ? (($n = floor($c / $p) + 1) ? sprintf($a['nav_number_link'], sprintf($u, $n), $n) : NULL) : (($n = floor($c / $p)) ? sprintf($a['nav_number_link'], sprintf($u, $n), $n) : NULL);
			}
			
			// Next page
			$this->last = ($l < $c - $p) ? !($r .= sprintf($a['nav_next'], sprintf($u, ($l + $p) / $p + 1))) : TRUE;
		}
		
		// Return content
		return $r;
	}
}
