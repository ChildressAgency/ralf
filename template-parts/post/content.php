<?php
/**
 * Template part for displaying ralfs
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <header class="entry-header">
    <?php if(is_single()): ?>
      <h1 class="entry-title"><?php the_title(); ?></h1>
    <?php else: ?>
      <h2 class="entry-title"><?php the_title(); ?></h2>
    <?php endif; ?>
  </header>

  <div class="entry-content">
    <?php if(is_archive()): ?>

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

      <?php else: ?>

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

    <?php else: //its a single page ?>

      <?php the_content(); ?>

      <?php if(get_post_type() == 'activities'): ?>

        <?php
        if(get_field('conditions')): ?>
          <div class="activity-conditions">
            <h3>Conditions</h3>
            <?php echo get_field('conditions'); ?>
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
                  "SELECT wp_posts.ID as impact_id, wp_posts.post_title as impact_title, wp_posts.post_content as impact_description, wp_terms.name as sector, wp_terms.slug as sector_slug
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
                      <h4><?php echo $impact->sector; ?>: <?php echo $impact->impact_title; ?></h4>
                      <?php echo $impact->impact_description; ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
            </div>

            <style>
              .report-button{
                text-align:right;
              }
              .report-button>span{
                display:block;
              }
              .save-to-report{
                border:2px solid #000;
                border-radius:6px;
                padding:5px 25px;
              }
              .save-to-report:hover{
                background-color:#eee;
              }
            </style>
            <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/js-cookie.js"></script>
            <?php $article_id = get_the_ID(); ?>
            <script>
              var article_id = "<?php echo $article_id; ?>";

              jQuery(document).ready(function($){
                var report_ids = Cookies.get('report_ids');
                var report_id_arr;
                //var report_button = '<a href="#" id="saveToReport" class="save-to-report">Save To Report</a>';
                $('.report-button').each(function(){
                  $(this).html('<a href="#" id="saveToReport" class="save-to-report">Save To Report</a>');
                });

                if(report_ids){
                  var report_id_arr = report_ids.split(',');

                  if(report_id_arr.indexOf(article_id) > -1){
                    //report_button = '<a href="#" id="removeFromReport" class="save-to-report">Remove From Report</a>';
                    $('.report-button').each(function(){
                      $(this).html('<a href="#" id="removeFromReport" class="save-to-report">Remove From Report</a>');
                    });
                  }
                }

                  $('.report-button').on('click', '#saveToReport', function(e){
                    e.preventDefault();
                    if(report_id_arr){
                      if(report_id_arr.indexOf(article_id) < 0){
                        report_id_arr.push(article_id);
                      }
                      
                      report_ids = report_id_arr.toString();
                    }
                    else{
                      report_ids = article_id;
                    }
                    
                    Cookies.set('report_ids', report_ids, {expires: 30});
                    $('.report-button').each(function(){
                      $(this).html('<span><em>Added to report</em></span><a href="#" id="removeFromReport" class="save-to-report">Remove From Report</a>');
                    });
                  });

                  $('.report-button').on('click', '#removeFromReport', function(e){
                    e.preventDefault();

                    var id_index = report_id_arr.indexOf(article_id);
                    if(id_index > -1){
                      report_id_arr.splice(id_index, 1);
                    }

                    report_ids = report_id_arr.toString();
                    Cookies.set('report_ids', report_ids, {expires: 30});
                    $('.report-button').each(function(){
                      $(this).html('<span><em>Removed from report</em></span><a href="#" id="saveToReport" class="save-to-report">Save To Report</a>');
                    });
                  });

              });
            </script>

            <div class="report-button">
              
            </div>
        <?php endif; //$impact_ids ?>

      <?php else: ?>

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

    <?php endif; ?>
  </div>
</article>