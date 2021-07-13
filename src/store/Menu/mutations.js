export default {
    show(state) {
        state.visible = true;
    },
    hide(state) {
        state.visible = false;
    },
    toggle (state) {
        console.log("toggled");
        state.visible = !state.visible;
    }
};