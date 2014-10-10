<?php

$plugin = array(
  'single' => TRUE,
  'title' => t('Feeds'),
  'description' => t('Shows log/news retrieved from GMU filtered by tags.'),
  'category' => t('Contextual Metadata'),
  'edit form' => 'contextual_feeds_edit_form',
  'render callback' => 'contextual_feeds_render',
  'admin info' => 'contextual_feeds_admin_info',
  'defaults' => array(
    'limit' => 100,
    'sensor_tag' => 'Atmospheric Infrared Sounder',
    'param_tag' => 'carbon monoxide',
    'app_tag' => 'atmospheric chemistry'
  )
);

function contextual_feeds_edit_form($form, &$form_state) {
  $conf = $form_state['conf'];

  $form['limit'] = array(
    '#title' => t('Number of results'),
    '#description' => t('Defaults to 100.'),
    '#type' => 'textfield',
    '#default_value' => $conf['limit'],
    '#required' => TRUE,
  );

  $form['sensor_tag'] = array(
    '#title' => t('Sensor tag'),
    '#description' => t('Sensor Tag to filter on, allowed values are viewable in the sensor_tag pane'),
    '#type' => 'textfield',
    '#default_value' => $conf['sensor_tag'],
    '#required' => FALSE
  );
  $form['param_tag'] = array(
    '#title' => t('Param tag'),
    '#description' => t('Param tag to filter on, allowed values are viewable in the param_tag pane'),
    '#type' => 'textfield',
    '#default_value' => $conf['param_tag'],
    '#required' => FALSE
  );
  $form['app_tag'] = array(
    '#title' => t('App tag'),
    '#description' => t('App tag to filter on, allowed values are viewable in the app_tag pane'),
    '#type' => 'textfield',
    '#default_value' => $conf['app_tag'],
    '#required' => FALSE
  );

  return $form;
}
function contextual_feeds_edit_form_submit($form, &$form_state) {
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    if (isset($form_state['values'][$key])) {
      $form_state['conf'][$key] = $form_state['values'][$key];
    }
  }
}

/**
 * Run-time rendering of the body of the block (content type)
 * See ctools_plugin_examples for more advanced info
 */
function contextual_feeds_render($subtype, $conf, $panel_args, $context = NULL) {
  $block = new stdClass();
  $block->title = '';

  $requestUrl = variable_get('contextual_requesturl', 'http://54.191.97.139/sites/all/modules/contextual_metadata/request.php');

  $filter = '[';

  // $filter .= '{%22operator%22%3A%22=%22%2C%22value%22%3A%22111%22%2C%22property%22%3A%22ms%22}';

  if (!empty($conf['sensor_tag'])){
    $filter .= ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'.urlencode($conf['sensor_tag']).'%27%22%2C%22property%22%3A%22sensor_tag%22}';
  }
  if (!empty($conf['param_tag'])){
    $filter .= ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'.urlencode($conf['param_tag']).'%27%22%2C%22property%22%3A%22param_tag%22}';
  }
  if (!empty($conf['app_tag'])){
    $filter .= ',{%22operator%22%3A%22=%22%2C%22value%22%3A%22%27'.urlencode($conf['app_tag']).'%27%22%2C%22property%22%3A%22app_tag%22}';
  }
  // $filter .= ',{"operator"%3A"lt"%2C"value"%3A"%272014-07-31%27"%2C"property"%3A"pubdate"},{"operator"%3A"gt"%2C"value"%3A"%272014-01-01%27"%2C"property"%3A"pubdate"}]&%20sort=[{"property":"title","direction":"ASC"}';
  $filter .= ']';

  // $filter = str_replace('+', '%20', $filter);

  $feedsurl = BASE_URL.FEEDS_URL;

  if ($filter != '[]'){
    $feedsurl .= "?filter=".$filter;
  }

  $block->content = array(
    '#theme' => 'feeds',
    '#content' => array('url' => $requestUrl.'?url='.urlencode($feedsurl)),
  );

  return $block;
}
