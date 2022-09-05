<template>
    <PanelLayout>
        <main class="h-75">
        <!-- Modal -->
            <div class="container-fluid">
                <h1 class="mt-4">Trade History</h1>
            </div>
            <div v-if="loaded===true && userTradeHistory.listOfTradeYears != null" class="h-75 container-fluid">
                <div class="row mt-5">
                    <div class="col-lg-2">
                        <select class="form-control" id="selectmonth" v-model="selectedDates.selectedMonth" @change="changeSelectedMonth($event)" data-width="120px">
                            <option class="optionTitle" selected="selected" :value="'Month'" disabled="disabled">Month</option>
                            <option class="tradeMonth" :value="'All Months'">All Months</option>
                            <option class="tradeMonth" :value="'January'">January</option>
                            <option class="tradeMonth" :value="'February'">February</option>
                            <option class="tradeMonth" :value="'March'">March</option>
                            <option class="tradeMonth" :value="'April'">April</option>
                            <option class="tradeMonth" :value="'May'">May</option>
                            <option class="tradeMonth" :value="'June'">June</option>
                            <option class="tradeMonth" :value="'July'">July</option>
                            <option class="tradeMonth" :value="'August'">August</option>
                            <option class="tradeMonth" :value="'September'">September</option>
                            <option class="tradeMonth" :value="'October'">October</option>
                            <option class="tradeMonth" :value="'November'">November</option>
                            <option class="tradeMonth" :value="'December'">December</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <!-- Displays years with trades you can select from -->
                        <select class="form-control" id="selectyear" v-model="selectedDates.selectedYear" @change="changeSelectedYear($event)" data-width="100px">
                            <option class="optionTitle" selected="selected" :value="-1" disabled="disabled">Year</option>
                            <option class="tradeYear" v-for="(tradeYear, index) in userTradeHistory.listOfTradeYears" :value="tradeYear">{{ tradeYear }}</option>
                        </select>
                    </div>
                    <div class="col-lg-8">
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-4">
                        <h2 id="tradeReportDateIdentifier"><span id="tradeReportMonthIdentifier">{{userTradeHistory.reportMonth}}</span> <span id="tradeReportYearIdentifier">{{userTradeHistory.reportYear}}</span> Report</h2>
                    </div>
                    <div class="col-lg-8">
                    </div>
                </div>
                <!-- @endif -->
                <div id="noReportDisplay" class="row">
                </div>
                <!-- @if(isset($trades)) -->
                <div v-if="userTradeHistory.trades.length > 0" class="row mt-5">
                    <!-- Profile Settings-->
                    <div class="col-lg-12 pb-5">
                        <form action='javascript:void(0)' id='closeTradeForm' method='POST' enctype='multipart/form-data' files='true'>
                            <div class="table-responsive-sm">
                                <table id="tradeHistoryTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Holding Name</th>
                                            <th>Price Purchased At</th>
                                            <th>Trade Size</th>
                                            <th>Trade Value</th>
                                            <th>Date Trade Opened</th>
                                            <th>Date Trade Closed</th>
                                            <th>Price Closed At</th>
                                            <th>Profit/Loss of Trade</th>
                                            <th>Screenshots</th>
                                            <th id="closeTradeHeader">Close Trade</th>
                                            <th id="deleteTradeHeader">Delete Trade</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tradeHistoryTableBody">
                                        <!-- Loops through each trade in a specific month and year and displays the values of each trade attribute -->
                                        <tr :class="'tradeRow' + trade.id" v-for="trade in userTradeHistory.trades" :key="trade.id">
                                            <td>{{trade.holding_name}}</td>
                                            <td v-if="trade.price_purchased_at >= 0">${{trade.price_purchased_at*1}}</td>
                                            <td v-else>-${{(trade.price_purchased_at*-1)*1}}</td>
                                            <td>{{trade.trade_size*1}}</td>
                                            <td v-if="trade.trade_value >= 0">${{trade.trade_value*1}}</td>
                                            <td v-else>-${{(trade.trade_value*-1)*1}}</td>
                                            <td>{{trade.date_trade_opened.split("-").reverse().join("-")}}</td>
                                            <td v-if="trade.trade_opened === 0">{{trade.date_trade_closed.split("-").reverse().join("-")}}</td>
                                            <td v-if="trade.trade_opened === 0 && trade.price_closed_at >= 0">${{trade.price_closed_at*1}}</td>
                                            <td v-if="trade.trade_opened === 0 && trade.price_closed_at < 0">-${{(trade.price_closed_at*-1)*1}}</td>
                                            <td v-if="trade.trade_opened === 0 && trade.profit_loss >= 0">${{(trade.profit_loss)*1}}</td>
                                            <td v-if="trade.trade_opened === 0 && trade.profit_loss < 0">-${{(trade.profit_loss*-1)*1}}</td>
                                            <td v-if="trade.trade_opened === 1"><input type="date" :id="'date_trade_closed_id_' + trade.id" :name="'date_trade_closed_id_' + trade.id" v-model="trade.date_trade_closed"/></td>
                                            <td v-if="trade.trade_opened === 1"><input :id="'price_closed_at_id_' + trade.id" :name="'price_closed_at_id_' + trade.id" v-model="trade.price_closed_at"/></td>
                                            <td v-if="trade.trade_opened === 1"><input :id="'profit_loss_id_' + trade.id" :name="'profit_loss_id_' + trade.id" v-model="trade.profit_loss"/></td>
                                            <!-- Only shows screeenshot display button if screenshots exist -->
                                            <td v-if="trade.has_screenshots === 1" class="screenshotColumn">
                                                <div class="span1">
                                                    <input type="image" :id="'screenshotTradeID' + trade.id" class="screenshotImage" v-on:click="openViewModal(trade.id)" :src="'/image/screenshotView.png'">
                                                </div>
                                                <div class="span2">
                                                    <button type="button" class="addNewScreenshotButton" :id="'uploadScreenshotTradeID' + trade.id" v-on:click="openUploadModal(trade.id)">
                                                        <span aria-hidden="true">&#43;</span>
                                                    </button>
                                                </div>
                                            </td>
                                            <td v-else class="screenshotColumn">
                                                <div class="span1"></div>
                                                <div class="span2">
                                                    <button type="button" class="addNewScreenshotButton" :id="'uploadScreenshotTradeID' + trade.id" v-on:click="openUploadModal(trade.id)"><span aria-hidden="true">&#43;</span></button></div></td>
                                            <td class="closeActionColumn">
                                                <!-- Only show close trade button if trade is not closed -->
                                                <button v-if="trade.trade_opened === 1" type="button" class="closeActionButton" v-on:click="closeTrade(trade)"><span aria-hidden="true">&times;</span></button>
                                                <span v-else aria-hidden="true">Closed</span>
                                            </td>
                                            <td class="deleteActionColumn"><button type="button" class="deleteActionButton" v-on:click="deleteTrade(trade.id)"><span aria-hidden="true">&times;</span></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- @endif -->
                <!-- Checks monthly balance and profit loss are set and displays them in the report -->
                <!-- @if(isset($monthlyBalance) && isset($monthlyProfitLoss)) -->
                <div v-if="userTradeHistory.trades.length < 1 && userTradeHistory.reportMonth !== 'Loading'" class="row mt-5">
                    <div class="col-lg-4">
                        <h2 id="tradeReportDateIdentifier">No Trade History Available</h2>
                    </div>
                    <div class="col-lg-8">
                    </div>
                </div>
                <div v-if="userTradeHistory.reportMonth !== 'Loading' && responseMessage[0] == 'Error' || responseMessage[0] == 'Success'" :class="'responseMessage' + responseMessage[0] + ' row mt-1 text-center'">
                    <div class="col-12">
                        <span id="tradeResponseMessage">{{responseMessage[1]}}</span>
                    </div>
                </div>
                <div v-if="userTradeHistory.listOfTradeYears.length > 0" class="row mt-5">
                        <div class="col-lg-4 col-sm-3"></div>
                        <div class="col-lg-4" id="tradeHistoryTotalContainer">
                            <table id="tradeHistoryTotalsTable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th id="monthlyStartingBalanceTradingHistoryHeader">MONTHLY BALANCE</th>
                                        <th id="monthlyProfitLossTradingHistoryHeader">MONTHLY PROFIT/LOSS</th>
                                    </tr>
                                </thead>
                                <tbody id="tradeHistoryTotalsTableBody">
                                    <tr>
                                        <td v-if="userTradeHistory.monthlyBalance >=0">${{userTradeHistory.monthlyBalance}}</td>
                                        <td v-else>-${{userTradeHistory.monthlyBalance*-1}}</td>
                                        <td v-if="userTradeHistory.monthlyProfitLoss >= 0">${{userTradeHistory.monthlyProfitLoss}}</td>
                                        <td v-else>-${{userTradeHistory.monthlyProfitLoss*-1}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4"></div>
                </div>
                <!-- @endif -->
            </div>
            <div v-if="loaded===false" class="h-75 container-fluid">
                <h1 class="mt-4">Loading....</h1>
            </div>
            <div v-if="loaded===true && userTradeHistory.listOfTradeYears == null" class="h-75 container-fluid">
                <h3 class="mt-4">No Trades Stored In Trading Portfolio Tracker</h3>
                <h3 class="mt-4">Trades must be added to the app to show trade history</h3>
            </div>
        </main>
        <ScreenshotViewModal @tradeNoScreenshots="removeScreenshotSymbol" @closeViewModal="setViewModalClosed" :tradeid="tradeIdSelected" :viewModalOpen="openViewModalStatus"/>
        <ScreenshotUploadModal @tradeNewScreenshots="addScreenshotSymbol" @closeUploadModal="setUploadModalClosed" :tradeid="tradeIdSelected" :uploadModalOpen="openUploadModalStatus"/>
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
import ScreenshotViewModal from'./ScreenshotViewModal.vue';
import ScreenshotUploadModal from'./ScreenshotUploadModal.vue';

export default {
    components: {PanelLayout, ScreenshotViewModal, ScreenshotUploadModal},
    setup(){
        const responseMessage = ref({});
        const userTradeHistory = ref({
            trades: [],
            listOfTradeYears: [],
            reportMonth: "Loading",
            reportYear: "",
            monthlyBalance: 0,
            monthlyProfitLoss: 0
        });
        const selectedDates = ref({
            selectedMonth: "Month",
            selectedYear: -1
        });
        const tradeIdSelected = ref(-1);
        const openViewModalStatus = ref(false);
        const openUploadModalStatus = ref(false);
        const loaded = ref(false);

        onMounted(async () => {
           let tradeHistory = await APIController.generateTradeHistory();
           userTradeHistory.value.listOfTradeYears = tradeHistory.listOfTradeYears;
           userTradeHistory.value.trades = tradeHistory.trades;
           userTradeHistory.value.reportMonth = tradeHistory.tradeMonth;
           userTradeHistory.value.reportYear = tradeHistory.tradeYear;
           userTradeHistory.value.monthlyBalance = tradeHistory.monthlyBalance;
           userTradeHistory.value.monthlyProfitLoss = tradeHistory.monthlyProfitLoss;
           if(userTradeHistory.value.listOfTradeYears != null){
            let latestYear = userTradeHistory.value.listOfTradeYears[userTradeHistory.value.listOfTradeYears.length-1];
           }
           loaded.value = true;
        });

        const changeSelectedMonth = (event) => {
            userTradeHistory.value.reportMonth = selectedDates.value.selectedMonth;
            getTradesWithDateMatchingSelected();
            responseMessage.value = {};
        }

        const changeSelectedYear = (event) => {
            userTradeHistory.value.reportYear = selectedDates.value.selectedYear;
            getTradesWithDateMatchingSelected();
            responseMessage.value = {};
        }
            /* Gets all trades that match a specific month and year */
        const getTradesWithDateMatchingSelected = async() => {
            let tradeDataForMatchingDate = await APIController.getTradeDataForSelectedDate(selectedDates.value);
            console.log(tradeDataForMatchingDate);
            if (tradeDataForMatchingDate !== undefined && tradeDataForMatchingDate.length !== 0) {
                console.log("here");
                userTradeHistory.value.trades = tradeDataForMatchingDate.tradesWithMatchingDate;
                userTradeHistory.value.monthlyBalance = tradeDataForMatchingDate.monthlyBalance;
                userTradeHistory.value.monthlyProfitLoss = tradeDataForMatchingDate.monthlyProfitLoss;
            }
            else{
                console.log(userTradeHistory.value);
                userTradeHistory.value.listOfTradeYears = [];
                userTradeHistory.value.trades = [];
            }
        }

        const closeTrade = async(trade) => {
            // let trade = userTradeHistory.value.trades.find(trade => trade.id === id);
            // console.log(trade);
            responseMessage.value = await APIController.closeTrade(trade);
            if(responseMessage.value[0] === "Success"){
                getTradesWithDateMatchingSelected();
            }
        }

        const deleteTrade = async(id) => {
            responseMessage.value = await APIController.deleteTrade(id);
            getTradesWithDateMatchingSelected();
        }

        const openViewModal = (id) => {
            tradeIdSelected.value = id;
            openViewModalStatus.value = true;
        }

        const openUploadModal = (id) => {
            tradeIdSelected.value = id;
            openUploadModalStatus.value = true;
        }

        const removeScreenshotSymbol = () => {
            getTradesWithDateMatchingSelected();
        }

        const addScreenshotSymbol = () => {
            getTradesWithDateMatchingSelected();
        }

        const setViewModalClosed = () => {
            openViewModalStatus.value = false;
        }

        const setUploadModalClosed = () => {
            openUploadModalStatus.value = false;
        }


        return {loaded, responseMessage, userTradeHistory, selectedDates, tradeIdSelected, openViewModalStatus, openUploadModalStatus, removeScreenshotSymbol, addScreenshotSymbol, 
            closeTrade, deleteTrade, changeSelectedMonth, changeSelectedYear, openViewModal, openUploadModal, setViewModalClosed, setUploadModalClosed}
    }
}
</script>

<style>

</style>