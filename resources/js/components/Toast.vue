<template>
    <div class="toast-container">
        <TransitionGroup name="toast">
            <div 
                v-for="toast in toasts" 
                :key="toast.id"
                class="toast"
                :class="toast.type"
            >
                <div class="toast-content-wrapper">
                    <div class="toast-icon">
                        <svg v-if="toast.type === 'success'" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                        </svg>
                        <svg v-else-if="toast.type === 'error'" viewBox="0 0 24 24" width="24" height="24">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                        </svg>
                        <svg v-else viewBox="0 0 24 24" width="24" height="24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                        </svg>
                    </div>
                    <div class="toast-content">
                        <h4>{{ toast.title }}</h4>
                        <p>{{ toast.message }}</p>
                    </div>
                </div>
                <button class="toast-close" @click="removeToast(toast.id)">×</button>
                <div class="toast-progress" :style="{ animationDuration: '5s' }"></div>
            </div>
        </TransitionGroup>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const toasts = ref([])
let toastId = 0

const addToast = (title, message, type = 'info') => {
    const id = toastId++
    toasts.value.push({ id, title, message, type })
    setTimeout(() => removeToast(id), 5000)
}

const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
        toasts.value.splice(index, 1)
    }
}

defineExpose({ addToast })
</script>

<style scoped>
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 12px;
    pointer-events: none;
}

.toast {
    position: relative;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    min-width: 300px;
    max-width: 400px;
    padding: 16px;
    border-radius: 12px;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15),
                0 8px 24px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    pointer-events: auto;
    animation: toastSlideIn 0.3s cubic-bezier(0.21, 1.02, 0.73, 1);
}

.toast-content-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.toast-icon {
    width: 24px;
    height: 24px;
    flex-shrink: 0;
    border-radius: 50%;
    padding: 2px;
}

.toast.success {
    background: linear-gradient(145deg, #43A047, #4CAF50);
    color: white;
}

.toast.error {
    background: linear-gradient(145deg, #E53935, #f44336);
    color: white;
}

.toast.info {
    background: linear-gradient(145deg, #1E88E5, #2196F3);
    color: white;
}

.toast-icon svg {
    width: 100%;
    height: 100%;
    fill: currentColor;
}

.toast-content {
    flex-grow: 1;
}

.toast-content h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.toast-content p {
    margin: 4px 0 0;
    font-size: 0.9rem;
    opacity: 0.9;
    line-height: 1.4;
}

.toast-close {
    background: none;
    border: none;
    color: inherit;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
    padding: 0 0 0 12px;
    margin: -4px -4px -4px 0;
}

.toast-close:hover {
    opacity: 1;
}

.toast-progress {
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.3);
    transform-origin: left;
    animation: progress linear forwards;
}

@keyframes progress {
    from {
        transform: scaleX(1);
    }
    to {
        transform: scaleX(0);
    }
}

@keyframes toastSlideIn {
    from {
        transform: translateX(calc(100% + 20px));
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast-enter-active {
    animation: toastSlideIn 0.3s cubic-bezier(0.21, 1.02, 0.73, 1);
}

.toast-leave-active {
    animation: toastSlideIn 0.3s cubic-bezier(0.21, 1.02, 0.73, 1) reverse;
}

@media (max-width: 480px) {
    .toast-container {
        top: auto;
        bottom: 20px;
        left: 20px;
        right: 20px;
    }

    .toast {
        min-width: 0;
        width: 100%;
    }
}
</style> 