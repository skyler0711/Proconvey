import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import Form from '@proconvey/ui/src/components/Form'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import IconButton from '@proconvey/ui/src/components/IconButton'
import Modal from '@proconvey/ui/src/components/Modals'
import PDFViewer from '@proconvey/ui/src/components/PDFViewer'
import Table from '@proconvey/ui/src/components/Table'
import { CircleTickIcon, DownloadIcon, PenIcon, CircleUnTickIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import { Media } from 'gql/graphql'
import useDownload from 'hooks/useDownload'
import useErrorHandler from 'hooks/useErrorHandler'
import useUpload from 'hooks/useUpload'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useRouter } from 'next/router'
import { useRef, useEffect, useState, SetStateAction, useMemo } from 'react'
import Skeleton from 'react-loading-skeleton'
import PackComplete from 'components/PackComplete'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { useMutation, useQuery } from 'urql'
import toast from 'react-hot-toast'
import Alert from '@proconvey/ui/src/components/Alert'
import useMedia from 'hooks/useMedia'

type UploadAdditionalDocumentsProps = {
  uploaded_document: File[]
  document_name: string | null
  upload_document: string | null
  file_id: string
}

type ReuploadDocObject = {
  id: string | null
  name: string | null
}

const Documents = () => {
  const router = useRouter()
  const download = useDownload()
  const [viewDoc, setViewDoc] = useState<Media | undefined>()
  const [complete, setComplete] = useState<Boolean>(false)
  const [addDoc, setAddDoc] = useState<boolean>(false)
  const [isUploading, setIsUploading] = useState<boolean>(false)
  const [isReuploading, setIsReuploading] = useState<boolean>(false)
  const [reuploadDoc, setReuploadDoc] = useState<boolean>(false)
  const [docPreview, setDocPreview] = useState<string>('')
  const [reuploadDocData, setReuploadDocData] = useState<ReuploadDocObject>({
    id: null,
    name: null,
  })

  const uploadAdditionalDocumentRef = useRef<any>(null)
  const { uploadFiles } = useUpload()
  const errorHandler = useErrorHandler()

  const {  mediaQueryId, media, getMedia, downloadMedia, isLoading: isMediaLoading } = useMedia()
  const propertyId = router.query.id as string

  const [{ data, fetching }, refetch] = useQuery({
    query: graphql(`
      query clientPropertyDocuments($id: ID!) {
        property(id: $id) {
          id
          address {
            line_1
            line_2
            city
            postcode
          }
          documents {
            id
            name
            custom_properties
          }
          active_forms {
            id
            pivot {
              ... on ActiveFormsPivot {
                id
              }
            }
            name
            image {
              id
              url
            }
            description
            group
            sections {
              id
              steps {
                id
                question
                answers {
                  id
                }
              }
            }
          }
          my_progress {
            pack_progress {
              completed
            }
            provided_answers {
              id
              value
              answer {
                id
                type
                step {
                  id
                  question
                  section {
                    id
                    form {
                      id
                      name
                      group
                    }
                  }
                }
              }
            }
          }
          users {
            id
            first_name
            last_name
            pivot {
              ...on PropertyUserPivot {
                role
              }
            }
            email
            invite_code_sent_at
          }
        }
      }
    `),
    variables: {
      id: propertyId,
    },
  })

  const outStandingDocuments = useMemo(() => {
    if (data && data.property && data.property.my_progress && data.property.my_progress.provided_answers) {
      return (data?.property?.my_progress?.provided_answers
        .filter((answerPayload) => answerPayload.value === 'Add later' && answerPayload.answer.type === 'File')
        .map((answerPayload) => (
          {
            url: `/properties/${data.property.id}/forms/${answerPayload.answer.step.section.form.id}/sections/${answerPayload.answer.step.section.id}/steps/${answerPayload.answer.step.id}`,
            name: answerPayload.answer.step.question,
            providedAnswer: answerPayload.value,
          }
        ))
      )
    }
    return []
  }, [data])

  useEffect(() => {
    if (data?.property.my_progress) {
      setComplete(data?.property.my_progress.pack_progress.completed)
    }
  }, [data])

  const [, uploadAdditionalDocumentsMutation] = useMutation(graphql(`
    mutation uploadAdditionalDocuments ($property_id: ID!, $input: UploadAdditionalDocumentsInput!) {
      uploadAdditionalDocuments(property_id: $property_id, input: $input) {
        name
      }
    }
  `))

  const [, reuploadAdditionalDocumentsMutation] = useMutation(graphql(`
  mutation reuploadAdditionalDocuments ($property_id: ID!, $input: ReuploadAdditionalDocumentsInput!) {
    reuploadAdditionalDocuments(property_id: $property_id, input: $input) {
      name
    }
  }
  `))

  const { register, handleSubmit, control, watch, reset, setError, formState: { errors }, clearErrors } = useForm<UploadAdditionalDocumentsProps>({
    defaultValues: {
      uploaded_document: undefined,
    },
  })

  const watchFields = watch(['document_name', 'uploaded_document']) // you can also target specific fields by their names

  const uploadAdditionalDocument: SubmitHandler<UploadAdditionalDocumentsProps> = async (form) => {
    setIsUploading(true)
    clearErrors()
    let upload = await uploadFiles(form.uploaded_document)
    const uploadAdditionalDocuments = await uploadAdditionalDocumentsMutation({
      property_id: propertyId,
      input: {
        name: form.document_name,
        ...upload && {
          uploaded_document: {
            key: upload.key,
            extension: upload.extension,
          },
        },
      },
    })

    if (uploadAdditionalDocuments.error) {
      errorHandler(uploadAdditionalDocuments.error, setError)
      toast.error(`There was a problem uploading ${form.document_name}`)
      setIsUploading(false)
    } else {
      toast.success(`${form.document_name} uploaded successfully`)
      setIsUploading(false)
      setAddDoc(false)
      reset()
      setDocPreview('')
      refetch()
    }
  }

  const reuploadAdditionalDocument: SubmitHandler<UploadAdditionalDocumentsProps> = async (form) => {
    setIsReuploading(true)
    clearErrors()
    let upload = await uploadFiles(form.uploaded_document)
    const uploadAdditionalDocuments = await reuploadAdditionalDocumentsMutation({
      property_id: propertyId,
      input: {
        file_id: String(reuploadDocData.id),
        name: form.document_name,
        ...upload && {
          uploaded_document: {
            key: upload.key,
            extension: upload.extension,
          },
        },
      },
    })

    if (uploadAdditionalDocuments.error) {
      errorHandler(uploadAdditionalDocuments.error, setError)
      toast.error(`There was a problem uploading ${form.document_name}`)
      setIsReuploading(false)
    } else {
      toast.success(`${form.document_name} uploaded successfully`)
      setIsReuploading(false)
      setReuploadDoc(false)
      reset()
      setDocPreview('')
    }
  }

  const reuploadHandler = (doc: { id: any; url?: string; name?: string | null | undefined; }) => {
    setReuploadDoc(true)
    setReuploadDocData(
      {
        id: doc.id,
        name: doc.name as string,
      },
    )
  }

  useEffect(() => {
    if (viewDoc) {
      getMedia(viewDoc.id)
    }
  }, [viewDoc, getMedia])

  return <>
    <ProtectedLayout>
      <ClientPortalLayout>
        <ClientPortalLayout.MainContent>
          <div className="md:pt-[3.125rem] md:pl-[1.875rem] md:pr-[3.125rem] p-[1.25rem] h-full">

            {complete
              ? <PackComplete propertyId={propertyId} />
              :
              <>
                <div className="mb-[1.875rem] flex justify-between items-center flex-col gap-5 md:flex-row">
                  <div>
                    <H1>{data?.property.address.line_1 || <Skeleton width={334} />}</H1>
                    <h2 className="text-[1.25rem] text-body/60">
                      {[
                        data?.property.address.line_2,
                        data?.property.address.city,
                        data?.property.address.postcode,
                      ].filter(Boolean).join(', ') || <Skeleton width={254} />}
                    </h2>
                  </div>
                  <Button icon={<PenIcon />} onClick={() => setAddDoc(true)}>Add additional document</Button>
                </div>

                <div className="space-y-[1.875rem]">
                  {
                    fetching || data?.property.documents
                      ?
                      <>
                        <Card>
                          <Card.Header>
                            <H3>Outstanding Documents</H3>
                          </Card.Header>
                          <hr />
                          <Table>
                            <Table.Body>
                              {
                                fetching
                                  ? <>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={400} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={200} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={300} /></Table.Cell>
                                    </Table.Row>
                                  </>
                                  : <></>
                              }

                              {
                                !fetching && outStandingDocuments
                                  .map((doc: SetStateAction<any>) => (
                                    <Table.Row key={doc.id}>
                                      <Table.Cell>
                                        <CircleUnTickIcon
                                          className="inline w-[1.5rem] h-[1.5rem] mr-[1rem] text-gray-300"
                                        />
                                        {(doc.name).slice(0, -1)}
                                      </Table.Cell>
                                      <Table.Cell className="flex justify-end gap-[1rem]">
                                        <Button className="px-6 py-2.5" variant="primary" onClick={() => router.push(doc.url)}>Upload</Button>
                                      </Table.Cell>
                                    </Table.Row>
                                  ))
                              }

                              {
                                !fetching && outStandingDocuments.length === 0 &&
                                <Table.Row>
                                  <Table.Cell colSpan={2}>
                                    <p className="text-center">No outstanding documents</p>
                                  </Table.Cell>
                                </Table.Row>
                              }
                            </Table.Body>
                          </Table>
                        </Card>
                      </>
                      :
                      <></>
                  }
                  {
                    fetching || data?.property.documents
                      ?
                      <>
                        <Card>
                          <Card.Header>
                            <H3>ID Checks</H3>
                          </Card.Header>
                          <hr />
                          <Table>
                            <Table.Body>
                              {
                                fetching
                                  ? <>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={400} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={200} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={300} /></Table.Cell>
                                    </Table.Row>
                                  </>
                                  : <></>
                              }

                              {
                                !fetching && data?.property.documents
                                  .filter(doc => doc.custom_properties?.type === 'idv')
                                  .map((doc: SetStateAction<any>) => (
                                    <Table.Row key={doc.id}>
                                      <Table.Cell>
                                        <CircleTickIcon
                                          className="inline w-[1.5rem] h-[1.5rem] mr-[1rem] text-mint"
                                        />
                                        {doc.name}
                                      </Table.Cell>
                                      <Table.Cell className="flex justify-end gap-[1rem]">
                                        <IconButton
                                          size="small"
                                          icon={<DownloadIcon className="w-[1rem] h-[1rem]" />}
                                          onClick={() => download(doc.url)}
                                        />

                                        <Button variant="secondary" size="small" onClick={() => setViewDoc(doc)}>
                                          View
                                        </Button>
                                      </Table.Cell>
                                    </Table.Row>
                                  ))
                              }

                              {
                                !fetching && data?.property.documents
                                  .filter(doc => doc.custom_properties?.type === 'idv').length === 0 &&
                                <Table.Row>
                                  <Table.Cell colSpan={2}>
                                    <p className="text-center">No ID documents</p>
                                  </Table.Cell>
                                </Table.Row>
                              }
                            </Table.Body>
                          </Table>
                        </Card>
                      </>
                      :
                      <></>
                  }

                  {
                    fetching || data?.property.documents
                      ?
                      <>
                        <Card>
                          <Card.Header>
                            <H3>Client Information</H3>
                          </Card.Header>
                          <hr />
                          <Table>
                            <Table.Body>
                              {
                                fetching
                                  ? <>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={400} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={200} /></Table.Cell>
                                    </Table.Row>
                                    <Table.Row>
                                      <Table.Cell><Skeleton height={24} width={300} /></Table.Cell>
                                    </Table.Row>
                                  </>
                                  : <></>
                              }
                              {
                                !fetching && data?.property.documents
                                  .filter(doc => !['idv'].includes(doc.custom_properties?.type))
                                  .map((doc: SetStateAction<any>) => (
                                    <Table.Row key={doc.id}>
                                      <Table.Cell>
                                        <CircleTickIcon
                                          className="inline w-[1.5rem] h-[1.5rem] mr-[1rem] text-mint"
                                        />
                                        {doc.name}
                                      </Table.Cell>
                                      <Table.Cell className="flex justify-end gap-[1rem]">
                                        <IconButton
                                          size="small"
                                          icon={<DownloadIcon className="w-[1rem] h-[1rem]" />}
                                          loading={isMediaLoading && mediaQueryId === doc.id}
                                          onClick={() => downloadMedia(doc.id)}
                                        />

                                        <Button variant="secondary" size="small" onClick={() => reuploadHandler(doc)}>
                                          Reupload
                                        </Button>

                                        <Button variant="secondary" size="small" onClick={() => setViewDoc(doc)}>
                                          View
                                        </Button>
                                      </Table.Cell>
                                    </Table.Row>
                                  ))
                              }

                              {
                                !fetching && data?.property.documents
                                  .filter(doc => !['idv'].includes(doc.custom_properties?.type)).length === 0 &&
                                <Table.Row>
                                  <Table.Cell colSpan={2}>
                                    <p className="text-center">No client documents</p>
                                  </Table.Cell>
                                </Table.Row>
                              }
                            </Table.Body>
                          </Table>
                        </Card>
                      </>
                      :
                      <></>
                  }
                </div>
              </>
            }
          </div>

        </ClientPortalLayout.MainContent>
      </ClientPortalLayout>
    </ProtectedLayout>

    <Modal isOpen={Boolean(viewDoc)} onClose={() => setViewDoc(undefined)} fullWidth>
      <Modal.ContentTitle>{viewDoc?.name}</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">
        {
          (!isMediaLoading && media) &&
          <PDFViewer url={media.url} />
        }
        {
          isMediaLoading &&
          <Skeleton height={967} />
        }
      </Modal.Content>
    </Modal>

    <Modal isOpen={Boolean(addDoc)} onClose={() => { setAddDoc(false) }}>
      <Modal.ContentTitle>
        Add new document
      </Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">

        <Form onSubmit={(e) => {
          e.preventDefault()
          clearErrors()
          handleSubmit(uploadAdditionalDocument)(e)
        }}>
          <div className="flex flex-col w-full gap-5">

            <Controller
              name="uploaded_document"
              control={control}
              render={({ field }) => {

                const handleChange = async (files: File[] | FileList | null) => {
                  if (files) {
                    setDocPreview(files[0].name)
                  }
                  field.onChange(files)
                }

                return (
                  <>
                    <Form.Input
                      label="Document Name"
                      error={errors.document_name?.message}
                      {...register('document_name', { required: true })}
                    />

                    <Form.Input
                      label="Selected Document"
                      {...register('uploaded_document')}
                      ref={uploadAdditionalDocumentRef}
                      type="file"
                      accept=".pdf"
                      className="hidden"
                      onChange={(input) => handleChange(input.target?.files)}
                      error={errors.uploaded_document?.message}
                    />

                    <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                      <span>{docPreview}</span>
                      <div className="flex justify-end gap-5">
                        <Button
                          disabled={isUploading}
                          onClick={() => (uploadAdditionalDocumentRef.current?.click())}
                          variant="secondary"
                        >
                          Select a document
                        </Button>
                        <Button
                          type="submit"
                          loading={isUploading}
                          disabled={watchFields[0] === '' || watchFields[1] === undefined}
                        >
                          Upload
                        </Button>
                      </div>

                    </div>
                  </>
                )
              }}
            />

          </div>
        </Form>

        {
          Object.keys(errors).length > 0 &&
          <div className="mt-[1.25rem]">
            <Alert variant="danger">
              There was a problem uploading the document. Please try again.
            </Alert>
          </div>
        }
      </Modal.Content>
    </Modal>


    {/* reuploading form */}
    <Modal isOpen={Boolean(reuploadDoc)} onClose={() => { setReuploadDoc(false) }}>
      <Modal.ContentTitle>
        Reupload {reuploadDocData.name}
      </Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">

        <Form onSubmit={(e) => {
          e.preventDefault()
          clearErrors()
          handleSubmit(reuploadAdditionalDocument)(e)
        }}>
          <div className="flex flex-col w-full gap-5">

            <Controller
              name="uploaded_document"
              control={control}
              render={({ field }) => {
                const handleChange = async (files: File[] | FileList | null) => {
                  clearErrors()
                  if (files) {
                    setDocPreview(files[0].name)
                  }

                  field.onChange(files)
                }

                return (
                  <>
                    <Form.Input
                      label="New Name"
                      {...register('document_name', { required: true })}
                      error={errors.document_name?.message}
                    />

                    <Form.Input
                      label="Selected Document"
                      {...register('uploaded_document', { required: true })}
                      ref={uploadAdditionalDocumentRef}
                      type="file"
                      accept=".pdf"
                      className="hidden"
                      onChange={(input) => handleChange(input.target?.files)}
                      error={errors.uploaded_document?.message}
                    />

                    <div className="flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
                      <span>{docPreview}</span>
                      <div className="flex justify-end gap-5">
                        <Button
                          disabled={isUploading}
                          onClick={() => (uploadAdditionalDocumentRef.current?.click())}
                          variant="secondary"
                        >
                          Select a document
                        </Button>
                        <Button
                          type="submit"
                          loading={isReuploading}
                          disabled={watchFields[0] === '' || watchFields[1] === undefined}
                        >

                          Reupload
                        </Button>
                      </div>

                    </div>
                  </>
                )
              }}
            />
          </div>
        </Form>

        {
          Object.keys(errors).length > 0 &&
          <div className="mt-[1.25rem]">
            <Alert variant="danger">
              There was a problem reuploading the document. Please try again.
            </Alert>
          </div>
        }
      </Modal.Content>
    </Modal>
  </>
}

export default Documents
