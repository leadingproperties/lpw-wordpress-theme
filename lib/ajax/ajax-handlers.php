<?php

add_action('wp_ajax_do_ajax', 'ajax_handler');
add_action('wp_ajax_nopriv_do_ajax', 'ajax_handler');

function ajax_handler() {

	switch($_REQUEST['fn']) {
		case 'get_objects':
			$output = ajax_get_objects($_REQUEST);
			break;
		case 'get_shorten_url':
			$output = get_shorten_url($_REQUEST['url']);
			break;
		case 'get_blog_posts':
			$tag = (isset($_REQUEST['tag'])) ? (int) $_REQUEST['tag'] : null;

			$output = ajax_get_posts((int) $_REQUEST['posts_per_page'], (int) $_REQUEST['offset'], $tag);
			break;
		case 'get_single_post':
			$output = ajax_get_single_post($_REQUEST['id'], $_REQUEST['type']);
			break;
		default :
			$output = 'No function specified, check your jQuery.ajax() call';
			break;
	}
	if ( is_array( $output ) ) {
		print_r( $output );
	} else {
		echo $output;
	}
	die;
}

function ajax_get_objects($args) {
	if(isset($args['fn'])) {
		unset($args['fn']);
	}
	if(isset($args['action'])) {
		unset($args['action']);
	}
	$objects = new LP_ObjectList($args);
	return $objects->get_json_objects();
}

function get_shorten_url($url) {
	$api_key = get_field('google_shortener_api', 'option');


	if( $api_key ) :
		$api_url = 'https://www.googleapis.com/urlshortener/v1/url?key=' . $api_key;
		$content = json_encode([
			'longUrl' => $url
		]);

		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			["Content-type: application/json"]);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$response = curl_exec($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		curl_close($curl);

		echo $response;

		return $body;

	else :
		return json_encode(['error' => true, 'error_message' => 'No API key']);
	endif;
}

function ajax_get_posts($per_page, $offset, $tag) {

	$args = [
		'post_type'         => 'post',
		'posts_per_page'    => $per_page,
		'offset'            => $offset
	];
	if( $tag ) {
		$args['tag_id'] = $tag;
	}
	$return = [];
	$posts = new WP_Query($args);
	if($posts->have_posts()) {

		while ($posts->have_posts()) {
			$posts->the_post();
			$img = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'post-thumbnail');
			$tagstring = '';
			if($tags = get_the_tags()) {
				foreach ( $tags as $tag ) {
					$tagstring .= '<span class="post-tag"><a href="' . get_tag_link( $tag->term_id ) . '" class="tag-' . $tag->slug . '" title="View all posts in ' . esc_attr( $tag->name ) . '">' . $tag->name . '</a></span>';
				}
			}

			$return[] = [
				'id'    => get_the_ID(),
				'title' => get_the_title(),
				'link' => get_permalink(),
				'image' => $img[0],
				'dates' => array(
					'view' => get_the_date('j M Y'),
					'format' => get_the_date('c')
				),
				'tag'   => $tagstring
			];
		}
		return json_encode($return);
	} else {
		return false;
	}
}

function ajax_get_single_post($id, $type) {
	$args = [
		'post_type'         => 'post'
	];
	if($type === 'slug') {
		$args['name'] = $id;
	} else {
		$args['p'] = (int) $id;
	}
	$return = [];
	$post = new WP_Query($args);
	if( $post->have_posts()) {
		while($post->have_posts()) {
			$post->the_post();

			$img = wp_get_attachment_image_src(get_post_thumbnail_id( get_the_ID() ), 'featured');
			$tagstring = '';
			if($tags = get_the_tags()) {
				foreach ( $tags as $tag ) {
					$tagstring .= '<span class="post-tag"><a href="' . get_tag_link( $tag->term_id ) . '" class="tag-' . $tag->slug . '" title="View all posts in ' . esc_attr( $tag->name ) . '">' . $tag->name . '</a></span>';
				}
			}

			$return['post'] = [
				'id'    => get_the_ID(),
				'title' => get_the_title(),
				'link' => get_permalink(),
				'image' => $img[0],
				'content'   => apply_filters( 'the_content', get_the_content() ),
				'dates' => array(
					'view' => get_the_date('j M Y'),
					'format' => get_the_date('c')
				),
				'tag'   => $tagstring,
				'url' => urlencode(get_the_permalink()),
				'share_title' => urlencode(html_entity_decode(get_the_title())),
				'excerpt'   => urlencode(get_the_excerpt())
			];
			$return['adj'] = get_lp_adjacent_posts();
		}
		wp_reset_postdata();
		return json_encode($return);
	} else {
		return false;
	}

}