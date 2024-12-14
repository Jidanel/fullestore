<?php
/**
 * Astra Child Theme functions and definitions
 */

function astra_child_enqueue_styles() {
    wp_enqueue_style( 
        'astra-child-style', 
        get_stylesheet_uri(),
        array('astra-theme-css'), // Dépendance au style du thème parent
        wp_get_theme()->get('Version') // Version du thème enfant
    );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles' );

// Enqueue WooCommerce styles
function electro_shop_enqueue_styles() {
    wp_enqueue_style(
        'electro-shop-woocommerce',
        get_stylesheet_directory_uri() . '/assets/css/woocommerce-custom.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'electro_shop_enqueue_styles');

// Personnalisation du nombre de produits par page
function electro_shop_products_per_page() {
    return 12;
}
add_filter('loop_shop_per_page', 'electro_shop_products_per_page');

// Ajout des badges promotion
function electro_shop_sale_badge() {
    global $product;
    if ($product->is_on_sale()) {
        $percentage = round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100);
        echo '<span class="onsale">-' . $percentage . '%</span>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'electro_shop_sale_badge');

// Ajout du système de notation par étoiles
function electro_shop_rating_stars() {
    global $product;
    $rating = $product->get_average_rating();
    $count = $product->get_review_count();
    
    echo wc_get_rating_html($rating, $count);
}
add_action('woocommerce_after_shop_loop_item_title', 'electro_shop_rating_stars');

// Personnalisation des onglets produit
function electro_shop_product_tabs($tabs) {
    // Ajout d'un onglet personnalisé
    $tabs['specifications'] = array(
        'title'    => __('Spécifications', 'astra-child'),
        'priority' => 15,
        'callback' => 'electro_shop_specifications_tab_content'
    );
    
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'electro_shop_product_tabs');

function electro_shop_specifications_tab_content() {
    // Contenu de l'onglet spécifications
    $specs = get_post_meta(get_the_ID(), '_product_specifications', true);
    if ($specs) {
        echo '<div class="product-specifications">' . wpautop($specs) . '</div>';
    }
}


function add_custom_stock_fields() {
    add_action('woocommerce_product_options_inventory_product_data', 'add_stock_threshold_fields');
    add_action('woocommerce_process_product_meta', 'save_stock_threshold_fields');
}
add_action('init', 'add_custom_stock_fields');

/**
 * Ajoute les champs dans l'interface d'administration
 */
function add_stock_threshold_fields() {
    global $post;

    echo '<div class="options_group">';
    
    // Seuil d'alerte personnalisé
    woocommerce_wp_text_input(
        array(
            'id' => '_custom_low_stock_threshold',
            'label' => __('Seuil d\'alerte personnalisé', 'astra-child'),
            'desc_tip' => true,
            'description' => __('Définir un seuil d\'alerte spécifique pour ce produit', 'astra-child'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
            )
        )
    );

    // Stock optimal
    woocommerce_wp_text_input(
        array(
            'id' => '_optimal_stock_level',
            'label' => __('Niveau de stock optimal', 'astra-child'),
            'desc_tip' => true,
            'description' => __('Niveau de stock idéal pour ce produit', 'astra-child'),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => '1',
                'min' => '0'
            )
        )
    );

    echo '</div>';
}

/**
 * Sauvegarde les valeurs des champs personnalisés
 */
function save_stock_threshold_fields($post_id) {
    $custom_threshold = isset($_POST['_custom_low_stock_threshold']) ? sanitize_text_field($_POST['_custom_low_stock_threshold']) : '';
    $optimal_stock = isset($_POST['_optimal_stock_level']) ? sanitize_text_field($_POST['_optimal_stock_level']) : '';

    update_post_meta($post_id, '_custom_low_stock_threshold', $custom_threshold);
    update_post_meta($post_id, '_optimal_stock_level', $optimal_stock);
}

/**
 * Ajoute une colonne stock dans la liste des produits
 */
function add_stock_status_column($columns) {
    $columns['stock_status'] = __('Statut du stock', 'astra-child');
    return $columns;
}
add_filter('manage_edit-product_columns', 'add_stock_status_column', 20);

/**
 * Affiche le statut du stock dans la colonne
 */
function display_stock_status_column($column, $postid) {
    if ($column === 'stock_status') {
        $product = wc_get_product($postid);
        if ($product && $product->managing_stock()) {
            $stock = $product->get_stock_quantity();
            $optimal = get_post_meta($postid, '_optimal_stock_level', true);
            $threshold = get_post_meta($postid, '_custom_low_stock_threshold', true);
            
            echo '<div class="stock-status">';
            echo sprintf(__('Stock: %d', 'astra-child'), $stock) . '<br>';
            
            if ($stock <= $threshold) {
                echo '<span class="stock-alert">' . __('Stock bas!', 'astra-child') . '</span>';
            } elseif ($stock >= $optimal) {
                echo '<span class="stock-optimal">' . __('Stock optimal', 'astra-child') . '</span>';
            }
            echo '</div>';
        }
    }
}
add_action('manage_product_posts_custom_column', 'display_stock_status_column', 10, 2);

// Support WooCommerce
function astra_child_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'astra_child_add_woocommerce_support');

// Force WooCommerce templates from child theme
function astra_child_wc_template_override($template, $template_name, $template_path) {
    $child_path = get_stylesheet_directory() . '/woocommerce/' . $template_name;
    return file_exists($child_path) ? $child_path : $template;
}
add_filter('woocommerce_locate_template', 'astra_child_wc_template_override', 10, 3);

function add_ajax_support() {
    if (function_exists('is_product')) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
}
add_action('wp_enqueue_scripts', 'add_ajax_support');

// Mise à jour du mini-panier
function update_mini_cart_count($fragments) {
    ob_start();
    ?>
    <span class="cart-count">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'update_mini_cart_count');

function remove_admin_bar() {
    if (!current_user_can('edit_posts')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

function setup_custom_roles() {
    // Supprimer le rôle existant d'éditeur pour le recréer
    remove_role('shop_manager');
    remove_role('shop_vendor');
    remove_role('inventory_manager');
    
    // Gestionnaire de boutique (équivalent Shop Manager)
    add_role(
        'shop_manager',
        'Gestionnaire de boutique',
        array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'publish_posts' => true,
            'upload_files' => true,
            'manage_woocommerce' => true,
            'view_woocommerce_reports' => true,
            'edit_product' => true,
            'read_product' => true,
            'delete_product' => true,
            'edit_products' => true,
            'publish_products' => true,
            'read_private_products' => true,
            'delete_products' => true,
            'manage_product_terms' => true,
            'edit_shop_orders' => true,
            'read_shop_orders' => true,
            'delete_shop_orders' => true,
            'publish_shop_orders' => true,
            'manage_shop_order_terms' => true,
            'manage_product_categories' => true,
            'manage_coupons' => true
        )
    );

    // Vendeur (peut gérer ses propres produits)
    add_role(
        'shop_vendor',
        'Vendeur',
        array(
            'read' => true,
            'edit_posts' => true,
            'upload_files' => true,
            'edit_product' => true,
            'read_product' => true,
            'edit_products' => true,
            'publish_products' => true,
            'edit_published_products' => true,
            'assign_product_terms' => true,
            'view_woocommerce_reports' => true
        )
    );

    // Gestionnaire des stocks
    add_role(
        'inventory_manager',
        'Gestionnaire des stocks',
        array(
            'read' => true,
            'edit_product' => true,
            'read_product' => true,
            'edit_products' => true,
            'edit_published_products' => true,
            'read_private_products' => true,
            'manage_stock' => true,
            'view_woocommerce_reports' => true
        )
    );
}
register_activation_hook(__FILE__, 'setup_custom_roles');

// Ajouter des capacités personnalisées pour le gestionnaire des stocks
function add_inventory_capabilities() {
    $role = get_role('inventory_manager');
    if ($role) {
        $role->add_cap('manage_stock', true);
        $role->add_cap('update_stock', true);
        $role->add_cap('view_stock_reports', true);
    }
}
add_action('init', 'add_inventory_capabilities');

// Restreindre l'accès au stock pour les vendeurs
function restrict_stock_management($allcaps, $caps, $args) {
    if (isset($args[0]) && $args[0] === 'manage_stock') {
        if (isset($allcaps['shop_vendor']) && $allcaps['shop_vendor']) {
            return false;
        }
    }
    return $allcaps;
}
add_filter('user_has_cap', 'restrict_stock_management', 10, 3);

// Masquer les menus en fonction des rôles
function hide_menu_items() {
    if (current_user_can('shop_vendor')) {
        remove_menu_page('edit.php?post_type=shop_order');
        remove_menu_page('wc-settings');
        remove_menu_page('wc-reports');
    }
    
    if (current_user_can('inventory_manager')) {
        remove_menu_page('edit.php?post_type=shop_coupon');
        remove_menu_page('wc-settings');
        remove_menu_page('users.php');
    }
}
add_action('admin_menu', 'hide_menu_items', 999);

// Configuration du checkout pour les non-connectés
function custom_checkout_fields($fields) {
    // Rendre l'inscription facultative
    $fields['account']['createaccount']['required'] = false;
    
    // Ajouter un champ téléphone obligatoire
    $fields['billing']['billing_phone']['required'] = true;
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'custom_checkout_fields');

// Optimiser le processus de checkout
function optimize_checkout_process() {
    // Permettre le checkout sans compte
    add_filter('woocommerce_enable_guest_checkout', '__return_true');
    
    // Désactiver l'inscription obligatoire
    add_filter('woocommerce_enable_signup_and_login_from_checkout', '__return_false');
    
    // Désactiver la création de compte obligatoire
    add_filter('woocommerce_create_account_default_checked', '__return_false');
}
add_action('init', 'optimize_checkout_process');