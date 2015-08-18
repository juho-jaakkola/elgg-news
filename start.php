<?php
/**
* News plugin
* @package AkNews
* @license http://www.gnu.org/licenses/gpl.html GNU Public License version 2
* @author Juho Jaakkola <juho.jaakkola@mediamaisteri.com>
* @copyright (C) Mediamaisteri Group 2012
* @link http://www.mediamaisteri.com/
*/

function news_init() {
	// register a library of helper functions
	elgg_register_library('elgg:news', elgg_get_plugins_path() . 'news/lib/news.php');

	// Extend system CSS with our own styles, which are defined in the blog/css view
	elgg_extend_view('css/elgg','news/css');

	// Register new entity types
	//add_subtype('object', 'news', 'ElggNews');

	// Register for search
	elgg_register_entity_type('object', 'news');

	// Register page handler
	elgg_register_page_handler('news', 'news_page_handler');

	// Register url handlers for entities
	elgg_register_plugin_hook_handler('entity:url', 'object', 'news_url_handler');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'news_icon_url_override');

	// Register an icon handler for news
	elgg_register_page_handler('articleicon', 'news_icon_handler');

	elgg_register_plugin_hook_handler('register', 'menu:entity', 'news_entity_menu_setup');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'news/actions/news';
	elgg_register_action('news/edit', "$action_path/edit.php", 'admin');
	elgg_register_action('news/delete', "$action_path/delete.php", 'admin');

	// section, name, (parent)
	elgg_register_admin_menu_item('administer', 'all', 'news');
	elgg_register_admin_menu_item('administer', 'add', 'news');

	elgg_register_menu_item('site', array(
		'name' => 'news',
		'href' => 'news',
		'text' => elgg_echo('news'),
	));
}

/**
 * Format and return the URL for entity.
 *
 * @param news $entity
 * @return string URL of entity.
 */
function news_url_handler($hook, $type, $url, $params) {
	$entity = elgg_extract('entity', $params);

	if ($entity->getSubtype() !== 'news') {
		return $url;
	}

	$name = elgg_get_friendly_title($entity->title);

	return "news/view/{$entity->guid}/$name";
}

/**
 * Override the default entity icon for news
 *
 * @return string Relative URL
 */
function news_icon_url_override($hook, $type, $returnvalue, $params) {
	$article = $params['entity'];

	if ($article->getSubType() != 'news') {
		return $returnvalue;
	}

	$size = $params['size'];

	if (isset($article->icontime)) {
		// return thumbnail
		$icontime = $article->icontime;
		return "articleicon/$article->guid/$size/$icontime.jpg";
	}

	// @todo Do we need default news icon?
	//return "mod/articles/graphics/default{$size}.gif";
}

/**
 * Handle article icons.
 *
 * @param array $page
 * @return void
 */
function news_icon_handler($page) {
	// The username should be the file we're getting
	if (isset($page[0])) {
		set_input('guid', $page[0]);
	}
	if (isset($page[1])) {
		set_input('size', $page[1]);
	}
	// Include the standard profile index
	$plugin_dir = elgg_get_plugins_path();
	include("$plugin_dir/news/icon.php");
	return true;
}

/**
 * Dispatches pages.
 *
 * @param array $page
 */
function news_page_handler($page) {
	elgg_load_library('elgg:news');

	$page_type = $page[0];
	if (!$page_type) {
		$page_type = 'all';
	}

	if (isset($page[1])) {
		elgg_push_breadcrumb(elgg_echo('news'), 'news');
	} else {
		// No link in list view
		elgg_push_breadcrumb(elgg_echo('news'));
	}

	// @todo Is there a need for CRUD page outside the admin panel?
	switch ($page_type) {
		case 'view':
			$params = news_get_page_content_read($page[1]);
			break;
		case 'add':
			set_input('container_guid', $page[1]);
			$params = array(); //@todo
			break;
		case 'edit':
			set_input('guid', $page[1]);
			$params = array(); //@todo
			break;
		case 'all':
			news_register_toggle();
			$params = news_get_page_content_list();
			break;
		default:
			return false;
	}

	$params['filter'] = '';

	if (isset($params['sidebar'])) {
		// View default sidebar and page specific content
		$params['sidebar'] .= elgg_view('news/sidebar', array('page' => $page_type));
	} else {
		// View default sidebar
		$params['sidebar'] = elgg_view('news/sidebar', array('page' => $page_type));
	}

	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($params['title'], $body);
	return true;
}

/**
 * Add/Remove particular links/info in entity menu
 */
function news_entity_menu_setup($hook, $type, $return, $params) {
	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);

	if ($handler != 'news') {
		return $return;
	}

	$denied_items = array('access');

	if (elgg_is_admin_logged_in()) {
		// Remove items associated with likes plugin
		$denied_items = array('likes', 'likes_count', 'tags');

		$label = elgg_echo('edit');

		$options = array(
			'name' => 'edit',
			'text' => "<span>$label</span>",
			'href' => "admin/news/edit/?guid=$entity->guid",
		);

		$return[] = ElggMenuItem::factory($options);
	}

	// Remove items from menu depending on situation
	foreach ($return as $index => $item) {
		if (in_array($item->getName(), $denied_items)) {
			unset($return[$index]);
		}
	}

	return $return;
}

/**
 * Adds a toggle to extra menu for switching between list and gallery views
 */
function news_register_toggle() {
	$url = elgg_http_remove_url_query_element(current_page_url(), 'list_type');

	if (get_input('list_type', 'list') == 'list') {
		$list_type = "gallery";
		$icon = elgg_view_icon('grid');
	} else {
		$list_type = "list";
		$icon = elgg_view_icon('list');
	}

	if (substr_count($url, '?')) {
		$url .= "&list_type=" . $list_type;
	} else {
		$url .= "?list_type=" . $list_type;
	}

	elgg_register_menu_item('extras', array(
		'name' => 'news_list',
		'text' => $icon,
		'href' => $url,
		'title' => elgg_echo("news:list:$list_type"),
		'priority' => 1000,
	));
}

elgg_register_event_handler('init', 'system', 'news_init');
