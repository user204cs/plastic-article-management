<template>
  <div v-if="show" class="alert-overlay">
    <div class="alert-container" :class="type">
      <div class="alert-icon">
        <svg v-if="type === 'success'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg v-if="type === 'error'" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 9V13M12 17H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <div class="alert-content">
        <h4>{{ title }}</h4>
        <p>{{ message }}</p>
      </div>
      <button class="alert-close" @click="close">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AlertModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'success',
      validator: (value) => ['success', 'error', 'warning'].includes(value)
    },
    title: {
      type: String,
      required: true
    },
    message: {
      type: String,
      required: true
    },
    autoClose: {
      type: Number,
      default: 4000
    }
  },
  mounted() {
    if (this.show && this.autoClose > 0) {
      setTimeout(() => {
        this.close()
      }, this.autoClose)
    }
  },
  methods: {
    close() {
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
:root {
  --space-cadet: #2b2d42;
  --cool-gray: #8d99ae;
  --antiflash-white: #edf2f4;
  --red-pantone: #ef233c;
  --fire-engine-red: #d90429;
}

.alert-overlay {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1100;
  pointer-events: none;
}

.alert-container {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  min-width: 300px;
  max-width: 400px;
  backdrop-filter: blur(20px);
  position: relative;
  pointer-events: all;
  animation: slideInRight 0.3s ease-out;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

@keyframes slideInRight {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

.alert-container.success {
  background: rgba(34, 197, 94, 0.95);
  border-left: 4px solid #16a34a;
}

.alert-container.error {
  background: rgba(239, 68, 68, 0.95);
  border-left: 4px solid #dc2626;
}

.alert-container.warning {
  background: rgba(245, 158, 11, 0.95);
  border-left: 4px solid #d97706;
}

.alert-icon {
  flex-shrink: 0;
  width: 24px;
  height: 24px;
  color: white;
  margin-top: 0.125rem;
}

.alert-content {
  flex: 1;
  color: white;
}

.alert-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.25;
}

.alert-content p {
  margin: 0;
  font-size: 0.875rem;
  opacity: 0.9;
  line-height: 1.375;
}

.alert-close {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.alert-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.alert-close svg {
  width: 16px;
  height: 16px;
}

@media (max-width: 768px) {
  .alert-overlay {
    top: 15px;
    right: 15px;
    left: 15px;
  }
  
  .alert-container {
    min-width: auto;
    max-width: none;
  }
}
</style>
