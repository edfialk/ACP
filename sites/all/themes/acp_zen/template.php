<?php
/**
 * @file
 * Contains the theme's functions to manipulate Drupal's default markup.
 *
 * Complete documentation for this file is available online.
 * @see https://drupal.org/node/1728096
 */

drupal_add_js(drupal_get_path('theme', 'acp_zen') . '/js/login.js');
drupal_add_js(drupal_get_path('theme', 'acp_zen') . '/js/heightfix.js', array('group' => JS_THEME));
drupal_add_js(drupal_get_path('theme', 'acp_zen') .'/js/bootstrap.js');
// drupal_add_js(drupal_get_path('theme', 'acp_zen') .'/js/login.js');


drupal_add_css(drupal_get_path('theme', 'acp_zen') . '/css/styles.css');

function acp_zen_form_alter(&$form, $form_state, $form_id) {
  // drupal_set_message("Form ID is : " . $form_id);

  // dpm($form);

  if (isset($form['actions']) && isset($form['actions']['submit'])){
    $form['actions']['submit']['#attributes']['class'][] = 'btn';
    $form['actions']['submit']['#attributes']['class'][] = 'btn-primary';
  }
  if (isset($form['submit'])){
    $form['submit']['#attributes']['class'][] = 'btn';
    $form['submit']['#attributes']['class'][] = 'btn-info';
  }

  if ($form_id == 'user_login_block'){
    $form['actions']['submit']['#attributes']['class'][] = 'btn-info';
  }

  if (isset($form['actions']['preview'])){
    $form['actions']['preview']['#attributes']['class'][] = 'btn';
    $form['actions']['preview']['#attributes']['class'][] = 'btn-info';
  }

  foreach($form as $key=>&$input){
    if (is_array($input) && isset($input['#type'])){
      if ($input['#type'] == 'textfield' || $input['#type'] == 'password' || $input['#type'] == 'select'){
        $input['#attributes']['class'][] = 'form-control';
      }
    }
    if (is_array($input) && isset($input['#type']) && $input['#type'] == 'container' && isset($input['und']) && is_array($input['und'])){
      foreach($input['und'] as &$subinput){
        if (is_array($subinput) && isset($subinput['#type']) && $subinput['#type'] == 'text_format'){
          $subinput['#attributes']['class'][] = 'form-control';
        }
      }
    }
  }
}

//show tags as badges
function acp_zen_preprocess_field(&$variables, $hook){
  if (
    isset($variables['items']) && count($variables['items']) > 0 &&
    isset($variables['items'][0]['#options']) &&
    isset($variables['items'][0]['#options']['entity_type']) &&
    $variables['items'][0]['#options']['entity_type'] == 'taxonomy_term'
  ){
    foreach($variables['items'] as &$tag){
      $tag['#attributes']['class'][] = 'badge';
    }
  }
}

//set height of ckeditor
function larsboorg_wysiwyg_editor_settings_alter(&$settings, $context) {
  dsm($settings);
  if($context['profile']->editor == 'ckeditor') {
    $settings['height'] = 100;
  }
}

// function acp_zen_menu_link(&$variables) {
//   dpm($variables);
// }


function acp_zen_js_alter(&$javascript) {
  // dpm($javascript);
}
/**
 * Override or insert variables into the maintenance page template.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("maintenance_page" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_maintenance_page(&$variables, $hook) {
  // When a variable is manipulated or added in preprocess_html or
  // preprocess_page, that same work is probably needed for the maintenance page
  // as well, so we can just re-use those functions to do that work here.
  acp_zen_preprocess_html($variables, $hook);
  acp_zen_preprocess_page($variables, $hook);
}
// */

/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // acp_zen_preprocess_node_page() or acp_zen_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the region templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("region" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_region(&$variables, $hook) {
  // Don't use Zen's region--sidebar.tpl.php template for sidebars.
  //if (strpos($variables['region'], 'sidebar_') === 0) {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('region__sidebar'));
  //}
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function acp_zen_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  // $variables['classes_array'][] = 'count-' . $variables['block_id'];

  // By default, Zen will use the block--no-wrapper.tpl.php for the main
  // content. This optional bit of code undoes that:
  //if ($variables['block_html_id'] == 'block-system-main') {
  //  $variables['theme_hook_suggestions'] = array_diff($variables['theme_hook_suggestions'], array('block__no_wrapper'));
  //}
}
// */

