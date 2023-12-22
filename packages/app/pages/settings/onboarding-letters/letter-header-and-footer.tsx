import Button from '@proconvey/ui/src/components/Button'
import { H1, H4 } from '@proconvey/ui/src/components/Headers'
import Link from 'next/link'
import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { useEffect, useState } from 'react'
import { graphql } from 'gql'
import { useMutation, useQuery } from 'urql'
import { useRouter } from 'next/router'
import { OnboardingLettersData } from 'components/OnboardingLetters'
import { NextSeo } from 'next-seo'
import HtmlEditor from '@proconvey/ui/src/components/HtmlEditor'
import { useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'

export default function ClientCareLetter () {
  const router = useRouter()

  const { setValue, getValues, watch } = useForm<OnboardingLettersData>({
    defaultValues: {
      letter_header: '',
      letter_footer: '',
    },
  })

  const [{ data: defaultData }] = useQuery({
    query: graphql(`
      query onboardingLettersLetterHeaderAndFooter {
        me {
          id
          conveyancer {
            id
            letter_header
            letter_footer
          }
        }
      }
    `),
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

  useEffect(() => {
    if (defaultData) {
      setValue('letter_header', defaultData.me?.conveyancer?.letter_header ?? '')
      setValue('letter_footer', defaultData.me?.conveyancer?.letter_footer ?? '')
    }
  }, [defaultData, setValue])


  useEffect(() => {
    if (letterData) {
      const subscription = watch(value => setLetterData({ ...getValues(), ...value }))
      return () => subscription.unsubscribe()
    }
  }, [letterData, watch, getValues])

  const [{ fetching }, updateConveyancerMutation] = useMutation(graphql(`
    mutation updateConveyancer ($input: UpdateConveyancerInput!) {
      updateConveyancer(input: $input) {
        id
      }
    }
  `))

  const handleSubmit = async () => {
    const result = await updateConveyancerMutation({
      input: letterData,
    })

    if (result.error) {
      toast.error('Something went wrong, please try again later')
      return
    }

    router.push('/settings/onboarding')
  }

  return (
    <>
      <NextSeo
        title="Letter Header and Footer"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Letter Header and Footer</H1>
            </div>

            <div className="bg-white px-5 py-[1.4688rem] border border-b-0 border-primary border-opacity-10 rounded-t-[10px] ">
              <H4>Header</H4>
            </div>
            <HtmlEditor height={600} onChange={v => setValue('letter_header', v)} />

            <div className="bg-white px-5 py-[1.4688rem] border border-b-0 border-primary border-opacity-10 rounded-t-[10px] mt-5 ">
              <H4>Footer</H4>
            </div>
            <HtmlEditor height={600} onChange={v => setValue('letter_footer', v)} />
            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/settings/onboarding">
                <Button variant="outlined">Back</Button>
              </Link>

              <Button onClick={handleSubmit} loading={fetching}>Done</Button>
            </div>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
