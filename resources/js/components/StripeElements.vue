<template>
    <div></div>
</template>

<script>
    export default {
        props: [],
          mounted() {
            this.stripe = Stripe('tokenkey')
            this.elements = this.stripe.elements()
            // Custom styling can be passed to options when creating an Element.
            var style = {
            }

            // Create an instance of the card Element.
            this.card = this.elements.create('card', {
              style: {
                base: {
                // Add your base input styles here. For example:
                fontSize: '16px',
                fontSmoothing: 'antialiased',
                color: 'rgb(70, 85, 104)',
                '::placeholder' : {
                    color: 'rgba(70, 85, 104, .5)',
                }
              }
            }
        })

            // Add an instance of the card Element into the `card-element` <div>.
            this.card.mount(this.$el)
        },
        methods: {
            createToken() {
            return this.stripe.createToken(this.card).then(function(result) {
                if (result.error) {
                    console.log(result.error)
                    // Inform the customer that there was an error.
                    // var errorElement = document.getElementById('card-errors')
                    // errorElement.textContent = result.error.message
                    } else {
                        return result.token

                        // Send the token to your server.
                        // stripeTokenHandler(result.token)
                    }
                })
            }
        }
    }
</script>
