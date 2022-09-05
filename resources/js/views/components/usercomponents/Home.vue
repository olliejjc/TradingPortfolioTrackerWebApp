<template>
    <PanelLayout>
        <main>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4">
                        <h2 class="mt-4">Dashboard</h2>
                        <select v-if="listOfTradeYears.length !== 0 && loaded === true" class="form-control mt-4" id="selectPortfolioView" data-width="120px" 
                                v-model="selectedPortfolioView" @change="changePortfolioView($event)">
                            <option id="portfolioChartView" selected="selected" :value="0"><h2 class="mt-4">Portfolio Performance</h2></option>
                            <option id="portfolioHoldingsView" :value="1"><h2 class="mt-4">Portfolio Holdings</h2></option>
                        </select>
                    </div>
                    <div class="col-lg-8"></div>
                </div>
            </div>
            <div v-show="listOfTradeYears.length !== 0 && loaded === true" class="h-75 container-fluid" id="dashboardBodyContainer">
                <div class="row mt-5">
                    <div v-show="selectedPortfolioView === 0" id="timePeriodSelectContainer" class="col-lg-2">
                        <select class="form-control" id="selecttimeperiod" data-width="120px" v-model="selectedTimePeriod" @change="changeSelectedTimePeriod($event)">
                            <option id="3monthsChartView" :value="'3 Month View'">3 Month View</option>
                            <option id="6monthsChartView" :value="'6 Month View'">6 Month View</option>
                            <option id="optionTitle" :value="'Yearly View'">Yearly View</option>
                            <option id="overallChartView" :value="'Overall View'">Overall View</option>
                        </select>
                    </div>
                    <div v-show="selectedPortfolioView === 0" id="yearPeriodSelectContainer" class="col-lg-2">
                        <select v-show="listOfTradeYears && selectedTimePeriod === 'Yearly View'" class="form-control" id="selectyearperiod" data-width="100px" v-model="selectedYear" @change="changeSelectedTimePeriod($event)">
                                <option class="tradeYear" v-for="tradeYearSelect in listOfTradeYears" :value="tradeYearSelect">
                                    {{tradeYearSelect}}
                                </option>
                        </select>
                    </div>
                    <div class="col-lg-8">
                    </div>
                </div>
                <div v-show="selectedPortfolioView === 0" id="dashboardContentContainer">
                    <canvas id="myChart"></canvas>
                </div>
                <div v-show="selectedPortfolioView === 1" id="portfolioHoldingsContainer">
                    <table id="portfolioHoldingsTable" class="table table-bordered">
                        <thead>
                            <tr v-show="tradesOpened === true">
                                <th>Holding Name</th><th>Price Purchased At</th><th>Trade Size</th><th>Trade Value</th><th>Date Trade Opened</th>
                            </tr>
                        </thead>
                        <tbody id="portfolioHoldingsTableBody">
                            <tr :id="'portfolioHoldingsRow'+trade.id" v-for="trade in tradesOpenedList" :key="trade.id">
                                <td>{{trade.holding_name}}</td>
                                <td>{{trade.price_purchased_at*1}}</td>
                                <td>{{trade.trade_size*1}}</td>
                                <td>{{trade.trade_value*1}}</td>
                                <td>{{trade.date_trade_opened.split("-").reverse().join("-")}}</td>
                            </tr>
                        </tbody>
                    </table>
                    <h2 v-if="selectedPortfolioView === 1 && tradesOpened == false">
                        No Currently Open Trades
                    </h2>
                </div>
            </div>
            <div v-if="loaded===false" class="h-75 container-fluid" id="dashboardBodyContainer">
                <h1 class="mt-4">Loading....</h1>
            </div>
            <div v-if="loaded===true && listOfTradeYears.length === 0 " class="h-75 container-fluid" id="dashboardBodyContainer">
                <h3 class="mt-4">No Trades Stored In Trading Portfolio Tracker</h3>
                <h3 class="mt-4">Trades must be added to the app to show performance over time</h3>
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
import Chart from 'chart.js/auto';

