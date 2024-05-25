<template>
    <div>
        <input v-model="search" placeholder="Search files..." @input="updateQuery" />
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Size (MB)</th>
                <th>Extension</th>
                <th>Preview</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="file in files.data" :key="file.id">
                <td>{{ file.name || file.original_name }}</td>
                <td>{{ (file.size / 1048576).toFixed(2) }}</td>
                <td>{{ file.extension }}</td>
                <td v-if="isImage(file.extension)">
                    <img :src="file.url" alt="Preview" style="width: 100px; height: 100px; object-fit: cover;" />
                </td>
                <td v-else></td>
                <td>
                    <a :href="file.url" download>Download</a>
                    <Link :href="route('files.edit', file.id)">Edit</Link>
                    <button @click="confirmDelete(file.id)">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
        <div>
            Total records: {{ files.total }} | Records on this page: {{ files.data.length }}
        </div>
        <button @click="previousPage" :disabled="!files.prev_page_url">Previous</button>
        <button @click="nextPage" :disabled="!files.next_page_url">Next</button>
        <ConfirmModal v-if="showModal" @confirm="deleteFile" @cancel="showModal = false" />
    </div>
</template>

<script>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/inertia-vue3';
import ConfirmModal from '@/Pages/ConfirmModal.vue';

export default {
    components: {
        ConfirmModal,
        Link
    },
    setup() {
        const { props } = usePage();
        const files = ref(props.files);
        const search = ref(props.search || '');
        const showModal = ref(false);
        const fileIdToDelete = ref(null);

        const updateQuery = () => {
            Inertia.get(route('files.index'), { search: search.value }, { preserveState: true });
        };

        const confirmDelete = (id) => {
            fileIdToDelete.value = id;
            showModal.value = true;
        };

        const deleteFile = () => {
            Inertia.delete(route('files.destroy', fileIdToDelete.value), {
                onSuccess: () => {
                    showModal.value = false;
                }
            });
        };

        const previousPage = () => {
            Inertia.get(files.value.prev_page_url, {}, { preserveState: true });
        };

        const nextPage = () => {
            Inertia.get(files.value.next_page_url, {}, { preserveState: true });
        };

        const isImage = (extension) => {
            return ['jpg', 'jpeg', 'png', 'gif'].includes(extension.toLowerCase());
        };

        return {
            files,
            search,
            showModal,
            updateQuery,
            confirmDelete,
            deleteFile,
            previousPage,
            nextPage,
            isImage
        };
    }
};
</script>
