<template>
    <div
      class="el-input-tag input-tag-wrapper"
      :class="[size ? 'el-input-tag--' + size : '']"
      @click="focusTagInput">
      <el-tag
        v-for="(tag, idx) in innerTags"
        v-bind="$attrs"
        :key="tag"
        :size="size"
        :closable="!readOnly"
        :disable-transitions="false"
        @close="remove(idx)">
        {{tag}}
      </el-tag>
      <input
        ref="tag-input"
        v-if="!readOnly"
        class="tag-input"
        :placeholder="placeholder"
        @input="inputTag"
        :value="newTag"
        @keydown.delete.stop = "removeLastTag"
        @keydown = "addNew"
        @blur = "addNew"/>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
const props = defineProps({
    modelValue: {
        type: Array,
        default: () => []
    },
    addTagOnKeys: {
        type: Array,
        default: () => [13, 188, 9]
    },
    readOnly: {
        type: Boolean,
        default: false
    },
    size: {
        type : String,
        default : ''
    },
    placeholder: String,
});

const tagInput = ref()
const newTag = ref(null);
const innerTags = ref([...props.modelValue])

const emit = defineEmits(['update:modelValue']);

const focusTagInput = () => {
    if (props.readOnly || !tagInput.value) {
        return
    } else {
        tagInput.value.focus()
    }
}
// Watch for changes in the value prop 
watch(() => props.modelValue, (newValue) => {
    innerTags.value = [...newValue];
});

const inputTag = (ev) => {
    newTag.value = ev.target.value
}

const addNew = (e) => {
    if (e && (!props.addTagOnKeys.includes(e.keyCode)) && (e.type !== 'blur')) {
        return
    }

    if (e) {
        e.stopPropagation()
        e.preventDefault()
    }

    let addSuccess = false

    if (newTag.value.includes(',')) {
        newTag.value.split(',').forEach(item => {
            if (addTag(item.trim())) {
                addSuccess = true
            }
        })
    } else {
        if (addTag(newTag.value.trim())) {
            addSuccess = true
        }
    }
    if (addSuccess) {
        tagChange()
        newTag.value = ''
    }
}

const addTag = (tag) => {
    tag = tag.trim()
    if (tag && !innerTags.value.includes(tag)) {
        innerTags.value.push(tag)
        return true
    }
    return false
}

const remove = (index) => {
    innerTags.value.splice(index, 1)
    tagChange()
}
const removeLastTag = () => {
    if (newTag.value) {
        return
    }
    innerTags.value.pop()
    tagChange()
}
const tagChange = () => {
    emit('update:modelValue', innerTags.value)
}

</script>