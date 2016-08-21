<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php echo get_query_var('object_slug'); ?>
  <?php get_template_part('templates/content', 'page'); ?>
<?php endwhile; ?>
