<?php
/**
 * Delete news entity
 *
 * @package AkNews
 */

$entity_guid = get_input('guid');
$entity = get_entity($entity_guid);

if (elgg_instanceof($entity, 'object', 'news')) {
	if ($entity->delete()) {
		system_message(elgg_echo('news:message:deleted_post'));
	} else {
		register_error(elgg_echo('news:error:cannot_delete_post'));
	}
} else {
	register_error(elgg_echo('news:error:post_not_found'));
}

forward(REFERER);