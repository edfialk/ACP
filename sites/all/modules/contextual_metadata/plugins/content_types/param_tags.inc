<?php

$plugin = array(
  'single' => TRUE,
  'title' => t('Param Tags'),
  'description' => t('Shows the feed list of param tags retrieved from GMU.'),
  'category' => t('Contextual Metadata'),
  'edit form' => 'metadata_param_tags_edit_form',
  'render callback' => 'metadata_param_tags_render',
  'admin info' => 'metadata_param_tags_admin_info',
  'defaults' => array(
    'limit' => 100
  )
);

/**
 * Run-time rendering of the body of the block (content type)
 * See ctools_plugin_examples for more advanced info
 */
function metadata_param_tags_render($subtype, $conf, $panel_args, $context = NULL) {
  $block = new stdClass();

  $block->title = '';

  $serverUrl = variable_get('metadata_requesturl', 'http://54.191.97.139/sites/all/modules/contextual_metadata/request.php');
  $requestUrl = BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=param_tag";

  $block->content = array(
    '#theme' => 'param_tags',
    '#content' => array('url' => $serverUrl."?url=".urlencode($requestUrl) )
  );

  return $block;
}

function metadata_param_tags_edit_form_submit($form, &$form_state) {
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    if (isset($form_state['values'][$key])) {
      $form_state['conf'][$key] = $form_state['values'][$key];
    }
  }
}


function metadata_param_tags_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $form['limit'] = array(
    '#title' => t('Number of results'),
    '#description' => t('Defaults to 100.'),
    '#type' => 'textfield',
    '#default_value' => $conf['limit'],
    '#required' => TRUE,
  );

  return $form;
}

/**
 * 'admin info' callback for panel pane.
 */
function metadata_param_tags_admin_info($subtype, $conf, $contexts) {
  if (!empty($conf)) {
    $block = new stdClass;
    $block->title = $conf['override_title'] ? $conf['override_title_text'] : '';
    $block->content = t('Showing @limit feeds from GMU.', array(
      '@limit' => $conf['limit']
    ));
    return $block;
  }
}