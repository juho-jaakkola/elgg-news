<?php

elgg_load_library('elgg:news');

$form_vars = array('enctype' => 'multipart/form-data',);
$body_vars = news_prepare_form_vars();

echo elgg_view_form('news/edit', $form_vars, $body_vars);
