
<?php
/**
 * The Template for displaying all single products
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<div class="single-product-wrapper">
    <?php while ( have_posts() ) : ?>
        <?php the_post(); ?>
        
        <div class="product-main">
            <div class="product-gallery">
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 * 
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>

            <div class="product-summary">
                <?php
                /**
                 * Hook: woocommerce_single_product_summary.
                 *
                 * @hooked woocommerce_template_single_title - 5
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 */
                do_action( 'woocommerce_single_product_summary' );
                ?>

                <!-- Ajout d'une section garantie -->
                <div class="product-guarantee">
                    <h3>Nos garanties</h3>
                    <ul>
                        <li>Livraison gratuite dès 50€</li>
                        <li>Retour gratuit sous 30 jours</li>
                        <li>Garantie 2 ans minimum</li>
                        <li>Paiement sécurisé</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="product-tabs">
            <?php
            /**
             * Hook: woocommerce_after_single_product_summary.
             *
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action( 'woocommerce_after_single_product_summary' );
            ?>
        </div>
    <?php endwhile; ?>
</div>

<?php
get_footer( 'shop' );