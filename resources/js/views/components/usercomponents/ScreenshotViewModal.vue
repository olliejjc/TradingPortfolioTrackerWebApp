<template>
    <!-- Modal for screenshot display -->
    <div v-show="screenshots.length > 0 && viewModalVisible" class="modal fade show" id="screenshotDisplayModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog" 
 style="display:block">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Screenshots</h5>
                    <button type="button" class="close" @click="closeViewModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBodyScreenshotDisplay">
                    <div id='carouselExample' class='carousel slide' data-bs-slide="carousel" data-bs-interval='false'>
                        <div class='carousel-inner' id='carousel-inner-screenshotupload'>
                            <div v-for="(screenshot, index) in screenshots" :key="screenshot.id" :class="index === screenshotSlideNumber-1 ? 'carousel-item active' : 'carousel-item'">
                                <img class='screenshotImage d-block w-100' :src="'/screenshots/' + screenshot.screenshot_image">
                            </div>
                        </div>
                        <a v-if="screenshots.length > 1" class='carousel-control-prev' role='button' v-on:click="carouselChangeSlide(-1, index)">
                                <span class='carousel-control-prev-icon' aria-hidden='true'></span>
                                <span class='sr-only'>Previous</span>
                        </a>
                        <a v-if="screenshots.length > 1" class='carousel-control-next' role='button' v-on:click="carouselChangeSlide(+1, index)">
                            <span class='carousel-control-next-icon' aria-hidden='true'></span>
                            <span class='sr-only'>Next</span>
                        </a>
                    </div>
                </div>
                <div class="modal-footer" id="modalFooterScreenshotDisplay">
                    <div class='col-sm d-flex justify-content-start'>
                        <button type='button' class='btn btn-secondary deleteScreenshotButton' @click="deleteScreenshot(screenshotSlideNumber-1)">Delete</button>
                    </div>
                    <div class='col-sm d-flex justify-content-center'>
                        <span id='numbertext'>{{screenshotSlideNumber}}/{{screenshots.length}}</span>
                    </div>
                    <div class='col-sm d-flex justify-content-end'>
                        <button type='button' class='btn btn-secondary' @click="closeViewModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { onMounted } from 'vue'
import APIController from '../../../api.js';
export default {
    props: ["tradeid", "viewModalOpen"],
    emits: ["tradeNoScreenshots", "closeViewModal"],
    setup(props, {emit}){
        const screenshots = ref([]);
        const screenshotSlideNumber = ref(1);
        const viewModalVisible = ref(false);
        const tradeId = ref(props.tradeid);

        onMounted(async () => {
            console.log(tradeId);
        });

        watch(props, async() => {
            if(props.tradeid !== -1 && props.viewModalOpen === true){
                tradeId.value = props.tradeid;
                await generateScreenshotDisplayModal(tradeId.value);
                viewModalVisible.value = true;
            }
        })

        const generateScreenshotDisplayModal = async(tradeId) => {
            screenshots.value = await APIController.getScreenshotsByTrade(tradeId);
            if(screenshots.value.length > 0){
                for(let i = 0; i < screenshots.value.length; i++){
                    if(i === 0){
                        screenshots.value[i]['active'] = true;
                    }
                    else{
                        screenshots.value[i]['active'] = false;
                    }
                }
            }
        }

        const carouselChangeSlide = (navigationChange, index) => {
            if((screenshotSlideNumber.value + navigationChange) < 1){
                screenshotSlideNumber.value = 1;
                index = 0;
            }
            else if((screenshotSlideNumber.value + navigationChange) > screenshots.value.length){
                screenshotSlideNumber.value = screenshots.value.length;
                index = screenshots.value.length - 1;
            }
            else{
                screenshotSlideNumber.value += navigationChange;
                index += navigationChange;
            }
        }

        const deleteScreenshot = async(screenshotIndex) => {
            let screenshotImagePath = screenshots.value[screenshotIndex].screenshot_image;
            let responseMessage = await APIController.deleteScreenshot(screenshotImagePath);
            if(responseMessage[0] === "Success"){
                await generateScreenshotDisplayModal(tradeId.value);

                if((screenshotSlideNumber.value -1) < 1){
                    screenshotSlideNumber.value = 1;
                }
                else{
                    screenshotSlideNumber.value = screenshotSlideNumber.value - 1;
                }

                if(screenshots.value.length === 0){
                    emit("tradeNoScreenshots", tradeId.value);
                }
            }
        }

        const closeViewModal = () => {
            viewModalVisible.value = false;
            screenshotSlideNumber.value = 1;
            screenshots.value = [];
            emit("closeViewModal", false);
        }

        return {screenshots, screenshotSlideNumber, viewModalVisible, deleteScreenshot, generateScreenshotDisplayModal, carouselChangeSlide, closeViewModal}
    }
}
</script>

<style>

</style>