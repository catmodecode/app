<template>

        <MenuPublic />
</template>

<script>
import MenuPublic from "../MenuPublic/MenuPublic.vue";

export default {
    name: "SideBarPublic",
    components: {
        MenuPublic
    },
    data: function () {
        return {
            show: false
        };
    },
    methods: {
        toggleMenu: function() {
            this.show = !this.show
        }
    },
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