export default {
  components: {PanelLayout},
  setup(){
    const listOfTradeYears = ref([]);
    let selectedYear = ref();
    let selectedTimePeriod = ref("Yearly View");
    let selectedPortfolioView = ref(0);
    let tradesOpened = ref(-1);
    let tradesOpenedList = ref([]);
    const loaded = ref(false);


    onMounted(async () => {
        listOfTradeYears.value = await APIController.generateListOfTradeYears();
        if(listOfTradeYears.value.length !== 0){
            selectedYear.value = listOfTradeYears.value[0];
            generatePortfolioChart(selectedTimePeriod.value, selectedYear.value);
        }
        loaded.value = true;
    });

    const changeSelectedTimePeriod = (event) => {
        let timePeriod = selectedTimePeriod.value;
        let yearPeriod = selectedYear.value;
        console.log(timePeriod);
        generatePortfolioChart(timePeriod, yearPeriod);
    }

    const changePortfolioView = async(event) => {
        if(selectedPortfolioView.value === 0){
            let timePeriod = selectedTimePeriod.value;
            let yearPeriod = selectedYear.value;
            let chartStatus = Chart.getChart("myChart"); // <canvas> id
            if (chartStatus != undefined) {
                chartStatus.destroy();
            }
            console.log(timePeriod);
            generatePortfolioChart(timePeriod, yearPeriod);
        }
        else{
            tradesOpenedList.value = [];
            let trades = await APIController.getTrades();
            if(trades.length > 0){
                trades.sort(function(a, b) {
                    return new Date(a.date_trade_opened) - new Date(b.date_trade_opened);
                });
                for (var i = 0; i < trades.length; i++) {
                    console.log(trades[i].trade_opened);
                    if(trades[i].trade_opened){
                        tradesOpenedList.value.push(trades[i]);
                        console.log("here");
                    }
                }
                if(tradesOpenedList.value.length > 0){
                    tradesOpened.value = true;
                }
                else{
                    tradesOpened.value = false;
                }
            }
        }
    }

    const generatePortfolioChart = async(timePeriod, yearPeriod) => {

        let ctx = document.getElementById('myChart').getContext('2d');
        let dataSet = await getChartDataSet(timePeriod, yearPeriod);
        console.log(dataSet);
        let suggestedMinYAxis = getSuggestedMinYAxis(dataSet);
        let suggestedMaxYAxis = getSuggestedMaxYAxis(dataSet);
        let yearsWithTrades = await getYearsWithTrades();
        let labels = getLabels(timePeriod, yearsWithTrades);
        let chartStatus = Chart.getChart("myChart"); // <canvas> id
        if (chartStatus != undefined) {
            chartStatus.destroy();
        }
        let chart = new Chart(ctx, {
            /* The type of chart we want to create */
            type: 'line',

            /* The data for our dataset */
            data: {
                labels: labels,
                datasets: [{
                    label: 'Portfolio ($)',
                    backgroundColor: 'aliceblue',
                    borderColor: 'steelblue',
                    data: dataSet[2],
                    fill: {
                        target: 'origin',
                        below: 'aliceblue' // And blue below the origin
                        // below: '#FFF0F8'    // And blue below the origin
                    }
                }]
            },

            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        display: true,
                        suggestedMin: suggestedMinYAxis,
                        suggestedMax: suggestedMaxYAxis,
                    }
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                }
            }
        });
    }

    const getChartDataSet = async(timePeriod, yearPeriod) => {
        let chartDataSetString;
        chartDataSetString = await APIController.generateChartDataSets({'timePeriod':timePeriod, 'yearPeriod':yearPeriod});
        return chartDataSetString;
    }

    const getSuggestedMinYAxis = (dataset) => {
        const portfolioSizeValues = dataset[2];
        var smallestValue = parseFloat(portfolioSizeValues[0]);
        for (var i = 0; i < portfolioSizeValues.length; i++) {
            var tradeBalance = parseFloat(portfolioSizeValues[i]);
            if(tradeBalance < smallestValue){
                smallestValue = tradeBalance;
            }
        }
        if(smallestValue > 0){
            var minYAxis = smallestValue * 0.97;
            return minYAxis;
        }
        else{
            var minYAxis = smallestValue * 1.03;
            console.log(minYAxis);
            return minYAxis;
        }
    }

    const getSuggestedMaxYAxis = (dataset) => {
        const portfolioSizeValues = dataset[2];
        var largestValue = parseFloat(portfolioSizeValues[0]);
        for (var i = 0; i < portfolioSizeValues.length; i++) {
            var tradeBalance = parseFloat(portfolioSizeValues[i]);
            if(tradeBalance > largestValue){
                largestValue = tradeBalance;
            }
        }
        if(largestValue < 0){
            var maxYAxis = largestValue * 0.97;
            return maxYAxis;
        }
        else{
            var maxYAxis = largestValue * 1.03;
            return maxYAxis;
        }
    }

    /* Generate chart labels for different time periods */
    function getLabels(timePeriod, yearsWithTrades){
        var date = new Date();
        const formatter = new Intl.DateTimeFormat('en', { month: 'long' });

        if(timePeriod == "3 Month View"){
            date.setMonth(date.getMonth()-2);
            var month1 = formatter.format(date);

            date.setMonth(date.getMonth()+1);
            var month2 = formatter.format(date);

            date.setMonth(date.getMonth()+1);
            var month3 = formatter.format(date);

            return [month1, month2, month3];
        }
        else if(timePeriod == "6 Month View"){
            date.setMonth(date.getMonth()-5);
            var month1 = formatter.format(date);
            date.setMonth(date.getMonth()+1);
            var month2 = formatter.format(date);
            date.setMonth(date.getMonth()+1);
            var month3 = formatter.format(date);
            date.setMonth(date.getMonth()+1);
            var month4 = formatter.format(date);
            date.setMonth(date.getMonth()+1);
            var month5 = formatter.format(date);
            date.setMonth(date.getMonth()+1);
            var month6 = formatter.format(date);
            return [month1, month2, month3, month4, month5, month6];
        }
        else if(timePeriod == "Yearly View"){
            return ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        }
        else{
            var tradeYearLabels = [];
            var firstTradeYear = Math.min.apply(Math, yearsWithTrades);
            var currentYear = new Date().getFullYear();
            /* Generate list of years between the first trade year and this year */
            for (var year = firstTradeYear; year <= currentYear; year++) {
                tradeYearLabels.push(year);
            }
            tradeYearLabels.push("Current Balance");
            return tradeYearLabels;
        }
    }

    /* Generates a list of years that trades have been made in */
     const getYearsWithTrades = async() => {
        let yearsWithTradesStringArray;
        let yearsWithTrades = await APIController.getListOfTradeYears();
        yearsWithTradesStringArray = yearsWithTrades.map(String);
        return yearsWithTradesStringArray;
    }
    
    return {loaded, listOfTradeYears, selectedTimePeriod, selectedYear, changeSelectedTimePeriod, selectedPortfolioView, changePortfolioView, tradesOpenedList, tradesOpened}
  }
}
</script>

<style>

</style>