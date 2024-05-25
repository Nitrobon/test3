<template>
    <div>
        <label for="fileName">File Name</label>
        <input id="fileName" type="text" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" />
    </div>
</template>

<script>
export default {
    props: ['modelValue'],
};
</script>
