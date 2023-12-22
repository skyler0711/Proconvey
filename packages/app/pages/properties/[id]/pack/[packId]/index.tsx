import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import { ChevronLeftIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { NextSeo } from 'next-seo'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useEffect } from 'react'
import { useMutation, useQuery } from 'urql'

const PackSigning = () => {
  const router = useRouter()

  const propertyId = router.query.id as string
  const packId = router.query.packId as string

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query packPropertyPack($id: ID!) {
        property(id: $id) {
          id
          active_forms {
            id
            name
            pivot {
              ... on ActiveFormsPivot {
                id
                title
              }
            }
          }
        }
      }
    `),
    variables: {
      id: propertyId,
    },
  })

  const [{ fetching: isLoading, data: formData, error }, createFormSigningUrl] = useMutation(graphql(`
    mutation createFormSigningUrl($property_id: ID!, $form_id: ID!) {
      createFormSigningUrl(property_id: $property_id, form_id: $form_id )
    }
  `))


  const currentForm = data?.property?.active_forms.filter((item) => item.id === router.query.packId)?.[0]!
  const formName = currentForm?.pivot?.title ?? currentForm?.name

  useEffect(() => {
    createFormSigningUrl({
      property_id: propertyId,
      form_id: packId,
    })
  }, [createFormSigningUrl, propertyId, packId])

  return (
    <>
      <NextSeo
        title={formName || 'Loading...'}
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <Link href={`/properties/${propertyId}/pack`}>
                <Button variant="link" className="mb-[1.375rem]">
                  <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to all forms
                </Button>
              </Link>
              <H1>{formName}</H1>
            </div>

            <div className="mt-[1.75rem] flex flex-grow flex-col">
              {
                fetching || isLoading
                  ? (
                    <div className="flex items-center justify-center h-full">
                      <LoadingSpinner />
                    </div>
                  )
                  : (
                    error
                      ? (
                        <Alert variant="danger">
                          There was a problem fetching this document.
                        </Alert>
                      )
                      : (
                        formData?.createFormSigningUrl
                          ? (
                            <iframe
                              title="onboarding-letters"
                              src={formData?.createFormSigningUrl}
                              className="w-full h-full"
                            />
                          )
                          : (
                            <Alert variant="success">
                              This document has already been signed.
                            </Alert>
                          )
                      )
                  )
              }
            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default PackSigning
