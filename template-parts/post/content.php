<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.2
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( is_sticky() && is_home() ) :
		echo twentyseventeen_get_svg( array( 'icon' => 'thumb-tack' ) );
	endif;
	?>
	<header class="entry-header">
		<?php
		if ( 'post' === get_post_type() ) {
			echo '<div class="entry-meta">';
				if ( is_single() ) {
					twentyseventeen_posted_on();
				} else {
					echo twentyseventeen_time_link();
					twentyseventeen_edit_link();
				};
			echo '</div><!-- .entry-meta -->';
		};

		if ( is_single() ) {
			the_title( '<h1 class="entry-title">', '</h1>' );
		} elseif ( is_front_page() && is_home() ) {
			the_title( '<h3 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' );
		} else {
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		?>
	</header><!-- .entry-header -->

	<?php if ( '' !== get_the_post_thumbnail() && ! is_single() ) : ?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
    /* translators: %s: Name of current post */
    /*orig template: 
		the_content( sprintf(
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ),
			get_the_title()
		) );

		wp_link_pages( array(
			'before'      => '<div class="page-links">' . __( 'Pages:', 'twentyseventeen' ),
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
    ) );*/
    
    //new
      the_content();

      if(get_post_type() == 'activities'){
        $impact_ids = get_field('related_impacts', false, false);
        if(!empty($impact_ids)){
          echo '<div class="activity-conditions">';
            echo '<h3>Conditions</h3>';
            echo get_field('conditions');
          echo '</div>';

          echo '<div class="impact-by-sector">';

            echo '<h3>Impact by Sector</h3>';

            //$impact_sectors = array();

            global $wpdb;

            $impact_ids_placeholders = implode(', ', array_fill(0, count($impact_ids), '%d'));
            $impacts_by_sector = $wpdb->get_results($wpdb->prepare(
              "SELECT wp_posts.post_title as impact_title, wp_posts.post_content as impact_description, wp_terms.name as sector
              FROM `wp_posts`
                JOIN wp_term_relationships ON wp_posts.ID = wp_term_relationships.object_id
                JOIN wp_terms ON wp_term_relationships.term_taxonomy_id = wp_terms.term_id
                JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id
              WHERE wp_term_taxonomy.taxonomy = 'sectors'
                AND wp_posts.ID IN($impact_ids_placeholders)
                AND post_type = 'impacts'", $impact_ids));

            echo '<ul>';
            foreach($impacts_by_sector as $impact){
              //var_dump($impact->parent_sector);
              //$sector_parent_term = get_term_by('id', intval($impact->parent_sector), 'sectors');
              echo '<li><h4>' . $impact->sector . ': ' . $impact->impact_title . '</h4>';
              echo $impact->impact_description . '</li>';

            }
        } 
      }
      else{

      }
     // echo '</div>'; //.impact-by-sector

    //end new
		?>
	</div><!-- .entry-content -->

	<?php
	if ( is_single() ) {
		twentyseventeen_entry_footer();
	}
	?>

</article><!-- #post-## -->
