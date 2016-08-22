<?php while (have_posts()) : the_post(); ?>
<?php
	$url = urlencode(get_the_permalink());
	$title = urlencode(html_entity_decode(get_the_title()));
	$img = '';
	if (has_post_thumbnail( ) ):
		$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'featured' );
	endif;

	?>
	<article class="single-post-container" itemscope itemtype="http://schema.org/Article">
		<header class="single-post-header">
			<div class="single-post-wrap">
				<div class="social-sharing">
					<ul>
						<li class="label">Share</li>
						<li><a href="mailto:?subject=<?= html_entity_decode(get_the_title()); ?>&body=<?= $url ?>" class="soc-icon email-icon"></a></li>
						<li><a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url; ?>" target="_blank" class="soc-icon fb-icon"></a></li>
						<li><a href="https://twitter.com/intent/tweet?text=<?= $title; ?>&url=<?= $url; ?>&via=leadingpro" target="_blank" class="soc-icon twitter-icon"></a></li>
						<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url; ?>&title=<?= $title; ?>&summary=<?= urlencode(get_the_excerpt()) ?>" target="_blank" class="soc-icon ln-icon"></a></li>
						<li><a href="https://plus.google.com/share?url=<?= $url; ?>" target="_blank" class="soc-icon gplus-icon"></a></li>
					</ul>
				</div>
			</div>
		</header><!-- /.single-post-header -->
		<div class="single-post-content">
			<div class="single-post-content-inner">
				<?php if( $img ) { ?>
					<div class="single-post-thumbnail">
						<img src="<?= $img[0] ?>" alt="<?= $title; ?>" class="img-responsive">
					</div>
				<?php } ?>
				<div class="single-post-details">
					<h1 class="single-post-title" itemprop="headline"><?php the_title(); ?></h1>
					<div class="entry-meta">
						<?php get_template_part('templates/entry', 'meta'); ?>
					</div>
					<div class="entry-content" itemprop="articleBody">
						<?php the_content(); ?>
					</div><!-- /.entry-content -->
				</div><!-- /.single-post-details -->
			</div><!-- /.single-post-content-inner -->
		</div><!-- /.single-post-content -->
		<?php show_lp_adjacent_posts(); ?>
	</article><!-- /.single-post-container -->
<?php endwhile; ?>

