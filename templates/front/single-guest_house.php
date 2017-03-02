<?php
 /*Template Name: GHOIB Single Post Page
	To be modified--this is scrap data
 
 */
 
get_header(); while ( have_posts() ) : the_post(); ?>
<script type="text/javascript">
var $jq112 = jQuery.noConflict();
	$jq112(document).ready(function() {
		$jq112('.fancybox-details').fancybox({
			helpers	: {
				 title: {
					type: 'inside'
				},
				thumbs	: {
					width	: 277,
					height	: 155
				}
			}
		});
	});
</script>
	<div class="inner-banner">
		<div class="container">
			<h2><?php the_title(); ?></h2>
		</div>
	</div>
	<div class="container">
		<h1><?php the_title(); ?><span></span></h1>
		<?php the_content(); ?>
		<h2>Gallery<span></span></h2>
		<ul class="fancy">
		<?php echo get_homepage_gallery_item(get_the_ID()); ?>
		</ul> 
	</div>

<?php
endwhile;
get_footer(); 
?>