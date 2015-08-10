<?php
/*
Plugin Name: Ouverture Widget Plugin
Plugin URI: https://github.com/marito59/ouverture
Description: This is a plugin to display opening hours of a facility in a wordpress widget
Version: 1.1
Author: C. Maritorena
Author URI: http://www.lechevabignien.com/ 
License: GPLv2
*/

// use widgets_init Action hook to execute custom function
add_action( 'widgets_init', 'ouverture_register_widgets' );

 //register our widget
function ouverture_register_widgets() {

    register_widget( 'ouverture_widget' );

}

//ouverturewidget class
class ouverture_widget extends WP_Widget {

    //process our new widget
    function __construct() {

        $widget_ops = array(
            'classname'   => 'ouverture_widget_class',
            'description' => 'Widget qui afffiche les horaires d\'ouverture d\'une activité.' );
        parent::__construct( 'ouverture_widget', 'Widget Ouverture', $widget_ops );

    }

     //build our widget settings form
    function form( $instance ) {
        $defaults = array (
            'titre' => '',
            'lien' => 'http://www.enclunisois.com/loisirs-culture-tourisme/piscine/',
            'desciption' => 'Communauté de Communes en Clunisois'
        );
        $instance = wp_parse_args( (array) $instance, $defaults );
        $title = $instance['titre'];
        $link = $instance['lien'];
        $description = $instance['description'];
?>
    <p>Titre :<input class="widefat" name="<?php echo $this->get_field_name( 'titre' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
    <p>Lien :<input class="widefat" name="<?php echo $this->get_field_name( 'lien' ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" /></p>
    <p>Description :<input class="widefat" name="<?php echo $this->get_field_name( 'description' ); ?>" type="text" value="<?php echo esc_attr( $description ); ?>" /></p>
<?php
    }

    //save our widget settings
    function update( $new_instance, $old_instance ) {

        $instance = $old_instance;
        
        $instance ['titre'] = sanitize_text_field( $new_instance['titre'] );
        $instance ['lien'] = sanitize_text_field( $new_instance['lien'] );
        $instance ['description'] = sanitize_text_field( $new_instance['description'] );
        
        return $instance;

    }

    //display our widget
    function widget( $args, $instance ) {
        extract( $args );

        echo $before_widget;
        
        $title = apply_filters( 'widget_title', $instance['titre'] );
        $link = (empty( $instance['lien'] )) ? '&nbsp;' : $instance['lien'];
        $description = (empty( $instance['description'] )) ? '&nbsp;' : $instance['description'];
        
        if ( !empty( $title ) ) {
            echo $before_title . esc_html( $title ) . $after_title;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ouverture';
        
        $today = date ('Y-m-d');
        $jour = date ('N');
        //$col = 3 + $jour;
        $jours = array("lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi", "dimanche");
        $sql = 'SELECT ' . $jours[$jour-1] . ' FROM `chevab_ouverture` WHERE \'' . $today .'\' >= debut and \'' . $today . '\' <= fin';
        
        $row = $wpdb->get_row($wpdb->prepare($sql), ARRAY_N);

        echo '<p>Aujourd\'hui ' . $jours[$jour-1] . ',<br /> la <strong>piscine Daniel Decerle</strong> à <i>La Guiche</i> est <br />';
        echo ($row[0] == "fermé") ? "<strong>fermée</strong>" : "<strong>ouverte</strong> : " . $row[0] . "</p>";
        
        echo "<p>Plus d'informations : <a href='" . esc_html ( $link ) . "' title='" . esc_html ( $description ) . "'>" . esc_html ( $description ) . "</a></p>";

        echo $after_widget;

    }
}
