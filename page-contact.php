<?php
/**
 * Template Name: Contact page
 */
?>
<?php
  get_template_part('templates/head');
  get_header();
?>
<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/page', 'header'); ?>
  <?php get_template_part('templates/content', 'contact'); ?>
<?php endwhile; ?>
<?php
  get_footer();
