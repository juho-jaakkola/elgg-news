<?php

/**
 * List all news articles.
 *
 * @return array
 */
function news_get_page_content_list() {
	$return = array();

	$options = array(
		'type' => 'object',
		'subtype' => 'news',
		'full_view' => FALSE,
		'gallery_class' => 'elgg-gallery-news',
		'list_class' => 'elgg-list-news',
	);

	$site = elgg_get_site_entity();

	// Support for filtering by category
	$category = get_input('category');
	if ($category) {
		if (in_array($category, $site->categories)) {
			elgg_push_breadcrumb($category);
		}

		$options['metadata_name_value_pairs'] = array(
			'universal_categories' => $category,
		);
	}

	$return['title'] = elgg_echo('news');
	$return['filter_context'] = 'all';

	$list = elgg_list_entities_from_metadata($options);

	if (!$list) {
		$return['content'] = elgg_echo('news:none');
	} else {
		$return['content'] = $list;
	}

	// Add categories to sidebar
	$categories = elgg_view('news/categories', array('selected' => $category));
	$return['sidebar'] = elgg_view_module('aside', elgg_echo('news:categories'), $categories);

	// Add latest comments to sidebar
	$latest_comments = elgg_view('page/elements/comments_block', array('subtypes' => 'news'));
	$return['sidebar'] .= $latest_comments;


	return $return;
}

/**
 * List all news articles.
 *
 * @param int $guid Guid of the news article
 * @return array
 */
function news_get_page_content_read($guid = NULL) {
	$return = array();

	$article = get_entity($guid);

	// no header or tabs for viewing an individual article
	$return['filter'] = '';

	if (!elgg_instanceof($article, 'object', 'news')) {
		$return['content'] = elgg_echo('news:error:post_not_found');
		return $return;
	}

	$return['title'] = htmlspecialchars($article->title);

	// @todo Is it ok to use category in breadcrumbs since there may be multiple?
	if ($article->universal_categories) {
		if (is_array($article->universal_categories)) {
			$category = array_pop($article->universal_categories);
		} else {
			$category = $article->universal_categories;
		}

		elgg_push_breadcrumb($category, "news?category=$category");
	}
	elgg_push_breadcrumb($article->title);

	$return['content'] = elgg_view_entity($article, array('full_view' => true));

	$return['content'] .= '<div class="clearfloat"></div>';

	// Check to see if comment are on
	if (elgg_get_plugin_setting('comments_on') != 'no') {
		$return['content'] .= '<a name="news-comments"></a>';
		$return['content'] .= elgg_view_comments($article);
	}

	return $return;
}

/**
 * Pull together news variables for the save form
 *
 * @param ElggNews $article
 * @return array
 */
function news_prepare_form_vars($article = NULL) {

	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'access_id' => ACCESS_DEFAULT,
		'tags' => NULL,
		'caption' => NULL,
		'guid' => NULL,
	);

	if ($article) {
		foreach (array_keys($values) as $field) {
			if (isset($article->$field)) {
				$values[$field] = $article->$field;
			}
		}

		// Add also the whole entity
		$values['entity'] = $article;
	}

	if (elgg_is_sticky_form('news')) {
		$sticky_values = elgg_get_sticky_values('news');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('news');

	return $values;
}