<template>
    <q-layout view="hHh lpR lFf">
        <router-view />
    </q-layout>
</template>

<style lang="scss">
</style>

<script>
import authActions from "./actions/AuthActions";
import { useQuasar } from 'quasar'

export default {
    name: "App",
    data: function () {
        return {
            quasar: useQuasar()
        }
    },
    computed: {
        isAuth: function () {
            return authActions.getAuthStatus();
        },
        darkMode: function () {
            return this.$store.state.app.darkMode
        }
    },
    watch: {
        darkMode: function (darkMode) {
            this.quasar.dark.set(darkMode)
        }
    },
    created: function () {
        this.quasar.dark.set(this.darkMode)
    }
};
</script>
