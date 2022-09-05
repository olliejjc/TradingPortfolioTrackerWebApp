<template>
  <MainLayout>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">Register</div>

                        <div class="card-body">
                            <form @submit.prevent="register">

                                <div class="form-group row">
                                    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                                    <div class="col-md-6">
                                        <input id="name" type="text" class="form-control" name="name" v-model="newUser.name" required autocomplete="name" autofocus>

                                        <!-- @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror -->
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="username" class="col-md-4 col-form-label text-md-right">Username</label>

                                    <div class="col-md-6">
                                        <input id="username" type="text" class="form-control" name="username" v-model="newUser.username" required autocomplete="username" autofocus>

                                        <!-- @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror -->
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Address</label>

                                    <div class="col-md-6">
                                        <input id="email" type="email" class="form-control" name="email" v-model="newUser.email" required autocomplete="email">

                                        <!-- @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror -->
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password" class="col-md-4 col-form-label text-md-right">Password</label>

                                    <div class="col-md-6">
                                        <input id="password" type="password" class="form-control" name="password" 
                                            v-model="newUser.password" required autocomplete="new-password">

                                        <!-- @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror -->
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Confirm Password</label>

                                    <div class="col-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" v-model="newUser.passwordConfirm" 
                                            required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-9 col-lg-10"></div>
                                    <div class="col-md-1 col-lg-2">
                                        <button class="btn btn-primary" v-on:click="addNewUser">Register</button>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div :class="'responseMessage'+responseMessage[0] + ' text-center mt-3'" >
                <span>{{responseMessage[1]}}</span>
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
        let newUser = ref({
            name: "",
            username: "",
            email: "",
            password: "",
            passwordConfirm: ""
        });
        const responseMessage = ref({});

        const addNewUser = async() => {
            responseMessage.value = await APIController.registerUser(newUser.value);
            if(responseMessage.value.length>2){
                if (typeof responseMessage.value[2] !== 'undefined') {
                    console.log(responseMessage.value[2]);
                    newUser = ref({
                        name: "",
                        username: "",
                        email: "",
                        password: "",
                        passwordConfirm: ""
                    });
                }
            }
            console.log(newUser);
        }

        return {newUser, addNewUser, responseMessage}

    }
}
</script>

<style>

</style>