export default {
    show(state) {
        state.visible = true;
    },
    hide(state) {
        state.visible = false;
    },
    toggle(state) {
        state.visible = !state.visible;
    }
};