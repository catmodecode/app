export default {
    setDarkMode({ commit }) {
        commit("setDarkMode");
    },
    setLightMode({ commit }) {
        commit("setLightMode");
    },
    toggleMode({ commit }) {
        commit("toggleMode");
    }
};