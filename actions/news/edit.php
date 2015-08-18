<?php

// start a new sticky form session in case of failure
elgg_make_sticky_form('news');

$guid = get_input('guid');

// @todo Write access?
if ($guid) {
	$article = get_entity($guid);
	$new_post = false;
} else {
	$article = new ElggObject;
	$article->subtype = 'news';
	$new_post = true;
}

$fields = array(
	'title',
	'description',
	'caption', // @todo Consider saving this as description of ElggFile (image of news article)
	'tags',
);

foreach ($fields as $field) {
	$value = get_input($field);

	switch ($field) {
		case 'tags':
			if ($value) {
				$value = string_to_tag_array($value);
			} else {
				continue;
			}
			break;
	}

	$article->$field = $value;
}

// @todo Selectable access rights?
$article->access_id = ACCESS_PUBLIC;

// @todo Error handling
if ($article->save()) {
	// remove sticky form entries
	elgg_clear_sticky_form('news');

	if ($new_post) {
		elgg_create_river_item(array(
			'view' => 'river/object/news/create',
			'action_type' => 'create',
			'subject_guid' => elgg_get_logged_in_user_guid(),
			'object_guid' => $article->getGUID()
		));
	}
}

// See if new image has been added
$new_image = (isset($_FILES['image'])) && (substr_count($_FILES['image']['type'], 'image/'));

// Existing image doesn't have to be removed if overwriting it with new one anyway
if (get_input('image_delete') && !$new_image) {
	// The actual image files in dataroot are not deleted
	unset($article->icontime);
}

// Add the new image
if ($new_image) {
	$user = elgg_get_logged_in_user_entity();

	$icon_sizes = elgg_get_config('icon_sizes');
	$prefix = "news/" . $article->guid;
	$article->icontime = time();

	$filehandler = new ElggFile();
	$filehandler->owner_guid = $user->guid;
	$filehandler->container_guid = $article->guid;
	$filehandler->setFilename("$prefix.jpg");
	$filehandler->open("write");
	$filehandler->write(get_uploaded_file('image'));
	$filehandler->close();

	// Resize the images and save their file handlers into an array
	// so we can do clean up if one fails.
	$files = array();
	foreach ($icon_sizes as $name => $size_info) {
		$resized = get_resized_image_from_existing_file(
			$filehandler->getFilenameOnFilestore(),
			$size_info['w'],
			$size_info['h'],
			$size_info['square']
		);

		if ($resized) {
			$file = new ElggFile();
			$file->owner_guid = $user->guid;
			$file->container_guid = $article->guid;
			$file->setMimeType('image/jpeg');
			$file->setFilename($prefix.$name.".jpg");
			$file->open("write");
			$file->write($resized);
			$file->close();
			$files[] = $file;
		} else {
			// cleanup on fail
			foreach ($files as $file) {
				$file->delete();
			}

			unset($article->icontime);

			register_error(elgg_echo('news:error:image_resize_fail'));
			forward(REFERER);
		}
	}
}

system_message(elgg_echo('news:message:saved'));

forward($article->getUrl());
