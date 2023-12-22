import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Link from 'next/link'
import SetupLayout from 'layouts/SetupLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useState } from 'react'
import { graphql } from 'gql'
import { CombinedError, useMutation } from 'urql'
import { useRouter } from 'next/router'
import OnboardingLetters, { OnboardingLettersData } from 'components/OnboardingLetters'
import { NextSeo } from 'next-seo'
import Modal from '@proconvey/ui/src/components/Modals'

export default function OnboardingLettersPage () {
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [errors, setErrors] = useState<CombinedError | undefined>(undefined)
  const [isSkipModalOpen, setIsSkipModalOpen] = useState<boolean>(false)
  const [isNextModalOpen, setIsNextModalOpen] = useState<boolean>(false)
  const [currentType, setCurrentType] = useState(0)

  const [letterData, setLetterData] = useState<OnboardingLettersData>({
    client_care_letter: '',
    client_care_letter_sale: '',
    client_care_letter_purchase: '',
    client_care_letter_remortgage: '',
    terms_and_conditions: '',
    letter_header: '',
    letter_footer: '',
  })

  const [_, updateConveyancerMutation] = useMutation(graphql(`
    mutation updateConveyancer ($input: UpdateConveyancerInput!) {
      updateConveyancer(input: $input) {
        id
      }
    }
  `))

  const handleSubmit = async () => {
    setErrors(undefined)
    setIsLoading(true)

    const result = await updateConveyancerMutation({
      input: letterData,
    })

    setIsLoading(false)

    if (result.error) {
      setErrors(result.error)
      return
    }

    if (letterData?.terms_and_conditions) {
      router.push('/register/payments')
      return
    }
    setIsNextModalOpen(true)
  }

  const handleSkip = async () => {
    if (letterData?.client_care_letter_sale || letterData?.client_care_letter_purchase || letterData?.client_care_letter_remortgage || !letterData?.terms_and_conditions) {
      router.push('/register/payments')
      return
    }
    setIsSkipModalOpen(true)
  }

  return (
    <>
      <NextSeo
        title="Onboarding Letters"
      />
      <ProtectedLayout>
        <SetupLayout currentStep={3}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Create your accounts</H1>
            </div>

            <div className="mt-[1.5rem]">
              <OnboardingLetters onChange={setLetterData} formErrors={errors} currentType={currentType} setCurrentType={setCurrentType} />
            </div>

            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/register/business">
                <Button variant="outlined">Back</Button>
              </Link>

              <div className="flex justify-end items-center gap-[2.5rem]">
                {/* <Link href="/register/payments"> */}
                <Button variant="link" onClick={handleSkip}>Skip</Button>
                {/* </Link> */}
                { currentType >= 3 ? <Button loading={isLoading} onClick={handleSubmit}>Next</Button> : <Button loading={isLoading} onClick={handleSubmit} className="text-opacity-50" disabled>Next</Button>}
              </div>
            </div>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>

      <Modal isOpen={isSkipModalOpen} onClose={() => setIsSkipModalOpen(false)}>
        <Modal.Title>Skip Confirmation</Modal.Title>
        <Modal.Content>
          Are you sure you dont want to add your client care letter and terms and conditions?
        </Modal.Content>
        <Modal.Footer>
          <Button size="small" variant="secondary" onClick={() => setIsSkipModalOpen(false)}>No</Button>
          <Button size="small" onClick={() => router.push('/register/payments')}>Yes</Button>
        </Modal.Footer>
      </Modal>
      <Modal isOpen={isNextModalOpen} onClose={() => setIsNextModalOpen(false)}>
        <Modal.Title>Are you sure?</Modal.Title>
        <Modal.Content>
          Are you sure you dont want to add terms and conditions? You can copy and paste your terms and conditions here.
        </Modal.Content>
        <Modal.Footer>
          <Button size="small" variant="secondary" onClick={() => setIsNextModalOpen(false)}>No</Button>
          <Button size="small" onClick={() => router.push('/register/payments')}>Yes</Button>
        </Modal.Footer>
      </Modal>
    </>
  )
}
