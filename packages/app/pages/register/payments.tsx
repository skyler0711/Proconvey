import Button from '@proconvey/ui/src/components/Button'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import Link from 'next/link'
import { useState } from 'react'
import SetupLayout from 'layouts/SetupLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useRouter } from 'next/router'
import PaymentOnAccountSetup from 'components/PaymentOnAccountSetup'
import Card from '@proconvey/ui/src/components/Card'
import { NextSeo } from 'next-seo'
import { PaymentOnAccountImage } from '@proconvey/ui/src/images'

export default function Payments () {
  const router = useRouter()
  const [isConnected, setIsConnected] = useState(false)

  const stripeCode = router.query?.code as string | undefined

  const handleConnected = () => {
    setIsConnected(true)
    router.replace({
      query: null,
    })
  }

  const handleDisconnected = () => {
    setIsConnected(false)
  }

  return (
    <>
      <NextSeo title="Payments" />
      <ProtectedLayout>
        <SetupLayout currentStep={4}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Complete your account creation</H1>
            </div>

            <div className="mt-[1.5rem]">
              <Card>
                <Card.Header>
                  <H3 className="mb-[1.25rem]">Payment on account setup</H3>
                  <hr className="mb-6 -mx-5" />
                  <div className="flex flex-col justify-between lg:flex-row">

                    <div className="max-w-2xl flex flex-col">
                      <p className="text-base text-body">Connect or create a Stripe account to accept payment on account directly into your bank account</p>

                      {/* Desktop View */}
                      <br className="hidden lg:block" />
                      <ol type="1" className="hidden text-base lg:block text-body text-opacity-80">
                        <li><span className="inline-block w-[20px] text-right">1.</span> Connect your existing Stripe account instantly or create a new one (takes 5 minutes)</li>
                        <li><span className="inline-block w-[20px] text-right">2.</span> Receive funds instantly into your bank account via debit card or bank transfer</li>
                      </ol>
                      <br className="hidden lg:block" />
                      <p className="hidden text-base lg:block text-body text-opacity-80">Stripe handles Know Your Customer (KYC) obligations for payments and meets requirements for payments compliance</p>
                    </div>

                    <br className="lg:hidden" />
                    <PaymentOnAccountImage className="w-full max-w-[339px] h-full max-h-[247px] self-center" />
                    <br className="lg:hidden" />

                    {/* Mobile View */}
                    <ol type="1" className="text-base lg:hidden text-body text-opacity-80">
                      <li><span className="inline-block w-[20px] text-right">1.</span> Connect your existing Stripe account or create a new one</li>
                      <li><span className="inline-block w-[20px] text-right">2.</span> Receive funds instantly into your bank account</li>
                    </ol>
                    <br className="lg:hidden" />
                    <p className="text-base lg:hidden text-body text-opacity-80">Stripe handles Know Your Customer (KYC) obligations for payments and meets requirements for payments compliance</p>
                  </div>
                </Card.Header>


                <Card.Body>
                  <H4>Connect with Stripe</H4>

                  <PaymentOnAccountSetup
                    stripeCode={stripeCode}
                    onConnected={handleConnected}
                    onDisconnected={handleDisconnected}
                  />
                </Card.Body>
              </Card>
            </div>

            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/register/onboarding-letters">
                <Button variant="outlined">Back</Button>
              </Link>

              <div className="flex justify-end items-center gap-[2.5rem]">
                <Link href="/register/id-provider">
                  <Button variant="link">Skip</Button>
                </Link>

                <Link href="/register/id-provider">
                  <Button disabled={!isConnected}>Next</Button>
                </Link>
              </div>
            </div>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>
    </>
  )
}
