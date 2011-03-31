<?php
/**
 * The Template for displaying all single evipnet metadata.
 */

$display_fields = array();
$display_fields['_evipnet_dc_contribuitor_author'] = 'Author ';
$display_fields['_evipnet_dc_description_abstract'] = 'Abstract';
$display_fields['_evipnet_dc_source'] = 'Source';
$display_fields['_evipnet_volume_issue'] = 'Volume and Issue';
$display_fields['_evipnet_pages'] = 'Pages';
$display_fields['_evipnet_dc_date_issued'] = 'Date';
$display_fields['_evipnet_dc_publisher'] = 'Publisher';
$display_fields['_evipnet_fulltext_url'] = 'Full text URL';
$display_fields['_evipnet_fulltext_file'] = 'Full text file';

get_header(); ?>

<div id="container">
    <div id="content" role="main">

        <!-- Start the Loop. -->
         <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                <div class="post">
                     <!-- Display the Title as a link to the Post's permalink. -->
                     <h1 class="entry-title"> <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

                     <!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
                     <small><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?></small>

                     <!-- Display the Post's Content in a div box. -->
                     <div class="entry">
                       <?php the_content(); ?>
                     
                       <?php 
                       
                            $meta_fields = get_post_custom($post->ID);
                                                        
                            foreach ( $display_fields as $field => $label) {
                                
                                $field_value  = implode('; ', (array)$meta_fields[$field]);
                                
                                if ( $field_value != ''){
                                    if ($field == '_evipnet_fulltext_file'){
                                        $file_url = wp_get_attachment_url($field_value);                    
                                        $file_info = pathinfo($file_url);                    
                                        $field_value = '<a href="' . $file_url . '">' . $file_info['basename'] . '</a>';
                                    }
                                    if ($field == '_evipnet_fulltext_url'){
                                        $field_value = '<a href="' . $field_value . '">' . $field_value . '</a>';
                                    }
                                    
                                    echo '<div class="metafield">';
                                    echo '    <div class="metaname">' . $label . '</div>';
                                    echo '    <div class="metavalue">' .$field_value .'</div>';
                                    echo '</div>';
                                }
                           }     
                       ?>
                       
                       <?php echo get_the_term_list( $post->ID, 'evipnet_keywords', '<p>Keyword(s)  : ', ', ', '</p>' ) ?>
                       <?php echo get_the_term_list( $post->ID, 'evipnet_countries', '<p>Country(ies)  : ', ', ', '</p>' ) ?>
                       <?php echo get_the_term_list( $post->ID, 'evipnet_language', '<p>Language(s) : ', ', ', '</p>' ) ?>
                       <?php echo get_the_term_list( $post->ID, 'evipnet_topics', '<p>Topic(s) : ', ', ', '</p>' ) ?>
                       <?php echo get_the_term_list( $post->ID, 'type_of_evidence', '<p>Type of evidence(s) : ', ', ', '</p>' ) ?>

                     </div>

                     <!-- Display a comma separated list of the Post's Categories. -->
                     <!--p class="postmetadata">Posted in <?php the_category(', '); ?></p-->
                     </div> <!-- closes the first div box -->

                     <!-- Stop The Loop (but note the "else:" - see next line). -->
                     <?php endwhile; else: ?>

                     <!-- The very first "if" tested to see if there were any Posts to -->
                     <!-- display.  This "else" part tells what do if there weren't any. -->
                     <p>Sorry, no posts matched your criteria.</p>

                     <!-- REALLY stop The Loop. -->
                     <?php endif; ?>
                </div> <!-- #post -->
    </div><!-- #content -->
</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
