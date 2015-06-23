<?php
/**
 * News plugin settings
 */

// set default value
if (!isset($vars['entity']->comments_on)) {
	$vars['entity']->comments_on = 'no';
}

echo '<div>';
echo elgg_echo('news:comments_on');
echo ' ';
echo elgg_view('input/dropdown', array(
	'name' => 'params[comments_on]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	),
	'value' => $vars['entity']->comments_on,
));
echo '</div>';
