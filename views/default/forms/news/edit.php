<?php

$form = array();

$form[] = array(
	'label' => elgg_echo('news:title'),
	'body' => elgg_view('input/text', array(
		'name' => 'title',
		'value' => $vars['title'],
	)
));

$form[] = array(
	'label' => elgg_echo('news:description'),
	'body' => elgg_view('input/longtext', array(
		'name' => 'description',
		'value' => $vars['description'],
	)
));

$form[] = array(
	'label' => elgg_echo('tags'),
	'body' => elgg_view('input/tags', array(
		'name' => 'tags',
		'value' => $vars['tags'],
	)
));

$image_body = elgg_view('input/file', array(
	'name' => 'image',
));

// View image if exists
if ($vars['entity']->icontime) {
	$image_body .= "<br />";
	$image_body .= elgg_view_entity_icon($vars['entity'], 'small');

	// Option for deleting existing image
	$image_body .= elgg_view('input/checkbox', array(
		'name' => 'image_delete',
	));
	$image_body .= elgg_echo('news:image:delete');
}

$form[] = array(
	'label' => elgg_echo('news:image'),
	'body' => $image_body,
);

$form[] = array(
	'label' => elgg_echo('news:image:caption'),
	'body' => elgg_view('input/text', array(
		'name' => 'caption',
		'value' => $vars['caption'],
	)
));

foreach ($form as $field) {
	if (isset($field['label'])) {
		$label = "<label>{$field['label']}</label>";
	} else {
		$label = '';
	}
	echo "<div>{$label}{$field['body']}</div>";
}

echo elgg_view('input/hidden', array(
	'name' => 'guid',
	'value' => $vars['guid'],
));

echo "<br />";
echo elgg_view('input/submit', array('value' => elgg_echo('save')));
