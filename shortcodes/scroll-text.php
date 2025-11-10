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
:root{--fade-opacity:0.18;--visible-opacity:1;--char-gap:0.08rem;--font-size:22px}
.sw-scroll-wrap{max-width:960px;margin:0 auto;min-height:190vh;padding:0 24px;display:flex;justify-content:center;align-items:flex-start;position:relative}
.sw-scroll-wrap.sw-align-left{justify-content:flex-start}
.sw-scroll-wrap.sw-align-right{justify-content:flex-end}
.sw-scroll-inner{position:sticky;top:18vh;display:flex;flex-direction:column;align-items:flex-start;gap:16px;padding:72px 0;width:min(100%,720px)}
.sw-animated-paragraph{font-size:48px;font-weight:500;background:#fff;padding:28px;border-radius:12px;display:inline-block}
.sw-animated-paragraph .char{display:inline-block;opacity:var(--fade-opacity);transform:translateY(6px);transition:opacity 420ms ease,transform 420ms ease;letter-spacing:var(--char-gap);color:rgba(0,0,0,1);white-space:pre}
.sw-animated-paragraph .char.visible{opacity:var(--visible-opacity);transform:translateY(0)}
.sw-animated-paragraph .char.space{width:0.4rem}
.sw-animated-paragraph .char.always-visible{opacity:var(--visible-opacity)!important;transform:translateY(0)!important}
.sw-scroll-progress{height:4px;width:100%;background:linear-gradient(90deg,#111 var(--p,0%),rgba(0,0,0,0.08) 0%);border-radius:4px;transition:background 140ms linear}
@media (max-width:520px){:root{--font-size:18px};.sw-scroll-inner{top:14vh;padding:72px 0}}
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

    const words = original.split(/\s+/);
    let firstNChars = 0;
    for(let i=0;i<Math.min(firstWords, words.length);i++){
      firstNChars += words[i].length + (i < firstWords-1 ? 1 : 0);
    }

    const chars = [];
    for(let i=0;i<original.length;i++){
      const ch = original[i];
      const span = document.createElement('span');
      span.className = 'char';
      if(ch === ' '){
        span.classList.add('space');
        span.textContent = ' ';
      } else {
        span.textContent = ch;
      }
      container.appendChild(span);
      chars.push(span);
    }

    let marked = 0;
    while(marked < firstNChars && marked < chars.length){
      chars[marked].classList.add('always-visible','visible');
      marked++;
    }

    let progressBar = container.nextElementSibling;
    if(!progressBar || !progressBar.classList.contains('sw-scroll-progress')){
      progressBar = document.createElement('div');
      progressBar.className = 'sw-scroll-progress';
      container.parentNode.insertBefore(progressBar, container.nextSibling);
    }

    let visibleCount = Math.max(firstNChars, 0);
    const maxChars = chars.length;
    const remaining = Math.max(0, maxChars - firstNChars);

    function applyVisibleCount(n){
      const clamped = Math.max(firstNChars, Math.min(maxChars, Math.round(n)));
      visibleCount = clamped;
      for(let i=0;i<maxChars;i++){
        if(i < visibleCount){
          chars[i].classList.add('visible');
        } else if(!chars[i].classList.contains('always-visible')){
          chars[i].classList.remove('visible');
        }
      }
      const pct = maxChars > 0 ? Math.round((visibleCount / maxChars) * 100) : 0;
      progressBar.style.setProperty('--p', pct + '%');
    }

    applyVisibleCount(visibleCount);

    const state = {
      container,
      wrap: container.closest('.sw-scroll-wrap'),
      inner: container.parentElement,
      firstNChars,
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

      const revealCount = state.firstNChars + Math.round(state.remaining * state.progress);
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
        $atts = shortcode_atts(
            array(
                'text'  => '',
                'words' => 0,
                'speed' => 0.85,
                'class' => '',
                'align' => 'center',
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

        if ( ! empty( $content ) ) {
            $text = wp_kses( trim( $content ), $allowed_tags );
        } else {
            $text = wp_kses( $atts['text'], $allowed_tags );
        }

        if ( empty( $text ) ) {
            $text = 'We exist to create a world where every individual receives care that honors their dignity, respects their independence, and enhances their quality of life.';
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

        $out  = '<div class="' . esc_attr( trim( $wrap_class_attr . ' ' . $wrap_align_class ) ) . '">';
        $out .= '<div class="sw-scroll-inner">';
        $out .= '<div class="' . esc_attr( $p_class ) . '" data-words="' . esc_attr( $words ) . '" data-speed="' . esc_attr( $speed ) . '">' . $text . '</div>';
        $out .= '<div class="sw-scroll-progress" style="--p:0%"></div>';
        $out .= '</div>';
        $out .= '</div>';

        return $out;
    }
    add_shortcode( 'scroll_reveal_text', 'sw_scroll_reveal_shortcode' );
}
?>
