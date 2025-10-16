<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@300;400;700&family=Poppins:wght@300;400;500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site min-h-screen flex flex-col">
    <header id="masthead" class="site-header <?php echo (is_front_page() || is_home()) ? 'bg-transparent backdrop-blur-sm' : 'bg-slate-700'; ?> fixed shadow-lg top-0 pt-4 w-full z-50">
        <div class="container-custom">
            <div class="flex items-center justify-between py-4">
                <!-- Site Logo/Title -->
            <div class="site-branding">
                <?php if (has_custom_logo()) : ?>
                    <div class="site-logo">
                    <?php the_custom_logo(); ?>
                    </div>
                <?php else : ?>
                    <h1 class="site-title text-2xl font-bold">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-white hover:text-navy-200 transition-colors duration-200">
                     <?php bloginfo('name'); ?>
                    </a>
                    </h1>
                <?php endif; ?>
            </div>


                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-white hover:text-navy-200 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-navy-400" aria-expanded="false">
                    <span class="sr-only"><?php esc_html_e('Open main menu', 'neways-theme'); ?></span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Desktop Navigation -->
                <nav id="site-navigation" class="main-navigation hidden lg:block">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'flex space-x-4',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2, // Allow submenus
                    ));
                    ?>
                </nav>

                <?php // Search icon removed from navbar ?>
            </div>

            <!-- Mobile Navigation -->
            <nav id="mobile-navigation" class="mobile-navigation lg:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 border-t border-navy-700">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'mobile-menu',
                        'menu_class'     => 'space-y-1',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'depth'          => 2, // Allow submenus
                    ));
                    ?>
                </div>
            </nav>

            <?php // Collapsible search form removed ?>
        </div>
    </header>

    <div id="content" class="site-content flex-grow"><?php // Opening tag closed in footer.php ?>
