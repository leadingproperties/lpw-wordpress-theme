<?php
  global $header_type;
	$header_type = 'default';
    $style = '';
	  if(get_field('image_banner')) {
	    $header_type = 'image';
		$style = ' style="background-image: url(' . get_field('banner_image') . '); height: ' . get_field('banner_height') . 'px;"';
	  }
?>
<div class="<?php if($header_type === 'image') { echo 'page-banner'; } else { echo 'page-header'; } ?>"<?= $style; ?>>
	<?php if($header_type === 'default') : ?>
	  <div class="container">
	    <h1><?php the_title(); ?></h1>
	  </div>
	<?php endif; ?>
</div>
