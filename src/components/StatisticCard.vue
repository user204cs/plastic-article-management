<template>
  <div class="card">
    <div class="img-container">
      <div class="img" :style="{ background: cardGradient }">
        <div class="icon-wrapper" :style="{ background: iconColor }">
          <!-- Check Icon for Validated -->
          <svg v-if="title.includes('Validées')" class="card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <!-- X Icon for Rejected -->
          <svg v-else-if="title.includes('Non Validées')" class="card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <!-- Clock Icon for Pending -->
          <svg v-else-if="title.includes('En Attente')" class="card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
            <polyline points="12,6 12,12 16,14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <!-- Default Icon -->
          <svg v-else class="card-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
      </div>
      <div class="description card">
        <span class="title">{{ title }}</span>
        <div class="count">{{ count }}</div>
        <div class="subtitle">{{ subtitle }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StatisticCard',
  props: {
    title: {
      type: String,
      required: true
    },
    count: {
      type: Number,
      required: true
    }
  },
  mounted() {
    // Set the zellij background image for this card
    this.$nextTick(() => {
      const cardElement = this.$el;
      if (cardElement) {
        // Set CSS custom property for zellij background
        cardElement.style.setProperty('--zellij-bg', 'url("./assets/zelij.webp"), url("./assets/zellij.PNG")');
      }
    });
  },
  computed: {
    cardGradient() {
      if (this.title.includes('Validées')) {
        return 'linear-gradient(180deg, var(--antiflash-white) 0%, #f8f9fa 100%)';
      }
      if (this.title.includes('Non Validées')) {
        return 'linear-gradient(180deg, var(--antiflash-white) 0%, #f8f9fa 100%)';
      }
      if (this.title.includes('En Attente')) {
        return 'linear-gradient(180deg, var(--antiflash-white) 0%, #f8f9fa 100%)';
      }
      return 'linear-gradient(180deg, var(--antiflash-white) 0%, #f8f9fa 100%)';
    },
    iconColor() {
      if (this.title.includes('Non Validées')) {
        return 'var(--red-pantone)';
      }
      if (this.title.includes('Validées')) {
        return 'var(--space-cadet)';
      }
      if (this.title.includes('En Attente')) {
        return 'var(--space-cadet)';
      }
      return 'var(--space-cadet)';
    },
    subtitle() {
      if (this.title.includes('Non Validées')) return 'Demandes rejetées';
      if (this.title.includes('Validées')) return 'Demandes approuvées';
      if (this.title.includes('En Attente')) return 'En cours de traitement';
      return '';
    }
  }
};
</script>

<style scoped>
:root {
  --space-cadet: #2b2d42;
  --cool-gray: #8d99ae;
  --antiflash-white: #edf2f4;
  --red-pantone: #ef233c;
  --fire-engine-red: #d90429;
}

.card {
  width: 280px;
  height: min-content;
  transition: all 0.2s ease;
  position: relative;
  border-radius: 12px;
  background: var(--antiflash-white);
  box-shadow: 
    0 2px 8px rgba(43, 45, 66, 0.08),
    0 1px 3px rgba(43, 45, 66, 0.06);
  cursor: pointer;
  border: 1px solid rgba(141, 153, 174, 0.15);
  border-left: 4px solid var(--red-pantone);
  overflow: hidden;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 80px;
  height: 80px;
  background-image: var(--zellij-bg, none);
  background-size: 40px 40px;
  background-repeat: repeat;
  background-position: 0 0;
  opacity: 0.08;
  z-index: 1;
}

