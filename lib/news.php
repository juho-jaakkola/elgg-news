<?php

/**
 * List all news articles.
 *
 * @return array
 */
function news_get_page_content_list() {
	// Get the latest article
	$newest = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'news',
		'limit' => 1,
	));

	if (!$newest) {
		// No articles
		$content = elgg_echo('news:none');
	} else {
		// Display the first one separately
		$newest_article = elgg_view('news/preview', array('entity' => $newest[0]));

		$list = elgg_list_entities(array(
			'type' => 'object',
			'subtype' => 'news',
			'wheres' => array("guid != {$newest[0]->guid}"),
			'full_view' => FALSE,
			'gallery_class' => 'elgg-gallery-news',
			'list_class' => 'elgg-list-news',
			'list_type' => 'gallery',
			'limit' => 6,
		));

		$content = $newest_article . $list;
	}

	// Add latest comments to sidebar
	$latest_comments = elgg_view('page/elements/comments_block', array('subtypes' => 'news'));

	$return = array(
		'title' => elgg_echo('news'),
		'sidebar' => $latest_comments,
		'content' => $content,
	);

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

	elgg_push_breadcrumb($article->title);

	$return['content'] = elgg_view_entity($article, array('full_view' => true));

	$return['content'] .= '<div class="clearfloat"></div>';

	// Check to see if comment are on
	if (elgg_get_plugin_setting('comments_on', 'news') != 'no') {
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