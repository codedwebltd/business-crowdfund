import { reactive } from 'vue';

const state = reactive({
    toasts: []
});

let nextId = 0;

export function useToast() {
    const show = (message, type = 'info', duration = 4000) => {
        const id = nextId++;
        const toast = { id, message, type, duration, visible: true };

        state.toasts.push(toast);

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }

        return id;
    };

    const remove = (id) => {
        const index = state.toasts.findIndex(t => t.id === id);
        if (index > -1) {
            state.toasts.splice(index, 1);
        }
    };

    const success = (message, duration) => show(message, 'success', duration);
    const error = (message, duration) => show(message, 'error', duration);
    const warning = (message, duration) => show(message, 'warning', duration);
    const info = (message, duration) => show(message, 'info', duration);

    return {
        toasts: state.toasts,
        show,
        remove,
        success,
        error,
        warning,
        info
    };
}
