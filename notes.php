<?php

//search only specific custom post types
function searchfilter($query) {
 
    if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('post','page'));
    }
 
return $query;
}
 
add_filter('pre_get_posts','searchfilter');


