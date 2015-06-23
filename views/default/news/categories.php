<?php
/**
 * View categories of news articles
 *
 * @uses $vars['selected']
 */

$liststr = '';

$site = elgg_get_site_entity();
$categories = $site->categories;

$selected = elgg_extract('selected', $vars);

if (!empty($categories)) {
	if (!is_array($categories)) {
		$categories = array($categories);
	}

	$url = elgg_http_remove_url_query_element(current_page_url(), 'category');

	$text = elgg_echo('all');
	$class = empty($selected) ? 'class="selected"' : '';
	$liststr .= "<li><a href=\"$url\" $class>&#171; $text</a></li>";

	if (substr_count($url, '?')) {
		$url .= "&category=";
	} else {
		$url .= "?category=";
	}

	foreach($categories as $category) {
		$link = $url . urlencode($category);

		$class = '';
		if ($category === $selected) {
			$class = 'class="selected"';
		}

		$liststr .= "<li><a href=\"$link\" $class>&#171; $category</a></li>";
	}
}

if ($liststr) {
	echo "<ul class=\"aside-category-list\">$liststr</ul>";
}
