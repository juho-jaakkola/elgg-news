<?php
/**
 * View full, list or gallery view.
 *
 * @uses $vars['entity']     The entity the icon represents - uses getIconURL() method
 * @uses $vars['full_view']  Full or list view
 * @uses $vars['size']       topbar, tiny, small, medium (default), large, master, none
 */

$full    = elgg_extract('full_view', $vars, FALSE);
$article = elgg_extract('entity', $vars, FALSE);
$size    = elgg_extract('size', $vars, 'master');

if (!$article) {
	return TRUE;
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $article,
	'handler' => 'news',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

// The "on" status changes for comments, so best to check for !no
if (elgg_get_plugin_setting('comments_on', 'news') != 'no') {
	$comments_count = $article->countComments();
	//only display if there are commments
	if ($comments_count != 0) {
		$text = elgg_echo("comments") . " ($comments_count)";
		$comments_link = elgg_view('output/url', array(
			'href' => $article->getURL() . '#news-comments',
			'text' => $text,
			'is_trusted' => true,
		));
	} else {
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

// @todo Make date format generic
$date = date("j.m.Y", $article->time_created);

if ($full) {
	$owner = $article->getOwnerEntity();
	$owner_icon = elgg_view_entity_icon($owner, 'tiny');
	$owner_link = elgg_view('output/url', array(
		'href' => $owner->getUrl(),
		'text' => $owner->name,
		'is_trusted' => true,
	));

	$author_text = elgg_echo('byline', array($owner_link));
	$tags = elgg_view('output/tags', array('tags' => $article->tags));

	$subtitle = "$author_text $date $comments_link";

	$params = array(
		'entity' => $article,
		'title' => false,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
	);
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	$body = '';

	if ($article->icontime) {
		$icon = elgg_view_entity_icon($article, 'master');

		$caption = "<div>{$article->caption}</div>";
		$image_block = '<div class="news-image-block">' . $icon . $caption . "</div>";

		$body .= $image_block;
	}

	$article = elgg_view('output/longtext', array(
		'value' => $article->description,
		'class' => 'article-post',
	));

	$body .= $article;

	echo elgg_view('object/elements/full', array(
		'summary' => $summary,
		//'icon' => $icon,
		'body' => $body,
		'class' => 'news-article',
	));
} elseif (elgg_in_context('gallery')) {
	echo '<div class="news-gallery-item">';
	echo "<a href=\"{$article->getURL()}\">";
	if ($size !== 'none') {
		echo elgg_view_entity_icon($article, $size, array('href' => false));
	}
	echo elgg_view_icon('clock-o');
	echo "<span class='subtitle'>$date</span>";
	echo "<h3>" . elgg_get_excerpt($article->title, 60) . "</h3>";
	echo '</a>';
	echo '</div>';
} else {
	$title = elgg_view('output/url', array(
		'href' => $article->getURL(),
		'text' => elgg_get_excerpt($article->title, 40),
	));

	$subtitle = "$date $comments_link";

	$params = array(
		'metadata' => $metadata,
		'title' => $title,
		'subtitle' => $subtitle,
		'content' => elgg_get_excerpt($article->description),
		'tags' => false,
	);

	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	$image = elgg_view_entity_icon($article, $size);

	echo elgg_view_image_block($image, $list_body);
}