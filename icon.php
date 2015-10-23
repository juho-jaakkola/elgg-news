<?php
/**
 * Icon display
 *
 * @package news
 */

$article_guid = get_input('guid');
$article = get_entity($article_guid);

// If is the same ETag, content didn't changed.
$etag = $article->icontime . $article_guid;
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
	header("HTTP/1.1 304 Not Modified");
	exit;
}

$size = strtolower(get_input('size'));
if (!in_array($size, array('original', 'large', 'medium', 'small', 'tiny', 'master', 'topbar'))) {
	$size = "medium";
}

if ($size === 'original') {
	// The original image gets saved without prefix
	$filename = "{$article->guid}.jpg";
} else {
	$filename = "{$article->guid}{$size}.jpg";
}

$success = false;

$filehandler = new ElggFile();
$filehandler->owner_guid = $article->owner_guid;
$filehandler->setFilename("news/{$filename}");

$success = false;
if ($filehandler->open("read")) {
	if ($contents = $filehandler->read($filehandler->getSize())) {
		$success = true;
	}
}

if (!$success) {
	$location = elgg_get_plugins_path() . "news/graphics/default{$size}.jpg";
	$contents = @file_get_contents($location);
}

header("Content-type: image/jpeg");
header('Expires: ' . date('r',time() + 864000));
header("Pragma: public");
header("Cache-Control: public");
header("Content-Length: " . strlen($contents));
header("ETag: $etag");
echo $contents;
