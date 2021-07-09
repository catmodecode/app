import { createStore } from 'vuex'
import user from './User/store';
import menu from './Menu/store'

export default createStore({
    modules: {
        user,
        menu
    }
});
