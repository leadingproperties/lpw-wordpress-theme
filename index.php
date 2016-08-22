<?php
  get_template_part('templates/head');
  get_header();
?>
<?php // get_template_part('templates/panel', 'blog'); ?>

<?php if (!have_posts()) : ?>
  <div class="no-matches">
    <div class="container">
      <h5 class="text-red">Sorry, no posts were found</h5>
    </div>
  </div><!-- /.no-matches -->
<?php endif; ?>
  <section class="blog-list-wrapper">
    <div class="container">
      <div class="row">
        <?php while (have_posts()) : the_post(); ?>
          <?php get_template_part('templates/content', get_post_type() != 'post' ? get_post_type() : get_post_format()); ?>
        <?php endwhile; ?>
      </div>
    </div>
  </section><!-- /.objects-list-wrapper -->
  <div class="loader">
    <span class="spin"></span>
  </div>
<?php // get_template_part('templates/panel', 'blog-footer'); ?>
<?php
get_footer();
