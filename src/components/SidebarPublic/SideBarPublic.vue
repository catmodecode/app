<template>
    <q-drawer
        v-model="visible"
        side="left"
        overlay
        bordered
    >
        <MenuPublic />
    </q-drawer>
</template>

<script>
import MenuPublic from "../MenuPublic/MenuPublic.vue";

export default {
    name: "SideBarPublic",
    components: {
        MenuPublic
    },
    computed: {
        visible: {
            get: function () {
                return this.$store.state.menu.visible
            },
            set: function (visible) {
                if (visible) {
                    this.$store.dispatch('menu/show')
                } else {
                    this.$store.dispatch('menu/hide')
                }
            }
        }
    }
};
</script>

<style scoped lang="scss">
.p-component-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>
