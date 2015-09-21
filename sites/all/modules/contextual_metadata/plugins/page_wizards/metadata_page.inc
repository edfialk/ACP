<?php
/**
 * @file
 * A page creation wizard to quickly create metadata pages.
 *
 */
$plugin = array(
  'title' => t('Metadata page'),
  'page title' => t('Contextual Metadata page wizard'),
  'description' => t('An ACP Contextual Metadata page to display filtered data retrieved from GMU.'),

  'type' => 'panels',

  'form info' => array(
    'order' => array(
      'basic' => t('Basic settings'),
      'content' => t('Content'),
    ),

    'forms' => array(
      'basic' => array(
        'form id' => 'panels_metadata_page_basic',
      ),
      'content' => array(
        'form id' => 'panels_metadata_page_content',
      ),
    ),
  ),

  'default cache' => 'panels_metadata_page_new_page',

  'finish' => 'panels_metadata_page_finish',
);

/**
 * Provide defaults for a new cache.
 *
 * The cache will store all our temporary data; it isn't really a page
 * in itself, but it does contain everything we need to make one at the end.
 */
function panels_metadata_page_new_page(&$cache) {
  $cache->name = '';
  $cache->admin_title = '';
  $cache->admin_description = '';
  $cache->path = '';
  $cache->menu_entry = FALSE;
  $cache->menu = array(
    'type' => 'none',
    'title' => '',
    'weight' => 0,
    'name' => 'navigation',
    'parent' => array(
      'type' => 'none',
      'title' => '',
      'weight' => 0,
      'name' => 'navigation',
    ),
  );
  $cache->display = panels_new_display();
  $cache->display->layout = 'flexible';
}

/**
 * First page of our page creator wizard.
 */
function panels_metadata_page_basic($form, &$form_state) {
  $cache = &$form_state['wizard cache'];
  ctools_include('dependent');

  $form['admin_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Administrative title'),
    '#description' => t('The name of this page. This will appear in the administrative interface to easily identify it.'),
    '#default_value' => $cache->admin_title,
    '#required' => TRUE,
  );

  $form['name'] = array(
    '#type' => 'machine_name',
    '#title' => t('Machine name'),
    '#machine_name' => array(
      'exists' => 'page_manager_page_load',
      'source' => array('admin_title'),
    ),
    '#description' => t('The machine readable name of this page. It must be unique, and it must contain only alphanumeric characters and underscores. Once created, you will not be able to change this value!'),
    '#default_value' => $cache->name,
  );

  $form['admin_description'] = array(
    '#type' => 'textarea',
    '#title' => t('Administrative description'),
    '#description' => t('A description of what this page is, does or is for, for administrative use.'),
    '#default_value' => $cache->admin_description,
  );

  // path
  $form['path'] = array(
    '#type' => 'textfield',
    '#title' => t('Path'),
    '#default_value' => $cache->path,
    '#field_prefix' => url(NULL, array('absolute' => TRUE)) . (variable_get('clean_url', 0) ? '' : '?q='),
    '#required' => TRUE,
  );

  // ctools_include('page-wizard', 'panels');
  // panels_page_wizard_add_layout($form, $form_state);

  // Ensure all 'page' features are loaded.
  $page_task = page_manager_get_task('page');
  return $form;
}

/**
 * Submit function to store the form data in our cache.
 */
