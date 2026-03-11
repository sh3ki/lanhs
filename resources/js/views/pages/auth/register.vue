<template>
    <div>
        <!-- Role Badge Display -->
        <div v-if="roleName" class="mb-6 p-4 rounded-lg border-2" :class="roleBadgeClass">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg v-if="user.role_id === 2" class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <svg v-else-if="user.role_id === 3" class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $t('Registering as') }}</p>
                        <p class="text-lg font-bold" :class="roleTextClass">{{ roleName }}</p>
                    </div>
                </div>
                <router-link
                    to="/auth/login"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                >
                    {{ $t('Change') }}
                </router-link>
            </div>
        </div>

        <!-- Enhanced Registration Form -->
        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold leading-5 text-gray-800 mb-2" for="name">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ $t('Full Name') }}
                    </span>
                </label>
                <input
                    id="name"
                    v-model="user.name"
                    :placeholder="$t('Enter your full name')"
                    class="form-input block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-semibold leading-5 text-gray-800 mb-2" for="email">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        {{ $t('Email Address') }}
                    </span>
                </label>
                <input
                    id="email"
                    v-model="user.email"
                    :placeholder="$t('Enter your email')"
                    class="form-input block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    required
                    type="email"
                />
            </div>

            <div>
                <label class="block text-sm font-semibold leading-5 text-gray-800 mb-2" for="password">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ $t('Password') }}
                    </span>
                </label>
                <input
                    id="password"
                    v-model="user.password"
                    class="form-input block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    :placeholder="$t('Minimum 6 characters')"
                    required
                    type="password"
                    minlength="6"
                />
                <p class="mt-1 text-xs text-gray-500">{{ $t('Must be at least 6 characters') }}</p>
            </div>

            <div>
                <label class="block text-sm font-semibold leading-5 text-gray-800 mb-2" for="password_confirmation">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $t('Confirm Password') }}
                    </span>
                </label>
                <input
                    id="password_confirmation"
                    v-model="user.password_confirmation"
                    class="form-input block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    :placeholder="$t('Re-enter your password')"
                    required
                    type="password"
                />
            </div>

            <div class="pt-2">
                <button
                    id="submit-register"
                    class="w-full flex justify-center items-center bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-8 rounded-lg focus:outline-none focus:ring-4 focus:ring-green-300 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02]"
                    data-style="zoom-in"
                    type="submit"
                >
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    {{ $t('Create Account') }}
                </button>
            </div>

            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">{{ $t('Or') }}</span>
                </div>
            </div>

            <div class="text-center">
                <p class="text-gray-700 text-sm">
                    {{ $t('Already have an account?') }}
                    <router-link class="font-semibold text-blue-600 hover:text-blue-800 transition-colors duration-200" to="/auth/login">
                        {{ $t('Sign In') }}
                    </router-link>
                </p>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: "register",
    metaInfo() {
        return {
            title: this.$i18n.t('Register')
        }
    },
    data() {
        return {
            user: {
                name: null,
                email: null,
                password: null,
                password_confirmation: null,
                captcha: null,
                role_id: null
            }
        }
    },
    computed: {
        roleName() {
            if (this.user.role_id === 2) {
                return this.$i18n.t('Department');
            } else if (this.user.role_id === 3) {
                return this.$i18n.t('Technician');
            }
            return null;
        },
        roleBadgeClass() {
            if (this.user.role_id === 2) {
                return 'border-blue-300 bg-blue-50';
            } else if (this.user.role_id === 3) {
                return 'border-green-300 bg-green-50';
            }
            return 'border-gray-300 bg-gray-50';
        },
        roleTextClass() {
            if (this.user.role_id === 2) {
                return 'text-blue-700';
            } else if (this.user.role_id === 3) {
                return 'text-green-700';
            }
            return 'text-gray-700';
        }
    },
    mounted() {
        // Get role from query parameter if provided
        if (this.$route.query.role) {
            this.user.role_id = parseInt(this.$route.query.role);
        }
    },
    methods: {
        submit() {
            const self = this;
            if (self.$store.state.settings.recaptcha_enabled) {
                self.$recaptcha('register').then(function (token) {
                    self.user.captcha = token;
                    self.register();
                });
            } else {
                self.register();
            }
        },
        register() {
            const self = this;
            const ladda = Ladda.create(document.querySelector('#submit-register'));
            ladda.start();
            axios.post('api/auth/register', this.user).then(function (response) {
                self.$notify({
                    title: self.$i18n.t('Success').toString(),
                    text: self.$i18n.t('Account created successfully').toString(),
                    type: 'success'
                })
                self.$store.commit('login', response.data);
                if (response.data.user.role.dashboard_access) {
                    self.$router.push('/dashboard/home');
                } else {
                    self.$router.push('/tickets/list');
                }
            }).catch(function () {
                self.user.password = null;
                self.user.password_confirmation = null;
            });
        }
    }
}
</script>

                self.user.password_confirmation = null;
            });
        }
    }
}
</script>
