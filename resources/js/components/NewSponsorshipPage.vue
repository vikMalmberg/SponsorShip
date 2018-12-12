<template>
<div>
<div class="bg-grey-light min-h-screen">
    <div class="max-w-md mx-auto px-6 pt-16 pb-24">
        <h1 class="text-2xl text-center font-semibold mb-8 ">Which episodes would you like to sponsor</h1>
        <ul class="list-reset">
            <li class="mb-4" v-for="sponsorableSlot in sponsorableSlots" :key="sponsorableSlot.id">
                <sponsorable-slot :sponsorableSlot="sponsorableSlot"
                    :selected="selectedSlots.includes(sponsorableSlot.id )"
                    @input="handleSponsorableSlotInput(sponsorableSlot, $event)"
                ></sponsorable-slot>
            </li>
        </ul>
    </div>
    <div class="bg-white border-t-2 border-grey fixed pin-b pin-x">
        <div class="max-w-md mx-auto px-6 py-6">
            <div class="flex justify-between items-center">
                <div class="w-3/4 flex justify-between items-center">
                    <div class="text-lg">{{ selectedSlots.length }} {{ selectedSlots.length ===1 ? 'episode' : 'episodes' }}</div>
                    <div class="text-right">
                        <div class="text-lg font-bold">${{ totalInDollars }}</div>
                        <div class="text-sm text-grey-dark">USD</div>
                    </div>
                </div>
                <div class="w-1/4 flex justify-end items-center">
                    <button class="rounded px-5 py-2 text-lg leading-normal font-bold text-white rounded bg-indigo hover:bg-indigo-light" type="button">Pay Now</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div v-show="false" class="fixed pin bg-black-20 flex justify-center items-center p-6">
    <div class="max-w-sm w-full  bg-white p-8 rounded-lg shadow-lg">
        <h2 class="font-semibold text-xl text-center mb-4">Complete your purchase</h2>
        <form action="#" method ="POST">
            <label class="block mb-4">
                <span class="block text-sm font-bold mb-2">Company</span>
                <input class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="DigiTechnosoft Inc.">
            </label>
            <label class="block mb-4">
                <span class="block text-sm font-bold mb-2">Email</span>
                <input class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="ricardo@milos.br">
            </label>
            <label class="block mb-6">
                <span class="block text-sm font-bold mb-2">Credit Card</span>
                <input class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="ricardo@milos.br">
            </label>
            <div>
                <button class="block w-full  px-5 py-2 text-lg leading-normal font-bold text-white rounded bg-indigo hover:bg-indigo-light mb-4" type="submit">
                    Pay $1500 now
                </button>
                <p class="text-grey-dark leading-normal text-center">We'll Reach out for your sponsorship information after you've confirmed your purchase.</p>
            </div>
        </form>
    </div>
</div>
</div>
</template>

<script>
    import SponsorableSlot from './SponsorableSlot'
    import _ from 'lodash'
    import formatNumber from 'accounting-js/lib/formatNumber'

    export default {
        components:{
            SponsorableSlot,

        },
        data() {
            return {
                selectedSlots: [2,3],
                sponsorableSlots: [
                    {
                        id: 1,
                        title: 'Full Stack Radio Episode 90',
                        publish_date : '2018-06-06',
                        price: 50000,
                        image_url : 'https://media.simplecast.com/podcast/image/279/small_1413649662-artwork.jpg'
                    },
                    {
                        id: 2,
                        title: 'Full Stack Radio Episode 91',
                        publish_date : '2018-06-20',
                        price: 45000,
                        image_url : 'https://media.simplecast.com/podcast/image/279/small_1413649662-artwork.jpg'
                    },
                    {
                        id: 3,
                        title: 'Full Stack Radio Episode 92',
                        publish_date : '2018-07-04',
                        price: 55000,
                        image_url : 'https://media.simplecast.com/podcast/image/279/small_1413649662-artwork.jpg'
                    },
                    {
                        id: 4,
                        title: 'Full Stack Radio Episode 93',
                        publish_date : '2018-07-18',
                        price: 60000,
                        image_url : 'https://media.simplecast.com/podcast/image/279/small_1413649662-artwork.jpg'
                    },
                ],
            }
        },
        computed: {
            totalInDollars() {
                return formatNumber(this.total / 100,{precision: 0})
            },
            total() {
                return this.sponsorableSlots.filter((slot) => this.selectedSlots.includes(slot.id)).reduce((total,slot) => {
                    return total + slot.price
                },0)
            }
        },
        methods: {
            handleSponsorableSlotInput(sponsorableSlot, newValue) {
                if (newValue) {
                    this.selectedSlots = [...this.selectedSlots, sponsorableSlot.id]
                } else {
                    this.selectedSlots = _.without(this.selectedSlots, sponsorableSlot.id)

                }
            }
        }
    }
</script>
