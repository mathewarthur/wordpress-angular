<!doctype html>
<html lang="en" ng-app="wpAngularPlugin">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title><? bloginfo("title");?></title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="stylesheet" href="<? bloginfo("template_directory")?>/assets/css/main.css?version=1" />
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<!-- JavaScript -->
		<!--[if IE]><![endif]-->
		<!--[if lt IE 9]>
		<script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<? wp_head(); ?>
	</head>
	<body ng-template-ready>
		<div id="loading" ng-hide="templateReady">
			<div class="loader"></div>
		</div>			
		<header ng-sticky-nav>
			<img id="logo" src="<? bloginfo('template_directory')?>/assets/img/zebra_logo_centred.svg">

			<form  style="float:right;" role="search" method="get" class="searchform group" action="<? echo home_url( '/' ); ?>">
				<i class="material-icons">search</i><!--<input style="float:right;background:transparent;border:0px;border-bottom:3px solid #222;color:#222;font-family:AkzidenzGroteskBE-Md, Arial, sans-serif;outline:none;padding-top:10px;padding-bottom:8px;" type="text" class="search-field" placeholder="Search" value="<? echo get_search_query() ?>" name="s"/>-->
			</form>

			<ng-archive-pagenav post-type="pages" post-order="ASC" per-page="10" page-parent="0" post-orderby="menu_order">
			</ng-archive-pagenav>

			<span id="tagline">PRODUCTS CANADA</span>
		</header>
		<? if (is_front_page()) { ?>
			<section id="header" ng-show="templateReady">
				<ng-archive-categories ng-cloak post-type="categories" class="loop">
				</ng-archive-categories>
			</section>
			<section id="main" ng-show="templateReady"> 
				<ng-single-basic ng-cloak post-type="pages" id="5" class="loop front">
				</ng-single-basic>
				<h2>Products</h2>
				<ng-archive-post ng-cloak vertilize-container post-type="pages" post-order="ASC" page-parent="6" per-page="100" post-orderby="menu_order" class="loop">
				</ng-archive-post>
			</section>
		<? } ?>
			<? if (is_page() && !is_page("products")) { ?>
				<section id="main" ng-template-ready ng-show="templateReady">
					<ng-single-post vertilize-container ng-cloak id="<?=$post->ID; ?>" post-type="pages" class="loop">
					</ng-single-post>
				</section>
			<? } ?> 
			<? if (is_category("closure-system")) { ?>
				<section id="main" ng-template-ready ng-show="templateReady">
					<ng-single-archive ng-cloak post-type="categories" id="6" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-single-archive>
					<ng-archive-post ng-cloak vertilize-container post-type="pages" cat-slug="closure-system" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-archive-post>
				</section>
			<? } ?> 
			<? if (is_category("racking-system")) { ?>
				<section id="main" ng-template-ready ng-show="templateReady">
					<ng-single-archive ng-cloak post-type="categories" id="4" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-single-archive>
					<ng-archive-post ng-cloak vertilize-container post-type="pages" cat-slug="racking-system" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-archive-post>
				</section>
			<? } ?> 
			<? if (is_category("multiport-mounting-system")) { ?>
				<section id="main" ng-template-ready ng-show="templateReady">
					<ng-single-archive ng-cloak post-type="categories" id="5" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-single-archive>
					<ng-archive-post ng-cloak vertilize-container post-type="pages" cat-slug="multiport-mounting-system" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-archive-post>
				</section>
			<? } ?> 
			<? if (is_search()) { ?>
				<section id="main" ng-template-ready ng-show="templateReady">
					<h2>Search Results</h2>
					<ng-archive-post ng-cloak vertilize-container post-type="pages" search="<?php echo get_search_query() ?>" per-page="100" post-orderby="menu_order" post-order="ASC" class="loop">
					</ng-archive-post>
				</section>
			<? } ?> 
		<footer ng-show="templateReady">
		</footer>
		<? wp_footer(); ?>
		<script>
			function go(el) {
				var theurl = jQuery(el).find(".button").attr("href");
				window.location.href = theurl;
			}
			jQuery("body").on("click", "#about a", function(e){
				if (jQuery("header").hasClass("controls-fixed")) {
					if (jQuery("body").css("clear") == "left") {
						var theoffset = 73;
					} else {
						var theoffset = 73;
					}
				} else {
					if (jQuery("body").css("clear") == "left") {
						var theoffset = 74;
					} else {
						var theoffset = 74;
					}
				}
				jQuery("html, body").animate({
				      scrollTop: (jQuery("ng-single-basic").offset().top - theoffset)
				}, 1000);
				e.preventDefault();
			});
		</script>
	</body>
</html>