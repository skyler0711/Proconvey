import Alert from '@proconvey/ui/src/components/Alert'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import Upload from '@proconvey/ui/src/components/Form/Upload'
import { H1 } from '@proconvey/ui/src/components/Headers'
import { ChevronLeftIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import useUpload from 'hooks/useUpload'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { NextSeo } from 'next-seo'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useEffect, useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import { useMutation, useQuery } from 'urql'

const SourceOfFunds = () => {
  const router = useRouter()
  const { uploadFiles } = useUpload()

  const [files, setFiles] = useState<File[] | string[] | undefined>()
  const [fileError, setFileError] = useState<string | undefined>()
  const [isUploading, setIsUploading] = useState(false)

  const errorHandler = useErrorHandler()

  const propertyId = router.query.id as string

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query sofProgress($property_id: ID!) {
        property(id: $property_id) {
          id
          my_progress {
            sof {
              required
              completed
              files {
                id
                name
              }
            }
          }
        }
      }
    `),
    variables: {
      property_id: propertyId,
    },
  })

  const [_uploadResult, uploadDocuments] = useMutation(graphql(`
    mutation uploadSofCheckDocuments($property_id: ID!, $input: UploadSofCheckDocumentsInput!) {
      uploadSofCheckDocuments(property_id: $property_id, input: $input) {
        id
      }
    }
  `))

  useEffect(() => {
    if (data) {
      setFiles(data?.property?.my_progress?.sof?.files?.map(f => f.name) as string[])
    }
  }, [data])

  const handleSubmit = async () => {
    setIsUploading(true)
    setFileError(undefined)

    if (files) {
      const uploads = (await Promise.all(
        files.map(async (file) => {
          return file instanceof File
            ? uploadFiles([file])
            : null
        }),
      ) ?? []).filter(f => f !== null)

      if (files.length !== 0 && uploads.length === 0) {
        // Set timeout to give the illusion of loading however nothing should be uploaded
        await new Promise(resolve => setTimeout(resolve, 400))
      } else {
        const response = await uploadDocuments({
          property_id: propertyId,
          input: {
            documents: uploads.map(upload => ({
              key: upload.key,
              extension: upload.extension,
            })),
          },
        })

        // Handle Error Response
        if (response.error) {
          errorHandler(response.error, (_: any, error: { message: string }, __: any) => {
            setFileError(error.message)
          })
        }
      }
    }

    setIsUploading(false)

    router.push(`/properties/${propertyId}`)
  }

  return (
    <>
      <NextSeo
        title="Source of Funds Check"
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
              <H1>Source of Funds Check</H1>
            </div>

            <Card>
              <Card.Body>
                {
                  data?.property?.my_progress?.sof?.completed
                    ? <>
                      <Alert variant="success">Your source of funds check has already been completed</Alert>
                    </>
                    : (
                      <div>
                        {
                          fetching
                            ? <Skeleton width="100%" height={500} />
                            : <>
                              <Upload
                                onChange={files => {
                                  setFiles(typeof files === 'string' ? [files] : files)
                                  if (fileError) {
                                    setFileError(undefined)
                                  }
                                }}
                                label="Please upload a copy of your bank statement or other proof of funds to show that you have the funds available to purchase this property."
                                value={
                                  (files === undefined || files === null)
                                    ? undefined
                                    : files.map(file => typeof file === 'string'
                                      ? { name: file }
                                      : file,
                                    )
                                }
                                error={fileError}
                              />

                              <div className="flex justify-end">
                                <Button
                                  variant="primary"
                                  className="mt-4"
                                  onClick={handleSubmit}
                                  loading={isUploading}
                                >
                                  Submit
                                </Button>
                              </div>
                            </>
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

export default SourceOfFunds
