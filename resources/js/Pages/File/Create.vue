<template>
    <div>
        <form @submit.prevent="handleSubmit">
            <FileNameInput :modelValue="form.name" @update:modelValue="form.name = $event" />
            <div v-if="errors.name" class="error">{{ errors.name }}</div>

            <FileUpload :modelValue="form.file" @update:modelValue="form.file = $event" />
            <div v-if="errors.file" class="error">{{ errors.file }}</div>

            <button type="submit">Save</button>
            <button v-if="isEdit" @click.prevent="confirmDelete">Delete</button>
        </form>
        <ConfirmModal v-if="showModal" @confirm="deleteFile" @cancel="showModal = false" />
    </div>
</template>

<script>
import { ref, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import FileNameInput from '@/Pages/File/FileNameInput.vue';
import FileUpload from '@/Pages/File/FileUpload.vue';
import ConfirmModal from '@/Pages/ConfirmModal.vue';
import { usePage } from '@inertiajs/inertia-vue3';

export default {
    components: { FileNameInput, FileUpload, ConfirmModal },
    setup() {
        const { props } = usePage();
        const isEdit = props.file ? true : false;
        const form = reactive({
            name: props.file ? props.file.name : '',
            file: null,
        });
        const errors = ref(props.errors || {});
        const showModal = ref(false);

        const handleSubmit = () => {
            const url = isEdit ? route('files.update', props.file.id) : route('files.store');
            const method = isEdit ? 'put' : 'post';
            Inertia.visit(url, {
                method,
                data: form,
                onError: (page) => {
                    errors.value = page.props.errors;
                },
                onFinish: () => {
                    if (isEdit) {
                        form.file = null;
                    }
                }
            });
        };

        const confirmDelete = () => {
            showModal.value = true;
        };

        const deleteFile = () => {
            Inertia.delete(route('files.destroy', props.file.id), {
                onSuccess: () => {
                    showModal.value = false;
                    Inertia.visit(route('files.index'));
                }
            });
        };

        return { form, handleSubmit, confirmDelete, deleteFile, showModal, isEdit, errors };
    }
};
</script>

<style>
.error {
    color: red;
}
</style>

