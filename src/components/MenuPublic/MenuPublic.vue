<template>
    <q-scroll-area class="fit">
        <q-list>
            <template
                v-for="(menuItem, index) in menuItems"
                :key="index"
            >
                <q-separator
                    :key="'sep' + index"
                    v-if="menuItem.separator"
                ></q-separator>
                <q-expansion-item
                    v-else-if="menuItem.items?.length"
                    :label="menuItem.label"
                    :icon="menuItem.icon"
                >
                    <template
                        v-for="(subMenuItem, subIndex) in menuItem.items"
                        :key="subIndex"
                    >
                        <q-separator
                            :key="'sep' + subIndex"
                            v-if="subMenuItem.separator"
                        ></q-separator>
                        <q-item
                            class="text-white text-bold"
                            clickable
                            v-ripple
                            v-else
                            :to="subMenuItem.to"
                            exact
                        >
                            <q-item-section avatar>
                                <q-icon :name="subMenuItem.icon" />
                            </q-item-section>
                            <q-item-section>
                                {{ subMenuItem.label }}
                            </q-item-section>
                            <q-item-section side>
                                <q-icon name="mdi-link" />
                            </q-item-section>
                        </q-item>
                    </template>
                </q-expansion-item>
                <q-item
                    class="text-white text-bold"
                    clickable
                    v-ripple
                    v-else
                    :to="menuItem.to"
                    exact
                >
                    <q-item-section avatar>
                        <q-icon :name="menuItem.icon" />
                    </q-item-section>
                    <q-item-section>
                        {{ menuItem.label }}
                    </q-item-section>
                    <q-item-section side>
                        <q-icon name="mdi-link" />
                    </q-item-section>
                </q-item>
            </template>
            <q-item>
                <q-item-section avatar>
                    <q-icon name="mdi-theme-light-dark" />
                </q-item-section>
                <q-item-section>
                    Dark Mode
                </q-item-section>
                <q-item-section side>
                    <q-toggle v-model="darkMode" />
                </q-item-section>
            </q-item>

        </q-list>
    </q-scroll-area>
</template>

<script>
import menuItems from "../../constants/menu";

export default {
    name: "MenuPublic",
    components: {
    },
    data: function () {
        return {
            menuItems
        };
    },
    computed: {
        darkMode: {
            set: function (darkMode) {
                if (darkMode) {
                    this.$store.dispatch('app/setDarkMode')
                } else {
                    this.$store.dispatch('app/setLightMode')
                }
            },
            get: function () {
                return this.$store.state.app.darkMode
            }
        }
    }
};
</script>

<style scoped lang="scss">
</style>
