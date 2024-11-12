<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Resume_Filtering_Ajax
{
    public static function fetch_filtered_resumes()
    {
        try {
            $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

            $args = array(
                'post_type' => 'resume',
                'posts_per_page' => 8,
                'paged' => $paged,
                'orderby' => 'modified',
                'order' => 'DESC',
                'post_status' => 'publish',
                'meta_query' => array(
                    'relation' => 'AND'
                ),
                'tax_query' => array(
                    'relation' => 'AND'
                )
            );

            // Search query filter
            if (!empty($_POST['search_query'])) {
                $args['s'] = sanitize_text_field($_POST['search_query']);
            }

            // Date Post filter
            if (!empty($_POST['date_post']) && $_POST['date_post'] !== 'anytime') {
                switch ($_POST['date_post']) {
                    case '24_hours':
                        $args['date_query'] = array(
                            array(
                                'column' => 'post_modified_gmt',
                                'after' => '24 hours ago'
                            )
                        );
                        break;
                    case '1_week':
                        $args['date_query'] = array(
                            array(
                                'column' => 'post_modified_gmt',
                                'after' => '1 week ago'
                            )
                        );
                        break;
                    case '1_month':
                        $args['date_query'] = array(
                            array(
                                'column' => 'post_modified_gmt',
                                'after' => '1 month ago'
                            )
                        );
                        break;
                }
            }

            // Location filter
            if (!empty($_POST['resume_location'])) {
                $args['meta_query'][] = array(
                    'key' => '_candidate_location',
                    'value' => sanitize_text_field($_POST['resume_location']),
                    'compare' => 'LIKE'
                );
            }

            // Resume Category filter
            if (!empty($_POST['resume_category'])) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'resume_category',
                    'field' => 'slug',
                    'terms' => $_POST['resume_category'],
                );
            }

            $query = new WP_Query($args);
            $total_resumes = $query->found_posts;

            ob_start();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $featured_image = get_post_meta(get_the_ID(), '_candidate_photo', true);
                    $title = get_the_title();
                    $role_name = get_post_meta(get_the_ID(), '_candidate_title', true);

                    echo '<div class="ae_resume_card">';
                    echo '<div class="ae_resume_card-top">';
                    echo '<img class="ae_resume_card__img" src="' . esc_url($featured_image) . '" alt="' . esc_attr($title) . '">';
                    echo '<div>';
                    echo '<h4 class="ae_resume_card__title"><a href="' . get_the_permalink() . '">' . esc_html($title) . '</a></h4>';
                    echo '<span class="ae_resume_card__role">' . esc_html($role_name) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo 'No resumes found.';
            }

            $pagination = paginate_links(array(
                'total' => $query->max_num_pages,
                'current' => $paged,
                'format' => '?page=%#%',
                'type' => 'array',
                'prev_text' => '<svg width="10" height="18" viewBox="0 0 10 18" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M8.75 16.5L1.25 9L8.75 1.5" stroke="#FF8200" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
                'next_text' => '<svg width="10" height="18" viewBox="0 0 10 18" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M1.25 16.5L8.75 9L1.25 1.5" stroke="#FF8200" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </svg>',
            ));

            if ($pagination) {
                echo '<div class="pagination">';
                foreach ($pagination as $page_link) {
                    echo $page_link;
                }
                echo '</div>';
            }


            $resume_results = ob_get_clean();

            wp_send_json_success(array(
                'total_resumes' => $total_resumes,
                'resume_results' => $resume_results
            ));
        } catch (Exception $e) {
            error_log('Error in Resume Filtering AJAX: ' . $e->getMessage());
            wp_send_json_error(array('message' => 'An error occurred while processing the request.'));
        }
    }
}
