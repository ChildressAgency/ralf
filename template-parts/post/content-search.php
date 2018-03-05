<?php
/**
 * Template part for showing ralf search results
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
  </header>

  <div class="entry-content">

    <?php the_excerpt(); ?>

    <?php 
      if(get_post_type() == 'activities'):
        if(get_field('conditions')): ?>
          <div class="activity-conditions">
            <h3>Conditions</h3>
            <?php echo get_field_excerpt('conditions'); ?>
          </div>
        <?php endif; //conditions field ?>

        <?php 
          $impact_ids = get_field('related_impacts', false, false);
          if(!empty($impact_ids)): ?>

            <div class="impact-by-sector">
              <h3>Impact by Sector</h3>
              <?php
                global $wpdb;
                $impact_ids_placeholders = implode(', ', array_fill(0, count($impact_ids), '%d'));
                $impacts_by_sector = $wpdb->get_results($wpdb->prepare(
                  "SELECT wp_posts.ID as impact_id, wp_posts.post_title as impact_title, wp_posts.guid as impact_link, wp_terms.name as sector, wp_terms.slug as sector_slug
                  FROM wp_posts
                    JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id
                    JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
                    JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id
                  WHERE wp_term_taxonomy.taxonomy = 'sectors'
                    AND wp_posts.ID IN($impact_ids_placeholders)
                    AND post_type = 'impacts'", $impact_ids)); ?>

                <ul>
                  <?php foreach($impacts_by_sector as $impact): ?>
                    <li>
                      <a href="/sectors/<?php echo $impact->sector_slug; ?>"><?php echo $impact->sector; ?></a>: 
                      <a href="<?php echo get_permalink($impact->impact_id); ?>"><?php echo $impact->impact_title; ?></a>
                    </li>
                  <?php endforeach; ?>
                </ul>
            </div>

        <?php endif; //$impact_ids ?>

    <?php else: //impacts ?>
      
        <?php
          $activities = get_posts(array(
            'post_type' => 'activities',
            'meta_query' => array(
              array(
                'key' => 'related_impacts',
                'value' => '"' . get_the_ID() . '"',
                'compare' => 'LIKE'
              )
            )
          ));

          if($activities): ?>
            <h3>Related Activities</h3>
            <ul>
              <?php foreach($activities as $activity): ?>
                <li>
                  <a href="<?php echo get_permalink($activity->ID); ?>"><?php echo get_the_title($activity->ID); ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>

    <?php endif; ?>

  </div>
</article>
<hr />