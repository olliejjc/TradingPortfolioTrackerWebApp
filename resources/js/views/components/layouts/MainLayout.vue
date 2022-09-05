<template>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Trading Portfolio Tracker</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/portfoliotracker.css" rel="stylesheet" />
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <nav class="navbar navbar-expand-md navbar-light bg-dark shadow-sm">
                <div class="container">
                    <!-- <a class="navbar-brand" href="{{ url('/') }}"> -->
                    <a class="navbar-brand">
                        <router-link to="/"><span class="text-white">Trading Portfolio Tracker</span></router-link>
                    </a>
                    <button class="navbar-toggler" type="button" v-on:click="toggleNavbarDisplay">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div id="navbarSupportedContent" :class="navbarDisplayed ? 'collapse navbar-collapse show' : 'collapse navbar-collapse'">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav mr-auto">
                        </ul>

                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ml-auto">
                            <!-- Authentication Links -->
                                <li class="nav-item">
                                    <router-link class="nav-link" to="/login"><span class="text-white">Login</span></router-link>
                                </li>
                               
                                <li class="nav-item">
                                    <router-link class="nav-link" to="/register"><span class="text-white">Register</span></router-link>
                                </li>
                                <!-- <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li> -->
                        </ul>
                    </div>
                </div>
            </nav>

            <main class="py-4">
                <slot/>
            </main>
        </div>
    </body>
</template>

<script>
import { ref } from 'vue';

export default {
  components: {},
    setup(){
        let navbarDisplayed = ref(false);

        const toggleNavbarDisplay = () => {
            if(navbarDisplayed.value === false){
                navbarDisplayed.value = true;
            }
            else{
                navbarDisplayed.value = false;
            }
        }
    
        return{navbarDisplayed, toggleNavbarDisplay}
    }
}
</script>

<style>

</style>