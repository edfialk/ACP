<?php

$plugin = array(
  'single' => TRUE,
  'icon' => 'icon_page.png',
  'title' => t('JS files'),
  'no title override' => TRUE,
  'description' => t('Inject JS files into a page.'),
  'category' => t('Page elements'),
  'defaults' => array(
    'admin_title' => '',
    'panels_css_js_files_js' => '',
  ),
);

function panels_css_js_js_files_content_type_admin_title($subtype, $conf) {
  $output = t('JS files');

  if ($conf['admin_title'] != '') {
    $params = array('@title' => $conf['admin_title']);
    $output = t('JS files: @title', $params);
  }

  return $output;
}

function panels_css_js_js_files_content_type_edit_form($form, &$form_state) {
  $conf = &$form_state['conf'];

  $form['admin_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Administrative title'),
    '#description' => t('This title will be used administratively to identify this pane. If blank, the regular title will be used.'),
    '#default_value' => $conf['admin_title'],
  );

  panels_css_js_files_js_edit_form($form, $conf);

  return $form;
}

function panels_css_js_js_files_content_type_edit_form_submit($form, &$form_state) {
  $conf = &$form_state['conf'];
  $conf['admin_title'] = $form_state['values']['admin_title'];

  panels_css_js_files_js_edit_form_submit($form_state, $conf);
}

function panels_css_js_js_files_content_type_render($subtype, $conf, $panel_args, $context) {
  if (isset($conf['panels_css_js_files_js'])) {
    panels_css_js_files_js_render($conf);
  }
}
