<template>
    <Transition name="fade">
        <div v-if="show" class="alert-overlay">
            <div class="alert-modal" :class="type">
                <div class="alert-icon">
                    <svg v-if="type === 'success'" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                    <svg v-else-if="type === 'error'" viewBox="0 0 24 24" width="24" height="24">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                    </svg>
                    <svg v-else viewBox="0 0 24 24" width="24" height="24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                </div>
                <div class="alert-content">
                    <h3>{{ title }}</h3>
                    <p>{{ message }}</p>
                </div>
                <div class="alert-actions">
                    <button @click="confirm" class="confirm-btn">{{ confirmText }}</button>
                    <button v-if="showCancel" @click="cancel" class="cancel-btn">{{ cancelText }}</button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
    title: String,
    message: String,
    type: {
        type: String,
        default: 'info'
    },
    confirmText: {
        type: String,
        default: 'OK'
    },
    cancelText: {
        type: String,
        default: 'Cancel'
    },
    showCancel: {
        type: Boolean,
        default: false
    }
})

const show = ref(false)
const emit = defineEmits(['confirm', 'cancel'])

const confirm = () => {
    show.value = false
    emit('confirm')
}

const cancel = () => {
    show.value = false
    emit('cancel')
}

defineExpose({ show })
</script>

<style scoped>
.alert-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.alert-modal {
    background: white;
    border-radius: 12px;
    padding: 24px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.alert-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.alert-icon svg {
    fill: white;
    width: 28px;
    height: 28px;
}

.success .alert-icon {
    background: #4CAF50;
}

.error .alert-icon {
    background: #f44336;
}

.info .alert-icon {
    background: #2196F3;
}

.alert-content {
    text-align: center;
    margin-bottom: 24px;
}

.alert-content h3 {
    color: #333;
    font-size: 1.25rem;
    margin-bottom: 8px;
}

.alert-content p {
    color: #666;
    font-size: 1rem;
    margin: 0;
}

.alert-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.alert-actions button {
    padding: 8px 24px;
    border: none;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.confirm-btn {
    background: #4CAF50;
    color: white;
}

.confirm-btn:hover {
    background: #43A047;
}

.cancel-btn {
    background: #f5f5f5;
    color: #333;
}

.cancel-btn:hover {
    background: #e0e0e0;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style> 