<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Resume_Filtering_Plugin
{
    public static function resume_filter_form()
    {
        $locations = self::get_resume_locations();

        $search_query = isset($_GET['search_query']) ? sanitize_text_field($_GET['search_query']) : '';
        $resume_location = isset($_GET['resume_location']) ? sanitize_text_field($_GET['resume_location']) : '';
        $resume_category = isset($_GET['resume_category']) ? sanitize_text_field($_GET['resume_category']) : '';

        ob_start();
?>
        <div id="ae_resume_filter_wrapper">
            <form id="resume-filter-form" action="<?php echo esc_url(home_url('/resume-filter')); ?>" method="GET">
                <div id="resume-filter-form__left">
                    <div id="resume-filter-form__left-header">
                        <div class="form-group">
                            <h6 class="noto-sans-h6">Filter</h6>
                            <button type="button" id="reset-filters">Clear all</button>
                        </div>
                    </div>
                    <div id="resume-filter-form__left-body">
                        <!-- Date Post Filter -->
                        <div class="form-group">
                            <h5 class="noto-sans-filter_title">Date Post</h5>

                            <div class="dropdown">
                                <label class="dropdown__options-filter">
                                    <ul class="dropdown__filter" role="listbox" tabindex="-1">
                                        <li class="dropdown__filter-selected" aria-selected="true">
                                            <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.98822 12.5C2.98822 8.72876 2.98822 6.84315 4.15979 5.67157C5.33137 4.5 7.21698 4.5 10.9882 4.5H14.9882C18.7595 4.5 20.6451 4.5 21.8166 5.67157C22.9882 6.84315 22.9882 8.72876 22.9882 12.5V14.5C22.9882 18.2712 22.9882 20.1569 21.8166 21.3284C20.6451 22.5 18.7595 22.5 14.9882 22.5H10.9882C7.21698 22.5 5.33137 22.5 4.15979 21.3284C2.98822 20.1569 2.98822 18.2712 2.98822 14.5V12.5Z" stroke="#636363" stroke-width="1.5" />
                                                <path d="M7.98822 4.5V3" stroke="#636363" stroke-width="1.5" stroke-linecap="round" />
                                                <path d="M17.9882 4.5V3" stroke="#636363" stroke-width="1.5" stroke-linecap="round" />
                                                <path d="M3.48822 9.5H22.4882" stroke="#636363" stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                            <span>Anytime</span>
                                        </li>
                                        <li>
                                            <ul class="dropdown__select">
                                                <li class="dropdown__select-option" role="option" data-value="anytime">
                                                    Anytime
                                                </li>
                                                <li class="dropdown__select-option" role="option" data-value="24_hours">
                                                    24 hours ago
                                                </li>
                                                <li class="dropdown__select-option" role="option" data-value="1_week">
                                                    1 week ago
                                                </li>
                                                <li class="dropdown__select-option" role="option" data-value="1_month">
                                                    1 month ago
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </label>
                                <input type="hidden" name="date_post" id="date_post" value="anytime" />
                            </div>
                        </div>

                        <hr>

                        <!-- Resume Category Filter -->
                        <div class="form-group">
                            <h5 class="noto-sans-filter_title">Role Category</h5>
                            <?php
                            $resume_categories = get_terms(array(
                                'taxonomy' => 'resume_category',
                                'hide_empty' => false,
                            ));
                            if (!is_wp_error($resume_categories) && !empty($resume_categories)) {
                                foreach ($resume_categories as $category) {
                                    // Check if the current category is selected
                                    $checked = in_array($category->slug, explode(',', $resume_category)) ? 'checked' : '';
                                    echo '<label class="custom_checkbox"><input type="checkbox" name="resume_category[]" value="' . esc_attr($category->slug) . '" ' . $checked . '>' . esc_html($category->name) . '<span class="checkbox"></span></label>';
                                }
                            }
                            ?>
                            <button type="button" id="toggle-more-categories">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.98822 1.5V16.5M16.4882 9H1.48822" stroke="#FF8200" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>More Categories</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="resume-filter-form__right">
                    <div class="form-group">
                        <div id="search_filter">
                            <input type="text" name="search_query" placeholder="Type your search" value="<?php echo esc_attr($search_query); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="dropdown">
                            <label class="dropdown__options-filter">
                                <ul class="dropdown__filter" role="listbox" tabindex="-1">
                                    <li class="dropdown__filter-selected" aria-selected="true">
                                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.33337 8.95258C3.33337 5.20473 6.31814 2.1665 10 2.1665C13.6819 2.1665 16.6667 5.20473 16.6667 8.95258C16.6667 12.6711 14.5389 17.0102 11.2192 18.5619C10.4453 18.9236 9.55483 18.9236 8.78093 18.5619C5.46114 17.0102 3.33337 12.6711 3.33337 8.95258Z" stroke="#3D3935" stroke-width="1.5" />
                                            <ellipse cx="10" cy="8.8335" rx="2.5" ry="2.5" stroke="#3D3935" stroke-width="1.5" />
                                        </svg>
                                        <span><?php echo $resume_location ? esc_html($resume_location) : 'Any Location'; ?></span>
                                    </li>
                                    <li>
                                        <ul class="dropdown__select">
                                            <li class="dropdown__select-option" role="option" data-value="">
                                                Any Location
                                            </li>
                                            <?php foreach ($locations as $location) : ?>
                                                <li class="dropdown__select-option" role="option" data-value="<?php echo esc_attr($location); ?>">
                                                    <?php echo esc_html($location); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                </ul>
                            </label>
                            <input type="hidden" name="resume_location" id="resume_location" value="<?php echo esc_attr($resume_location); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="button" id="apply-filters">Search</button>
                    </div>
                </div>
            </form>

            <div id="resume_results_wrapper">
                <div id="total-results"></div>
                <div id="resume-results"></div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }

    private static function get_resume_locations()
    {
        global $wpdb;
        // Query to get distinct locations from published resumes only
        $locations = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM {$wpdb->prefix}postmeta 
            WHERE meta_key = '_candidate_location' 
            AND post_id IN (
                SELECT ID FROM {$wpdb->prefix}posts 
                WHERE post_status = 'publish' 
                AND post_type = 'resume'
            )
        ");
        return $locations;
    }
}

add_shortcode('resume_filter_form', array('Resume_Filtering_Plugin', 'resume_filter_form'));
