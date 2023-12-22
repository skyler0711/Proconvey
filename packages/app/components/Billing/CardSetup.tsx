import AddressFinder from '@proconvey/ui/src/components/AddressFinder'
import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import Form from '@proconvey/ui/src/components/Form'
import Group from '@proconvey/ui/src/components/Form/Group'
import Input from '@proconvey/ui/src/components/Form/Input'
import Label from '@proconvey/ui/src/components/Form/Label'
import { H3 } from '@proconvey/ui/src/components/Headers'
import { CardCvc, CardExpiry, CardNumber } from '@proconvey/ui/src/components/StripeElements'
import { CardNumberElement, useElements, useStripe } from '@stripe/react-stripe-js'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import { useRouter } from 'next/router'
import { useState } from 'react'
import { useForm } from 'react-hook-form'
import { useMutation } from 'urql'

type Inputs = {
  name: string
  card_number: boolean
  card_cvv: boolean
  card_expiry_date: boolean
  email: string
  address: {
    line_1: string
    line_2?: string | null
    city: string
    postcode: string
  }
}

const CardSetup = () => {
  const { register, handleSubmit, setError, setValue, formState: { errors }, clearErrors } = useForm<Inputs>({
    defaultValues: {
      name: '',
      email: '',
      address: {
        line_1: '',
        line_2: '',
        city: '',
        postcode: '',
      },
    },
  })

  const stripe = useStripe()
  const elements = useElements()
  const [isLoading, setIsLoading] = useState(false)
  const [generalError, setGeneralError] = useState<string | undefined>()
  const router = useRouter()
  const errorHandler = useErrorHandler()

  const [_createResult, createSetupIntent] = useMutation(graphql(`
    mutation createSetupIntent ($input: CreateSetupIntentInput!) {
      createSetupIntent(input: $input)
    }
  `))

  const [_completeResult, completeSetupIntent] = useMutation(graphql(`
    mutation completeSetupIntent ($input: CompleteSetupIntentInput!) {
      completeSetupIntent(input: $input)
    }
  `))

  const addPaymentMethod = async (data: Inputs) => {
    clearErrors()
    setGeneralError(undefined)
    setIsLoading(true)

    if (!stripe || !elements) {
      setIsLoading(false)
      return
    }

    // Create a new setup intent
    const setupIntentResult = await createSetupIntent({
      input: {
        email: data.email,
        address: data.address,
        name: data.name,
        card_expiry_date: data.card_expiry_date,
        card_cvv: data.card_cvv,
        card_number: data.card_number,
      },
    })

    if (setupIntentResult.error) {
      setIsLoading(false)
      errorHandler(setupIntentResult.error, setError)
      return
    }

    // Confirm the card setup
    const result = await stripe.confirmCardSetup(
      setupIntentResult.data!.createSetupIntent,
      {
        payment_method: {
          card: elements.getElement(CardNumberElement)!,
          billing_details: {
            name: data.name,
            email: data.email,
            address: {
              line1: data.address.line_1,
              line2: data.address.line_2 ?? undefined,
              city: data.address.city,
              postal_code: data.address.postcode,
              country: 'GB',
            },
          },
        },
      },
    )

    // Handle Stripe errors
    if (result.error) {
      clearErrors()
      setGeneralError(result.error.message)
      setIsLoading(false)
      return
    }

    // Complete with api
    await completeSetupIntent({
      input: {
        payment_method: result.setupIntent.payment_method as string,
        email: data.email,
      },
    })

    router.push('/settings/billing')

    setIsLoading(false)
  }

  return (
    <>
      <Card>
        <Card.Header>
          <H3>Set Up Credit Card</H3>
        </Card.Header>
        <hr />
        <Card.Body>
          <Form>
            <Group>
              <Input label="Cardholder name" placeholder="Enter cardholder name" {...register('name')} error={errors?.name?.message} />
            </Group>

            <Group>
              <div className="flex gap-[1.25rem] w-full">
                <div className="w-1/2">
                  <Label>Card number</Label>
                  <CardNumber error={errors?.card_number?.message} onChange={a => {
                    clearErrors('card_number')
                    setValue('card_number', a.complete)
                  }} />
                </div>
                <div className="flex w-1/2 gap-[1.25rem]">
                  <div className="w-full">
                    <Label>Expiry date</Label>
                    <CardExpiry error={errors?.card_expiry_date?.message} {...register('card_expiry_date')} onChange={a => {
                      clearErrors('card_expiry_date')
                      setValue('card_expiry_date', a.complete)
                    }} />
                  </div>
                  <div className="w-full">
                    <Label>CVV code</Label>
                    <CardCvc error={errors?.card_cvv?.message} {...register('card_cvv')} onChange={a => {
                      clearErrors('card_cvv')
                      setValue('card_cvv', a.complete)
                    }} />
                  </div>
                </div>
              </div>
            </Group>

            <Group>
              <Input label="Email address for billing" type="email" placeholder="name@company.com" {...register('email')} error={errors?.email?.message} />
            </Group>

            <Label>Billing address</Label>
            <AddressFinder error={errors.address} onChange={a => {
              clearErrors('address')
              setValue('address', a)
            }} />
          </Form>

          {
            (Object.keys(errors).length > 0 || generalError) &&
            <div className="mt-[1.875rem]">
              <Alert variant="danger">There was a problem. Please try again.</Alert>
            </div>
          }
        </Card.Body>
      </Card>

      <div className="mt-[2.75rem] flex justify-end">
        <Button type="submit" onClick={handleSubmit(addPaymentMethod)} loading={isLoading}>
          Save payment method
        </Button>
      </div>
    </>
  )
}

export default CardSetup
