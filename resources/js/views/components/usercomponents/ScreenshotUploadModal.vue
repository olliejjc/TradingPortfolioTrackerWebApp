<template>
    <!-- Modal for screenshot display -->
    <div v-show="uploadModalVisible" class="modal fade show" id="screenshotUploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog" 
 style="display:block">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form id='uploadScreenshotForm' @submit.prevent="addNewScreenshots">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Screenshots</h5>
                        <button type="button" class="close" @click="closeUploadModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modalBodyScreenshotUpload">
                            <!-- <input type='hidden' name='csrfmiddlewaretoken' value=" + $('meta[name="csrf-token"]').attr('content') + "> -->
                            <input class='form-control' type='file' id='screenshots' ref="fileRef" name='screenshots[]' @change="handleScreenshots"  multiple>
                            <input class='classRowIdentifierUploadScreenshot' id='rowIdentifier' name='rowIdentifier' hidden>
                    </div>
                    <div :class="'modal-footer responseMessage'+responseMessage[0]" id="modalFooterScreenshotUpload">
                            <button class='btn btn-style-1 btn-primary' type='submit' id='btn-new-screenshot'>Upload Screenshots</button>
                            <span id='addedScreenshotMessage'>{{responseMessage[1]}}</span>
                        <div class='col-sm d-flex justify-content-end'>
                            <button type='button' class='btn btn-secondary' @click="closeUploadModal()">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, watch } from 'vue';
import { onMounted } from 'vue'
import APIController from '../../../api.js';
export default {
    props: ["tradeid", "uploadModalOpen"],
    emits: ["tradeNewScreenshots", "closeUploadModal"],
    setup(props, {emit}){
        const screenshots = ref([]);
        const uploadModalVisible = ref(false);
        const fileInput = ref(null);
        let fileRef = ref(null);
        const tradeId = ref(props.tradeid);
        let responseMessage = ref({});

        onMounted(async () => {
        });

        watch(props, async() => {
            console.log(props);
            if(props.tradeId !== -1 && props.uploadModalOpen === true){
                tradeId.value = props.tradeid;
                uploadModalVisible.value = true;
            }
        })

        const closeUploadModal = () => {
            console.log("closeUploadModal");
            uploadModalVisible.value = false;
            responseMessage.value = {};
            if(fileInput.value !== null){
                fileInput.value.target.value = null;
            }
            emit("closeUploadModal", false);
        }

         const handleScreenshots = (e) => {
            let fileList = e.target.files;
            console.log(fileList);
            for (let i = 0; i < fileList.length; i++) {
                let file = fileList.item(i);
                screenshots.value.push(file);
            }
            fileInput.value = e;
        }

        const addNewScreenshots = async() => {
            let fd = new FormData();

            for( var i = 0; i < screenshots.value.length; i++ ){
                let file = screenshots.value[i];
                fd.append('screenshots[]', file);
            }
            fd.append('tradeId', tradeId.value);
            // append rest of form data
            console.log(fd);
            responseMessage.value = await APIController.addNewScreenshot(fd);
            if(responseMessage.value[0] === "Success"){
                screenshots.value = [];
                console.log(fileInput.value);
                if(fileInput.value !== null){
                    console.log("notnull");
                    fileInput.value.target.value = null;
                }
                if(screenshots.value.length === 0){
                    emit("tradeNewScreenshots", tradeId);
                }
            }
        }

        return {screenshots, uploadModalVisible, addNewScreenshots, responseMessage, handleScreenshots, fileRef, tradeId, closeUploadModal}
    }
}
</script>

<style>

</style>