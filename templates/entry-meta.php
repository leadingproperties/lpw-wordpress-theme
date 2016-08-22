<time class="updated" datetime="<?= get_post_time('c', true); ?>"><?= get_the_date('j F Y'); ?></time>
<?php
if($tags = get_the_tags()) {
	foreach ( $tags as $tag ) {
		echo '<span class="post-tag"><a href="' . get_tag_link( $tag->term_id ) . '"  class="tag-' . $tag->slug . '" title="View all posts in ' . esc_attr( $tag->name ) . '">' . $tag->name . '</a></span>';
	}
}
