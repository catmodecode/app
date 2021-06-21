import { createStore } from 'vuex'
import user from './User/store';

export default createStore({
  modules: {
    user
  }
})
