<?php

$plugin = array(
  'single' => TRUE,
  'title' => t('App Tags'),
  'description' => t('Shows the feed list of app tags retrieved from GMU.'),
  'category' => t('Contextual Metadata'),
  'edit form' => 'contextual_metadata_param_tags_edit_form',
  'render callback' => 'contextual_metadata_app_tags_render',
  'admin info' => 'contextual_metadata_param_tags_admin_info',
  'defaults' => array(
    'limit' => 100
  )
);

/**
 * Run-time rendering of the body of the block (content type)
 * See ctools_plugin_examples for more advanced info
 */
function contextual_metadata_app_tags_render($subtype, $conf, $panel_args, $context = NULL) {
  $block = new stdClass();

  $block->title = '';

  $serverUrl = variable_get('contextual_requesturl', 'http://54.191.97.139/sites/all/modules/contextual_metadata/request.php');
  $requestUrl = BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=app_tag";

  $block->content = array(
    '#theme' => 'app_tags',
    '#content' => array('url' => $serverUrl."?url=".urlencode($requestUrl) )
  );

  return $block;
}