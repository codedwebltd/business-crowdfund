import { ref, computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useRecaptcha() {
  const page = usePage();
  const recaptchaToken = ref(null);
  const recaptchaError = ref(false);
  const recaptchaLoaded = ref(false);
  const widgetId = ref(null);

  // Get settings from global props - use computed to make it reactive
  const shouldShow = computed(() => {
    const settings = page.props.globalSettings;
    const hasRecentFraud = page.props.hasRecentFraud || false;
    const isEnabled = settings?.recaptcha_enabled || false;
    return isEnabled && hasRecentFraud;
  });

  const siteKey = computed(() => page.props.globalSettings?.recaptcha_site_key || '').value;

  /**
   * Load reCAPTCHA script (only if should show)
   */
  const loadRecaptcha = () => {
    console.log('[reCAPTCHA] loadRecaptcha called', {
      shouldShow: shouldShow.value,
      siteKey,
      hasRecentFraud: page.props.hasRecentFraud,
      isEnabled: page.props.globalSettings?.recaptcha_enabled
    });

    if (!shouldShow.value || !siteKey) {
      console.log('[reCAPTCHA] Not loading - shouldShow:', shouldShow.value, 'siteKey:', siteKey);
      recaptchaLoaded.value = true;
      return;
    }

    if (window.grecaptcha) {
      console.log('[reCAPTCHA] Already loaded');
      recaptchaLoaded.value = true;
      return;
    }

    console.log('[reCAPTCHA] Loading script from Google');
    const script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
    script.async = true;
    script.defer = true;
    script.onload = () => {
      console.log('[reCAPTCHA] Script loaded successfully');
      recaptchaLoaded.value = true;
    };
    script.onerror = () => {
      console.error('[reCAPTCHA] Failed to load script from Google');
    };
    document.head.appendChild(script);
  };

  /**
   * Render reCAPTCHA widget (only if should show)
   * @param {string} containerId - The ID of the container element
   */
  const renderRecaptcha = async (containerId) => {
    console.log('[reCAPTCHA] renderRecaptcha called', { containerId, shouldShow: shouldShow.value, recaptchaLoaded: recaptchaLoaded.value });

    if (!shouldShow.value) {
      console.log('[reCAPTCHA] Not rendering - shouldShow is false');
      return;
    }

    // Wait for script to load if it hasn't loaded yet
    if (!recaptchaLoaded.value) {
      console.log('[reCAPTCHA] Waiting for script to load...');
      await new Promise((resolve) => {
        const checkLoaded = setInterval(() => {
          if (recaptchaLoaded.value && window.grecaptcha) {
            console.log('[reCAPTCHA] Script loaded during wait');
            clearInterval(checkLoaded);
            resolve();
          }
        }, 100);

        // Timeout after 10 seconds
        setTimeout(() => {
          console.log('[reCAPTCHA] Wait timeout after 10 seconds');
          clearInterval(checkLoaded);
          resolve();
        }, 10000);
      });
    }

    if (!window.grecaptcha) {
      console.error('[reCAPTCHA] grecaptcha object not found - script failed to load');
      return;
    }

    // Check if container exists
    const container = document.getElementById(containerId);
    if (!container) {
      console.error('[reCAPTCHA] Container not found:', containerId);
      return;
    }

    try {
      // Check if already rendered
      if (widgetId.value !== null) {
        console.log('[reCAPTCHA] Already rendered, widgetId:', widgetId.value);
        return;
      }

      console.log('[reCAPTCHA] Rendering widget with siteKey:', siteKey);
      widgetId.value = window.grecaptcha.render(containerId, {
        sitekey: siteKey,
        callback: (token) => {
          console.log('[reCAPTCHA] Token received');
          recaptchaToken.value = token;
          recaptchaError.value = false;
        },
        'expired-callback': () => {
          console.log('[reCAPTCHA] Token expired');
          recaptchaToken.value = null;
          recaptchaError.value = true;
        },
        'error-callback': () => {
          console.error('[reCAPTCHA] Error callback triggered');
          recaptchaError.value = true;
        },
      });
      console.log('[reCAPTCHA] Widget rendered successfully, widgetId:', widgetId.value);
    } catch (error) {
      console.error('[reCAPTCHA] Failed to render:', error);
    }
  };

  /**
   * Get reCAPTCHA token (only if should show)
   * @returns {string|null} The reCAPTCHA token or null
   */
  const getToken = () => {
    if (!shouldShow.value) return null;

    if (!recaptchaToken.value) {
      recaptchaError.value = true;
    }

    return recaptchaToken.value;
  };

  /**
   * Reset reCAPTCHA (only if should show)
   */
  const reset = () => {
    if (!shouldShow.value || !window.grecaptcha || widgetId.value === null) {
      return;
    }

    try {
      window.grecaptcha.reset(widgetId.value);
      recaptchaToken.value = null;
      recaptchaError.value = false;
    } catch (error) {
      console.error('Failed to reset reCAPTCHA:', error);
    }
  };

  onMounted(() => {
    loadRecaptcha();
  });

  return {
    shouldShow, // TRUE only if enabled AND user has recent fraud
    siteKey,
    recaptchaToken,
    recaptchaError,
    recaptchaLoaded,
    renderRecaptcha,
    getToken,
    reset,
  };
}
