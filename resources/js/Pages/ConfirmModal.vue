<template>
    <div class="modal-overlay">
        <div class="modal">
            <p>Are you sure you want to delete this file?</p>
            <button @click="$emit('confirm')">Confirm</button>
            <button @click="$emit('cancel')">Cancel</button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ConfirmModal'
};
</script>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
}
.modal {
    background: white;
    padding: 20px;
    border-radius: 10px;
}
</style>