function panels_metadata_page_basic_validate(&$form, &$form_state) {
  // Validate that the name is ok.
  $test = page_manager_page_load($form_state['values']['name']);
  if ($test) {
    form_error($form['name'], t('That name is used by another page: @page', array('@page' => $test->admin_title)));
  }

  // Ensure name fits the rules:
  if (preg_match('/[^a-zA-Z0-9_]/', $form_state['values']['name'])) {
    form_error($form['name'], t('Page name must be alphanumeric or underscores only.'));
  }

  // Validate that the path is ok.
  if (preg_match('/[%!\?#&]/', $form_state['values']['path'])) {
    form_error($form['path'], t('%, !, ?, #, or & cannot appear in the path.'));
  }

  // Check to see if something is already using the path
  $result = db_query("SELECT * FROM {menu_router} WHERE path = :path", array(':path' => $form_state['values']['path']))->fetch();
  if ($result) {
    form_error($form['path'], t('That path is already in use. This system cannot override existing paths.'));
    return;
  }

  // Ensure the path is not already an alias to something else.
  $alias = db_query('SELECT alias, source FROM {url_alias} WHERE alias = :path', array(':path' => $form_state['values']['path']))->fetchObject();
  if ($alias) {
    form_error($form['path'], t('That path is currently assigned to be an alias for @alias. This system cannot override existing aliases.', array('@alias' => $alias->src)));
  }
}

/**
 * Submit function to store the form data in our cache.
 */
function panels_metadata_page_basic_submit(&$form, &$form_state) {
  $cache = &$form_state['wizard cache'];
  $cache->name = $form_state['values']['name'];
  $cache->admin_title = $form_state['values']['admin_title'];
  $cache->admin_description = $form_state['values']['admin_description'];
  $cache->path = $form_state['values']['path'];
  // $cache->display->layout = 'flexible';
  // $cache->display->title = $form_state['values']['admin_title'];
}

/**
 * Second page of our wizard. This one provides a layout and lets the
 * user add content.
 */
