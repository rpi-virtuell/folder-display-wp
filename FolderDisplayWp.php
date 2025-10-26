<?php
/**
 * Plugin Name: Folder Display WP
 * Description: A simple WordPress plugin to display a taxonomy like a folder system using a shortcode.
 * Version: 1.0
 * Author: Daniel Reintanz
 */
class FolderDisplayWp{
    function __construct(){
        add_shortcode('folder_display', array($this, 'render_folder_display'));
    }
    
    public function render_folder_display($atts){
        $atts = shortcode_atts(array(
            'taxonomy' => 'category',
        ), $atts, 'folder_display');

        $taxonomy = sanitize_text_field($atts['taxonomy']);

        // Get current term from URL if it exists
        $current_term = get_term_by('slug', get_query_var($taxonomy), $taxonomy);

        // Get all terms
        $terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0
        ));

        if (empty($terms)) {
            return '<p>No terms found.</p>';
        }

        $output = '<ul class="folder-display">';

        // Display breadcrumb if we're in a term
        if ($current_term) {
            $ancestors = get_ancestors($current_term->term_id, $taxonomy);
            $ancestors = array_reverse($ancestors);

            // Add home/root level
            $output .= '<li class="folder-breadcrumb">';
            $output .= '<a href="' . get_site_url() . '">' . __('Home') . '</a> / ';

            // Add ancestors
            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_term($ancestor_id);
                $output .= '<a href="' . get_term_link($ancestor) . '">' .
                    esc_html($ancestor->name) . '</a> / ';
            }

            // Add current term
            $output .= '<span>' . esc_html($current_term->name) . '</span>';
            $output .= '</li>';

            // Get child terms of current term
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'parent' => $current_term->term_id
            ));
        }

        // Display current level terms
        foreach ($terms as $term) {
            $output .= '<li class="folder">';
            $output .= '<a href="' . get_term_link($term) . '">';
            $output .= '<span class="folder-icon">📁</span> ';
            $output .= esc_html($term->name);
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        return $output;
    }
}
new FolderDisplayWp();