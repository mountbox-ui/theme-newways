    </div><!-- #content -->

    <footer id="colophon" class="site-footer bg-[#0F172B] text-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-24">
                <!-- Logo and Tagline Section -->
                <div class="flex flex-col">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block mb-4">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" 
                             alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                             class="h-16 w-auto object-contain mb-2" />
                    </a>
                    <p class="text-[#99A1AF] text-sm leading-relaxed font-lato font-normal tracking-[-0.15px]">
                        Empowering Better Living Through Compassionate Care
                    </p>
                </div>

                <!-- Our Sub-Brands Section -->
                <div>
                    <h4 class="text-white font-semibold text-base mb-4 font-lato">Our Sub-Brands</h4>
                    <ul class="space-y-3">
                        <?php
                        $sub_brands = array(
                            array('title' => 'Neways at Home', 'url' => '#'),
                            array('title' => 'Neways Residential', 'url' => '#'),
                            array('title' => 'Neways Recruitment', 'url' => '#'),
                            array('title' => 'Neways Consulting', 'url' => '#'),
                        );
                        foreach ($sub_brands as $brand) : ?>
                            <li>
                                <a href="<?php echo esc_url($brand['url']); ?>" 
                                   class="text-[#99A1AF] hover:text-gray-300 transition-colors duration-200 text-sm font-lato font-normal tracking-[-0.15px]">
                                    <?php echo esc_html($brand['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Quick Links Section -->
                <div>
                    <h4 class="text-white font-semibold text-base mb-4 font-lato">Quick Links</h4>
                    <ul class="space-y-3">
                        <?php
                        $quick_links = array(
                            array('title' => 'About Us', 'url' => '#'),
                            array('title' => 'Services', 'url' => '#'),
                            array('title' => 'Our Expertise', 'url' => '#'),
                            array('title' => 'Careers', 'url' => '#'),
                            array('title' => 'Contact', 'url' => '#'),
                        );
                        foreach ($quick_links as $link) : ?>
                            <li>
                                <a href="<?php echo esc_url($link['url']); ?>" 
                                   class="text-[#99A1AF] hover:text-gray-300 transition-colors duration-200 text-sm font-lato font-normal tracking-[-0.15px]">
                                    <?php echo esc_html($link['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Us Section -->
                <div>
                    <h4 class="text-white font-semibold text-base mb-4 font-lato">Contact Us</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
  <path d="M9.22137 11.045C9.35906 11.1082 9.51418 11.1227 9.66117 11.086C9.80816 11.0493 9.93826 10.9636 10.03 10.843L10.2667 10.533C10.3909 10.3674 10.5519 10.233 10.7371 10.1404C10.9222 10.0479 11.1264 9.99967 11.3334 9.99967H13.3334C13.687 9.99967 14.0261 10.1402 14.2762 10.3902C14.5262 10.6402 14.6667 10.9794 14.6667 11.333V13.333C14.6667 13.6866 14.5262 14.0258 14.2762 14.2758C14.0261 14.5259 13.687 14.6663 13.3334 14.6663C10.1508 14.6663 7.09853 13.4021 4.84809 11.1516C2.59766 8.90119 1.33337 5.84894 1.33337 2.66634C1.33337 2.31272 1.47385 1.97358 1.7239 1.72353C1.97395 1.47348 2.31309 1.33301 2.66671 1.33301H4.66671C5.02033 1.33301 5.35947 1.47348 5.60952 1.72353C5.85956 1.97358 6.00004 2.31272 6.00004 2.66634V4.66634C6.00004 4.87333 5.95185 5.07749 5.85928 5.26263C5.76671 5.44777 5.6323 5.60881 5.46671 5.73301L5.15471 5.96701C5.03232 6.06046 4.94605 6.1934 4.91057 6.34324C4.87508 6.49308 4.89256 6.65059 4.96004 6.78901C5.87116 8.63959 7.36966 10.1362 9.22137 11.045Z" stroke="#99A1AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            <a href="tel:+441234567890" class="text-[#99A1AF] hover:text-gray-300 transition-colors duration-200 text-sm font-lato font-normal tracking-[-0.15px]">
                                +44 (0) 123 456 7890
                            </a>
                        </li>
                        <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
  <path d="M14.6667 4.66699L8.67271 8.48499C8.4693 8.60313 8.23827 8.66536 8.00304 8.66536C7.76782 8.66536 7.53678 8.60313 7.33337 8.48499L1.33337 4.66699" stroke="#99A1AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M13.3334 2.66699H2.66671C1.93033 2.66699 1.33337 3.26395 1.33337 4.00033V12.0003C1.33337 12.7367 1.93033 13.3337 2.66671 13.3337H13.3334C14.0698 13.3337 14.6667 12.7367 14.6667 12.0003V4.00033C14.6667 3.26395 14.0698 2.66699 13.3334 2.66699Z" stroke="#99A1AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            <a href="mailto:info@newayshealthcare.co.uk" class="text-[#99A1AF] hover:text-gray-300 transition-colors duration-200 text-sm font-lato font-normal tracking-[-0.15px]">
                                info@newayshealthcare.co.uk
                            </a>
                        </li>
                        <li class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
  <path d="M13.3333 6.66634C13.3333 9.99501 9.64063 13.4617 8.40063 14.5323C8.28511 14.6192 8.14449 14.6662 7.99996 14.6662C7.85543 14.6662 7.71481 14.6192 7.59929 14.5323C6.35929 13.4617 2.66663 9.99501 2.66663 6.66634C2.66663 5.25185 3.22853 3.8953 4.22872 2.89511C5.22892 1.89491 6.58547 1.33301 7.99996 1.33301C9.41445 1.33301 10.771 1.89491 11.7712 2.89511C12.7714 3.8953 13.3333 5.25185 13.3333 6.66634Z" stroke="#99A1AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8 8.66699C9.10457 8.66699 10 7.77156 10 6.66699C10 5.56242 9.10457 4.66699 8 4.66699C6.89543 4.66699 6 5.56242 6 6.66699C6 7.77156 6.89543 8.66699 8 8.66699Z" stroke="#99A1AF" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            <span class="text-[#99A1AF] text-sm font-lato font-normal tracking-[-0.15px]">
                                123 Healthcare Avenue<br>London, UK
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright Section -->
            <div class="border-t border-white/10 mt-12 pt-6">
                <p class="text-[#99A1AF] text-sm font-lato font-normal tracking-[-0.15px]">
                    &copy; <?php echo date('Y'); ?> Neways Healthcare. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
