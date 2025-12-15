<?php
/**
  * Scroll Reveal Text Shortcode (viewport-activated, smooth slower animation)
 * Add into your theme's functions.php or a site-specific plugin.
 *
 * Usage:
 *  [scroll_reveal_text text="Your paragraph..." words="2"]
 *  or
 *  [scroll_reveal_text words="2"]Your paragraph...[/scroll_reveal_text]
 */

if ( ! function_exists( 'sw_enqueue_scroll_reveal_assets' ) ) {
    function sw_enqueue_scroll_reveal_assets() {
        wp_register_style( 'sw-scroll-reveal-inline', false );
        wp_enqueue_style( 'sw-scroll-reveal-inline' );

        $css = <<<'CSS'
:root{--fade-opacity:0.46;--visible-opacity:1;--word-gap:0.5rem;--font-size:22px}
.sw-scroll-wrap{max-width:1200px;margin:0 auto;min-height:190vh;display:flex;align-items:flex-start;position:relative}
.sw-scroll-wrap.sw-align-left{justify-content:flex-start}
.sw-scroll-wrap.sw-align-right{justify-content:flex-end}
.sw-scroll-inner{position:sticky;top:18vh;display:flex;flex-direction:column;align-items:flex-start;gap:28px;}
.sw-scroll-heading{display:block;font-family:"Lato";font-weight:600;font-size:16px;letter-spacing:1.44px;text-transform:uppercase;color:#312F60;}
.sw-animated-paragraph{font-family:"Lato";font-size:48px;font-weight:500;line-height:normal;color:#211F3E;background:#fff;display:inline-block ; max-width:970px;}
.sw-scroll-description{font-family:"Lato";font-size:20px;line-height:30px;color:#4b5563;max-width:970px; padding-top:8px; font-weight: 500;padding-bottom:80px }
.sw-animated-paragraph .word{display:inline-block;opacity:var(--fade-opacity);transform:translateY(6px);transition:opacity 420ms ease,transform 420ms ease;margin-right:var(--word-gap);color:#211F3E}
.sw-animated-paragraph .word.visible{opacity:var(--visible-opacity);transform:translateY(0)}
.sw-animated-paragraph .word.always-visible{opacity:var(--visible-opacity)!important;transform:translateY(0)!important}
@media (max-width:520px){:root{--font-size:18px};.sw-scroll-inner{top:14vh;padding:72px 0}}
@media (max-width:1024px){.sw-scroll-wrap{padding:0 24px;max-width:960px;min-height:170vh}.sw-scroll-inner{top:16vh;padding:64px 0}.sw-animated-paragraph{font-size:42px;line-height:1.18;}.sw-scroll-description{max-width:520px;font-size:15px}}
@media (max-width:768px){.sw-scroll-wrap{padding:0 24px;max-width:100%;min-height:155vh}.sw-scroll-inner{top:14vh;gap:22px;padding:56px 0}.sw-animated-paragraph{font-size:36px;border-radius:16px}.sw-scroll-description{max-width:100%;font-size:14.5px;padding-bottom:0px;}}
@media (max-width:520px){.sw-scroll-wrap{padding:0 24px;min-height:140vh}.sw-scroll-inner{top:12vh;padding:64px 0}.sw-animated-paragraph{font-size:30px;border-radius:14px}.sw-scroll-description{font-size:14px;padding-bottom:0px;}}
CSS;
        wp_add_inline_style( 'sw-scroll-reveal-inline', $css );

        wp_register_script( 'sw-scroll-reveal-inline', false, array(), false, true );
        wp_enqueue_script( 'sw-scroll-reveal-inline' );

        $js = <<<'JS'
(function(){
  function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }

  const containers = [];
  const states = new WeakMap();
  let lastScrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
  let scrollDirection = 'down';

  function updateScrollDirection(){
    const current = window.pageYOffset || document.documentElement.scrollTop || 0;
    if(current > lastScrollY + 2) scrollDirection = 'down';
    else if(current < lastScrollY - 2) scrollDirection = 'up';
    lastScrollY = current;
  }

  function findNextSection(wrap){
    if(!wrap) return null;
    let parent = wrap.parentElement;
    while(parent){
      if(parent.nextElementSibling){
        return parent.nextElementSibling;
      }
      parent = parent.parentElement;
    }
    return wrap.nextElementSibling;
  }

  function recalcMetrics(state){
    const wrap = state.wrap;
    const inner = state.inner;
    const rect = wrap.getBoundingClientRect();
    const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const wrapTop = rect.top + scrollY;
    const wrapHeight = wrap.offsetHeight;
    const innerHeight = inner.offsetHeight;
    const computedTop = parseFloat(window.getComputedStyle(inner).top) || 0;
    state.stickTop = wrapTop - computedTop;
    state.stickBottom = wrapTop + wrapHeight - innerHeight - computedTop;
    if(state.stickBottom <= state.stickTop){
      state.stickBottom = state.stickTop + 1;
    }
  }

  function initContainer(container){
    const original = container.textContent;
    if(!original) return;
    container.textContent = '';

    const firstWords = parseInt(container.dataset.words, 10) || 0;
    const speedAttr = parseFloat(container.dataset.speed);
    const speedFactor = Number.isFinite(speedAttr) && speedAttr > 0 ? speedAttr : 0.85;

    const wordArray = original.trim().split(/\s+/);
    const maxWords = wordArray.length;
    const firstNWords = Math.min(firstWords, maxWords);
    const remaining = Math.max(0, maxWords - firstNWords);

    const words = [];
    for(let i=0;i<wordArray.length;i++){
      const word = wordArray[i];
      const span = document.createElement('span');
      span.className = 'word';
      span.textContent = word;
      container.appendChild(span);
      words.push(span);
    }

    let marked = 0;
    while(marked < firstNWords && marked < words.length){
      words[marked].classList.add('always-visible','visible');
      marked++;
    }

    let visibleCount = Math.max(firstNWords, 0);

    function applyVisibleCount(n){
      const clamped = Math.max(firstNWords, Math.min(maxWords, Math.round(n)));
      visibleCount = clamped;
      for(let i=0;i<maxWords;i++){
        if(i < visibleCount){
          words[i].classList.add('visible');
        } else if(!words[i].classList.contains('always-visible')){
          words[i].classList.remove('visible');
        }
      }
    }

    applyVisibleCount(visibleCount);

    const state = {
      container,
      wrap: container.closest('.sw-scroll-wrap'),
      inner: container.parentElement,
      firstNWords,
      remaining,
      speed: speedFactor,
      progress: 0,
      target: 0,
      autoTriggered: false,
      autoScrolling: false,
      startThreshold: 0.1,
      updateVisible: applyVisibleCount,
      stickTop: 0,
      stickBottom: 1
    };

    recalcMetrics(state);

    states.set(container, state);
    containers.push(container);
  }

  function animate(){
    const scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;

    containers.forEach(container => {
      const state = states.get(container);
      if(!state) return;

      const wrap = state.wrap;
      const inner = state.inner;
      const wrapRect = wrap.getBoundingClientRect();
      const vh = window.innerHeight || document.documentElement.clientHeight;
      const intersectionHeight = Math.max(0, Math.min(wrapRect.bottom, vh) - Math.max(wrapRect.top, 0));
      const visibleRatio = wrapRect.height > 0 ? clamp(intersectionHeight / wrapRect.height, 0, 1) : 0;

      const rangeStart = state.stickTop;
      const rangeEnd = state.stickBottom;
      let targetProgress = 0;

      if(scrollY <= rangeStart){
        targetProgress = 0;
      } else if(scrollY >= rangeEnd){
        targetProgress = 1;
      } else {
        const raw = (scrollY - rangeStart) / (rangeEnd - rangeStart);
        const normalized = Math.max(0, raw - state.startThreshold) / (1 - state.startThreshold);
        targetProgress = clamp(normalized * state.speed + visibleRatio * 0.2, 0, 1);
      }

      state.target = targetProgress;
      const smoothing = scrollY > rangeStart && scrollY < rangeEnd ? (0.12 * state.speed) : 0.08;
      state.progress += (state.target - state.progress) * smoothing;
      if(Math.abs(state.target - state.progress) < 0.0005){
        state.progress = state.target;
      }

      const revealCount = state.firstNWords + Math.round(state.remaining * state.progress);
      state.updateVisible(revealCount);

      if(scrollDirection === 'up' && state.progress <= 0.05){
        state.autoTriggered = false;
      }
    });

    requestAnimationFrame(animate);
  }

  function initAll(){
    const nodes = document.querySelectorAll('.sw-animated-paragraph');
    nodes.forEach(initContainer);
    requestAnimationFrame(animate);
  }

  function onResize(){
    containers.forEach(container => {
      const state = states.get(container);
      if(state) recalcMetrics(state);
    });
  }

  function smoothScrollTo(targetY, duration){
    const startY = window.pageYOffset || document.documentElement.scrollTop || 0;
    const distance = targetY - startY;
    if(Math.abs(distance) < 1){
      window.scrollTo(0, targetY);
      return;
    }
    const start = performance.now();
    function easeOutQuad(t){ return t * (2 - t); }
    function step(now){
      const elapsed = Math.min(1, (now - start) / duration);
      const eased = easeOutQuad(elapsed);
      window.scrollTo(0, startY + distance * eased);
      if(elapsed < 1){
        requestAnimationFrame(step);
      }
    }
    requestAnimationFrame(step);
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

  window.addEventListener('scroll', updateScrollDirection, {passive:true});
  window.addEventListener('wheel', updateScrollDirection, {passive:true});
  window.addEventListener('resize', onResize);
})();
JS;

        wp_add_inline_script( 'sw-scroll-reveal-inline', $js );
    }
    add_action( 'wp_enqueue_scripts', 'sw_enqueue_scroll_reveal_assets' );
}

if ( ! function_exists( 'sw_scroll_reveal_shortcode' ) ) {
    function sw_scroll_reveal_shortcode( $atts = array(), $content = null ) {
        $raw_atts = $atts;
        $atts = shortcode_atts(
            array(
                'text'  => '',
                'words' => 0,
                'speed' => 0.85,
                'class' => '',
                'align' => 'center',
                'heading' => '',
                'description' => '',
            ),
            $atts,
            'scroll_reveal_text'
        );

        $allowed_tags = array(
            'br'   => array(),
            'strong' => array(),
            'em'   => array(),
            'span' => array( 'style' => true, 'class' => true ),
            'b'    => array(),
            'i'    => array(),
        );

        $raw_content = (string) $content;

        $embedded_heading = '';
        if ( preg_match( '/\[scroll_heading\](.*?)\[\/scroll_heading\]/is', $raw_content, $heading_match ) ) {
            $embedded_heading = wp_strip_all_tags( $heading_match[1] );
            $raw_content = str_replace( $heading_match[0], '', $raw_content );
        }

        $embedded_description = '';
        if ( preg_match( '/\[scroll_description\](.*?)\[\/scroll_description\]/is', $raw_content, $description_match ) ) {
            $embedded_description = wp_kses_post( $description_match[1] );
            $raw_content = str_replace( $description_match[0], '', $raw_content );
        }

        if ( trim( $raw_content ) !== '' ) {
            $text = wp_kses( trim( $raw_content ), $allowed_tags );
        } else {
            $text = wp_kses( $atts['text'], $allowed_tags );
        }

        if ( empty( $text ) ) {
            $text = 'We exist to create a world where every individual receives care that honors their dignity, respects their independence, and enhances their quality of life.';
        }

        $split_heading = sw_resolve_split_attribute_value( $raw_atts, 'heading' );
        $split_description = sw_resolve_split_attribute_value( $raw_atts, 'description' );

        $heading_source = $split_heading !== '' ? $split_heading : $atts['heading'];
        $description_source = $split_description !== '' ? $split_description : $atts['description'];

        $heading_attr = trim( wp_strip_all_tags( html_entity_decode( $heading_source, ENT_QUOTES | ENT_HTML5 ) ) );
        $heading_attr = str_replace( array( '“', '”', '"', '"', '”', '“', '"' ), '', $heading_attr );
        $heading_attr = trim( $heading_attr );

        $description_attr = wp_kses_post( html_entity_decode( $description_source, ENT_QUOTES | ENT_HTML5 ) );
        $description_attr = trim( $description_attr );
        $description_attr = str_replace( array( '“', '”', '"', '"', '”', '“' ), '', $description_attr );

        $heading = $embedded_heading !== '' ? $embedded_heading : $heading_attr;
        $description = $embedded_description !== '' ? $embedded_description : $description_attr;

        if ( '' === $heading ) {
            $heading = 'Our story & why we exist?';
        }

        if ( '' === $description ) {
            $description = 'Our purpose is rooted in the belief that compassionate, person-centered care isn’t just a service — it is a fundamental human right. We are committed to building a healthcare ecosystem where families feel supported, professionals feel valued, and those we care for feel truly seen and heard.';
        }

        $words = intval( $atts['words'] );
        if ( $words < 0 ) $words = 0;

        $wrap_classes = array_filter( array_map( 'trim', explode( ' ', (string) $atts['class'] ) ) );
        $wrap_classes[] = 'sw-scroll-wrap';

        $wrap_class_attr = implode( ' ', array_map( 'sanitize_html_class', $wrap_classes ) );

        $wrap_align_class = '';
        if ( 'left' === strtolower( $atts['align'] ) ) {
            $wrap_align_class = 'sw-align-left';
        } elseif ( 'right' === strtolower( $atts['align'] ) ) {
            $wrap_align_class = 'sw-align-right';
        }

        $p_class = 'sw-animated-paragraph';
        $speed = floatval( $atts['speed'] );
        if ( $speed <= 0 ) {
            $speed = 1;
        }

        $heading = sw_normalize_punctuation( $heading );
        $description = sw_normalize_punctuation( $description );

        $out  = '<div class="' . esc_attr( trim( $wrap_class_attr . ' ' . $wrap_align_class ) ) . '">';
        $out .= '<div class="sw-scroll-inner">';
        $out .= '<span class="sw-scroll-heading">' . esc_html( $heading ) . '</span>';
        $out .= '<div class="sw-animated-paragraph" data-words="' . esc_attr( $words ) . '" data-speed="' . esc_attr( $speed ) . '">' . $text . '</div>';
        $out .= '<p class="sw-scroll-description">' . wp_kses_post( $description ) . '</p>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode( 'scroll_reveal_text', 'sw_scroll_reveal_shortcode' );
}

if ( ! function_exists( 'sw_resolve_split_attribute_value' ) ) {
    function sw_resolve_split_attribute_value( $raw_atts, $target ) {
        if ( empty( $raw_atts ) ) {
            return '';
        }

        $known_keys = array(
            'text',
            'words',
            'speed',
            'class',
            'align',
            'heading',
            'description',
        );

        $target   = strtolower( $target );
        $parts    = array();
        $collect  = false;

        foreach ( $raw_atts as $key => $value ) {
            $value = is_array( $value ) ? '' : (string) $value;

            $key_lower = is_int( $key ) ? '' : strtolower( $key );

            if ( $key_lower === $target ) {
                $collect = true;
            }

            if ( $collect ) {
                if ( $key_lower !== '' && $key_lower !== $target && in_array( $key_lower, $known_keys, true ) ) {
                    break;
                }

                if ( $value !== '' ) {
                    $parts[] = $value;
                }
                continue;
            }
        }

        if ( empty( $parts ) ) {
            return '';
        }

        $joined = implode( ' ', array_map( 'trim', $parts ) );
        $joined = trim( $joined );

        if ( $joined === '' ) {
            return '';
        }

        $joined = preg_replace( '/^[\s\p{Pf}\p{Pi}"\']+|[\s\p{Pf}\p{Pi}"\']+$/u', '', $joined );
        return trim( $joined );
    }
}

if ( ! function_exists( 'sw_normalize_punctuation' ) ) {
    function sw_normalize_punctuation( $string ) {
        $map = array(
            '“' => '"',
            '”' => '"',
            '‘' => "'",
            '’' => "'",
            '—' => '—',
            '–' => '-',
        );
        return strtr( $string, $map );
    }
}
?>
