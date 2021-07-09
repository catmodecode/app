<template>
    <v-app>
        <router-view />
    </v-app>
</template>

<style lang="scss">
</style>

<script>
import authActions from "./actions/AuthActions";

export default {
    name: "App",
    computed: {
        isAuth: function () {
            return authActions.getAuthStatus();
        },
    },
};
</script>