/* Fallback pattern if zellij images don't load */
.card::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 80px;
  height: 80px;
  background-image: 
    /* Mini étoile à 8 branches */
    conic-gradient(from 0deg at 20px 20px, 
      var(--red-pantone) 0deg, var(--red-pantone) 20deg, transparent 20deg, transparent 45deg,
      var(--space-cadet) 45deg, var(--space-cadet) 65deg, transparent 65deg, transparent 90deg,
      var(--red-pantone) 90deg, var(--red-pantone) 110deg, transparent 110deg, transparent 135deg,
      var(--space-cadet) 135deg, var(--space-cadet) 155deg, transparent 155deg, transparent 180deg,
      var(--red-pantone) 180deg, var(--red-pantone) 200deg, transparent 200deg, transparent 225deg,
      var(--space-cadet) 225deg, var(--space-cadet) 245deg, transparent 245deg, transparent 270deg,
      var(--red-pantone) 270deg, var(--red-pantone) 290deg, transparent 290deg, transparent 315deg,
      var(--space-cadet) 315deg, var(--space-cadet) 335deg, transparent 335deg, transparent 360deg
    ),
    
    /* Losange de connexion */
    linear-gradient(45deg, transparent 40%, var(--cool-gray) 40%, var(--cool-gray) 45%, transparent 45%),
    linear-gradient(-45deg, transparent 40%, rgba(239, 35, 60, 0.3) 40%, rgba(239, 35, 60, 0.3) 45%, transparent 45%),
    
    /* Point central */
    radial-gradient(circle at 20px 20px, var(--space-cadet) 1px, transparent 1px);
    
  background-size: 40px 40px, 20px 20px, 20px 20px, 40px 40px;
  background-position: 0 0, 0 0, 0 0, 0 0;
  opacity: 0.06;
  z-index: 1;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 
    0 4px 16px rgba(43, 45, 66, 0.12),
    0 2px 6px rgba(43, 45, 66, 0.08),
    0 0 0 1px rgba(239, 35, 60, 0.1);
  border-left-color: var(--fire-engine-red);
}

.img {
  transition: all 0.2s ease;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  padding: 24px 0;
  z-index: 2;
}

.img:hover {
  transform: none;
}

.img-container {
  display: grid;
  border-radius: 12px;
  height: 160px;
  overflow: hidden;
  position: relative;
}

.icon-wrapper {
  width: 56px;
  height: 56px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
  transition: all 0.2s ease;
  position: relative;
  z-index: 3;
}

.card-icon {
  width: 28px;
  height: 28px;
  color: var(--antiflash-white);
}

.description {
  position: absolute;
  bottom: 0;
  left: 0;
  text-align: start;
  padding: 20px 24px;
  width: 100%;
  transition: all 0.2s ease;
  background: var(--antiflash-white);
  border-top: 1px solid rgba(141, 153, 174, 0.12);
  z-index: 2;
}

.description:hover {
  transform: none;
  background: var(--antiflash-white);
}

.title {
  color: var(--space-cadet);
  font-size: 14px;
  font-weight: 500;
  display: block;
  margin-bottom: 8px;
  letter-spacing: -0.01em;
  line-height: 1.3;
  text-transform: uppercase;
}

.count {
  color: var(--space-cadet);
  font-size: 32px;
  font-weight: 700;
  display: block;
  margin-bottom: 4px;
  line-height: 1;
  position: relative;
}

.count::after {
  content: '';
  position: absolute;
  bottom: -4px;
  left: 0;
  width: 24px;
  height: 2px;
  background: var(--red-pantone);
  border-radius: 1px;
}

.subtitle {
  color: var(--cool-gray);
  font-size: 12px;
  font-weight: 400;
  opacity: 0.8;
  line-height: 1.4;
  letter-spacing: 0.01em;
}

/* Responsive design */
@media (max-width: 768px) {
  .card {
    width: 260px;
  }
  
  .img-container {
    height: 140px;
  }
  
  .icon-wrapper {
    width: 48px;
    height: 48px;
  }
  
  .card-icon {
    width: 24px;
    height: 24px;
  }
  
  .title {
    font-size: 13px;
  }
  
  .count {
    font-size: 28px;
  }
  
  .description {
    padding: 16px 20px;
  }
}
</style>