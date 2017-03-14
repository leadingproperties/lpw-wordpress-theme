<?php
/**
 *  Template Name: Rent
 */
?>

<?php
	get_template_part('templates/head');
	get_header();
	get_template_part('templates/search', 'panel');
	get_template_part('templates/sorting', 'panel');
?>
	<section class="objects-list-wrapper">
		<div class="container">
			<div id="object-list" class="row">
				<?php if( have_posts() ) {
					while (have_posts()) {
						the_post();
						$excerpt = get_the_excerpt();
						$content = get_the_content();
						if(has_excerpt()) { ?>
                            <div class="seo-block-wrap">
                                <div class="seo-block">
									<?= apply_filters('the_content', $excerpt); ?>
									<?php if($content) { ?>
                                        <div id="seo-hidden-text" class="seo-hidden-text collapse">
											<?= apply_filters('the_content', $content); ?>
                                        </div>
                                        <a class="seo-toggle collapsed" role="button" data-toggle="collapse" href="#seo-hidden-text" aria-expanded="false" aria-controls="seo-hidden-text">
                                            <span class="sr-only">Expand</span>
                                        </a>
									<?php } ?>
                                </div>
                            </div>
						<? }
					}
				} ?>
            </div>
		</div>
	</section>
	<div class="loader">
		<span class="spin"></span>
	</div>

<?php
	get_template_part('templates/modal', 'map');
	get_footer();

