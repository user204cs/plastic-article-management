<template>
  <div class="action-cards">
    <div class="card archive">
      <div class="overlay"></div>
      <div class="circle">
        <img src="/assets/balls.PNG?url" alt="Archive" class="circle-img" />
      </div>

      <button @click="$emit('archive-click')">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
        <path d="M20.54 5.23l-1.39-1.68A2 2 0 0 0 17.13 3H6.87a2 2 0 0 0-2.02 1.55l-1.39 1.68A2 2 0 0 0 3 7v2a2 2 0 0 0 2 2v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-7a2 2 0 0 0 2-2V7a2 2 0 0 0-.46-1.77zM6.87 5h10.26l1.39 1.68c.09.11.13.25.13.32V7H5V7c0-.07.04-.21.13-.32L6.87 5zM19 18a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-7h14v7zm2-9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2zm-7 6v2h-2v-2H8l4-4 4 4h-3z"/>
      </svg>
      Archiver
    </button>
    </div>

    <div class="card duplicate">
      <div class="overlay"></div>
      <div class="circle">
        <img src="/assets/zellij.PNG?url" alt="Duplicate" class="circle-img" />
      </div>
      
      <button @click="$emit('duplicate-click')">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
          <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
        </svg>
        Dupliquer
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ActionCards',
  emits: ['archive-click', 'duplicate-click']
}
</script>

<style scoped>
/* Archive card colors */
.archive {
  --bg-color: var(--red-pantone);
  --bg-color-light: #f5a3b3;
  --text-color-hover: var(--antiflash-white);
  --box-shadow-color: rgba(239, 35, 60, 0.48);
}

/* Duplicate card colors */
.duplicate {
  --bg-color: var(--space-cadet);
  --bg-color-light: #9da5c1;
  --text-color-hover: var(--antiflash-white);
  --box-shadow-color: rgba(43, 45, 66, 0.48);
}

.action-cards {
  display: flex;
  gap: 5rem;
  justify-content: center;
  flex-wrap: wrap;
  padding: 2rem;
}

.card {
  width: 220px;
  height: 321px;
  background: var(--antiflash-white);
  border-top-right-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  position: relative;
  box-shadow: 0 14px 26px rgba(0, 0, 0, 0.04);
  transition: all 0.3s ease-out;
  text-decoration: none;
}

.card:hover {
  transform: translateY(-5px) scale(1.005) translateZ(0);
  box-shadow: 0 24px 36px rgba(0, 0, 0, 0.11),
    0 24px 46px var(--box-shadow-color);
}

.card:hover .overlay {
  transform: scale(4) translateZ(0);
}

.card:hover .circle {
  border-color: var(--bg-color-light);
  background: var(--bg-color);
}

.card:hover .circle:after {
  background: var(--bg-color-light);
}

.card:hover p {
  color: var(--text-color-hover);
}

.card:hover button {
  background: white;
  color: var(--bg-color);
}

.card p {
  font-size: 17px;
  color: var(--space-cadet);
  margin-top: 30px;
  z-index: 1000;
  transition: color 0.3s ease-out;
  font-weight: 600;
}

.circle {
  width: 131px;
  height: 131px;
  border-radius: 50%;
  background: var(--antiflash-white);
  border: 2px solid var(--bg-color);
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  z-index: 1;
  transition: all 0.3s ease-out;
}

.circle:after {
  content: "";
  width: 118px;
  height: 118px;
  display: block;
  position: absolute;
  background: var(--bg-color);
  border-radius: 50%;
  top: 7px;
  left: 7px;
  transition: opacity 0.3s ease-out;
}

.circle svg {
  z-index: 10000;
  transform: translateZ(0);
  width: 40px;
  height: 40px;
  color: var(--antiflash-white);
}

.circle-img {
  z-index: 10000;
  transform: translateZ(0);
  width: 90px;
  height: 90px;
  object-fit: cover;
  border-radius: 50%;
}

.overlay {
  width: 118px;
  position: absolute;
  height: 118px;
  border-radius: 50%;
  background: var(--bg-color);
  top: 70px;
  left: 50px;
  z-index: 0;
  transition: transform 0.3s ease-out;
  pointer-events: none; /* Permet aux clics de passer à travers */
}

button {
  background: var(--bg-color);
  color: var(--antiflash-white);
  border: none;
  padding: 0.8rem 1.5rem;
  border-radius: 25px;
  font-weight: 600;
  cursor: pointer;
  margin-top: 1rem;
  transition: all 0.3s ease;
  z-index: 10000;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: auto; /* S'assurer que le bouton peut être cliqué */
}

button svg {
  flex-shrink: 0;
}

button:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px var(--box-shadow-color);
  color: var(--antiflash-white);
  background: var(--bg-color);
}

@media (max-width: 768px) {
  .action-cards {
    flex-direction: column;
    align-items: center;
  }
}
</style>
