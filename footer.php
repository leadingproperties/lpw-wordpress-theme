<?php
	$address = get_field('address', 'option');
	$social = get_social_links();
?>
<footer class="site-footer">
  <div class="container">
    <div class="row">
      <div class="footer-menu">
	      <?php if (has_nav_menu('footer_navigation')) : ?>
		      <?php wp_nav_menu([
			      'theme_location' => 'footer_navigation',
			      'container' => false,
			      'menu_class' => 'footer-main-menu',
			      'depth' => 1
		      ]);
		      ?>
	      <?php endif; ?>
      </div><!-- /.footer-menu -->
      <div class="address">
	     <?php if($social) : ?>
		    <div class="social-links">
		      <?= $social; ?>
		    </div><!-- /.social-links -->
	    <?php endif; ?>
	    <?php if($address) : ?>
	        <address>
	          <?= $address; ?>
	        </address>
		<?php endif; ?>
      </div><!-- /.address -->
	   <div class="footer-dt">
		   <div class="social-links">
			   <?php echo get_social_links(); ?>
		   </div><!-- /.social-links -->
		   <div class="legal-protection">
			   <a href="https://www.leadingproperties.com/">
				   <img src="<?php echo get_template_directory_uri(); ?>/dist/images/leadingproperties.svg" alt="The Leading Properties of the World">
				   <p>Powered by</p>
				   <h1>The Leading Properties of the World</h1>
			   </a>
		   </div><!-- /.legal-protection -->
	   </div>
    </div>
  </div>
</footer><!-- /.site-footer -->

		      </div><!-- /.site-content-inner -->
		    </div><!-- /.site-content -->
		  </div><!-- /.menu-overlay -->

		</div><!-- /.wrap -->
        <?php wp_footer(); ?>
	</body>
</html>