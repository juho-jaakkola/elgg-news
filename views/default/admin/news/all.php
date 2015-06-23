<?php
/**
 * View news as table.
 */

$limit = get_input('limit', 5);
$offset = get_input('offset');

$options = array(
	'type' => 'object',
	'subtype' => 'news',
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => false,
);

$news = elgg_get_entities($options);

if ($news) {

	$alt = '';

	$options['count'] = true;
	$count = elgg_get_entities($options);

	$nav = elgg_view('navigation/pagination',array(
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
	));

	echo $nav;
?>

	<table class="elgg-table">
		<tr>
			<th></th>
			<th><?php echo elgg_echo('Guid'); ?></th>
			<th><?php echo elgg_echo('title'); ?></th>
			<th><?php echo elgg_echo('news:date_created'); ?></th>
			<th></th>
			<th></th>
		</tr>

<?php
	foreach ($news as $article) {
		$icon = elgg_view_entity_icon($article, 'tiny');

		$title = elgg_view('output/url', array(
			'text' => $article->title,
			'href' => $article->getUrl(),
		));

		$date = elgg_view('output/date', array('value' => $article->time_created));

		$edit = elgg_view('output/url', array(
			'text' => elgg_echo('edit'),
			'href' => "admin/news/edit?guid={$article->guid}",
		));

		$delete = elgg_view('output/confirmlink', array(
			'text' => elgg_view_icon('delete'),
			'href' => "action/news/delete?guid={$article->guid}",
			'is_action' => true,
		));

		echo "<tr $alt>";
			echo "<td>$icon</td>";
			echo "<td>$article->guid</td>";
			echo "<td>$title</td>";
			echo "<td>$date</td>";
			echo "<td>$edit</td>";
			echo "<td>$delete</td>";
		echo "</tr>";

		$alt = $alt ? '' : 'class="alt"';
	}
	echo "</table>";

	echo $nav;
}

