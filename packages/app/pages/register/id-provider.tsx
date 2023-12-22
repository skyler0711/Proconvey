import Button from '@proconvey/ui/src/components/Button'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import { IDProviderIcon, TickIcon } from '@proconvey/ui/src/icons'
import Link from 'next/link'
import SetupLayout from 'layouts/SetupLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { NextSeo } from 'next-seo'
import { useMutation } from 'urql'
import { graphql } from 'gql'
import { useState } from 'react'
import { IDProviders } from 'types/enums/IDProviders'
import { toast } from 'react-hot-toast'
import { useRouter } from 'next/router'
import { ProconveyIdProviderImage } from '@proconvey/ui/src/images'

export default function IDProviderPage () {
  const [isLoading, setIsLoading] = useState(false)
  const router = useRouter()
  const [idProvider, setIdProvider] = useState<IDProviders | undefined>(undefined)

  const [_, updateIDProviderMutation] = useMutation(graphql(`
    mutation updateIDProvider ($input: UpdateIDProviderInput!) {
      updateIDProvider(input: $input) 
    }
`))

  const handleSelectIDProvider = async () => {
    setIsLoading(true)
    const response = await updateIDProviderMutation({
      input: {
        provider: idProvider as string,
      },
    })

    if (response.error) {
      toast.error('Something went wrong, please try again later')
      setIsLoading(false)
    } else {
      setIsLoading(false)
      router.push('/register/team-members')
    }
  }

  return (
    <>
      <NextSeo
        title="ID Provider"
      />
      <ProtectedLayout>
        <SetupLayout currentStep={5}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Create your account</H1>
            </div>

            <div className="bg-white rounded-[0.625rem] border border-primary border-opacity-10">

              <div className="py-[1.5rem] px-[1.25rem]">
                <H3>ID checks</H3>
              </div>

              <hr className="mb-6" />

              <div className="px-[1.375rem]">
                <div className="flex flex-col justify-between lg:flex-row">
                  <div className="max-w-[45.875rem] w-full">
                    <p className="text-base text-body">Perform ID checks automatically for each client</p>
                    <br />
                    <p className="hidden text-base text-body text-opacity-80 lg:block">Select the ID provider below and your clients will automatically run through ID and AML checks.</p>
                  </div>

                  <IDProviderIcon className="w-full max-w-[16.625rem] mx-auto" />
                  <br className="lg:hidden" />
                  <p className="text-base text-body text-opacity-80 lg:hidden">Select the ID provider below and your clients will automatically run through ID and AML checks.</p>
                  <br />
                </div>
              </div>

              <H4 className="px-5 mb-[19px]">Select your ID provider</H4>

              <div className="grid grid-cols-1 gap-5 px-5 mb-5 sm:grid-cols-2 md:grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 ">
                <div className="flex flex-col items-center justify-center border rounded-lg">
                  <ProconveyIdProviderImage className="w-[219px] mt-[1.4375rem] mb-[1.875rem]" />
                  <div className="flex items-baseline ">
                    <p className="text-xl text-center text-body">£7.50</p>
                    <p className="text-[0.625rem] text-body text-opacity-80 font-thin">+VAT</p>
                  </div>
                  <p className="text-base text-center text-body text-opacity-80">per ID check</p>
                  {
                    idProvider === IDProviders.ProConvey ?
                      <div className=" px-8 py-4 my-[1.875rem] rounded-lg flex gap-[14px] items-center justify-center text-primary bg-secondary">
                        <TickIcon className="w-4 h-3 text-primary" />
                        <p className="text-base font-bold leading-5 rounded-lg">
                          Selected
                        </p>
                      </div>
                      :
                      <Button onClick={() => setIdProvider(IDProviders.ProConvey)} className="my-[1.875rem]">Select ID Provider</Button>
                  }
                </div>
              </div>

            </div>

            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/register/payments">
                <Button variant="outlined">Back</Button>
              </Link>

              <Button type="submit" loading={isLoading} disabled={!idProvider} onClick={handleSelectIDProvider}>Next</Button>
            </div>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>
    </>
  )
}
