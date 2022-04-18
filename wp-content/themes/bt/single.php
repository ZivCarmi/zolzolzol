<?php defined('ABSPATH') || exit;

use app\classes\helpers\Debug;

$context = Timber::context();

$context['shop_categories'] = get_categories([
    'taxonomy' => 'product_cat',
    'orderby' => 'name',
    'posts_per_page' => -1,
]);

$context['posts'] = get_posts([
    'post_type' => 'post',
    'orderby' => 'rand',
    'posts_per_page' => 4,
]);

// ===============================================
// Related form by category
// ===============================================

// current category ID of this post
$current_cat_id = get_the_category()[0]->term_id;

// get form main title
$context['form_main_title'] = get_term_meta($current_cat_id, 'form_main_title', true);

// get form subtitle
$context['form_subtitle'] = get_term_meta($current_cat_id, 'form_subtitle', true);

// get current category to retrieve related companies
$selected_category = get_term_meta($current_cat_id, 'companies_by_category', true);

// get companies by selected category
$cellular_companies = get_term_meta($selected_category, 'product_cat_companies', true);

$context['cellular_companies'] = [];

foreach ($cellular_companies as $company) {
    $context['cellular_companies'][] = get_term($company);
}

// ===============================================
// Related form by category - end
// ===============================================

Timber::render('pages/single.twig', $context);