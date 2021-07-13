import { createStore } from 'vuex'
import user from './User';
import menu from './Menu'
import app from './App'

export default createStore({
    modules: {
        user,
        menu,
        app,
    }
});
