<template>
    <div class="img-upload" :style="{ 'height': height, 'width': width }">
        <template v-if="cropImg">
            <div class="img-upload_wrapper">
                <img class="img-fluid" :src="cropImg" />
                <div class="img-upload-actions">
                    <el-button link @click="removeImage" class="text-white" size="large">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M14.28 2a2 2 0 0 1 1.897 1.368L16.72 5H20a1 1 0 1 1 0 2l-.003.071l-.867 12.143A3 3 0 0 1 16.138 22H7.862a3 3 0 0 1-2.992-2.786L4.003 7.07A1.01 1.01 0 0 1 4 7a1 1 0 0 1 0-2h3.28l.543-1.632A2 2 0 0 1 9.721 2zm3.717 5H6.003l.862 12.071a1 1 0 0 0 .997.929h8.276a1 1 0 0 0 .997-.929zM10 10a1 1 0 0 1 .993.883L11 11v5a1 1 0 0 1-1.993.117L9 16v-5a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v5a1 1 0 1 1-2 0v-5a1 1 0 0 1 1-1m.28-6H9.72l-.333 1h5.226z"/></g></svg>
                    </el-button
                    >
                </div>
            </div>
        </template>
        <div v-else class="img-upload_wrapper">
            <div class="img-upload_box">
                <input type="file" name="image" accept="image/*" @change="setImage">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" font-size="1.5rem" class="iconify iconify--mdi" width="1em" height="1em"
                    viewBox="0 0 24 24">
                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"></path>
                </svg>
            </div>
        </div>
        <el-dialog v-model="modalOpen" :title="$t('common.adjust_image')" width="400px">
            <vue-cropper ref="cropper" 
            :aspect-ratio="ratio" 
            :src="imgSrc" 
            :cropBoxResizable="true"
            preview=".preview"
            />
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="modalOpen = false">
                        {{ $t('common.cancel')}}
                    </el-button>
                    <el-button type="primary" @click="submitImage">
                        {{ $t('common.save') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>
<style lang="scss" scoped>

.img-upload {
	display: block;

	.img-upload_wrapper {
		position: relative;
		display: flex;
		-webkit-box-align: center;
		align-items: center;
		-webkit-box-pack: center;
		justify-content: center;
		flex-shrink: 0;
		font-size: 1.25rem;
		line-height: 1;
		overflow: hidden;
		user-select: none;
		border-radius: 8px;
		color: rgba(76, 78, 100, 0.6);
		width: 100%;
		height: 100%;
		background-color: transparent;
		border: 2px dashed rgba(76, 78, 100, 0.12);

        :hover {
            border: var(--el-primary);
        }
            
        .img-upload-actions:hover {
            opacity: 1;
        }

        .img-upload-actions {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            cursor: default;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            opacity: 0;
            font-size: 20px;
            background-color: var(--el-overlay-color-lighter);
            transition: opacity var(--el-transition-duration);
        }
	}
    
	.img-upload_box {
		width: 30px;
		height: 30px;
		display: flex;
		border-radius: 8px;
		-webkit-box-align: center;
		align-items: center;
		color: rgba(76, 78, 100, 0.54);
		-webkit-box-pack: center;
		justify-content: center;
		background-color: rgba(109, 120, 141, 0.12);
	}

	input {
		width: 100%;
		height: 100%;
		position: absolute;
		left: 0;
		opacity: 0;
		cursor: pointer;
	}

	.btn-remove {
		position: absolute;
		padding: 0px 5px;
		top: -8px;
		right: 6px;
		color: #6c757d;
		background: red;
		border: none;
		cursor: pointer;
		border-radius: 50%;
		z-index: 2;
	}

    .preview {
        width: 100%;
        height: 370px;
        overflow: hidden;
    }
}
</style>
<script setup>
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";
import { ref, watch, computed } from 'vue';
const props = defineProps({
    ratio: {
        type: Number,
        default: 1 / 1,
    },
    height: {
        type: String,
        default: '120px',
    },
    width: {
        type: String,
        default: '120px',
    },
    imageWidth: {
        type: Number,
        default: 600,
    },
    imageHeight: {
        type: Number,
        default: 600,
    },
    autoCrop: {
        type: Boolean,
        default: false,
    },
    modelValue: [String, File],
});

const emit = defineEmits(['update:modelValue'])

const imgSrc = ref(null);
const cropImg = ref(null);
const filename = ref("");
const mimeType = ref("");
const modalOpen = ref(false);
const cropper = ref();
const heightWrap = computed(() => (cropImg.value ? '100%' : `${props.height}px`));

watch(() => props.modelValue, (newValue) => {
    if (typeof newValue === 'string') {
        cropImg.value = newValue;
    }
});

function setImage(e) {
    const file = e.target.files[0];

    if (!file.type.includes('image/')) {
        alert('Please select an image file');
        return;
    }

    if (FileReader) {
        const reader = new FileReader();

        reader.onload = (event) => {
            imgSrc.value = event.target.result;
            cropper.value.replace(event.target.result);
        };

        reader.readAsDataURL(file);
    } else {
        alert('Sorry, FileReader API not supported');
    }

    filename.value = file.name;
    mimeType.value = file.type;
    modalOpen.value = true;
}

async function cropImage() {
    cropImg.value = cropper.value.getCroppedCanvas({
        minWidth: props.imageWidth,
        minHeight: props.imageHeight,
        fillColor: '#fff',
        imageSmoothingEnabled: false,
        imageSmoothingQuality: 'high',
    }).toDataURL();
}

async function dataURLToFile(imageString, filename, mimeType) {
    const res = await fetch(imageString);
    const blob = await res.blob();
    return new File([blob], filename, {
        type: mimeType
    });
}

async function submitImage() {
    await cropImage();
    const imageFile = await dataURLToFile(cropImg.value, filename.value, mimeType.value);
    emit('update:modelValue', imageFile);
    modalOpen.value = false;
}

function removeImage() {
    cropImg.value = null;
    emit('update:modelValue', null);
}

function close() {
    cropImg.value = null;
    modalOpen.value = false;
    imgSrc.value = null;
    //   emit('close');
}
</script>
