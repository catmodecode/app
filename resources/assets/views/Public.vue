<template>
    <div class="home">
        <SideBarPublic></SideBarPublic>
        <div>
            <h1>Public</h1>
            <router-view>no</router-view>
        </div>
    </div>
</template>

<script>
import SideBarPublic from "../components/SidebarPublic/SideBarPublic.vue"

export default {
    name: "Public",
    components: {
        SideBarPublic
    },
};
</script>
