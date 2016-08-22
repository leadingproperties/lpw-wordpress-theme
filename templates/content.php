<?php
	global $wp_query;
	$index = $wp_query->current_post + 1;
	$post_class = ($index === 1) ? 'blog-item first' : 'blog-item';
    $title = get_the_title();
	$link = get_the_permalink();
	$img = ($index === 1) ? wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'featured') : wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ), 'post-thumbnail');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($post_class); ?> itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
  <div class="blog-inner-wrapper">
	  <?php if($img) { ?>
	    <div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
		  <meta itemprop="contentUrl" content="<?= $img[0]; ?>">
	      <a href="<?= $link; ?>" class="blog-thumbnail-holder open-post-modal" data-id="<?php the_ID(); ?>">
		      <img src="<?= $img[0]; ?>" alt="<?= $title; ?>" class="img-responsive" itemprop="contentUrl">
	      </a>
	    </div>
	  <?php } ?>
    <div class="blog-info-holder">
      <h2 class="info-title" itemprop="headline">
        <a href="<?= $link; ?>" class="open-post-modal" data-id="<?php the_ID(); ?>"><?= $title; ?></a>
      </h2>
      <div class="entry-meta">
		<?php get_template_part('templates/entry', 'meta'); ?>
      </div>
	    <?php if( $index === 1) { ?>
		    <div class="entry-summary">
			    <?= get_lp_excerpt(); ?>
		    </div>
	    <?php } ?>
    </div>
  </div>
</article><!-- /.blog-item -->