<template>
    <div>
        <label for="fileUpload">Upload File</label>
        <input id="fileUpload" type="file" @change="handleFileChange" />
    </div>
</template>

<script>
export default {
    emits: ['update:modelValue'],
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file && file.size <= 8388608) { // 8 MB limit
                this.$emit('update:modelValue', file);
            } else {
                alert('File size should be less than 8 MB');
            }
        }
    }
};
</script>
