<template>
    <PanelLayout>
        <main class="h-75">
            <div class="container-fluid">
                <h1 class="mt-4">Risk Calculator</h1>
            </div>
            <div class="h-75 container-fluid">
                <div class="row mt-5">
                    <!-- Profile Settings-->
                    <div class="col-lg-12 pb-5">
                        <form class="row" id="risk-calculator-form" @submit.prevent="runRiskCalculator">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-fn">Portfolio Size</label>
                                    <!-- @if(isset($currentPortfolioSize)) -->
                                    <!-- <input class="form-control" type="text" id="portfolio_size" name="portfolio_size" v-model="user.name" readonly="readonly"> -->
                                    <input class="form-control" type="text" id="portfolio_size" name="portfolio_size" v-model="riskCalculator.portfolio_size" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-fn">Percentage Risk Per Trade</label>
                                    <input class="form-control" type="text" id="risk_percentage_per_trade" name="risk_percentage_per_trade" v-model="riskCalculator.risk_percentage_per_trade" readonly="readonly">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-fn">Entry Price</label>
                                    <input class="form-control" type="text" id="entry_price" name="entry_price" v-model="riskCalculator.entry_price">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="account-fn">Stop Loss</label>
                                    <input class="form-control" type="text" id="stop_loss" name="stop_loss" v-model="riskCalculator.stop_loss">
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="mt-2 mb-3">
                                <div class="d-flex flex-wrap justify-content-between align-items-center">
                                    <button class="btn btn-style-1 btn-primary" type="submit" id="btn-calculate-size" data-toast="" data-toast-position="topRight" data-toast-type="success" data-toast-icon="fe-icon-check-circle" data-toast-title="Success!" data-toast-message="Position size calculated.">Calculate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Max # of Shares to Purchase</label>
                                <input class="form-control" type="text" id="max_shares_purchase" name="max_shares_purchase" v-model="riskCalculator.max_shares_purchase" disabled="">
                            </div>
                    </div>
                    <div class="col-md-6">
                            <div class="form-group">
                                <label for="account-fn">Position Size</label>
                                <input class="form-control" type="text" id="position_size" name="position_size" v-model="riskCalculator.position_size" disabled="">
                            </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="account-fn">Risk of Position</label>
                                <input class="form-control" type="text" id="risk_of_position" name="risk_of_position" v-model="riskCalculator.risk_of_position" disabled="">
                            </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <div v-if="responseMessage[0] === 'Error'" id="errorMessageCalculatorContainer">
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
        let user = ref({});
        let riskCalculator = ref({
            portfolio_size : 0,
            risk_percentage_per_trade: 0,
            entry_price : 0,
            stop_loss: 0,
            max_shares_purchase: 0,
            position_size: 0,
            risk_of_position: 0
        });
        const responseMessage = ref({});

        onMounted(async () => {
            let riskCalculatorSettings = await APIController.showRiskCalculatorSettings();
            riskCalculator.value.portfolio_size = riskCalculatorSettings.currentPortfolioSize;
            riskCalculator.value.risk_percentage_per_trade = riskCalculatorSettings.user.risk_percentage_per_trade;
        });

        const runRiskCalculator = async() => {
            console.log(riskCalculator.value);
            responseMessage.value = await APIController.calculateTradePositionAndRisk(riskCalculator.value);
            if(responseMessage.value[0] === "Success"){
                riskCalculator.value.max_shares_purchase = responseMessage.value[2].maxSharesToPurchase;
                riskCalculator.value.position_size = responseMessage.value[2].positionSize;
                riskCalculator.value.risk_of_position = responseMessage.value[2].riskOfPosition;
            }

            // if(responseMessage.value.length>2){
            //     console.log(responseMessage.value);
            // }
        }

        return { user, riskCalculator, runRiskCalculator, responseMessage}
    }
}
</script>

<style>

</style>