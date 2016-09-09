<?php
	global $header_type;
	if( $header_type === 'image' ) : ?>
		<div class="page-title">
			<div class="container">
				<h1><?php the_title(); ?></h1>
			</div>
		</div>
	<?php endif; ?>
<div class="entry-content">
	<div class="container">
		<?php the_content(); ?>
	</div>
</div>

