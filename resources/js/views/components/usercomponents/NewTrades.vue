<template>
    <PanelLayout>
    <main class="h-75">
        <div class="container-fluid">
            <h1 class="mt-4">Add New Trade</h1>
        </div>
        <div class="h-75 container-fluid">
            <div class="row mt-5">
                <!-- Profile Settings-->
                <div class="col-lg-12 pb-5">
                    <form class="row" id="trade-add-form" @submit.prevent="addNewTrade">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Asset Name</label>
                                <input class="form-control" type="text" id="asset_name" name="asset_name" v-model="trade.asset_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Trade Size</label>
                                <input class="form-control" type="number" id="trade_size" name="trade_size" v-model="trade.trade_size" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Trade Value</label>
                                <input class="form-control" type="number" id="trade_value" name="trade_value" v-model="trade.trade_value" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Date Trade Opened</label>
                                <input class="form-control" type="date" id="date_trade_opened" name="date_trade_opened" v-model="trade.date_trade_opened">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Price Purchased At</label>
                                <input class="form-control" type="number" id="price_purchased_at" name="price_purchased_at" v-model="trade.price_purchased_at" step="0.00000001">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Screenshots</label>
                                <input class="form-control" type="file" ref="fileRef" id="screenshots" name="screenshots[]" @change="handleScreenshots" multiple>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="mt-2 mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <button class="btn btn-style-1 btn-primary" type="submit" id="btn-new-trade" data-toast="" data-toast-position="topRight" data-toast-type="success" data-toast-icon="fe-icon-check-circle" data-toast-title="Success!" data-toast-message="New Trade Submitted">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div :class="'responseMessage'+responseMessage[0]" >
                {{responseMessage[1]}}
            </div>
        </div>
    </main>
    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright &copy; Oliver Campion</div>
            </div>
        </div>
    </footer>
    </PanelLayout>
</template>

<script>
import PanelLayout from '../layouts/PanelLayout.vue';
import { ref } from 'vue';
import { onMounted } from 'vue'
import APIController from '../../../api.js';

export default {
components: {PanelLayout},
    setup(){
        let trade = ref({
            asset_name : "",
            trade_size: 0,
            trade_value : 0,
            date_trade_opened: "",
            price_purchased_at: 0,
            screenshots: []
        });
        const responseMessage = ref({});
        const fileInput = ref(null);
        let fileRef = ref(null);

        onMounted(async () => {
            
        });

        const handleScreenshots = (e) => {
            let fileList = e.target.files;
            console.log(fileList);
            for (let i = 0; i < fileList.length; i++) {
                let file = fileList.item(i);
                console.log(trade.value);
                console.log(trade.value.screenshots);
                trade.value.screenshots.push(file);
            }
            fileInput.value = e;
        }

        const addNewTrade = async() => {
            let fd = new FormData();
            fd.append('asset_name', trade.value.asset_name);
            fd.append('trade_size', trade.value.trade_size);
            fd.append('trade_value', trade.value.trade_value);
            fd.append('date_trade_opened', trade.value.date_trade_opened);
            fd.append('price_purchased_at', trade.value.price_purchased_at);

            for( var i = 0; i < trade.value.screenshots.length; i++ ){
                let file = trade.value.screenshots[i];
                fd.append('screenshots[]', file);
            }
            // append rest of form data
            console.log(fd);
            responseMessage.value = await APIController.registerTrade(fd);
            if(responseMessage.value[0] === "Success"){
                trade.value.asset_name = "",
                trade.value.trade_size = 0;
                trade.value.trade_value = 0;
                trade.value.date_trade_opened = "";
                trade.value.price_purchased_at = 0;
                trade.value.screenshots = [];
                console.log(fileInput.value);
                if(fileInput.value !== null){
                    console.log("notnull");
                    fileInput.value.target.value = null;
                }
            }


            // if(responseMessage.value.length>2){
            //     console.log(responseMessage.value);
            // }
        }

        return {trade, handleScreenshots, addNewTrade, responseMessage, fileRef}
    }
}
</script>

<style>

</style>