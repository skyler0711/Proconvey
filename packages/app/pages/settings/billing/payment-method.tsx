import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Button from '@proconvey/ui/src/components/Button'
import { ChevronLeftIcon } from '@proconvey/ui/src/icons'
import CardSetup from 'components/Billing/CardSetup'
import Link from 'next/link'
import { NextSeo } from 'next-seo'
import { StripeWrapper } from '@proconvey/ui/src/components/StripeElements'

export default function BillingPaymentMethod () {
  return (
    <>
      <NextSeo
        title="Add Payment Method"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem]">
              <div className="mb-[1.875rem]">
                <Link href="/settings/billing">
                  <Button variant="link" className="mb-[1.375rem]">
                    <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to Billing
                  </Button>
                </Link>
                <H1>Add Payment Method</H1>
              </div>

              <div className="mt-[1.25rem]">
                <StripeWrapper
                  publishableKey={process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!}
                >
                  <CardSetup />
                </StripeWrapper>
              </div>
            </div>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
