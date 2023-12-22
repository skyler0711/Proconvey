import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import Button from '@proconvey/ui/src/components/Button'
import { useState } from 'react'
import OnboardingLetters, { OnboardingLettersData } from 'components/OnboardingLetters'
import { useMutation } from 'urql'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import PaymentOnAccountSetup from 'components/PaymentOnAccountSetup'
import { useRouter } from 'next/router'
import Card from '@proconvey/ui/src/components/Card'
import Input from '@proconvey/ui/src/components/Form/Input'
import Label from '@proconvey/ui/src/components/Form/Label'
import toast from 'react-hot-toast'
import { useForm } from 'react-hook-form'
import { NextSeo } from 'next-seo'

type InputData = OnboardingLettersData & {
  payment_on_account_amount: number
}

export default function Overview () {
  const errorHandler = useErrorHandler()
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [currentType, setCurrentType] = useState(0)

  const { getValues, reset, watch, setError, setValue, formState: { errors } } = useForm<InputData>({
    defaultValues: {
      client_care_letter_sale: '',
      client_care_letter_purchase: '',
      client_care_letter_remortgage: '',
      terms_and_conditions: '',
      letter_header: '',
      letter_footer: '',
      payment_on_account_amount: 25000,
    },
  })

  const stripeCode = router.query?.code as string | undefined

  const handleConnected = () => {
    router.replace({
      query: null,
    })
  }

  const [_, updateConveyancerMutation] = useMutation(graphql(`
    mutation updateConveyancer ($input: UpdateConveyancerInput!) {
      updateConveyancer(input: $input) {
        id
      }
    }
  `))

  const handleSubmit = async () => {
    setIsLoading(true)

    const result = await updateConveyancerMutation({
      input: getValues(),
    })

    setIsLoading(false)

    if (result.error) {
      toast.error('Something went wrong. Please try again.')
      errorHandler(result.error, setError)
    } else {
      toast.success('Your changes have been saved')
    }
  }

  return (
    <>
      <NextSeo
        title="Onboarding Settings"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[1.875rem] flex flex-col sm:flex-row sm:items-center items-start gap-5 justify-between">
              <H1>Onboarding Settings</H1>
              <Button loading={isLoading} onClick={handleSubmit}>Save Changes</Button>
            </div>

            <div className="space-y-[1.875rem]">
              <OnboardingLetters onChange={v => reset({ ...getValues(), ...v })} currentType={currentType} setCurrentType={setCurrentType} />

              <Card>
                <Card.Header>
                  <H3>Payment on account setup</H3>
                </Card.Header>

                <hr />

                <Card.Body>
                  <H4>Connect with Stripe</H4>
                  <PaymentOnAccountSetup stripeCode={stripeCode} onConnected={handleConnected} />
                </Card.Body>

                <hr />

                <Card.Body>
                  <Label>Payment on account value</Label>
                  <Input
                    prefixIcon={<>£</>}
                    value={watch('payment_on_account_amount') / 100}
                    onChange={v => setValue('payment_on_account_amount', parseInt(v.target.value) * 100)}
                    error={errors.payment_on_account_amount?.message}
                  />
                </Card.Body>
              </Card>
            </div>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