function panels_metadata_page_content($form, &$form_state) {
  // ctools_include('page-wizard', 'panels');
  // panels_page_wizard_add_content($form, $form_state);

  // dpm($form);
  // dpm($form_state);

  $param_url = BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=param_tag";
  $sensor_url = BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=sensor_tag";
  $app_url = BASE_URL.FEEDS_URL."?limit=100&getdistvaluefield=app_tag";

  $param_json = file_get_contents($param_url);
  $sensor_json = file_get_contents($sensor_url);
  $app_json = file_get_contents($app_url);

  $params = json_decode($param_json);
  $sensors = json_decode($sensor_json);
  $apps = json_decode($app_json);

  $form['options'] = array(
    '#type' => 'fieldset',
    '#title' => t('Options'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE
  );

  $form['options']['filters'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow user to change filters live on page.'),
    '#description' => t('This will place the sensor/filter/app select boxes at the top of the page.')
  );
  $form['options']['Sort'] = array(
    '#type' => 'checkbox',
    '#title' => t('Allow user to change sort options live on page.'),
    '#default_value' => 1
  );
  $form['options']['feeds'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show feeds panel'),
    '#default_value' => 1
  );
  $form['options']['papers'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show papers panel'),
    '#default_value' => 1
  );
  $form['options']['title'] = array(
    '#type' => 'checkbox',
    '#title' => t('Show title'),
    '#default_value' => 1,
    '#description' => t('Include "'.$form_state['wizard cache']->name.'" as a header.')
  );

  $form['defaults'] = array(
    '#type' => 'fieldset',
    '#title' => t('Default filters'),
    '#description' => t('These filters will be applied to all items on first page load.'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE
  );

  $form['defaults']['sensor'] = array(
    '#type' => 'select',
    '#title' => t('Sensor'),
    '#options' => array(
      '' => t('None'),
    ),
    '#default_value' => 0,
    '#description' => t('Set this to filter items by a specific sensor.')
  );
  foreach($sensors->rows as $sensor){
    $form['defaults']['sensor']['#options'][$sensor->sensor_tag] = $sensor->sensor_tag;
  }
  $form['defaults']['parameter'] = array(
    '#type' => 'select',
    '#title' => t('Parameter'),
    '#options' => array(
      '' => t('None')
    ),
    '#default_value' => 0,
    '#description' => t('Set this to filter items by a specific parameter.')
  );
  foreach($params->rows as $param){
    $form['defaults']['parameter']['#options'][$param->param_tag] = $param->param_tag;
  }
    $form['defaults']['app'] = array(
    '#type' => 'select',
    '#title' => t('App'),
    '#options' => array(
      '' => t('None')
    ),
    '#default_value' => 0,
    '#description' => t('Set this to filter items by a specific app.')
  );
  foreach($apps->rows as $app){
    $form['defaults']['app']['#options'][$app->app_tag] = $app->app_tag;
  }

  return $form;
}

/**
 * Submit function to store the form data in our cache.
 */
function panels_metadata_page_submit(&$form, &$form_state) {
  panels_page_wizard_add_content_submit($form, $form_state);
}

/**
 * Finish callback for the wizard.
 *
 * When the wizard is finished, this callback will create the actual
 * page, save it, and redirect the user to view the new work.
 */
function panels_metadata_page_finish(&$form_state) {
  dpm($form_state);
  $cache = &$form_state['wizard cache'];

  dpm($cache);

  $options = $form_state['values']['options'];
  $defaults = $form_state['values']['defaults'];

  // Ensure all 'page' features are loaded.
  $page_task = page_manager_get_task('page');

  // Assemble a new page subtask.
  $subtask = page_manager_page_new();
  $subtask->name = $cache->name;
  $subtask->path = $cache->path;
  $subtask->admin_title = $cache->admin_title;
  $subtask->admin_description = $cache->admin_description;
  $subtask->path = $cache->path;
  $subtask->menu = $cache->menu;

  // Create the the panel context variant configured with our display
  $plugin = page_manager_get_task_handler('panel_context');

  // Create a new handler.
  $handler = page_manager_new_task_handler($plugin);
  // Assemble a new $page cache and assign it our page subtask and task handler.

  $page = new stdClass();
  $page->disabled = FALSE; /* Edit this to true to make a default page disabled initially */
  $page->api_version = 1;
  $page->name = $cache->name;
  $page->task = 'page';
  $page->admin_title = $cache->admin_title;
  $page->admin_description = '';
  $page->path = $cache->path;
  $page->access = array();
  $page->menu = array();
  $page->arguments = array();
  $page->conf = array(
    'admin_paths' => FALSE,
  );
  $handler->disabled = FALSE; /* Edit this to true to make a default handler disabled initially */
  $handler->api_version = 1;
  $handler->name = 'page_contextual_metadata_'.$cache->name.'_panel_context';
  $handler->task = 'page';
  $handler->subtask = 'contextual_metadata_'.$cache->name.'_panel';
  $handler->handler = 'panel_context';
  $handler->weight = 0;
  $handler->conf = array(
    'title' => $cache->name,
    'no_blocks' => 0,
    'pipeline' => 'ipe',
    'body_classes_to_remove' => '',
    'body_classes_to_add' => '',
    'css_id' => '',
    'css' => '',
    'contexts' => array(),
    'relationships' => array(),
  );

  // Build panel
  $display = new panels_display();
  $display->layout = 'flexible';
  $display->layout_settings = array(
    'items' => array(
      'canvas' => array(
        'type' => 'row',
        'contains' => 'column',
        'children' => array(
          0 => 'main',
        ),
        'parent' => NULL,
      ),
      'main' => array(
        'type' => 'column',
        'width' => 100,
        'width_type' => '%',
        'children' => array(
          0 => 3,
          1 => 'main-row',
          2 => 4,
        ),
        'parent' => 'canvas',
      ),
      'main-row' => array(
        'type' => 'row',
        'contains' => 'region',
        'children' => array(
          0 => 'center',
          1 => 'options',
        ),
        'parent' => 'main',
      ),
      'center' => array(
        'type' => 'region',
        'title' => 'Left Column',
        'width' => 50,
        'width_type' => '%',
        'parent' => 'main-row',
        'class' => 'col-sm-6',
      ),
      3 => array(
        'type' => 'row',
        'contains' => 'region',
        'children' => array(
          0 => 'top_row',
        ),
        'parent' => 'main',
        'class' => '',
      ),
      'options' => array(
        'type' => 'region',
        'title' => 'Right Column',
        'width' => 50,
        'width_type' => '%',
        'parent' => 'main-row',
        'class' => 'col-sm-6',
      ),
      'top_row' => array(
        'type' => 'region',
        'title' => 'Top Row',
        'width' => 100,
        'width_type' => '%',
        'parent' => '3',
        'class' => '',
      ),
      4 => array(
        'type' => 'row',
        'contains' => 'region',
        'children' => array(
          0 => 'one_column',
        ),
        'parent' => 'main',
        'class' => '',
      ),
      'one_column' => array(
        'type' => 'region',
        'title' => 'One Column',
        'width' => 100,
        'width_type' => '%',
        'parent' => '4',
        'class' => '',
      ),
    ),
  );
  $display->panel_settings = array(
    'style_settings' => array(
      'default' => NULL,
      'center' => NULL,
      'right_center' => NULL,
      'options' => NULL,
      'top_row' => NULL,
      'one_column' => NULL,
    ),
  );
  $display->cache = array();
  $display->title = '';
  $display->uuid = ctools_uuid_generate();
  $display->content = array();
  $display->panels = array();

  /** Papers pane */
  if ($options['papers'] === 1){
    $pane = new stdClass();
    $pane->uuid = ctools_uuid_generate();
    $pane->pid = 'new-'.$pane->uuid;
    $pane->panel = 'center';
    $pane->type = 'papers';
    $pane->subtype = 'papers';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'limit' => '100',
      'sensor_tag' => $defaults['sensor'],
      'param_tag' => $defaults['parameter'],
      'app_tag' => $defaults['app'],
      'override_title' => 1,
      'override_title_text' => 'Papers',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
      'style' => 'rounded corners',
    );
    $pane->css = array(
      'css_id' => '',
      'css_class' => '',
    );
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $display->content[$pane->pid] = $pane;
    $display->panels['center'][0] = $pane->pid;
  }

  /** Feeds Pane */
  if ($options['feeds'] === 1){
    $pane = new stdClass();
    $pane->uuid = ctools_uuid_generate();
    $pane->pid = 'new-'.$pane->uuid;
    $pane->panel = 'options';
    $pane->type = 'feeds';
    $pane->subtype = 'feeds';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'limit' => '100',
      'sensor_tag' => $defaults['sensor'],
      'param_tag' => $defaults['parameter'],
      'app_tag' => $defaults['app'],
      'override_title' => 1,
      'override_title_text' => 'Feeds',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
      'style' => 'rounded_corners',
    );
    $pane->css = array(
      'css_id' => '',
      'css_class' => '',
    );
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $display->content[$pane->pid] = $pane;
    $display->panels['options'][0] = $pane->pid;
  }

  /** Options Pane */
  if ($options['filters'] === 1){
    $pane = new stdClass();
    $pane->uuid = ctools_uuid_generate();
    $pane->pid = 'new-'.$pane->uuid;
    $pane->panel = 'top_row';
    $pane->type = 'options';
    $pane->subtype = 'options';
    $pane->shown = TRUE;
    $pane->access = array();
    $pane->configuration = array(
      'sensor_tag' => $defaults['sensor'],
      'param_tag' => $defaults['parameter'],
      'app_tag' => $defaults['app'],
      'override_title' => 0,
      'override_title_text' => '',
    );
    $pane->cache = array();
    $pane->style = array(
      'settings' => NULL,
      'style' => 'rounded corners',
    );
    $pane->css = array();
    $pane->extras = array();
    $pane->position = 0;
    $pane->locks = array();
    $display->content[$pane->pid] = $pane;
    $display->panels['top_row'][0] = $pane->pid;
  }

  $display->hide_title = PANELS_TITLE_NONE;
  $display->title_pane = '0';

  $handler->conf['display'] = $display;
  $page->default_handlers[$handler->name] = $handler;

  // Add panel to page
  page_manager_page_new_page_cache($subtask, $page);
  page_manager_handler_add_to_page($page, $handler);

  // Save it
  page_manager_save_page_cache($page);

  dpm($page);

  // Send us to the new page immediately.
  $form_state['redirect'] = url($cache->path);
}
