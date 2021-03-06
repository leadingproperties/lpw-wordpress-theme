<?php

add_action('wp_ajax_do_ajax', 'ajax_handler');
add_action('wp_ajax_nopriv_do_ajax', 'ajax_handler');

function ajax_handler() {

		switch($_REQUEST['fn']) {
		case 'get_objects':
			$output = ajax_get_objects($_REQUEST);
			break;
		case 'get_object':
			$output = ajax_get_single_object($_REQUEST);
			break;
		case 'get_shorten_url':
			$output = ajax_get_shorten_url($_REQUEST['url']);
			break;
		case 'get_blog_posts':
			$tag = (isset($_REQUEST['tag'])) ? (int) $_REQUEST['tag'] : null;

			$output = ajax_get_posts((int) $_REQUEST['posts_per_page'], (int) $_REQUEST['offset'], $tag);
			break;
		case 'get_single_post':
			$output = ajax_get_single_post($_REQUEST['id'], $_REQUEST['type']);
			break;
		case 'get_suggestions':
			if(isset($_GET['query']) && !empty($_GET['query'])) {
				$output = ajax_get_suggestions( [
					'query' => $_GET['query'],
					'action' => 'get_suggestions',
					'scope' => ($_GET['scope']) ? $_GET['scope'] : false
				] );
			} else {
				$output = '';
			}
			break;
		case 'get_geopoints':
			$output = ajax_get_geopoints([
				'type' => $_REQUEST['type'],
				'rent_category' => ($_REQUEST['rent_category']) ? $_REQUEST['rent_category'] : 'long_rent',
				'action' => 'get_geopoints'
			]);
			break;
		case 'contact_form':
			$output = ajax_contact_form($_REQUEST);
			break;
        case 'get_tags':
            $output = ajax_build_tags($_REQUEST);
            break;
		case 'get_pdf':
			$output = ajax_get_pdf($_REQUEST);
			break;
		case 'get_subtypes':
			$output = ajax_get_subtypes($_REQUEST);
			break;
			case 'get_tips':
				$output = ajax_get_tips($_REQUEST);
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
	$args['action'] = 'get_objects';
	return json_encode(get_object_list($args));
}
function ajax_get_single_object($args) {
	if(isset($args['fn'])) {
		unset($args['fn']);
	}
	$args['action'] = 'get_objects';
	$return =  single_object_html($args);
	if( is_array($return) ) {
		return json_encode($return);
	} else {
		return $return;
	}
}
function ajax_get_shorten_url($url) {
	return json_encode(get_shorten_url($url));
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
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_REFERER, home_url('/'));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			["Content-type: application/json"]);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$response = curl_exec($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		curl_close($curl);

		return json_decode($response);

	else :
		return ['id' => $url, 'error' => true, 'error_message' => 'No API key'];
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
function ajax_get_suggestions($args) {
	$return = new LP_ObjectList($args);
	return $return->get_json_objects();
}
function ajax_get_geopoints($args) {
	$return = new LP_ObjectList($args);
	return $return->get_json_objects();
}
function ajax_contact_form($args) {
	if(isset($args['fn'])) {
		unset($args['fn']);
	}
	$args['action'] = 'request_form';
	$return = new LP_ObjectList($args);
	return $return->send_request_form();
}
function ajax_build_tags($args){
    $tags = new \LPW\Tags($args);
    return $tags->get_tags_html($args);
}
function ajax_get_pdf($args) {
	$args['action'] = 'get_pdf';
	$return = new LP_ObjectList($args);
	return $return->get_json_objects();
}
function ajax_get_subtypes($args) {
	global $lp_settings;
	$args['action'] = 'get_subtypes';
	$args['lang'] = $lp_settings['lang'];
	$return = new LP_ObjectList($args);
	return $return->get_json_objects();
}
function ajax_get_tips($args) {
	$args['action'] = 'get_tips';
	$tips = new LP_ObjectList($args);
	return json_encode([
		'tips'  => $tips->get_objects_array(),
		'search_string' => __('search_panel:tips', 'leadingprops')
	]);
}