<template>
  <div class="request-item" :class="statusClass">
    <div class="request-details">
      <h4>{{ request.type }}</h4>
      <p>Status: {{ request.status }}</p>
    </div>
    <div class="request-actions">
      <button @click="validateRequest" :disabled="request.status === 'validated'">Valider</button>
      <button @click="deleteRequest">Supprimer</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'RequestItem',
  props: {
    request: {
      type: Object,
      required: true
    }
  },
  computed: {
    statusClass() {
      return {
        validated: this.request.status === 'validated',
        pending: this.request.status === 'pending',
        deleted: this.request.status === 'deleted'
      };
    }
  },
  methods: {
    validateRequest() {
      this.$emit('update-status', { id: this.request.id, status: 'validated' });
    },
    deleteRequest() {
      this.$emit('update-status', { id: this.request.id, status: 'deleted' });
    }
  }
};
</script>

<style scoped>
.request-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border: 1px solid var(--cool-gray);
  border-radius: 8px;
  margin-bottom: 1rem;
  background-color: var(--antiflash-white);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.request-item.validated {
  border-color: var(--space-cadet);
  background-color: #d4edda;
}

.request-item.pending {
  border-color: var(--red-pantone);
  background-color: #fff3cd;
}

.request-item.deleted {
  border-color: var(--fire-engine-red);
  background-color: #f8d7da;
}

.request-details h4 {
  margin: 0;
  font-size: 1.2rem;
  color: var(--space-cadet);
}

.request-details p {
  margin: 0.5rem 0 0;
  font-size: 0.9rem;
  color: var(--cool-gray);
}

.request-actions button {
  margin-left: 0.5rem;
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
}

.request-actions button:first-child {
  background-color: var(--space-cadet);
  color: white;
}

.request-actions button:last-child {
  background-color: var(--fire-engine-red);
  color: white;
}

.request-actions button:disabled {
  background-color: var(--cool-gray);
  cursor: not-allowed;
}
</style>
