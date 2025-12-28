<template>
  <div v-if="isEnabled">
    <div :id="containerId" class="flex justify-center my-4"></div>
    <p v-if="showError" class="text-red-400 text-xs mt-2 text-center">⚠️ Please complete the CAPTCHA verification</p>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  siteKey: {
    type: String,
    required: true
  },
  isEnabled: {
    type: Boolean,
    default: true
  },
  showError: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['verified', 'expired']);

const containerId = ref(`recaptcha-${Math.random().toString(36).substr(2, 9)}`);
const widgetId = ref(null);

const loadRecaptcha = () => {
  if (window.grecaptcha) {
    renderRecaptcha();
  } else {
    const script = document.createElement('script');
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
    script.async = true;
    script.defer = true;
    script.onload = () => {
      if (window.grecaptcha && window.grecaptcha.ready) {
        window.grecaptcha.ready(renderRecaptcha);
      }
    };
    document.head.appendChild(script);
  }
};

const renderRecaptcha = () => {
  if (!window.grecaptcha || !props.isEnabled) return;

  const container = document.getElementById(containerId.value);
  if (!container) return;

  try {
    widgetId.value = window.grecaptcha.render(container, {
      sitekey: props.siteKey,
      callback: onVerified,
      'expired-callback': onExpired,
      theme: 'dark'
    });
  } catch (error) {
    console.error('reCAPTCHA render failed:', error);
  }
};

const onVerified = (token) => {
  emit('verified', token);
};

const onExpired = () => {
  emit('expired');
};

const reset = () => {
  if (window.grecaptcha && widgetId.value !== null) {
    window.grecaptcha.reset(widgetId.value);
  }
};

onMounted(() => {
  if (props.isEnabled) {
    loadRecaptcha();
  }
});

onUnmounted(() => {
  // Cleanup is handled automatically by Google
});

defineExpose({ reset });
</script>
