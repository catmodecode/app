export default {
  show({ commit }) {
    commit('show')
  },
  hide({ commit }) {
    commit("hide");
  },
  toggle({ commit }) {
    commit("toggle");
  }
}