<?php

$plugin = array(
  'single' => TRUE,
  'title' => t('User Options'),
  'description' => t('Shows options for user to set tags for data retrieved from GMU.'),
  'category' => t('Contextual Metadata'),
  'edit form' => 'contextual_options_edit_form',
  'render callback' => 'contextual_options_render',
  'admin info' => 'contextual_options_admin_info',
  'defaults' => array(
    'sensor_tag' => 'Atmospheric Infrared Sounder',
    'param_tag' => 'carbon monoxide',
    'app_tag' => 'atmospheric chemistry'
  )
);

/**
 * Run-time rendering of the body of the block (content type)
 * See ctools_plugin_examples for more advanced info
 */
function contextual_options_render($subtype, $conf, $panel_args, $context = NULL) {
  $block = new stdClass();
  $block->title = '';

  $sensor = variable_get('sensor_tag', 'Atmospheric Infrared Sounder');
  $param = variable_get('param_tag', 'carbon monoxide');
  $app = variable_get('app_tag', 'atmospheric chemistry');

  $block->content = array(
    '#theme' => 'options',
    '#content' => array('sensor' => $sensor, 'param' => $param, 'app' => $app),
  );

  return $block;
}

function contextual_options_admin_info($subtype, $conf, $contexts) {
  if (!empty($conf)) {
    $block = new stdClass;
    $block->title = $conf['override_title'] ? $conf['override_title_text'] : '';
    $block->content = t('content');
    return $block;
  }
}

function contextual_options_edit_form($form, &$form_state) {
  return $form;
}

function contextual_options_edit_form_submit($form, &$form_state) {
  foreach (array_keys($form_state['plugin']['defaults']) as $key) {
    if (isset($form_state['values'][$key])) {
      $form_state['conf'][$key] = $form_state['values'][$key];
    }
  }
}