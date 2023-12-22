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
import React, { useEffect } from 'react'
import { useMutation } from 'urql'

const GiftorDeclaration = () => {
  const router = useRouter()
  const propertyId = router.query.id as string

  const [{ fetching, data, error }, createUrl] = useMutation(graphql(/* GraphQL */`
    mutation createGiftorDeclarationSigningUrl($property_id: ID!) {
      createGiftorDeclarationSigningUrl(property_id: $property_id)
    }
  `))

  useEffect(() => {
    createUrl({
      property_id: propertyId,
    })
  }, [createUrl, propertyId])

  return (
    <>
      <NextSeo title="Giftor Declaration" />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>

            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <Link href={`/properties/${propertyId}`}>
                <Button variant="link" className="mb-[1.375rem]">
                  <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to overview
                </Button>
              </Link>
              <H1>Gifted Giftor Declaration</H1>
            </div>

            <div className="mt-[1.75rem] flex flex-grow flex-col">
              {
                fetching
                  ? <div className="flex items-center justify-center h-full">
                    <LoadingSpinner />
                  </div>
                  : error
                    ? <Alert variant="danger">
                      There was a problem fetching this document.
                    </Alert>
                    : data?.createGiftorDeclarationSigningUrl
                      ? <iframe title="giftor-declaration" src={data?.createGiftorDeclarationSigningUrl} className="w-full h-full" />
                      : <Alert variant="success">
                        This document has already been signed.
                      </Alert>
              }
            </div>

          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default GiftorDeclaration
