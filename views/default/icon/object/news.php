<?php
/**
 * News icon view.
 *
 * @package News
 *
 * @uses $vars['entity']     The entity the icon represents - uses getIconURL() method
 * @uses $size       topbar, tiny, small, medium (default), large, master
 * @uses $vars['href']       Optional override for link
 * @uses $vars['img_class']  Optional CSS class added to img
 * @uses $vars['link_class'] Optional CSS class for the link
 */

$entity = $vars['entity'];

// Get size
$size = elgg_extract('size', $vars, '');
$sizes = array('small', 'medium', 'large', 'tiny', 'master', 'topbar');
if (!in_array($size, $sizes)) {
	$size = "medium";
}

// Get class
$class = elgg_extract('img_class', $vars, '');
if ($class) {
	$class .= " news-icon-$size";
} else {
	$class = "news-icon-$size";
}

// Get title
if (isset($entity->caption)) {
	$title = $entity->caption;
} else {
	if (isset($entity->name)) {
		$title = $entity->name;
	} else {
		$title = $entity->title;
	}
	$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8', false);
}

// Default parameters
$img_params = array(
	'alt' => $title,
	'title' => $title,
	'class' => $class,
);

$image_url = $entity->getIconURL($size);

if ($size === 'large') {
	// Large icon uses fixed size so set image as background
	$spacer_url = elgg_get_site_url() . 'mod/news/views/default/graphics/spacer.gif';
	$image_url = elgg_format_url($image_url);
	$img_params['src'] = $spacer_url;
	$img_params['style'] = "background-image: url($image_url);";
} else {
	$img_params['src'] = $image_url;
}

$img = elgg_view('output/img', $img_params);

$url = $entity->getURL();
if (isset($vars['href'])) {
	$url = $vars['href'];
}

if ($url) {
	$params = array(
		'href' => $url,
		'text' => $img,
		'is_trusted' => true,
	);
	$class = elgg_extract('link_class', $vars, '');
	if ($class) {
		$params['class'] = $class;
	}

	echo elgg_view('output/url', $params);
} else {
	echo $img;
}
