<template>
    <PanelLayout>
        <main>
            <div v-if="Object.keys(livePortfolio.binanceHoldings).length !== 0 && loaded===true" class="container-fluid">
                <h1 class="mt-4">Live Portfolio</h1>
                <h2 class="mt-4">Crypto Holdings Table</h2>
                <div class="row">
                    <table class="table table-striped mt-4">
                        <thead>
                            <tr>
                            <th scope="col">Symbol</th>
                            <th scope="col">Ticker</th>
                            <th scope="col">Holdings</th>
                            <th scope="col">Current Price</th>
                            <th scope="col">Dollar Value of Holdings</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loops through data from binance API and shows the details for each Binance Crypto Holding on your binance account -->
                            <tr :class="'binanceHoldingsRow'" v-for="binanceHolding in livePortfolio.binanceHoldings">
                                <td class="'binanceDetailsColumn'" v-for="(binanceDetails, index) in binanceHolding">
                                    <span v-if="index===0"><img :src="'/images/vendor/cryptocurrency-icons/svg/color/' + binanceDetails.toLowerCase() 
                                        + '.svg'" @error="$event.target.src='/images/vendor/cryptocurrency-icons/svg/color/generic.svg'" style="width:30px;height:30px;"></span>
                                    <span v-else>{{binanceDetails}}</span>
                                </td>
                            </tr>
                            <tr :class="'binanceHoldingsRow'">
                                <td class="binanceDetailsColumn">
                                    <span><img src='/images/vendor/cryptocurrency-icons/svg/color/usdt.svg' style="width:30px;height:30px;"></span>
                                </td>
                                <td class="binanceDetailsColumn"><span><b>TOTAL</b></span></td>
                                <td class="binanceDetailsColumn"><span></span></td>
                                <td class="binanceDetailsColumn"><span></span></td>
                                <td class="binanceDetailsColumn"><span><b>${{livePortfolio.binanceTotalDollarHoldings}}</b></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div v-else-if="loaded===false && responseMessage[0] !=='Error'" class="container-fluid">
            <!-- <div v-else class="container-fluid"> -->
                <h1 class="mt-4">Loading Live Portfolio....</h1>
            </div>
            <div v-else class="container-fluid">
            <!-- <div v-else class="container-fluid"> -->
                <h1 class="mt-4">Live Portfolio</h1>
                <h2 class="mt-4">No Live Portfolio Data Available - Check your Binance and IB API Settings</h2>
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
        const livePortfolio = ref({
            "binanceHoldings": [] ,
            "binanceTotalDollarHoldings" : 0, 
            "interactiveBrokersData": ""
        });
        const loaded = ref(false);
        const responseMessage = ref({});

        onMounted(async () => {
            responseMessage.value = await APIController.generateLivePortfolio();
            if(responseMessage.value[0] === "Success"){
                livePortfolio.value = responseMessage.value[2];
                loaded.value = true;
            }
        });
        return {livePortfolio, loaded, responseMessage}
    }
}
</script>

<style>

</style>