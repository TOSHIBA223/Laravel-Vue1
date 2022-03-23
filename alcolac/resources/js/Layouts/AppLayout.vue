<template>
    <div class="min-h-screen bg-gray-100" style="padding-left: 195px;">
        <nav class="bg-white border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div></div>
                        <!--<button class="mr-2" aria-label="Open Menu" @click="drawer">
                            <svg
                                fill="none"
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                                class="w-8 h-8"
                            >
                                <path d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>-->
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center" style="margin-left:42px;">
                            <slot name="header"></slot>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <jet-dropdown align="right" width="48">
                                <template #trigger>
                                    <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                        My Account
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>


                                    <a href="/admin/profile" class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    Profile
                                </a>

                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Management -->
                                    <template v-if="$page.jetstream.hasTeamFeatures">
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Team
                                        </div>

                                        <!-- Team Settings -->
                                        <jet-dropdown-link :href="'/teams/' + $page.user.current_team.id">
                                            Team Settings
                                        </jet-dropdown-link>

                                        <jet-dropdown-link href="/teams/create" v-if="$page.jetstream.canCreateTeams">
                                            Create New Team
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Team Switcher -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Switch Teams
                                        </div>

                                        <template v-for="team in $page.user.all_teams">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <jet-dropdown-link as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </jet-dropdown-link>
                                            </form>
                                        </template>

                                        <div class="border-t border-gray-100"></div>
                                    </template>

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout">
                                        <jet-dropdown-link as="button">
                                            Logout
                                        </jet-dropdown-link>
                                    </form>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="mt-3 space-y-1">
                        <jet-responsive-nav-link href="/user/profile" :active="$page.currentRouteName == 'profile.show'">
                            Profile
                        </jet-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <jet-responsive-nav-link as="button">
                                Logout
                            </jet-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot></slot>

        </main>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>

        <sidebar :isOpen="isOpen"></sidebar>

     <div class="text-center w-100  p-3 footer-amc"> {{ settings['3'] ? settings['3'].value : '' }}</div>
    </div>
</template>

<script>
    import JetApplicationLogo from 'Jetstream/ApplicationLogo'
    import JetApplicationMark from 'Jetstream/ApplicationMark'
    import JetDropdown from 'Jetstream/Dropdown'
    import JetDropdownLink from 'Jetstream/DropdownLink'
    import JetResponsiveNavLink from 'Jetstream/ResponsiveNavLink'
    import Sidebar from 'Layout/Components/Sidebar'

    export default {
        components: {
            JetApplicationLogo,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetResponsiveNavLink,
            Sidebar
        },
        data() {
            return {
                showingNavigationDropdown: false,
                isOpen: true,
                settings:[],
                //menuItems:this.menuItems,
             }
        },


        methods: {
            drawer() {
                this.isOpen = !this.isOpen;
            },
            logout() {
                axios.post('/logout').then(response => {
                    window.location = '/admin';
                })
            },

            getSettings(){
                axios.get('/admin/api/declarations/getsetting').then(response => {
                     this.settings = response.data;
                })
            },
             /*getmenuitem(){
                axios.get('/admin/api/declarations/getmenu').then(response => {
                     this.menuItems = response.data;
                })
            }*/
        },

        computed: {
            path() {
                return window.location.pathname
            }
        },
        mounted() {
           this.getSettings();
           //this.getmenuitem();
        }
    }
</script>
