<?php

$plugin = array(
  'single' => TRUE,
  'title' => t('Papers'),
  'description' => t('Shows log/news retrieved from GMU filtered by tags.'),
  'category' => t('Contextual Metadata'),
  'edit form' => 'metadata_papers_edit_form',
  'render callback' => 'metadata_papers_render',
  'admin info' => 'metadata_papers_admin_info',
  'defaults' => array(
    'limit' => 100,
    'sensor_tag' => 'Atmospheric Infrared Sounder',
    'param_tag' => 'carbon monoxide',
    'app_tag' => 'atmospheric chemistry'
  )
);

function metadata_papers_edit_form($form, &$form_state) {
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

function metadata_papers_edit_form_submit($form, &$form_state) {
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
function metadata_papers_render($subtype, $conf, $panel_args, $context = NULL) {

  $block = new stdClass();
  $block->title = '';

  // $requestUrl = variable_get('metadata_requesturl', 'http://54.191.97.139/sites/all/modules/contextual_metadata/request.php');

  $block->content = array(
    '#theme' => 'papers',
    '#content' => array(
      'requestUrl' => REQUEST_URL,
      'sensorTag' => $conf['sensor_tag'],
      'paramTag' => $conf['param_tag'],
      'appTag' => $conf['app_tag'],
      'baseUrl' => BASE_URL,
      'papersUrl' => PAPERS_URL,
      'feedsUrl' => FEEDS_URL,
      'sortby' => variable_get('metadata_sortby', 'title'),
      'sortdir' => variable_get('metadata_sortdir', 'ASC'),
      'papersFields' => variable_get('metadata_papersfields', 'aper_id,doi,title,abstract,pyear,payload'),
    ),
  );

  drupal_add_js(array('acp_contextual' => $block->content['#content']), array('type' => 'setting'));
  drupal_add_js(drupal_get_path('module', 'metadata') .'/main.js');

  return $block;
}
