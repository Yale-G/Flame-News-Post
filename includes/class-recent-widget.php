<?php
// Widget constructor
class crp_widget extends WP_Widget {

    function __construct() {
        parent::__construct(
           
            //ID 
            'crp_widget',

            //Name
            'Custom Recent Posts',

            //Description
            array( 'description' => 'A custom widget showing the 
                most recent posts in the website')
        );
    }

    // Widget front-end
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );

        
        // before & after widget args defined by the theme
        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
 
        // the custom query
        $query_args = array(
                             'posts_per_page' => absint( $instance[ 'number_of_posts' ] )
                             );
        var_dump( $query_args );
        if ( absint( $instance[ 'cat_display' ] ) > 0 ) {
            $query_args[ 'cat' ] = absint( $instance[ 'cat_display' ] );
        }  
        if ( absint( $instance[ 'tag_display' ] ) > 0 ) {
            $query_args[ 'tag_id' ] = absint( $instance[ 'tag_display' ] );
        }
        $the_query = new WP_Query( $query_args ); ?>
 
        <?php if ( $the_query->have_posts() ) : ?>
            
            <!-- the loop -->
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <?php 
                    if ( absint( $instance[ 'thumb_display' ] ) == 1 ) {
                        if ( has_post_thumbnail() ) { 
                            the_post_thumbnail( 'thumbnail' );
                        } 
                    }
                
                ?>
                <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                    <?php the_title(); ?>
                </a>
                <div class="post-dates">
                    <?php the_time('F j, Y') ?>
                </div>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
            <!-- end of the loop -->
 
        <?php else : ?>
            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>
      
        <?php 
        echo $args['after_widget'];
    }
    

    // back-end (if the title is not registered)
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
            
        } else {
            $title = 'New title';
        }
        if ( isset( $instance[ 'number_of_posts' ] ) ) {
            $number_of_posts = absint( $instance[ 'number_of_posts' ] );
        } else {
            $number_of_posts = 0;
        }
        if ( isset( $instance[ 'cat_display' ] ) ) {
            $cat_display = absint( $instance[ 'cat_display' ] );
        } else {
            $cat_display = 0;
        }
        if ( isset( $instance[ 'tag_display' ] ) ) {
            $tag_display = absint( $instance[ 'tag_display' ] );
        } else {
            $tag_display = 0;
        }
        if ( isset( $instance[ 'thumb_display'] ) ) {
            $thumb_display = absint( $instance[ 'thumb_display' ] );
        } else {
            $thumb_display = 0;
        }
        // widget admin form
        ?> 
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
                name="<?php echo $this->get_field_name( 'title' ); ?>" 
                type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'number_of_posts' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'number_of_posts' ); ?>" 
                name="<?php echo $this->get_field_name( 'number_of_posts' ); ?>" 
                value="<?php echo esc_attr( $number_of_posts ); ?>" />

            </p>
            
            <?php 
            $terms = get_terms( 
                array(
                    'taxonomy'   => 'category',
                    'hide_empty' => false,
                )
            );
            // Inline if:
            // condition ? true : false;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'cat_display' ); ?>"><?php _e( 'Select category:' ); ?> </label>
                <select name="<?php echo $this->get_field_name( 'cat_display' ) ?>" id="<?php echo $this->get_field_id( 'cat_display' )?>" class="widefat">
                    <option value="0">--Pick a category--</option>
                    <?php foreach ( $terms as $term ) { ?>
                        <option value=<?php echo esc_attr( $term->term_id ) ?><?php echo $term->term_id === $cat_display ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ) ?></option>
                    <?php  } ?> 
                </select>
            </p>
            <?php 
            $tag_terms = get_terms( 
                array(
                    'taxonomy'   => 'post_tag',
                    'hide_empty' => false,
                )
            );
            
            // Inline if:
            // condition ? true : false;
            ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'tag_display' ); ?>"><?php _e( 'Select tags:' ); ?> </label>
                <select name="<?php echo $this->get_field_name( 'tag_display' ) ?>" id="<?php echo $this->get_field_id( 'tag_display' )?>" class="widefat">
                    <option value="0">--Pick a tag--</option>
                    <?php foreach ( $tag_terms as $term ) { ?>
                        <option value=<?php echo esc_attr( $term->term_id ) ?><?php echo $term->term_id === $tag_display ? ' selected="selected"' : ''; ?>><?php echo esc_html( $term->name ) ?></option>
                    <?php  } ?> 
                </select>
            </p>
            <!-- thumbnail selection  -->
            <p>
                <label for="<?php echo $this->get_field_id( 'thumb_display' ); ?>"><?php _e( 'Show thumbnail: ') ?> </label> <br/>
                <input type="checkbox" name="<?php echo $this->get_field_name( 'thumb_display' ) ?>" id="<?php echo $this->get_field_id( 'thumb_display' )?>" class="widefat"
                     value="1"> Yes <br/>   
                <input type="checkbox" name="<?php echo $this->get_field_name( 'thumb_display' ) ?>" id="<?php echo $this->get_field_id( 'thumb_display' )?>" class="widefat"
                     value="0"> No <br/>
            </p>
        <?php    
    }

    // Updating widget replace old instance with new 
    public function update( $new_instance, $old_instance ) {
        $instance                      = array();
        $instance[ 'title' ]           = ( ! empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
        $instance[ 'number_of_posts' ] = ( ! empty( $new_instance[ 'number_of_posts' ] ) ) ? absint( $new_instance[ 'number_of_posts' ] ) : '';
        $instance[ 'cat_display' ]     = ( ! empty( $new_instance[ 'cat_display' ] ) ) ? absint( $new_instance[ 'cat_display' ] ) : '';
        $instance[ 'tag_display' ]     = ( ! empty( $new_instance[ 'tag_display' ] ) ) ? absint( $new_instance[ 'tag_display' ] ) : '';
        $instance[ 'thumb_display' ]   = ( ! empty( $new_instance[ 'thumb_display' ] ) ) ? ( $new_instance[ 'thumb_display' ] ) : '';
        return $instance;
    }

}

// Register/load the widget
function crp_load_widget() {
    register_widget( 'crp_widget' );
}
add_action( 'widgets_init' , 'crp_load_widget' );


?>

