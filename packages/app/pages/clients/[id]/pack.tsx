import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import IconButton from '@proconvey/ui/src/components/IconButton'
import Table from '@proconvey/ui/src/components/Table'
import { ArchiveIcon, ChevronLeftIcon, CircleTickIcon, DownloadIcon, PinIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import useDownload from 'hooks/useDownload'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useRouter } from 'next/router'
import React, { useEffect, useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import { useMutation, useQuery } from 'urql'
import { Media } from 'gql/graphql'
import Modal from '@proconvey/ui/src/components/Modals'
import PDFViewer from '@proconvey/ui/src/components/PDFViewer'
import Link from 'next/link'
import client from 'helpers/client'
import useMedia from 'hooks/useMedia'

const Pack = () => {

  const router = useRouter()
  const download = useDownload()

  const clientId = router.query.id as string

  const [buttonDisabled, setButtonDisabled] = useState(true)
  const [viewDoc, setViewDoc] = useState<Media | undefined>()
  const [downloadingAll, setDownloadingAll] = useState(false)

  const { mediaQueryId, downloadMedia, isLoading: isMediaLoading } = useMedia()

  const [{ fetching, data }] = useQuery({
    query: graphql(`
      query getPack($id: ID!) {
        property(id: $id) {
          id
          archived_at
          case_reference
          users {
            id
            first_name
            last_name
          }
          address {
            id
            line_1
            line_2
            city
            postcode
          }
          documents {
            id
            url
            name
            custom_properties
          }
        }
      }
    `),
    variables: {
      id: clientId,
    },
  })

  const handleDownloadAll = async () => {
    setDownloadingAll(true)

    const downloadData = await client.query(
      graphql(`
      query downloadPack($id: ID!) {
        property(id: $id) {
          all_documents_link
        }
      }
    `),
      {
        id: clientId,
      },
    ).toPromise()

    download(downloadData?.data!.property!.all_documents_link as string)
    setDownloadingAll(false)
  }


  const [{ fetching: isArchiving, data: archivingData }, archivePropertyMutation] = useMutation(graphql(`
    mutation archiveProperty($id: ID!) {
      archiveProperty(id: $id) {
        id
        archived_at
      }
    }
  `))

  const archiveClientHandler = () => {
    setButtonDisabled(true)
    archivePropertyMutation({
      id: clientId as string,
    })
  }

  useEffect(() => {
    if (data) {
      setButtonDisabled(data?.property.archived_at)
    }
  }, [data])

  return (
    <>
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="ml-[1.875rem]">
              <div className="md:mb-[1.875rem]">
                <Link href="/clients">
                  <Button variant="link" className="mb-[1.375rem]">
                    <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to All Clients
                  </Button>
                </Link>

                <div className="flex flex-col items-start xl:items-center gap-[1.25rem] justify-between xl:flex-row">
                  <div>
                    {
                      fetching
                        ? <Skeleton height={50} width={363} />
                        : <H1>{data?.property.users[0].first_name} {data?.property.users[0].last_name}</H1>
                    }

                    <div className="mt-[1.375rem]">
                      {
                        fetching
                          ? <Skeleton height={30} width={484} />
                          : <h2 className="text-[1.375rem]">
                            <PinIcon className="inline mr-[1rem]" />
                            {[
                              data?.property.address.line_1,
                              data?.property.address.line_2,
                              data?.property.address.city,
                              data?.property.address.postcode,
                            ].filter(Boolean).join(', ')}
                          </h2>
                      }
                    </div>
                  </div>

                  {
                    fetching
                      ? <Skeleton height={52} width={183} />
                      : (
                        <div className="flex flex-wrap items-center md:flex-nowrap gap-[1.25rem]">
                          <Button
                            disabled={buttonDisabled}
                            onClick={archiveClientHandler}
                            variant="outlined"
                            icon={<ArchiveIcon />}
                            loading={isArchiving}
                          >
                            {archivingData || data?.property.archived_at ? 'This file has been archived' : 'Archive the file'}
                          </Button>
                          <Button disabled={data?.property?.documents?.length === 0} loading={downloadingAll} onClick={handleDownloadAll} icon={<DownloadIcon className="w-[1.5rem]" />}>Download Pack</Button>
                        </div>
                      )
                  }
                </div>
              </div>

              {
                fetching || data?.property.documents
                  ? <>
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
                              .map(doc => (
                                <Table.Row key={doc.id}>
                                  <Table.Cell>
                                    <CircleTickIcon
                                      className="inline w-[1.5rem] h-[1.5rem] mr-[1rem] text-mint"
                                    />
                                    {doc.name}
                                  </Table.Cell>
                                  <Table.Cell className="flex gap-[1rem]">
                                    <IconButton
                                      size="small"
                                      icon={<DownloadIcon className="w-[1rem] h-[1rem]" />}
                                      loading={isMediaLoading && mediaQueryId === doc.id}
                                      onClick={() => downloadMedia(doc.id)}
                                    />

                                    <Button variant="secondary" size="small" onClick={() => setViewDoc(doc)}>
                                    View
                                    </Button>
                                  </Table.Cell>
                                </Table.Row>
                              ))
                          }
                        </Table.Body>
                      </Table>
                    </Card>
                  </>
                  : <></>
              }
            </div>

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>

      <Modal isOpen={Boolean(viewDoc)} onClose={() => setViewDoc(undefined)}>
        <Modal.ContentTitle>{viewDoc?.name}</Modal.ContentTitle>
        <Modal.Content className="mt-[1.25rem]">
          {
            viewDoc &&
            <PDFViewer url={viewDoc?.url} />
          }
        </Modal.Content>
      </Modal>
    </>
  )
}

export default Pack
