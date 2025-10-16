    </div><!-- #content -->

    <footer id="colophon" class="site-footer bg-navy-950 text-white border-t border-navy-800">
        <div class="container-custom">
            <?php if (is_active_sidebar('footer-1')) : ?>
                <div class="footer-widgets py-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <?php dynamic_sidebar('footer-1'); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="footer-bottom border-t border-navy-800 py-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="site-info text-sm text-navy-300">
                        <p>&copy; <?php echo date('Y'); ?> 
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="text-white hover:text-navy-200 transition-colors duration-200">
                                <?php bloginfo('name'); ?>
                            </a>. 
                            <?php esc_html_e('All rights reserved.', 'neways-theme'); ?>
                        </p>
                    </div>
                    
                    <div class="footer-menu mt-4 md:mt-0">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_id'        => 'footer-menu',
                            'menu_class'     => 'flex space-x-6 text-sm',
                            'container'      => false,
                            'fallback_cb'    => false,
                            'depth'          => 1,
                            'link_before'    => '<span class="text-navy-300 hover:text-white transition-colors duration-200">',
                            'link_after'     => '</span>',
                        ));
                        ?>
                    </div>
                </div>
                
                <!-- Location Pages Menu -->
                <?php if (has_nav_menu('location_pages')) : ?>
                    <div class="location-pages-section mt-6 pt-6 border-t border-navy-800">
                        <h4 class="text-sm font-semibold text-navy-200 mb-3">Location Pages</h4>
                        <div class="location-pages-menu">
                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'location_pages',
                                'menu_id'        => 'location-pages-menu',
                                'menu_class'     => 'flex flex-wrap gap-4 text-sm',
                                'container'      => false,
                                'fallback_cb'    => false,
                                'depth'          => 1,
                                'link_before'    => '<span class="text-navy-300 hover:text-white transition-colors duration-200">',
                                'link_after'     => '</span>',
                            ));
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
