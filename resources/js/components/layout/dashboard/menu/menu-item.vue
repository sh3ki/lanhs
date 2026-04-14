<template>
    <li>
        <!-- External link (Laravel blade page or full reload) -->
        <a
            v-if="external"
            :href="to"
            :class="mobile ? 'router-link router-link-mobile' : 'router-link router-link-desktop'"
        >
            <svg-vue
                :icon="icon"
                class="mr-3 w-5 h-5 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
            ></svg-vue>
            <span>{{ label }}</span>
            <span v-if="showDot" class="relative ml-auto flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-600"></span>
            </span>
        </a>

        <!-- Vue Router link -->
        <router-link
            v-else
            :to="to"
            :class="mobile ? 'router-link router-link-mobile' : 'router-link router-link-desktop'"
        >
            <svg-vue
                :icon="icon"
                class="mr-3 w-5 h-5 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
            ></svg-vue>
            <span>{{ label }}</span>
            <span v-if="showDot" class="relative ml-auto flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-red-600"></span>
            </span>
        </router-link>
    </li>
</template>

<script>
export default {
    name: "menu-item",
    props: {
        mobile: {
            type: Boolean,
            default: false,
        },
        to: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        external: {
            type: Boolean,
            default: false,
        },
        showDot: {
            type: Boolean,
            default: false,
        }
    }
}
</script>
