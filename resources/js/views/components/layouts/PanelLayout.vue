<template>
    <Header/>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <div>
                <router-link class="navbar-brand" to="/home">Trading Portfolio Tracker</router-link>
            </div>
            <div class="order-1 order-lg-0" id="sidebarToggleContainer">
                <button class="btn btn-link btn-sm" id="sidebarToggle" v-on:click="toggleSidebar"><font-awesome-icon icon="fa-solid fa-bars" /></button>
            </div>
            <ul class="navbar-nav ml-auto ml-md-30">
                <li :class="userDropdownDisplayed ? 'nav-item dropdown show' : 'nav-item dropdown'">
                    <button class="btn dropdown-toggle" id="userToggle" v-on:click="toggleUserDropDown"><font-awesome-icon icon="fa-solid fa-user" /></button>
                    <div :class="userDropdownDisplayed ? 'dropdown-menu dropdown-menu-right show' : 'dropdown-menu dropdown-menu-right'" aria-labelledby="userDropdown">
                        <router-link class="dropdown-item" to="/settings">Settings</router-link>
                        <div class="dropdown-divider"></div>
                        <div id="logoutButton" class="submit">
                            <button type="submit" class="button dropdown-item" @click="logout()">Logout</button>
                        </div>
                        <!-- <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form> -->
                    </div>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav" :style="showMobileMenu ? 'transform: translateX(-225px);' : 'transform: translateX(0px);'">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Portfolio Status</div>
                            <router-link class="nav-link" to="/home"> 
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </router-link>
                            <div class="sb-sidenav-menu-heading">Tools</div>
                            <router-link class="nav-link" to="/riskcalculator"> 
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                                Risk Calculator
                            </router-link>
                            <router-link class="nav-link" to="/newtrades"> 
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Add New Trade
                            </router-link>
                            <router-link class="nav-link" to="/tradehistory"> 
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                View Trade History
                            </router-link>
                            <router-link class="nav-link" to="/liveportfolio"> 
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Live Portfolio
                            </router-link>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as: {{user.name}}</div>
                        <!-- {{ Auth::user()->name }} -->
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content" :style="showMobileMenu ? 'margin-left: -225px;' : 'margin-left: 0px;'">
                <slot/>
            </div>
        </div>
        <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
        <script src="js/handleUserUpdates.js"></script>
        <script src="js/riskCalculator.js"></script>
        <script src="js/newTrade.js"></script>
        <script src="js/tradeHistory.js"></script>
        <script src="js/dashboardBuilder.js"></script> -->
        <!-- <script src="js/portfolioTracker.js"></script> -->
    </body>
    <Footer/>
</template>

<script>
import Header from '../layouts/Header.vue';
import Footer from '../layouts/Footer.vue';
import { ref } from 'vue';
import APIController from '../../../api.js';
import router from '../../../app.js';
import { onBeforeMount, onMounted, onUnmounted } from 'vue'

export default {
  components: {Header, Footer},
    setup(){
        let sidebarDisplayed = ref(true);
        let sidebarTogglePressed = false;
        let userDropdownDisplayed = ref(false)
        let showMobileMenu = ref(false);
        let user = ref({});

        onBeforeMount(() => {
            window.addEventListener("resize", resizeBrowserHandler);
            if(window.innerWidth < 992){
                showMobileMenu.value = true;
            }
        });

        onMounted(async () => {
            user.value = await APIController.showUserSettings();
        });

        onUnmounted(() => {
            window.removeEventListener("resize", resizeBrowserHandler);
        });

        const toggleSidebar = () => {
            if(sidebarTogglePressed === false){
                sidebarTogglePressed = true;
            }
            if(showMobileMenu.value === false){
                showMobileMenu.value = true;
            }
            else{
                showMobileMenu.value = false;
            }
        }

        const toggleUserDropDown = () => {
            if(userDropdownDisplayed.value === false){
                userDropdownDisplayed.value = true;
            }
            else{
                userDropdownDisplayed.value = false;
            }
        }

        const resizeBrowserHandler = (e) => {
            if(sidebarTogglePressed === false){
                if (e.target.innerWidth < 992) {
                    showMobileMenu.value = true;
                } 
                else {
                    showMobileMenu.value = false;
                }
            }
        }

        const logout = async () => {
            const isLoggedOut = await APIController.logout();
            if(isLoggedOut){
                console.log(isLoggedOut);
                sessionStorage.setItem('isLoggedIn', "'" + false + "'");
                sessionStorage.setItem('role', "''");
                router.push({ name: "Login" });
            }
            else{
                console.log("logout Failed");
            }
        }
    
        return{user, sidebarDisplayed, userDropdownDisplayed, toggleSidebar, toggleUserDropDown, resizeBrowserHandler, showMobileMenu, logout}
    }
}
</script>

<style>

</style>