export default {
  setUser({ commit }, user) {
    if (!user.verify()) {
      throw new Error('User verify error');
    }
    commit('setUser', user)
  }
}