<?php
function get_lp_excerpt() {
	$excerpt = get_the_content();
	$excerpt = preg_replace(" (\[.*?\])",'',$excerpt);
	$excerpt = strip_shortcodes($excerpt);
	$excerpt = strip_tags($excerpt);
	$excerpt = substr($excerpt, 0, 240);
	$excerpt = substr($excerpt, 0, strripos($excerpt, " "));
	$excerpt = trim(preg_replace( '/\s+/', ' ', $excerpt));
	$excerpt .= '&hellip; ';
	return $excerpt;
}

function show_lp_adjacent_posts(){
	global $post;
	$prev_post_obj = get_adjacent_post(false,'',true);
	$next_post_obj = get_adjacent_post(false,'',false);
	$both = [];

	if(empty($next_post_obj)){
		$exclude = array($post->ID);
		if(!empty($prev_post_obj)){
			$exclude[] =  $prev_post_obj->ID;
		}
		$next_post_arr = get_posts(array(
			'posts_per_page' => 1,
			'order' => 'ASC',
			'exclude' => $exclude
		));
		$next_post_obj = $next_post_arr[0];
	}
	if(empty($prev_post_obj)){
		$exclude = array($post->ID);
		if(!empty($next_post_obj)){
			$exclude[] =  $next_post_obj->ID;
		}
		$prev_post_arr = get_posts(array(
			'posts_per_page' => 1,
			'order' => 'DESC',
			'exclude' => $exclude
		));
		$prev_post_obj = $prev_post_arr[0];
	}

	if(!empty($prev_post_obj) && !empty($next_post_obj)){
		array_push($both, $prev_post_obj, $next_post_obj);

		echo '<div class="adjacent-posts-container blog-list-wrapper">
            <div class="container">
              <div class="row">';

		foreach($both as $post_obj) {
			$post_link  = get_permalink( $post_obj->ID );
			$post_title = get_the_title( $post_obj->ID );
			$img        = wp_get_attachment_image_src( get_post_thumbnail_id( $post_obj->ID ), 'post-thumbnail' );
			echo '<article id="post-' . $post_obj->ID . '" class="blog-item" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
              <div class="blog-inner-wrapper">';
			if ( $img ) {
				echo '<div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
          <meta itemprop="contentUrl" content="' . $img[0] . '">
            <a href="' . $post_link . '" class="blog-thumbnail-holder">
              <img src="' . $img[0] . '" alt="' . $post_title . '" class="img-responsive" itemprop="contentUrl">
            </a>
        </div>';
			}
			echo '<div class="blog-info-holder">
                <h2 class="info-title" itemprop="headline"><a href="' . $post_link . '">' . $post_title . '</a></h2>
        <div class="entry-meta">
          <time class="updated" datetime="' . get_post_time( 'c', true ) . '">' . get_the_date( 'j F Y' ) . '</time>';
			if ( $tags = get_the_tags() ) {
				foreach ( $tags as $tag ) {
					echo '<span class="post-tag"><a href="' . get_tag_link( $tag->term_id ) . '"  class="tag-' . $tag->slug . '" title="View all posts in ' . esc_attr( $tag->name ) . '">' . $tag->name . '</a></span>';
				}
			}

			echo '</div>
      </div>
    </div>
    </article><!-- /.blog-item -->';
		}
		echo '</div>
            </div>
            </div>';
	}
}

function get_lp_adjacent_posts() {
	global $post;
	$prev_post_obj = get_adjacent_post(false,'',true);
	$next_post_obj = get_adjacent_post(false,'',false);
	$both = [];
	$return = [];

	if(empty($next_post_obj)){
		$exclude = array($post->ID);
		if(!empty($prev_post_obj)){
			$exclude[] =  $prev_post_obj->ID;
		}
		$next_post_arr = get_posts(array(
			'posts_per_page' => 1,
			'order' => 'ASC',
			'exclude' => $exclude
		));
		$next_post_obj = $next_post_arr[0];
	}
	if(empty($prev_post_obj)){
		$exclude = array($post->ID);
		if(!empty($next_post_obj)){
			$exclude[] =  $next_post_obj->ID;
		}
		$prev_post_arr = get_posts(array(
			'posts_per_page' => 1,
			'order' => 'DESC',
			'exclude' => $exclude
		));
		$prev_post_obj = $prev_post_arr[0];
	}

	if(!empty($prev_post_obj) && !empty($next_post_obj)){
		array_push($both, $next_post_obj, $prev_post_obj);

		foreach ($both as  $post_obj) {
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_obj->ID ), 'post-thumbnail' );
			$img = ($img[0]) ? $img[0] : '';
			$tagstring = '';
			if($tags = get_the_tags()) {
				foreach ( $tags as $tag ) {
					$tagstring .= '<span class="post-tag"><a href="' . get_tag_link( $tag->term_id ) . '" class="tag-' . $tag->slug . '" title="View all posts in ' . esc_attr( $tag->name ) . '">' . $tag->name . '</a></span>';
				}
			}
			$return[] = [
				'id'    =>  $post_obj->ID,
				'title' => $post_obj->post_title,
				'link' => get_permalink($post_obj->ID),
				'image' => $img,
				'dates' => array(
					'view' => get_the_date('j M Y'),
					'format' => get_the_date('c')
				),
				'tag'   => $tagstring
			];
		}

		return $return;
	}
	return false;
}