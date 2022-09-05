import axios from "axios";

const apiClient = axios.create({
    baseURL: "http://127.0.0.1:8000",
    withCredentials: true,
});

const API_BASE = "http://127.0.0.1:8000/api"

const WEB_BASE = "http://127.0.0.1:8000"

export default{
    registerUser: async(user) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/register", user)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.user.name + " is registered successfully", data.response.user];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    generateListOfTradeYears: async() => {
        let listOfTradeYears = {}
        await apiClient.get(API_BASE + "/home")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    listOfTradeYears = data.response.listOfTradeYears
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return listOfTradeYears;
    },
    showRiskCalculatorSettings: async() => {
        let riskCalculatorSettings = {}
        await apiClient.get(API_BASE + "/riskcalculator")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    riskCalculatorSettings = data.response[1];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return riskCalculatorSettings;
    },
    showUserSettings: async() => {
        let userSettings = {}
        await apiClient.get(API_BASE + "/settings")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    userSettings = data.response.user
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
        });
        return userSettings;
    },
    updateUser: async(user) => {
        let responseMessage = {};
        await apiClient.put(API_BASE + "/update", user)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.user.name + " is updated successfully", data.response.user];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
        return responseMessage;
    },
    getTrades: async() => {
        let trades = {}
        await apiClient.get(API_BASE + "/trades")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    trades = data.response.trades
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return trades;
    },
    generateTradeHistory: async() => {
        let tradeHistory = {}
        await apiClient.get(API_BASE + "/tradehistory")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    tradeHistory = data.response.tradehistory
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return tradeHistory;
    },
    getTradeDataForSelectedDate: async(selectedMonthAndYear) => {
        let tradeDataWithMatchingDate = [];
        await apiClient.post(API_BASE + "/getTradeDataForSelectedDate", selectedMonthAndYear)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    tradeDataWithMatchingDate = data.response;
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return tradeDataWithMatchingDate;
    },
    calculateMonthlyTotals: async() => {
        let monthlyTotals = {}
        await apiClient.get(API_BASE + "/calculateMonthlyTotals")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    monthlyTotals = data.response.monthlyTotals
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return monthlyTotals;
    },
    getListOfTradeYears: async() => {
        let listOfTradeYears = {}
        await apiClient.get(API_BASE + "/tradeYearsWithTrades")
            .then(response => response.data)
            .then(data => {
                if (data !== undefined || data.length !== 0) {
                    listOfTradeYears = data;
                }
                else{
                    throw "No trade years exist";
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return listOfTradeYears;
    },
    registerTrade: async(trade) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/newtrades", trade, {
            headers: {
            'content-type': 'multipart/form-data'
            }})
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.trade.holding_name + " trade is registered successfully", data.response.trade];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    closeTrade: async(trade) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/closetrades", trade)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.trade.holding_name + " trade open on date " +  data.response.trade.date_trade_opened.split("-").reverse().join("-") + " was closed successfully", data.response.trade];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    generateChartDataSets: async(timeAndYearPeriods) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/generateTradeChartDataSets", timeAndYearPeriods)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", "Trade chart data sets were generated successfully", data.response.tradeChartDataSets];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    deleteTrade: async(id) => {
        let responseMessage = {};
        await apiClient.delete(API_BASE + "/deletetrades/"+id)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.trade.holding_name + " trade open on date " +  data.response.trade.date_trade_opened.split("-").reverse().join("-") + 
                        " was deleted successfully", data.response.trade];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
        return responseMessage;
    },
    calculateTradePositionAndRisk: async(tradeCalculatorData) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/calculate", tradeCalculatorData)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", "Trade position size and risk calculation was completed successfully", data.response.calculatedResults];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    getScreenshotsByTrade: async(id) => {
        let screenshots = {}
        await apiClient.get(API_BASE + "/tradescreenshots/" + id)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    screenshots = data.response.screenshots
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                console.log(err.response.status);
            });
        return screenshots;
    },
    addNewScreenshot: async(submittedScreenshots) => {
        let responseMessage = {};
        await apiClient.post(API_BASE + "/newscreenshots", submittedScreenshots, {
            headers: {
            'content-type': 'multipart/form-data'
            }})
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", "Screenshot(s) were added successfully", data.response.screenshots];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
            return responseMessage;
    },
    deleteScreenshot: async(screenshotPath) => {
        let responseMessage = {};
        await apiClient.delete(API_BASE + "/deletescreenshot/"+screenshotPath)
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", data.response.screenshot.screenshot_image + " was deleted successfully", data.response.screenshot];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
        return responseMessage;
    },
    generateLivePortfolio: async() => {
        let responseMessage = {}
        await apiClient.get(API_BASE + "/liveportfolio")
            .then(response => response.data)
            .then(data => {
                if(data.success){
                    responseMessage = ["Success", "Live Portfolio was generated successfully", data.response];
                }
                else{
                    throw data.response.error;
                }
            })
            .catch(err => {
                let errorMessageComplete = "";
                if (typeof err === 'string' || err instanceof String){
                    errorMessageComplete = err;
                }
                else{
                    errorMessageComplete = err.response.data.message + "\n";
                    let errors = err.response.data.errors;
                    for (const errorAttribute in errors) {
                        let errorObject = (errors[errorAttribute]);
                        let errorMessage = errorObject[0];
                        errorMessageComplete = errorMessageComplete + errorMessage + "\n";
                    }
                }
                responseMessage = ["Error", errorMessageComplete];      
            });
        return responseMessage;
    },
    login: async(userLoginData) => {
        let isValidLogin = {loggedIn: false, errorMessage: '', role: ''};
        await apiClient.get('/sanctum/csrf-cookie').then(async response => {
            await apiClient.post(WEB_BASE + "/login", userLoginData)
            .then(response => response.data)
            .then(data => {
                let loggedInData = data;
                //if(loggedInData){
                if(loggedInData === "Admin" || loggedInData === "User"){
                    isValidLogin.loggedIn = true;
                    isValidLogin.role = loggedInData;
                }
                else{
                    console.log(response);
                    throw data.response.errors;
                }
            })
            .catch(err => {
                console.log(err);
                console.log(err.response.data.errors);
                isValidLogin.errorMessage = err.response.data.errors;
            });
        });
        return isValidLogin;
    },
    logout: async() => {
        let isLoggedOut = false;
        await apiClient.post(WEB_BASE + "/logout")
            .then(response => {
                isLoggedOut=true;
            })
            .catch(err => {
                console.log(err);
            });
        return isLoggedOut;
    },
}