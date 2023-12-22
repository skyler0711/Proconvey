import dayjs from 'dayjs'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import { PackIcon } from '@proconvey/ui/src/icons'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useEffect, useMemo, useState } from 'react'
import toast from 'react-hot-toast'
import { NextSeo } from 'next-seo'
import Skeleton from 'react-loading-skeleton'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { useMutation, useQuery } from 'urql'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import Table from '@proconvey/ui/src/components/Table'
import Tag from '@proconvey/ui/src/components/Tag'
import { FormGroup } from 'gql/graphql'

const Pack = () => {
  const errorHandler = useErrorHandler()
  const router = useRouter()

  const propertyId = router.query.id as string

  const [invitingUserIds, setInvitingUserIds] = useState<Array<string>>([])
  const { loggedInUser } = useSelector((state: RootState) => ({
    loggedInUser: state.auth.user!,
  }))

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query clientPropertyPack($id: ID!) {
        property(id: $id) {
          id
          address {
            id
            line_1
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
          active_forms {
            id
            pivot {
              ... on ActiveFormsPivot {
                id
                title
              }
            }
            name
            group
            signed
            sections {
              id
              steps {
                id
                answers {
                  id
                  conditions {
                    id
                    selected_value
                    answer {
                      id
                    }
                  }
                }
                conditions {
                  id
                  selected_value
                  answer {
                    id
                  }
                }
              }
            }
          }
          my_progress {
            provided_answers {
              id
              value
              answer {
                id
                step {
                  id
                  section {
                    id
                    form {
                      id
                    }
                  }
                }
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

  const [{ fetching: isSending, data: inviteSent }, sendInviteMutation] = useMutation(graphql(`
    mutation sendInvite($input: SendInviteInput!) {
        sendInvite(input: $input) {
          id
          invite_code_sent_at
        }
      }
  `))

  const sendInviteHandler = async (userId: string) => {
    setInvitingUserIds([...invitingUserIds, userId])
    const response = await sendInviteMutation({
      input: {
        user_id: userId,
        property_id: propertyId,
      },
    })

    const filteredArray = invitingUserIds.filter((id) => id !== userId)
    setInvitingUserIds(filteredArray)

    if (response.error) {
      errorHandler(response.error)
      toast.error('There was a problem sending the invitation')
    } else {
      toast.success('Invitation sent')
    }
  }

  useEffect(() => {
    if (!isSending) {
      setInvitingUserIds([])
    }
  }, [inviteSent, isSending])

  const forms = useMemo(() => {
    const formIds = data
      ? data.property.my_progress?.provided_answers
        ?.map(pa => pa.answer.step.section.form.id)
        ?.filter((item, index, array) => array.indexOf(item) === index)
      : []

    return data?.property?.active_forms
      ?.filter(f => f.group === FormGroup.Protocol && formIds?.includes(f.id))
      ?.filter(f => !f.signed)
      ?.filter(form => {
        const completedQuestions = form.sections
          .reduce(
            (acc, section) => {
              return acc + (section.steps.reduce(
                (acc, step) => acc + (
                  data.property.my_progress?.provided_answers
                    ?.filter(fa => step.answers.map(a => a.id).includes(fa.answer.id) && fa?.value !== null)
                    .length ? 1 : 0),
                0,
              ))
            },
            0,
          )

        const totalQuestions = form.sections.reduce((acc, section) => {
          let length = 0

          section.steps.forEach(step => {
            if (step.conditions.length) {
              // check conditions and add if required
            } else {
              length++
            }
          })

          return acc + length
        }, 0)

        return completedQuestions >= totalQuestions
      })
      ?? []
  },
  [data],
  )

  return (
    <>
      <NextSeo
        title={`Property Pack - ${data?.property.address.line_1 || 'Loading...'}`}
      />

      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="md:pt-[3.125rem] md:pl-[1.875rem] md:pr-[3.125rem] p-[1.25rem] h-full">
              <H1 className="mb-[1.75rem]">Sign your conveyancing forms</H1>
              <Card>
                <Card.Body padContent={false}>
                  <div className="divide-y">
                    {
                      fetching &&
                      <>
                        <div className="p-[1.25rem] flex justify-between items-center">
                          <Skeleton width={400} />
                          <Skeleton width={161} />
                        </div>
                        <div className="p-[1.25rem] flex justify-between items-center">
                          <Skeleton width={200} />
                          <Skeleton width={161} />
                        </div>
                        <div className="p-[1.25rem] flex justify-between items-center">
                          <Skeleton width={300} />
                          <Skeleton width={161} />
                        </div>
                      </>
                    }

                    {
                      !fetching && !forms.length &&
                      <div className="p-[1.25rem] flex justify-center items-center">
                        <H4>No forms to sign yet</H4>
                      </div>
                    }

                    {
                      forms.map((form) => {
                        return (
                          <div key={form.id} className="p-[1.25rem] flex justify-between items-center">
                            <div className="flex items-center gap-[0.75rem]">
                              <div className="bg-mint/10 text-mint rounded-[0.625rem] h-[2.5rem] w-[2.5rem] flex items-center justify-center">
                                <PackIcon className="h-[1.25rem] w-[1.25rem]" />
                              </div>

                              {form.pivot?.title ?? form.name}
                            </div>

                            <Link href={`/properties/${propertyId}/pack/${form.id}`}>
                              <Button variant="primary" size="small">Review and Sign</Button>
                            </Link>
                          </div>
                        )
                      })
                    }
                  </div>
                </Card.Body>
              </Card>

              <H3 className="mb-[1.125rem] mt-[4.125rem]">Invite other parties to review and sign forms</H3>
              <Card>
                <Card.Body padContent={false}>
                  <Table>
                    <Table.Body>
                      {
                        fetching
                          ? (
                            <div className="flex justify-center p-5">
                              <LoadingSpinner />
                            </div>
                          )
                          : (
                            data ?
                              data?.property?.users.map((user, index: number) => {
                                return (
                                  <Table.Row key={index}>
                                    <Table.Cell>{user.first_name} {user.last_name}</Table.Cell>

                                    <Table.Cell>
                                      <Tag variant="danger" className="capitalize">{user.pivot?.role}</Tag>
                                    </Table.Cell>

                                    <Table.Cell>{user.email}</Table.Cell>

                                    <Table.Cell>
                                      {
                                        user.invite_code_sent_at &&
                                        <span className="text-[0.875rem] text-primary">Invite sent on {dayjs(user.invite_code_sent_at).format('DD.MM.YYYY')}</span>
                                      }
                                    </Table.Cell>

                                    <Table.Cell className="flex justify-end">
                                      {
                                        loggedInUser?.id !== user.id
                                          ? (
                                            <Button
                                              loading={isSending && invitingUserIds.includes(user.id as string)}
                                              onClick={() => sendInviteHandler(user.id as string)}
                                              variant={user.invite_code_sent_at ? 'secondary' : 'primary'}
                                            >
                                              {user.invite_code_sent_at ? 'Resend invite' : 'Invite'}
                                            </Button>
                                          )
                                          : <>&nbsp;</>
                                      }
                                    </Table.Cell>
                                  </Table.Row>
                                )
                              })
                              :
                              <div className="p-5">There are no users ready to invite</div>
                          )
                      }
                    </Table.Body>
                  </Table>
                </Card.Body>
              </Card>
            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default Pack
