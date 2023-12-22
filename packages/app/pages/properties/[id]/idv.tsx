import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import { ChevronLeftIcon, CircleTickIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { NextSeo } from 'next-seo'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useEffect } from 'react'
import Skeleton from 'react-loading-skeleton'
import { useMutation, useQuery } from 'urql'

const IdVerification = () => {
  const router = useRouter()

  const propertyId = router.query.id as string

  const [{ fetching, data }, createIdvQrCode] = useMutation(graphql(`
    mutation createIdvQrCode($property_id: ID!) {
      createIdvQrCode(property_id: $property_id)
    }
  `))

  const [{ data: mobileData }, refetchMobileData] = useQuery({
    query: graphql(`
      query idvMobileConnected($property_id: ID!) {
        property(id: $property_id) {
          id
          my_progress {
            idv {
              completed
              mobile_connected
            }
          }
        }
      }
    `),
    variables: {
      property_id: propertyId,
    },
    pause: !data,
  })

  useEffect(() => {
    createIdvQrCode({
      property_id: propertyId,
    })
  }, [createIdvQrCode, propertyId])

  useEffect(() => {
    const interval = setInterval(() => {
      if (mobileData?.property?.my_progress?.idv?.completed) {
        clearInterval(interval)
        return
      }
      refetchMobileData()
    }, 10000) // 10 seconds
    return () => clearInterval(interval)
  }, [refetchMobileData, mobileData?.property?.my_progress?.idv?.completed])

  return (
    <>
      <NextSeo
        title="ID Verification"
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <Link href={`/properties/${propertyId}`}>
                <Button variant="link" className="mb-[1.375rem]">
                  <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to overview
                </Button>
              </Link>
              <H1>ID Verification</H1>
            </div>

            <Card>
              <Card.Body>
                {
                  mobileData?.property?.my_progress?.idv?.completed
                    ? <>
                      <Alert variant="success">Your ID has already been verified</Alert>
                    </>
                    : (
                      <div className="flex justify-between">
                        <div>
                          <H3>Scan the QR code on your phone</H3>

                          <p className="max-w-sm mt-[3.125rem]">
                            To scan the QR code, open your smartphone camera or the ProConvey app and hover over it.
                          </p>

                          {
                            fetching
                              ? null
                              : (
                                <p className="mt-[3.125rem] flex items-center">
                                  {
                                    mobileData?.property?.my_progress?.idv?.mobile_connected
                                      ? (
                                        <div className="space-y-[0.5rem]">
                                          <p className="flex items-center"><CircleTickIcon className="w-4 h-4 text-mint inline mr-[0.5rem]" /> Connected to mobile app</p>
                                          <p className="flex items-center"><LoadingSpinner className="inline mr-[0.5rem]" /> Waiting for verification...</p>
                                        </div>
                                      )
                                      : <><LoadingSpinner className="inline mr-[0.5rem]" /> Waiting for mobile app connection...</>
                                  }
                                </p>
                              )
                          }
                        </div>

                        {
                          fetching
                            ? <Skeleton width={400} height={400} />
                            : <div className="w-[400px] h-[400px]" dangerouslySetInnerHTML={{ __html: data?.createIdvQrCode ?? '' }} />
                        }
                      </div>
                    )
                }
              </Card.Body>
            </Card>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default IdVerification
