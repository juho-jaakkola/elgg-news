<?php
/**
 * Displays a large but compact preview
 */

$entity = elgg_extract('entity', $vars);

$image = '';
if ($entity->icontime) {
	$image = elgg_view_entity_icon($entity, 'original');
}

$icon = elgg_view_icon('clock-o');

$excerpt = elgg_get_excerpt($entity->description);

$date = elgg_view('output/date', array(
	'value' => $entity->time_created,
));

$body = <<<HTML
	<div class="elgg-news-preview">
		$image
		<div class="elgg-content">
			$icon<span class="subtitle">$date</span>
			<h2>{$entity->title}</h2>
			$excerpt
		</div>
	</div>
HTML;

echo elgg_view('object/elements/full', array(
	'summary' => $summary,
	//'icon' => $icon,
	'body' => $body,
	'class' => 'news-article',
));
