<template>
<div @click="$emit('input', !selected)" class="bg-white rounded-lg shadow p-4 flex group border-2 border-transparent hover:border-indigo-light select-none cursor-pointer">
    <div class="w-3/4 flex items-center justify-between">
        <div class="flex items-center">
            <img class ="block h-16 w-16 mr-4 rounded" :src="sponsorableSlot.image_url">
            <div>
                <div class="text-lg font-bold">{{ sponsorableSlot.title }}</div>
                <div class="text-sm text-grey-dark">{{ dateForHumans }}</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-lg font-bold">${{ priceInDollars }}</div>
            <div class="text-sm text-grey-dark">USD</div>
        </div>
    </div>
    <div class="w-1/4  flex justify-end items-center">

        <span v-if="selected" class="block group-hover:hidden h-6 w-6 text-green"><i class="fas fa-check"></i></span>

        <span v-if="!selected" class="hidden group-hover:block h-6 w-6 text-indigo"><i class="fas fa-plus-circle"></i></span>
        <span v-if="!selected" class="block group-hover:hidden h-6 w-6 text-grey"><i class="fas fa-plus-circle"></i></span>
    </div>
</div>
</template>

<script>
    import parseDate from 'date-fns/parse'
    import formatDate from 'date-fns/format'
    import formatNumber from 'accounting-js/lib/formatNumber'

    export default {
        props:['sponsorableSlot', 'selected', ],
        computed: {
            dateForHumans() {
                return formatDate(parseDate(this.sponsorableSlot.publish_date), 'MMM D, YYYY')
            },
            priceInDollars() {
                return formatNumber(this.sponsorableSlot.price / 100, {precision: 0})
            }
        },
        methods: {

        },
    }


</script>
