<template>
    <button @click="modalOpen = true" class="rounded px-5 py-2 text-lg leading-normal font-bold text-white rounded bg-indigo hover:bg-indigo-light" type="button">
        <slot></slot>
        <complete-purchase-modal :open="modalOpen" @close="modalOpen = false" :amount="amount" :selected-slots="selectedSlots" ></complete-purchase-modal>
    </button>
</template>

<script>
    import CompletePurchaseModal from './CompletePurchaseModal.vue'

    export default {
        components: {
            CompletePurchaseModal,
        },
        props: ['amount', 'selectedSlots'],

        data() {
            return {
                modalOpen:false
            }
        },
        methods : {

        },
    }


</script>
