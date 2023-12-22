import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import Label from '@proconvey/ui/src/components/Form/Label'
import { H1, H2, H3 } from '@proconvey/ui/src/components/Headers'
import { ChevronLeftIcon, LockIcon, PaymentIcons, StripeIcon } from '@proconvey/ui/src/icons'
import { CardName, CardNumber, CardExpiry, CardCvc, StripeWrapper } from '@proconvey/ui/src/components/StripeElements'
import { CardNumberElement, useElements, useStripe } from '@stripe/react-stripe-js'
import { graphql } from 'gql'
import formatCurrency from 'helpers/formatCurrency'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { useMutation, useQuery } from 'urql'
import { NextSeo } from 'next-seo'
import dayjs from 'dayjs'

const PaymentOnAccountInner = () => {
  const router = useRouter()
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState<string | undefined>()
  const stripe = useStripe()
  const elements = useElements()

  const propertyId = router.query.id as string

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const [stripePaymentSuccess, setStripePaymentSuccess] = useState(false)
  const [stripeResponse, setStripeResponse] = useState<any>({
    paymentIntent: {
      status: '',
      id: '',
      payment_method_types: [],
    },
  })

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query clientPaymentConveyancerDetails ($property_id: ID!) {
        property(id: $property_id) {
          id
          my_progress {
            payment {
              required
              paid
            }
          }
          conveyancer {
            id
            name
            payment_on_account_amount
          }
        }
      }
    `),
    variables: {
      property_id: propertyId,
    },
  })

  const [_createResult, createPaymentIntent] = useMutation(graphql(`
    mutation createPaymentOnAccountPaymentIntent($property_id: ID!) {
      createPaymentOnAccountPaymentIntent(property_id: $property_id)
    }
  `))

  const handlePayment = async () => {
    setError(undefined)
    setIsLoading(true)

    if (!stripe || !elements) {
      setIsLoading(false)
      return
    }

    // Create a new payment intent
    const paymentIntentResult = await createPaymentIntent({
      property_id: propertyId,
    })

    // Confirm the payment
    const result = await stripe.confirmCardPayment(
      paymentIntentResult.data!.createPaymentOnAccountPaymentIntent!,
      {
        payment_method: {
          card: elements.getElement(CardNumberElement)!,
          billing_details: {
            name: `${user!.first_name} ${user!.last_name}`,
          },
        },
      },
    )

    // Handle Stripe errors
    if (result.error) {
      setError(result.error.message)
      setIsLoading(false)
      return
    }

    if (result.paymentIntent?.status === 'succeeded') {
      setError(undefined)
      setStripePaymentSuccess(true)
      setStripeResponse(result)
    }


    setIsLoading(false)
  }

  return (
    <>
      <NextSeo
        title="Payment on account"
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="mt-[3.125rem]">
              <Link href={`/properties/${propertyId}`}>
                <Button variant="link" className="mb-[1.375rem]">
                  <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to overview
                </Button>
              </Link>
              <H1>Payment on account</H1>
            </div>

            <div className="mt-[1.75rem]">
              {
                !fetching && !data?.property.my_progress?.payment.required
                  ? <Alert variant="success">Payment on account not required for this property</Alert>
                  : null
              }

              {
                !fetching && data?.property.my_progress?.payment.required && data?.property?.my_progress?.payment.paid
                  ? <Alert variant="success">Payment on account already made for this property</Alert>
                  : null
              }

              {
                (fetching || (data?.property.my_progress?.payment.required && !data?.property.my_progress.payment.paid)) && (stripePaymentSuccess === false)
                  ? <>
                    <Card className="mb-[1.75rem]">
                      <div className="flex flex-col justify-between lg:flex-row gap-10 p-8">
                        <div className="w-full lg:w-1/2 bg-white lg:pr-8 order-2 lg:order-1">
                          <form>
                            <div className="flex flex-col gap-[1.25rem]">
                              <div className="mb-4">
                                <Label className="text-xl font-bold mb-2.5">Name on card</Label>
                                {
                                  fetching
                                    ? <Skeleton height={45} />
                                    : <CardName />
                                }
                              </div>
                              <div className="mb-4">
                                <Label className="text-xl font-bold mb-2.5">Card number</Label>
                                {
                                  fetching
                                    ? <Skeleton height={45} />
                                    : <CardNumber />
                                }
                              </div>
                              <div className="flex gap-[1.25rem] mb-4">
                                <div className="w-1/2">
                                  <Label className="text-xl font-bold mb-2.5">Expiration date</Label>
                                  {
                                    fetching
                                      ? <Skeleton width="100%" height={45} />
                                      : <CardExpiry />
                                  }
                                </div>
                                <div className="w-1/2">
                                  <Label className="text-xl font-bold mb-2.5">Security code</Label>
                                  {
                                    fetching
                                      ? <Skeleton width="100%" height={45} />
                                      : <CardCvc />
                                  }
                                </div>
                              </div>
                              <div className="mb-4">
                                <Label className="text-xl font-bold mb-2.5">Email</Label>
                                {
                                  fetching
                                    ? <Skeleton height={45} />
                                    : <input type="text" className="w-full h-[45px] border border-input rounded-lg px-3 py-[14px] transition focus:border-input-active focus:outline-none" />
                                }
                              </div>
                              {
                                error &&
                                <div className="mt-[1.25rem]">
                                  <Alert variant="danger">{error}</Alert>
                                </div>
                              }
                              <div className="flex items-center gap-4 mb-4">
                                <H2>Total: {formatCurrency(data?.property.conveyancer.payment_on_account_amount!)}</H2>
                              </div>
                              <div className="flex justify-center mt-[1.375rem]">
                                {
                                  fetching
                                    ? <Skeleton width={140} height={52} />
                                    : (
                                      <Button className="w-full" onClick={handlePayment} loading={isLoading}>
                                        <span className="flex text-lg items-center gap-4">Pay Now <LockIcon className="w-6 h-6" fill="#fff" /></span>
                                      </Button>
                                    )
                                }
                              </div>
                            </div>
                          </form>
                        </div>
                        <div className="w-full lg:w-1/2 bg-outlined lg:p-16 p-4 rounded-[0.625rem] order-1 lg:order-2">
                          <div>
                            <h2 className="text-3xl font-bold mb-4">Amount due</h2>
                            <h2 className="text-3xl font-bold mb-8">{formatCurrency(data?.property.conveyancer.payment_on_account_amount!)}</h2>
                            <p className="mb-8 text-body">This is the agreed-upon figure to enable your conveyancer to initiate the necessary legal work for your property transaction.</p>
                            <div className="flex items-center gap-4 mb-8">
                              <LockIcon className="w-6 h-6" />
                              <p>Guaranteed Safe & Secure checkout</p>
                            </div>
                            <div className="flex items-left gap-4 mb-8">
                              <StripeIcon className="flex-shrink-0" />
                            </div>
                            <div className="bg-white p-4 mb-8 rounded-[0.625rem]">
                              <p className="text-body text-sm">SSL is used to protect online transactions and ensure that confidential and sensitive information (e.g. credit card information, user login credentials, personal data) is encrypted and transmitted securely.</p>
                            </div>
                            <div className="lg:flex justify-left gap-4 mb-4">
                              <p className="text-body">We accept</p>
                              <PaymentIcons className="w-[278px] h-[26px]" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </Card>
                  </>
                  : null
              }

              {
                stripePaymentSuccess && !fetching && data?.property.my_progress?.payment.required && !data?.property.my_progress.payment.paid
                  ?
                  <>
                    <Card className="mb-[1.75rem]">
                      <Card.Header>
                        <H3>Payment successful</H3>
                      </Card.Header>
                      <hr />
                      <div className="flex flex-col justify-between lg:flex-row gap-10 p-8">
                        <div className="w-full lg:w-2/3 lg:pr-8 order-2 lg:order-2">
                          <div className="flex items-center gap-4 mb-4">
                            <p className="text-body text-body/60 w-40">Ref number</p>
                            <p className="text-body">{stripeResponse.paymentIntent?.id}</p>
                          </div>
                          <div className="flex items-center gap-4 mb-4">
                            <p className="text-body text-body/60 w-40">Payment date</p>
                            <p className="text-body">{dayjs().format('dddd, MMMM D, YYYY')}</p>
                          </div>
                          <div className="flex items-center gap-4 mb-4">
                            <p className="text-body text-body/60 w-40">Payment method</p>
                            <p className="text-body">{stripeResponse.paymentIntent?.payment_method_types}</p>
                          </div>
                          <div className="flex items-center gap-4 mb-4">
                            <p className="text-body text-body/60 w-40">Name on card</p>
                            <p className="text-body">
                              {user?.first_name} {user?.last_name}
                            </p>
                          </div>
                          <div className="flex items-center gap-4 mb-4">
                            <p className="text-body text-body/60 w-40">Email address</p>
                            <p className="text-body">
                              {user?.email}
                            </p>
                          </div>
                        </div>
                        <div className="w-full lg:w-1/3 lg:pr-8 order-3 lg:order-3">
                          <div className="flex flex-col gap-[1.25rem]">
                            <div className="flex justify-center mt-[1.375rem]">
                              <Link href={`/properties/${propertyId}`}>
                                <Button className=" w-30">
                                  <span className="flex text-lg items-center gap-4">Back to overview</span>
                                </Button>
                              </Link>
                            </div>
                          </div>
                        </div>
                      </div>
                      <hr />
                      <div className="px-5 py-[1.4375rem]">
                        <h3 className="text-[1.375rem] font-bold text-body">Total payment : {formatCurrency(data?.property.conveyancer.payment_on_account_amount!)}</h3>
                      </div>
                    </Card>
                  </>
                  : null
              }
            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

const PaymentOnAccount = () => {
  const router = useRouter()

  const propertyId = router.query.id as string

  const [{ data }] = useQuery({
    query: graphql(`
      query paymentOnAccountStripeAccountId($property_id: ID!) {
        property(id: $property_id) {
          id
          conveyancer {
            id
            stripe_account_id
          }
        }
      }
    `),
    variables: {
      property_id: propertyId,
    },
  })

  return (
    <StripeWrapper
      publishableKey={process.env.NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY!}
      accountId={data?.property.conveyancer.stripe_account_id ?? undefined}
      awaitAccountId
    >
      <PaymentOnAccountInner />
    </StripeWrapper>
  )
}

export default PaymentOnAccount
