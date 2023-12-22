import Button from '@proconvey/ui/src/components/Button'
import { H1, H4 } from '@proconvey/ui/src/components/Headers'
import Link from 'next/link'
import SetupLayout from 'layouts/SetupLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useEffect, useState } from 'react'
import { graphql } from 'gql'
import { useMutation, useQuery } from 'urql'
import { useRouter } from 'next/router'
import { OnboardingLettersData } from 'components/OnboardingLetters'
import { NextSeo } from 'next-seo'
import HtmlEditor from '@proconvey/ui/src/components/HtmlEditor'
import { useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'

export default function ClientCareLetterPurchasePage () {
  const router = useRouter()

  const { setValue, getValues, watch } = useForm<OnboardingLettersData>({
    defaultValues: {
      client_care_letter: '',
      client_care_letter_sale: '',
      client_care_letter_purchase: '',
      client_care_letter_remortgage: '',
      terms_and_conditions: '',
      letter_header: '',
      letter_footer: '',
    },
  })

  const [letterData, setLetterData] = useState<OnboardingLettersData>({
    client_care_letter: '',
    client_care_letter_sale: '',
    client_care_letter_purchase: '',
    client_care_letter_remortgage: '',
    terms_and_conditions: '',
    letter_header: '',
    letter_footer: '',
  })

  const [{ data: defaultData }] = useQuery({
    query: graphql(`
      query onboardingLettersClientCareLetterPurchase {
        me {
          id
          conveyancer {
            id
            client_care_letter_purchase
          }
        }
      }
    `),
  })

  const [{ fetching }, updateConveyancerMutation] = useMutation(graphql(`
    mutation updateConveyancer ($input: UpdateConveyancerInput!) {
      updateConveyancer(input: $input) {
        id
      }
    }
  `))

  useEffect(() => {
    if (letterData) {
      const subscription = watch(value => setLetterData({ ...getValues(), ...value }))
      return () => subscription.unsubscribe()
    }
  }, [letterData, watch, getValues])

  useEffect(() => {
    if (defaultData) {
      setValue('client_care_letter_purchase', defaultData.me?.conveyancer?.client_care_letter_purchase ?? '')
    }
  }, [defaultData, setValue])

  const handleSubmit = async () => {
    const result = await updateConveyancerMutation({
      input: letterData,
    })

    if (result.error) {
      toast.error('Something went wrong, please try again later')
      return
    }

    router.push('/register/onboarding-letters')
  }

  return (
    <>
      <NextSeo
        title="Client Care Letter - Purchase"
      />
      <ProtectedLayout>
        <SetupLayout currentStep={4}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Client Care Letter (Purchase)</H1>
            </div>

            <div className="bg-white px-5 py-[1.4688rem] border border-b-0 border-primary border-opacity-10 rounded-t-[10px] ">
              <H4>Client care letter (Purchase)</H4>
            </div>
            <HtmlEditor onChange={v => setValue('client_care_letter_purchase', v)} />
            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/register/onboarding-letters">
                <Button variant="outlined">Back</Button>
              </Link>

              <Button loading={fetching} onClick={handleSubmit}>Done</Button>
            </div>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>
    </>
  )
}
