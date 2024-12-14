<?php
/**
 * The template for displaying product content within loops
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}
?>
<li <?php wc_product_class('product-card', $product); ?>>
    <div class="product-card__inner">
        <div class="product-card__image-wrapper">
            <?php
            /**
             * Hook: woocommerce_before_shop_loop_item
             */
            do_action('woocommerce_before_shop_loop_item');

            // Sale badge
            if ($product->is_on_sale()) {
                $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
                echo '<span class="product-card__sale-badge">-' . $percentage . '%</span>';
            }

            // Product image with link
            ?>
            <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="woocommerce-LoopProduct-link">
                <?php echo woocommerce_get_product_thumbnail('medium'); ?>
            </a>
        </div>

        <div class="product-card__content">
            <h2 class="product-card__title">
                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                    <?php echo get_the_title(); ?>
                </a>
            </h2>

            <div class="product-card__price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <div class="product-card__stock">
                <?php if ($product->is_in_stock()) : ?>
                    <span class="in-stock">En stock</span>
                <?php else : ?>
                    <span class="out-of-stock">Rupture de stock</span>
                <?php endif; ?>
            </div>

            <div class="product-card__actions">
                <?php woocommerce_template_loop_add_to_cart(); ?>
            </div>
        </div>
    </div>
</li>