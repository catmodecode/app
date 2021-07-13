import store from '@vue/cli-plugin-vuex'

export default (to, from, next) => {
    store.state.auth
    if (to.name !== 'Login' && !isAuthenticated) next({ name: 'Login' })
    else next()
  }