<template>
    <div class="relative z-10 flex-shrink-0 flex h-16 bg-white shadow">
        <button aria-label="Open sidebar" class="px-4 text-gray-500 focus:outline-none focus:bg-gray-100 focus:text-gray-600 md:hidden" @click="$emit('toggleSidebar')">
            <svg-vue class="h-6 w-6" icon="font-awesome.bars-regular"></svg-vue>
        </button>
        <div class="w-full px-4 flex justify-end">
            <div class="flex">
                <div class="ml-4 flex-1 flex items-center md:ml-6">
                    <div v-on-clickaway="closeDropdown" class="ml-3 relative">
                        <!-- Avatar button with pulsing notification dot -->
                        <button
                            id="user-menu"
                            aria-haspopup="true"
                            aria-label="User menu"
                            class="max-w-xs flex items-center text-sm rounded-full focus:outline-none focus:shadow-outline relative"
                            @click="toggleDropdown"
                        >
                            <img
                                :src="$store.state.user.avatar === 'gravatar' ? $store.state.user.gravatar : $store.state.user.avatar"
                                alt="User avatar"
                                class="h-8 w-8 rounded-full"
                            />
                            <!-- Pulsing red dot on avatar -->
                            <span v-if="notificationCount > 0" class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 flex h-3.5 w-3.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-600"></span>
                            </span>
                        </button>
                        <transition
                            duration="100"
                            enter-active-class="transition ease-out duration-100"
                            enter-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div v-show="dropdownOpen" class="origin-top-right z-10 absolute right-0 mt-2 w-56 rounded-md shadow-lg">
                                <div aria-labelledby="user-menu" aria-orientation="vertical" class="py-1 rounded-md bg-white shadow-xs" role="menu">
                                    <div class="flex items-center px-4 ce py-2 border-b border-gray-100">
                                        <img
                                            :src="$store.state.user.avatar === 'gravatar' ? $store.state.user.gravatar : $store.state.user.avatar"
                                            alt="User avatar"
                                            class="h-8 w-8 mr-3 align-middle rounded-full"
                                        />
                                        <div class="w-40">
                                            <div class="text-sm font-medium truncate text-gray-800">{{ $store.state.user.name }}</div>
                                            <div class="text-xs truncate text-gray-500">{{ $store.state.user.email }}</div>
                                        </div>
                                    </div>
                                    <router-link
                                        v-if="$store.state.user ? $store.state.user.role.dashboard_access : false"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150"
                                        role="menuitem"
                                        to="/dashboard/home"
                                        @click.native="dropdownOpen = false"
                                    >
                                        {{ $t('Dashboard') }}
                                    </router-link>
                                    <!-- My tickets with pulsing red dot -->
                                    <router-link
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150"
                                        role="menuitem"
                                        to="/tickets/list"
                                        @click.native="onMyTicketsClick"
                                    >
                                        <span class="flex-1">{{ $t('My tickets') }}</span>
                                        <span v-if="notificationCount > 0" class="flex h-2.5 w-2.5 ml-2">
                                            <span class="animate-ping absolute inline-flex h-2.5 w-2.5 rounded-full bg-red-500 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
                                        </span>
                                    </router-link>
                                    <router-link
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150"
                                        role="menuitem"
                                        to="/account"
                                        @click.native="dropdownOpen = false"
                                    >
                                        {{ $t('Account settings') }}
                                    </router-link>
                                    <a
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition ease-in-out duration-150"
                                        href="/auth/logout"
                                        role="menuitem"
                                        @click.prevent="signOut"
                                    >
                                        {{ $t('Sign out') }}
                                    </a>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {mixin as clickaway} from "vue-clickaway";

export default {
    name: "navbar",
    mixins: [clickaway],
    data() {
        return {
            dropdownOpen: false,
            notificationCount: 0,
            notificationInterval: null,
        }
    },
    mounted() {
        this.fetchNotificationCount();
        // Poll every 2 seconds for real-time updates
        this.notificationInterval = setInterval(() => {
            this.fetchNotificationCount();
        }, 2000);
    },
    beforeDestroy() {
        if (this.notificationInterval) {
            clearInterval(this.notificationInterval);
        }
    },
    methods: {
        fetchNotificationCount() {
            axios.get('api/dashboard/notifications/count').then((response) => {
                this.notificationCount = response.data.count || 0;
                // Share unread ticket UUIDs via Vuex so list.vue can show per-ticket dots
                this.$store.commit('setUnreadTicketUuids', response.data.ticket_uuids || []);
            }).catch(() => {
                // Silently fail — don't break the navbar
            });
        },
        markNotificationsRead() {
            if (this.notificationCount > 0) {
                axios.post('api/dashboard/notifications/mark-read').then(() => {
                    this.notificationCount = 0;
                }).catch(() => {});
            }
        },
        toggleDropdown() {
            this.dropdownOpen = !this.dropdownOpen;
        },
        onMyTicketsClick() {
            this.dropdownOpen = false;
        },
        signOut() {
            this.dropdownOpen = false;
            this.$store.commit('logout');
            this.$router.push('/auth/login');
        },
        closeDropdown() {
            this.dropdownOpen = false;
        }
    }
}
</script>
