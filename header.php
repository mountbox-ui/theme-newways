<?php
/**
 * The header for our theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Manrope:wght@200..800&family=Marcellus&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
// Determine header classes based on page type
if (is_front_page() || is_home()) {
    // Home page - transparent background
    $header_classes = 'site-header z-10 w-full bg-transparent';
    $container_classes = 'mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 pt-4 md:px-10';
    $logo_classes = 'w-20 h-24 object-contain';
} else {
    // All other pages - solid background with fixed height
    $header_classes = 'site-header z-50 w-full bg-[#140B2A] h-20';
    $container_classes = 'mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 h-full md:px-10';
    $logo_classes = 'h-16 w-auto object-contain';
    
    // Special background for careers pages
    if (is_singular('jobs') || is_page_template('jobs/page-jobs.php') || is_page('career')) {
        $header_classes = 'site-header z-50 w-full bg-[#1C1A1D] h-20 header-careers';
    }
}
?>
<div id="page" class="site min-h-screen flex flex-col">
    <header id="masthead" class="<?php echo esc_attr($header_classes); ?>">
        <div class="<?php echo esc_attr($container_classes); ?>">
            <div class="flex items-center gap-4">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="<?php echo esc_attr($logo_classes); ?>" />
                </a>
            </div>

            <nav id="site-navigation" class="main-navigation hidden flex-1 items-center justify-end gap-8 font-lato text-lg font-semibold text-white lg:flex">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'flex items-center gap-8',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2,
                    )
                );
                ?>
                <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn min-w-[150px] bg-[#453E60] ">
                    <?php esc_html_e( 'Contact us', 'neways-theme' ); ?>
                </a>
            </nav>

            <button id="mobile-menu-button" class="inline-flex items-center justify-center rounded-md p-2 text-white transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white lg:hidden" aria-expanded="false">
                <span class="sr-only"><?php esc_html_e( 'Open main menu', 'neways-theme' ); ?></span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <nav id="mobile-navigation" class="mobile-navigation hidden border-t border-white/10 bg-[#07143a]/95 px-4 py-4 text-white lg:hidden">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'mobile-menu',
                    'menu_class'     => 'space-y-2 text-base font-medium',
                    'container'      => false,
                    'fallback_cb'    => false,
                    'depth'          => 2,
                )
            );
            ?>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn ">
                <?php esc_html_e( 'Contact us', 'neways-theme' ); ?>
            </a>
        </nav>
    </header>

    <div id="content" class="site-content flex-grow min-h-screen">
