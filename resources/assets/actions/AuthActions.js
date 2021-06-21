import store from '@vue/cli-plugin-vuex';

export default {
    getAuthStatus() {
        return store?.auth === null;
    }
}