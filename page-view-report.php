<?php get_header(); ?>

<style>
  .page-header{
    float:none;
    width:100%;
  }
  body.page-two-column:not(.archive) #primary .entry-header{
    float:none;
    width:100%;
  }
  body.page-two-column:not(.archive) #primary .entry-content{
    float:none;
    width:100%;
  }
  article.type-activities{
    margin-bottom:60px;
  }
</style>

  <div class="wrap">
    <header class="page-header">
      <h1 class="page-title">Report of Activities and associated impacts</h1>
    </header>

    <div id="primary" class="content-area">
      <main id="main" class="site-main" role="main">

        <?php
          if(isset($_COOKIE['report_ids'])){
            $report_ids_cookie = $_COOKIE['report_ids'];

            $report_ids = explode(',', $report_ids_cookie);
            
            $activities_ids = array_map(
              function($value){ return (int)$value; },
              $report_ids
            );
            
            $activities_report = new WP_Query(array(
              'post_type' => 'activities',
              'posts_per_page' => -1,
              'post__in' => $activities_ids
            ));

            if($activities_report->have_posts()): while($activities_report->have_posts()): $activities_report->the_post();

              get_template_part('template-parts/post/content');

            endwhile; endif; 
          }
          else{
            echo '<p>You haven\'t saved any Activities to report.</p>';
          }
        ?>

      </main>
    </div>
  </div>
<?php get_footer(); ?>