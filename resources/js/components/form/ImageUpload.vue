<template>
    <div class="img-upload" :class="[size ? 'img-upload--' + size : '']">
        <div v-if="cropImg" class="img-upload__wrapper">
            <img :src="cropImg" class="w-full h-full object-fit" />
            <div class="img-upload__actions">
                <button type="button" @click="removeImage"
                    class="flex items-center me-2 justify-center w-8 h-8 bg-red-400 rounded-lg">
                    <icon icon="mingcute:close-fill" />
                </button>
            </div>
        </div>
        <div v-else class="img-upload__wrapper">
            <div class="img-upload__wrapper__input">
                <input type="file" name="image" accept="image/*" @change="onSelectImage"
                    class="absolute w-full h-full opacity-0 cursor-pointer" />
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" class="w-6 h-6 text-gray-600"
                    viewBox="0 0 24 24">
                    <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2Z"></path>
                </svg>
            </div>
        </div>
        <el-dialog v-model="modalOpen" :title="$t('common.adjust_image')" style="width:450px" center>
            <Cropper ref="cropper" class="twitter-cropper" background-class="twitter-cropper__background"
                foreground-class="twitter-cropper__foreground" image-restriction="stencil" :stencil-size="stencilSize"
                :stencil-props="{
                    lines: {},
                    handlers: {},
                    movable: false,
                    scalable: false,
                    aspectRatio: 1,
                    previewClass: 'twitter-cropper__stencil',
                }" :transitions="false" :canvas="true" :debounce="false" :default-size="defaultSize" :min-width="150"
                :min-height="150" :src="imgSrc" @change="onChange" />
            <cropper-navigate :zoom="zoom" @change="onZoom" />
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="modalOpen = false">
                        <Icon icon="mingcute:close-fill" class="me-2" />
                        {{ $t('common.cancel') }}
                    </el-button>
                    <el-button type="primary" @click="onSubmit">
                        <Icon icon="mingcute:check-fill" class="me-2" />
                        {{ $t('common.save') }}
                    </el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>

<style lang="scss">
.twitter-cropper {
    height: 400px;

    &__background {
        background-color: #edf2f4;
    }

    &__foreground {
        background-color: #edf2f4;
    }

    &__stencil {
        border: solid 5px rgb(29, 161, 242);
    }
}
</style>

<script setup>
import { ref, watch, onMounted } from 'vue';
import CropperNavigate from './CropperNavigate.vue';
import { Icon } from '@iconify/vue';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

const props = defineProps({
    size: {
        type: String,
        default: ''
    },
    ratio: {
        type: String,
        default: '1/1'
    },
    cropper: {
        type: Boolean,
        default: true,
    },
    modelValue: {
        type: [String, File]
    }
});

const imgSrc = ref(null);
const cropImg = ref(props.modelValue);
const filename = ref("");
const mimeType = ref("");
const modalOpen = ref(false);
const zoom = ref(0);
const cropper = ref(null);

watch(() => props.modelValue, (newValue) => {
    if (!(newValue instanceof File)) {
        cropImg.value = newValue;
    }
});

const emit = defineEmits(['update:modelValue', 'deleted']);
onMounted(() => {
    if (typeof props.modelValue === 'string' || props.modelValue instanceof String) {
        cropImg.value = props.modelValue;
    }
});

const onSelectImage = (e) => {
    const file = e.target.files[0];

    if (!file.type.includes('image/')) {
        alert('Please select an image file');
        return;
    }
    if (typeof FileReader === 'function') {
        const reader = new FileReader();

        reader.onload = (event) => {
            imgSrc.value = event.target.result;
        };

        reader.readAsDataURL(file);
    } else {
        alert('Sorry, FileReader API not supported');
    }

    filename.value = file.name
    mimeType.value = file.type
    if (props.cropper) {
        modalOpen.value = true;
    } else {
        cropImg.value = URL.createObjectURL(file);
        emit('update:modelValue', file);
    }
}

const removeImage = () => {
    emit('deleted', cropImg.value);
    cropImg.value = null;
    emit('update:modelValue', cropImg.value);
}

const onChange = (result) => {
    const {
        coordinates,
        imageSize
    } = cropper.value;
    if (imageSize.width / imageSize.height > coordinates.width / coordinates.height) {
        zoom.value = (imageSize.height - coordinates.height) / (imageSize.height - cropper.value.sizeRestrictions.minHeight);
    } else {
        zoom.value = (imageSize.width - coordinates.width) / (imageSize.width - cropper.value.sizeRestrictions.minWidth);
    }
}

const defaultSize = ({
    imageSize
}) => {
    return {
        width: Math.min(imageSize.height, imageSize.width),
        height: Math.min(imageSize.height, imageSize.width),
    };
}

const stencilSize = ({
    boundaries
}) => {
    return {
        width: Math.min(boundaries.height, boundaries.width) - 48,
        height: Math.min(boundaries.height, boundaries.width) - 48,
    };
}


const onZoom = (value) => {
    const {
        sizeRestrictions,
        imageSize
    } = cropper.value;
    if (imageSize.height < imageSize.width) {
        const minHeight = sizeRestrictions.minHeight;
        const imageHeight = imageSize.height;
        cropper.value.zoom((imageHeight - zoom.value * (imageHeight - minHeight)) / (imageHeight - value * (imageHeight - minHeight)));
    } else {
        const minWidth = sizeRestrictions.minWidth;
        const imageWidth = imageSize.width;
        cropper.value.zoom((imageWidth - zoom.value * (imageWidth - minWidth)) / (imageWidth - value * (imageWidth - minWidth)));
    }
}

// Helper function to convert data URL to File
const dataURLToFile = async (imageString, filename, mimeType) => {
    const res = await fetch(imageString);
    const blob = await res.blob();
    return new File([blob], filename, {
        type: mimeType
    });
};

// Function to handle the file submission
const onSubmit = async () => {
    const {
        canvas
    } = cropper.value.getResult();
    cropImg.value = canvas.toDataURL();
    const imageFileResponse = await dataURLToFile(canvas.toDataURL(), filename.value, mimeType.value);
    emit('update:modelValue', imageFileResponse);
    modalOpen.value = false;
};

</script>