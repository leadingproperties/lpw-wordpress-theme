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
		<?php if( have_rows('offices') ) : ?>
		<div class="row offices-row">
			<?php while( have_rows('offices') ) : the_row();
				$img =  wp_get_attachment_image_url(get_sub_field('image'), 'office');//lwp_get_image(get_sub_field('image'), 'office');
				$title = get_sub_field('title');
				$address = get_sub_field('address');
				?>
				<div class="office-box eqh">
					<div class="office-photo">
						<?php if( $img ) { ?>
							<img src="<?= $img; ?>" alt="<?= $title; ?>" class="img-responsive">
						<?php } ?>
					</div>
					<h2 class="office-title"><?= $title; ?></h2>
					<address class="office-address">
						<?= $address; ?>
					</address>
				</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>
		<?php if($a_title = get_field('agents_title')) {
			echo '<h2 class="agents-title">' . $a_title . '</h2>';
		} ?>
		<?php if( have_rows('agents') ) : ?>
		<div class="row agents-row">
			<?php while( have_rows('agents') ) : the_row();
				$photo = wp_get_attachment_image_url(get_sub_field('photo'), 'agent'); //lwp_get_image(get_sub_field('photo'), 'agent');
				$name = get_sub_field('name');
				$info = get_sub_field('info');
				?>
				<div class="team-box eqh">
					<?php if($photo) { ?>
						<div class="team-photo">
							<img src="<?= $photo; ?>" alt="<?= $name; ?>" class="img-responsive">
						</div>
					<?php } ?>
					<h2 class="team-name"><?= $name; ?></h2>
					<p class="team-contact"><?= $info; ?></p>
				</div>
			<?php endwhile; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

