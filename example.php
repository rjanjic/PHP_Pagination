<div>
<?php

include "pagination.class.php";

$p = new pagination;

// Items per page
$p->perPage = 10;

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
<style>
	a { 
		display:block;
		float:left;
		width:30px;
		height:30px;
		padding:5px;
		text-align:center;
		line-height:30px;
		background:#999;
		text-decoration:none;
		color:black;
		font-weight:bold;
		text-transform:uppercase;
		border:solid 1px #666666;
	}
	
	a:hover {
		background:#CCC;
	}
	
	a.active {
		background:#CCC;
	}
	
	a.prev, a.next {
		width:50px;	
	}
	
	a.active, a.more {
		cursor:default;
	}
	
	p {
		clear:left; 
		padding:50px; 
		color:#00F; 
		background:#CCC
	}
	
	div {
		width:800px;
		margin:0 auto;
	}
	
</style>