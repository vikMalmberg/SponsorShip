<template>
    <portal to="modals">
        <div  v-show="open" class="fixed pin bg-black-20 flex justify-center items-center p-6">
            <div @focusout ="handleFocusOut" ref="modal" class="max-w-sm w-full  bg-white p-8 rounded-lg shadow-lg">
                <h2 class="font-semibold text-xl text-center mb-4">Complete your purchase</h2>
                <form action="#" method ="POST" @submit.prevent="handleSubmit ">
                    <label class="block mb-4">
                        <span class="block text-sm font-bold mb-2">Company</span>
                        <input v-model="form.companyName" ref="companyInput" class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="DigiTechnosoft Inc.">
                    </label>
                    <label class="block mb-4">
                        <span class="block text-sm font-bold mb-2">Email</span>
                        <input v-model="form.email" class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="ricardo@milos.br">
                    </label>
                    <label class="block mb-6">
                        <span class="block text-sm font-bold mb-2">Credit Card</span>
                        <stripe-elements ref="cardInput"></stripe-elements>
                        <!-- <input class="leading-normal text-black w-full block bg-grey-light rounded px-4 py-2" placeholder="ricardo@milos.br"> -->
                    </label>
                    <div>
                        <button class="block w-full  px-5 py-2 text-lg leading-normal font-bold text-white rounded bg-indigo mb-4" type="submit"
                        :class="{'opacity-50' : working , 'hover:bg-indigo-light' :!working, 'cursor-not-allowed': working}"
                        :disabled ="working"
                        >
                            <span v-if="!working" >Pay $ {{ amountInDollars }} now</span>
                            <span v-if="working" >Processing... </span>
                        </button>
                        <p class="text-grey-dark leading-normal text-center">We'll Reach out for your sponsorship information after you've confirmed your purchase.</p>
                    </div>
                </form>
            </div>
        </div>
    </portal>
</template>

<script>
    import formatNumber from 'accounting-js/lib/formatNumber'
    import StripeElements from './StripeElements.vue'
    export default {
        components: {
            StripeElements
        },
        props:['open', 'amount', 'selectedSlots'],

        data() {
            return {
                form: {
                    companyName: '',
                    email: '',
                },
                working:false,
            }
        },
        computed: {
            amountInDollars() {
                return formatNumber(this.amount / 100,{precision: 0})
            },
        },
        watch: {
            open(newValue) {
                if(newValue) {
                    setTimeout(() => {
                        this.$refs.companyInput.focus()
                    },100)
                }
            }
        },

        created() {
            const escapeListener = (e) => {
                if(e.key === 'Escape' ) {
                    this.$emit('close')

                }
            }

            document.addEventListener('keydown',escapeListener)
            this.$on('hook:beforeDestroy', () => {
                document.removeEventListener('keydown', escapeListener)
            })
        },
        methods: {
            handleFocusOut(e) {
                if (this.$refs.modal.contains(e.relatedTarget)) {
                    return
                }
                // this.$emit('close')
            },
            handleSubmit() {
                this.working = true,

                this.$refs.cardInput.createToken().then(token => {
                    return axios.post('/full-stack-radio/sponsorships', {
                        company_name : this.form.companyName,
                        email : this.form.email,
                        sponsorable_slots : this.selectedSlots,
                        payment_token : token.id,
                    })
                }).then(response => {
                    this.working = false,
                    console.log(response)
                })
            }
        },
    }

</script>
