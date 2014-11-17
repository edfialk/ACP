<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print $user_picture; ?>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <div class="content"<?php print $content_attributes; ?>>
    <?php
      // We hide the comments and links now so that we can render them later.
      print render($content['body']);
    ?>
  </div>

  <?php
  	if (isset($content['field_import_type'])){
  		if ($content['field_import_type']['#items'][0]['value'] == 'javascript'){
	  		print '<div id="tool-display" class="tool-display"></div>';
	  		if (isset($content['field_javascript_libraries'])){
	  			foreach(explode("\n", $content['field_javascript_libraries']['#items'][0]['value']) as $script){
	  				drupal_add_js($script, 'external');
	  			}
	  		}
	  		if (isset($content['field_css_files'])){
	  			foreach(explode("\n", $content['field_css_files']['#items'][0]['value']) as $css){
	  				drupal_add_css($css, 'external');
	  			}
	  		}
	  		if (isset($content['field_javascript'])){
	  			print '<script>'.$content['field_javascript']['#items'][0]['value'].'</script>';
	  		}
	  	}else if ($content['field_import_type']['#items'][0]['value'] == 'frame'){
	  		print "<iframe width='100%' height='400' src='".$content['field_iframe_url']['#items'][0]['url']."''></iframe>";
	  	}else if ($content['field_import_type']['#items'][0]['value'] == 'embed'){
	  		print $content['field_embed_embed_code']['#items'][0]['value'];
	  	}


  	}
  ?>



  <?php print render($content['links']); ?>

  <?php print render($content['comments']); ?>

</div>

<?php
// dpm($content);
?>