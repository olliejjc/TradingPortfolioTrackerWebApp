<template>
    <MainLayout>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Login</div>
                        <div class="card-body">
                            <form @submit.prevent="login">

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>
                                    <div class="col-md-6">
                                        <input id="email" name="email" type="email" class="form-control" required v-model="userLogin.email">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>
                                    <div class="col-md-6">
                                        <input id="password" name="password" type="password" class="form-control" size="20" required v-model="userLogin.password">
                                    </div>
                                </div>

                                <!-- <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                            <label class="form-check-label" for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="form-group row mb-0">
                                    <div class="col-md-9 col-lg-10"></div>
                                    <div class="col-md-1 col-lg-2">
                                        <div id="loginButton" class="submit">
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>

                                <div v-if="!loggedIn.value" id="loginError">{{errorMessage}}</div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script>
import Footer from '../layouts/Footer.vue';
import MainLayout from '../layouts/MainLayout.vue';
import { ref } from 'vue';
import APIController from '../../../api.js';
import router from '../../../app.js';


export default {
  components: {Footer, MainLayout},
  setup(){
    const userLogin = {
        email: '',
        password: '',
    };
    let loggedIn = ref(false);
    let errorMessage = ref('');
    const login = async () => {
        if(!userLogin.email == "" || !userLogin.password == ""){
            const isValidLogin = await APIController.login(userLogin);
            console.log(isValidLogin);
            //should be &&
            if(isValidLogin.loggedIn == true){
                console.log("User with email " + userLogin.email + " has successfully logged in");
                sessionStorage.setItem('isLoggedIn', "'" + true + "'");
                sessionStorage.setItem('role', "'" + isValidLogin.role + "'");
                router.push({ name: "Home" });
            }
            else{
                console.log(isValidLogin.errorMessage.email[0]);
                loggedIn.value = isValidLogin.loggedIn;
                errorMessage.value = isValidLogin.errorMessage.email[0];
            }
        }
        else{
             console.log("Username and password must have a value to login");
        }
    }
    return{login, userLogin, loggedIn, errorMessage}
  }
}
</script>

<style>

</style>