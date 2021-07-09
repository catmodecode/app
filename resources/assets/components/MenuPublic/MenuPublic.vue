<template>

</template>

<script>
import menuItems from "../../constants/menu";

export default {
    name: "MenuPublic",
    components: {
    },
    data: function () {
        return {
            menuItems: menuItems,
            expandedKeys: {}
        };
    },
};
</script>

<style scoped lang="scss">
</style>
