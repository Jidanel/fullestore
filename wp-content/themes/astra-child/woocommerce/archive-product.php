<?php
/**
 * The Template for displaying product archives, including the main shop page
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Hook: woocommerce_before_main_content
 */
do_action('woocommerce_before_main_content');
?>

<header class="woocommerce-products-header">
    <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
        <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
    <?php endif; ?>

    <div class="archive-filters">
        <?php
        /**
         * Hook: woocommerce_archive_description
         */
        do_action('woocommerce_archive_description');
        ?>

        <div class="archive-filters__wrapper">
            <?php
            // Catégories
            $terms = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'parent' => 0
            ));

            if ($terms) : ?>
                <div class="archive-filters__categories">
                    <h3>Catégories</h3>
                    <ul>
                        <?php foreach ($terms as $term) : ?>
                            <li>
                                <a href="<?php echo get_term_link($term); ?>">
                                    <?php echo $term->name; ?>
                                    <span>(<?php echo $term->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            // Filtres de prix
            if (is_active_sidebar('shop-filters')) : ?>
                <div class="archive-filters__price">
                    <?php dynamic_sidebar('shop-filters'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php
if (woocommerce_product_loop()) {
    /**
     * Hook: woocommerce_before_shop_loop
     */
    do_action('woocommerce_before_shop_loop');

    woocommerce_product_loop_start();

    if (wc_get_loop_prop('total')) {
        while (have_posts()) {
            the_post();
            do_action('woocommerce_shop_loop');
            wc_get_template_part('content', 'product');
        }
    }

    woocommerce_product_loop_end();

    /**
     * Hook: woocommerce_after_shop_loop
     */
    do_action('woocommerce_after_shop_loop');
} else {
    /**
     * Hook: woocommerce_no_products_found
     */
    do_action('woocommerce_no_products_found');
}

/**
 * Hook: woocommerce_after_main_content
 */
do_action('woocommerce_after_main_content');

/**
 * Hook: woocommerce_sidebar
 */
do_action('woocommerce_sidebar');

get_footer('shop');