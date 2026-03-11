<template>
    <main class="flex-1 relative overflow-y-auto py-6 focus:outline-none" tabindex="0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-5">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="py-0.5 text-2xl font-semibold text-gray-900">{{ $t('Priorities') }}</h1>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <loading :status="loading"/>
            <template v-if="priorityList.length > 0">
                <div v-for="priority in priorityList" :key="priority.id" class="my-4">
                    <!-- Priority section header (toggle) -->
                    <button
                        class="w-full flex items-center justify-between px-4 py-3 rounded-t-md shadow-sm text-left focus:outline-none transition duration-150 ease-in-out"
                        :class="[priorityHeaderBg(priority.name), expanded[priority.id] ? 'rounded-b-none' : 'rounded-b-md']"
                        @click="toggle(priority.id)"
                    >
                        <div class="flex items-center space-x-3">
                            <span :class="priorityColor(priority.name)" class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold leading-4">
                                {{ priority.value }}
                            </span>
                            <span class="text-base font-semibold text-gray-900">{{ priority.name }}</span>
                            <span class="text-sm text-gray-500">({{ ticketCounts[priority.id] || 0 }} {{ $t('tickets') }})</span>
                        </div>
                        <svg-vue
                            class="h-4 w-4 text-gray-500 transform transition-transform duration-200"
                            :class="{ 'rotate-90': expanded[priority.id] }"
                            icon="font-awesome.chevron-right-solid"
                        ></svg-vue>
                    </button>
                    <!-- Tickets list for this priority -->
                    <div v-show="expanded[priority.id]" class="bg-white shadow overflow-hidden rounded-b-md border border-t-0 border-gray-200">
                        <loading :status="priorityLoading[priority.id]"/>
                        <template v-if="ticketsByPriority[priority.id] && ticketsByPriority[priority.id].length > 0">
                            <!-- Mobile view -->
                            <div class="sm:hidden">
                                <ul class="divide-y divide-gray-200">
                                    <li v-for="ticket in ticketsByPriority[priority.id]" :key="ticket.id">
                                        <router-link
                                            :to="'/dashboard/tickets/' + ticket.uuid + '/manage'"
                                            class="flex items-center justify-between px-4 py-4 hover:bg-gray-100 sm:px-6"
                                        >
                                            <div class="flex items-center truncate space-x-3">
                                                <img
                                                    :alt="$t('Avatar')"
                                                    :src="ticket.user ? (ticket.user.avatar !== 'gravatar' ? ticket.user.avatar : ticket.user.gravatar) : ''"
                                                    class="h-8 w-8 mr-4 rounded-full"
                                                >
                                                <div class="whitespace-no-wrap">
                                                    <div class="text-sm leading-5 text-gray-900">
                                                        <span
                                                            v-for="label in ticket.labels"
                                                            :key="label.id"
                                                            :style="{backgroundColor: label.color}"
                                                            class="inline-flex items-center px-2 py-0.5 mr-1 rounded text-xs font-medium leading-4 text-gray-100"
                                                        >{{ label.name }}</span>
                                                        {{ ticket.subject }}
                                                    </div>
                                                    <div class="text-sm leading-5 text-gray-500 w-full truncate">
                                                        {{ ticket.lastReply ? ticket.lastReply.body : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <svg-vue class="ml-4 h-5 w-5 text-gray-400" icon="font-awesome.angle-right-regular"></svg-vue>
                                        </router-link>
                                    </li>
                                </ul>
                            </div>
                            <!-- Desktop view -->
                            <div class="hidden sm:block">
                                <div class="align-middle inline-block min-w-full">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="hidden lg:table-cell px-3 py-2 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                                    {{ $t('Customer') }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider" colspan="2">
                                                    {{ $t('Ticket summary') }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                                    {{ $t('Status') }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                                    {{ $t('Agent') }}
                                                </th>
                                                <th class="px-3 py-2 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                                                    {{ $t('Updated at') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-100">
                                            <router-link
                                                v-for="ticket in ticketsByPriority[priority.id]"
                                                :key="ticket.id"
                                                :to="'/dashboard/tickets/' + ticket.uuid + '/manage'"
                                                class="cursor-pointer hover:bg-gray-100"
                                                tag="tr"
                                            >
                                                <td class="hidden lg:table-cell px-3 py-4 whitespace-no-wrap leading-5">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img
                                                                :alt="$t('Avatar')"
                                                                :src="ticket.user ? (ticket.user.avatar !== 'gravatar' ? ticket.user.avatar : ticket.user.gravatar) : ''"
                                                                class="h-10 w-10 rounded-full"
                                                            >
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm leading-5 font-medium text-gray-900">
                                                                {{ ticket.user ? ticket.user.name : $t('Unassigned') }}
                                                            </div>
                                                            <div class="text-sm leading-5 text-gray-500">
                                                                {{ ticket.user ? ticket.user.email : '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 max-w-0 w-full whitespace-no-wrap">
                                                    <div class="flex text-sm leading-5 text-gray-900">
                                                        <span
                                                            v-for="label in ticket.labels"
                                                            :key="label.id"
                                                            :style="{backgroundColor: label.color}"
                                                            class="hidden lg:inline-flex items-center px-2 py-0.5 mr-1 rounded text-xs font-medium leading-4 text-gray-100"
                                                        >{{ label.name }}</span>
                                                        <div class="w-full truncate">{{ ticket.subject }}</div>
                                                    </div>
                                                    <div class="text-sm leading-5 text-gray-500 w-full truncate">
                                                        {{ ticket.lastReply ? ticket.lastReply.body : '' }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 whitespace-no-wrap leading-5">
                                                    <div class="text-sm leading-5 font-medium text-gray-900">
                                                        {{ ticket.status ? ticket.status.name : $t('Unassigned') }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 whitespace-no-wrap leading-5">
                                                    <div class="text-sm leading-5 text-gray-900">
                                                        {{ ticket.agent ? ticket.agent.name : $t('Unassigned') }}
                                                    </div>
                                                </td>
                                                <td class="px-3 py-4 whitespace-no-wrap leading-5">
                                                    <div class="text-sm text-gray-500">
                                                        {{ ticket.updated_at | momentFormatDateTimeAgo }}
                                                    </div>
                                                </td>
                                            </router-link>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- Pagination per priority -->
                            <nav v-if="ticketPagination[priority.id] && ticketPagination[priority.id].totalPages > 1" class="px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                                <div class="hidden sm:block">
                                    <p class="text-sm leading-5 text-gray-700">
                                        {{ $t('Showing') }}
                                        <span class="font-medium">{{ (ticketPagination[priority.id].perPage * ticketPagination[priority.id].currentPage) - ticketPagination[priority.id].perPage + 1 }}</span>
                                        {{ $t('to') }}
                                        <span class="font-medium">{{ ticketPagination[priority.id].perPage * ticketPagination[priority.id].currentPage <= ticketPagination[priority.id].total ? ticketPagination[priority.id].perPage * ticketPagination[priority.id].currentPage : ticketPagination[priority.id].total }}</span>
                                        {{ $t('of') }}
                                        <span class="font-medium">{{ ticketPagination[priority.id].total }}</span>
                                        {{ $t('results') }}
                                    </p>
                                </div>
                                <div class="flex-1 flex justify-between sm:justify-end">
                                    <button
                                        :disabled="ticketPages[priority.id] <= 1"
                                        :class="ticketPages[priority.id] <= 1 ? 'opacity-50 cursor-not-allowed' : ''"
                                        class="pagination-link"
                                        type="button"
                                        @click="changePage(priority.id, ticketPages[priority.id] - 1)"
                                    >{{ $t('Previous') }}</button>
                                    <button
                                        :disabled="ticketPages[priority.id] >= ticketPagination[priority.id].totalPages"
                                        :class="ticketPages[priority.id] >= ticketPagination[priority.id].totalPages ? 'opacity-50 cursor-not-allowed' : ''"
                                        class="ml-3 pagination-link"
                                        type="button"
                                        @click="changePage(priority.id, ticketPages[priority.id] + 1)"
                                    >{{ $t('Next') }}</button>
                                </div>
                            </nav>
                        </template>
                        <template v-else-if="!priorityLoading[priority.id]">
                            <div class="py-8 text-center text-sm text-gray-500">
                                {{ $t('No tickets with this priority') }}
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template v-else-if="!loading">
                <div class="h-full flex">
                    <div class="m-auto">
                        <div class="grid grid-cols-1 justify-items-center h-full w-full px-4 py-10">
                            <div class="flex justify-center items-center">
                                <svg-vue class="h-full h-auto w-64 mb-12" icon="undraw.browsing"></svg-vue>
                            </div>
                            <div class="flex justify-center items-center">
                                <div class="w-full font-semibold text-2xl">{{ $t('No records found') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>
</template>

<script>
export default {
    name: "list",
    metaInfo() {
        return {
            title: this.$i18n.t('Priorities')
        }
    },
    data() {
        return {
            loading: true,
            priorityList: [],
            expanded: {},
            ticketsByPriority: {},
            ticketCounts: {},
            ticketPages: {},
            ticketPagination: {},
            priorityLoading: {},
        }
    },
    filters: {
        momentFormatDateTimeAgo: function (value) {
            return moment(value).locale(window.app.app_date_locale).fromNow();
        },
    },
    mounted() {
        this.getPriorities();
    },
    methods: {
        priorityColor(name) {
            const map = {
                'Low': 'bg-green-200 text-green-800',
                'Medium': 'bg-yellow-200 text-yellow-800',
                'High': 'bg-orange-400 text-white',
                'Urgent': 'bg-red-500 text-white',
            };
            return map[name] || 'bg-gray-200 text-gray-800';
        },
        priorityHeaderBg(name) {
            const map = {
                'Low': 'bg-green-50 hover:bg-green-100',
                'Medium': 'bg-yellow-50 hover:bg-yellow-100',
                'High': 'bg-orange-50 hover:bg-orange-100',
                'Urgent': 'bg-red-50 hover:bg-red-100',
            };
            return map[name] || 'bg-gray-50 hover:bg-gray-100';
        },
        toggle(priorityId) {
            this.$set(this.expanded, priorityId, !this.expanded[priorityId]);
            if (this.expanded[priorityId] && !this.ticketsByPriority[priorityId]) {
                this.getTicketsForPriority(priorityId);
            }
        },
        changePage(priorityId, page) {
            const pagination = this.ticketPagination[priorityId];
            if (page > 0 && page <= pagination.totalPages && page !== this.ticketPages[priorityId]) {
                this.$set(this.ticketPages, priorityId, page);
                this.getTicketsForPriority(priorityId);
            }
        },
        getPriorities() {
            const self = this;
            self.loading = true;
            axios.get('api/dashboard/admin/priorities').then(function (response) {
                self.priorityList = response.data.slice().sort(function (a, b) { return b.value - a.value; });
                // Initialize state for each priority
                self.priorityList.forEach(function (p) {
                    self.$set(self.expanded, p.id, true);
                    self.$set(self.ticketsByPriority, p.id, null);
                    self.$set(self.ticketCounts, p.id, 0);
                    self.$set(self.ticketPages, p.id, 1);
                    self.$set(self.ticketPagination, p.id, { currentPage: 0, perPage: 0, total: 0, totalPages: 0 });
                    self.$set(self.priorityLoading, p.id, false);
                });
                self.loading = false;
                // Auto-load tickets for all priorities
                self.priorityList.forEach(function (p) {
                    self.getTicketsForPriority(p.id);
                });
            }).catch(function () {
                self.loading = false;
            });
        },
        getTicketsForPriority(priorityId) {
            const self = this;
            self.$set(self.priorityLoading, priorityId, true);
            axios.get('api/dashboard/tickets', {
                params: {
                    page: self.ticketPages[priorityId],
                    perPage: 10,
                    sort: { order: 'desc', column: 'updated_at' },
                    priorities: [priorityId],
                }
            }).then(function (response) {
                self.$set(self.ticketsByPriority, priorityId, response.data.items);
                self.$set(self.ticketPagination, priorityId, response.data.pagination);
                self.$set(self.ticketCounts, priorityId, response.data.pagination.total);
                self.$set(self.priorityLoading, priorityId, false);
            }).catch(function () {
                self.$set(self.priorityLoading, priorityId, false);
            });
        },
    }
}
</script>
