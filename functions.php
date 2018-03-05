<?php
add_action('wp_footer', 'show_template');
function show_template() {
	global $template;
	print_r($template);
}

function my_theme_enqueue_styles() {

    $parent_style = 'twentyseventeen-style'; 

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

add_action('init', 'ralf_create_post_type');
function ralf_create_post_type(){
  $activity_labels = array(
    'name' => 'Activities',
    'singular_name' => 'Activity',
    'menu_name' => 'Activities',
    'add_new_item' => 'Add New Activity',
    'search_items' => 'Search Activities'
  );
  $activity_args = array(
    'labels' => $activity_labels,
    'public' => true,
    'menu_position' => 5,
    'supports' => array('title', 'author', 'revisions', 'editor')
  );
  register_post_type('activities', $activity_args);

  $impacts_labels = array(
    'name' => 'Impacts',
    'singular_name' => 'Impact',
    'menu_name' => 'Impacts',
    'add_new_item' => 'Add New Impact',
    'search_items' => 'Search Impacts'
  );
  $impacts_args = array(
    'labels' => $impacts_labels,
    'public' => true,
    'menu_position' => 6,
    'supports' => array('title', 'author', 'revisions', 'editor')
  );
  register_post_type('impacts', $impacts_args);
/*
  $conditions_labels = array(
    'name' => 'Conditions',
    'singular_name' => 'Condition',
    'menu_name' => 'Conditions',
    'add_new_item' => 'Add New Condition',
    'search_items' => 'Search Conditions'
  );
  $conditions_args = array(
    'labels' => $conditions_labels,
    'public' => true,
    'menu_position' => 7,
    'supports' => array('title', 'author', 'revisions', 'editor')
  );
  register_post_type('conditions', $conditions_args);
*/
  register_taxonomy('sectors',
    //array('impacts', 'activities', 'conditions'),
    'impacts',
    array(
      'hierarchical' => true,
      'show_admin_column' => true,
      'labels' => array(
        'name' => 'Sectors',
        'singular_name' => 'Sector'
      )
    )
  );
  register_taxonomy('impact_tags',
    'impacts',
    array(
      'hierarchical' => false,
      'show_admin_column' => true,
      'labels' => array(
        'name' => 'Impact Tags',
        'singular_name' => 'Impact Tag'
      )
    )
  );
}
/*
add_filter('acf/fields/relationship/query/key=field_5a980a2e5519d', 'ralf_related_impacts_relationship_query', 10, 3);
function ralf_related_impacts_relationship_query($args, $field, $post_id){
  $args['meta_key'] = 'impact_tag_names_$_impact_tag_name';

  return $args;
}
add_filter('posts_where', 'ralf_posts_where');
function ralf_posts_where($where){
  $where = str_replace("meta_key = 'impact_tag_names_$", "meta_key LIKE 'impact_tag_names_%", $where);

  return $where;
}*/

/*
add_filter('acf/fields/relationship/result/key=field_5a980a2e5519d', 'ralf_related_impacts_relationship_display', 10, 4);
function ralf_related_impacts_relationship_display($title, $post, $field, $post_id){
  //$impact_tag_names = get_field('impact_tag_names', $post->ID);
  $impact_tag_names = array();

  if(have_rows('impact_tag_names', $post->ID)){
    while(have_rows('impact_tag_names', $post->ID)){
      the_row();
      $impact_tag_names[] = get_sub_field('impact_tag_name');
    }
  }

  $title .= ' [' . implode(', ', $impact_tag_names) . ']';

  return $title;
}*/

add_filter('acf/fields/relationship/result/key=field_5a980a2e5519d', 'ralf_related_impacts_relationship_display', 10, 4);
function ralf_related_impacts_relationship_display($title, $post, $field, $post_id){
  //$impact_tag_names = get_field('impact_tag_names', $post->ID);
  //$impact_tag_names = get_the_tags($post->ID);
  $impact_tag_names = get_the_terms($post->ID, 'impact_tags');
  $impact_tag_name_list = array();

  if(!empty($impact_tag_names)){
    foreach($impact_tag_names as $impact_tag_name){
      $impact_tag_name_list[] = $impact_tag_name->name;
    }

    $title .= ' [' . implode(', ', $impact_tag_name_list) . ']';
  }

  return $title;
}

//search only specific custom post types
add_filter('pre_get_posts','ralf_searchfilter');
function ralf_searchfilter($query){

  if ($query->is_search && !is_admin() ) {
    $query->set('post_type',array('activities', 'impacts'));
  }
 
  return $query;
}