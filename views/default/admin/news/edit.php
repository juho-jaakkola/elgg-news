<?php

elgg_load_library('elgg:news');

$guid = get_input('guid');

if ($guid) {
	$entity = get_entity($guid);
	$body_vars = news_prepare_form_vars($entity);
} else {
	$body_vars = news_prepare_form_vars();
}

$form_vars = array('enctype' => 'multipart/form-data');

echo elgg_view_form('news/edit', $form_vars, $body_vars);
