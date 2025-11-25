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
// $header_classes = ( is_front_page() || is_home() )
//     ? 'bg-gradient-to-r from-[#07143a]/85 via-[#0c1f4a]/80 to-[#0c1f4a]/60 backdrop-blur-md border-b border-white/10'
//     : 'bg-[#07143a]/95 backdrop-blur-md border-b border-white/10';
?>
<div id="page" class="site min-h-screen flex flex-col">
    <header id="masthead" class="site-header z-10 w-full bg-transparent">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-4 md:px-10">
            <div class="flex items-center gap-4">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="w-20 h-24 object-contain" />
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
                <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn-primary bg-[#453E60] ">
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
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn-primary bg-[#453E60] ">
                <?php esc_html_e( 'Contact us', 'neways-theme' ); ?>
            </a>
        </nav>
    </header>

    <div id="content" class="site-content flex-grow">
