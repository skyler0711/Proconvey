import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H3 } from '@proconvey/ui/src/components/Headers'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import Table from '@proconvey/ui/src/components/Table'
import Tag from '@proconvey/ui/src/components/Tag'
import dayjs from 'dayjs'
import { graphql } from 'gql'
import { useEffect, useState } from 'react'
import { toast } from 'react-hot-toast'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { useMutation } from 'urql'
import useErrorHandler from 'hooks/useErrorHandler'

type Role = {
  role: string | null
}

type Invitee = {
  email: string | null | undefined
  first_name?: string | null | undefined
  id: string | null | undefined
  invite_code_sent_at?: string | null | undefined
  last_name?: string | null | undefined
  pivot?: Role | null | undefined
  __typename?: string
}

type PropTypes = {
  propertyId: string
  usersData: Array<Invitee> | undefined
  fetching: boolean
}

const Invites = ({ propertyId, usersData, fetching }: PropTypes) => {

  const errorHandler = useErrorHandler()

  const [invitingUserIds, setInvitingUserIds] = useState<Array<string>>([])
  const { loggedInUser } = useSelector((state: RootState) => ({
    loggedInUser: state.auth.user!,
  }))

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

  return (
    <div className="mt-[4.125rem]">
      <H3 className="mb-[1.1875rem]">Invite other parties to review and sign forms</H3>
      <Card>
        <Card.Body padContent={false}>
          <Table>
            <Table.Body>
              {
                fetching ?
                  <div className="flex justify-center p-5">
                    <LoadingSpinner />
                  </div>
                  :
                  usersData ?
                    usersData.filter(user => user.id !== loggedInUser.id).map((user, index: number) => {
                      return (
                        <Table.Row key={index}>
                          <Table.Cell>{user.first_name} {user.last_name}</Table.Cell>
                          <Table.Cell><Tag variant="danger">{user.pivot?.role}</Tag></Table.Cell>
                          <Table.Cell>{user.email}</Table.Cell>
                          <Table.Cell>
                            {
                              user.invite_code_sent_at &&
                              <span className="text-[0.875rem] text-primary">Invite sent on {dayjs(user.invite_code_sent_at).format('DD.MM.YYYY')}</span>
                            }

                          </Table.Cell>
                          <Table.Cell className="flex justify-end">
                            {

                              loggedInUser.id !== user.id ?
                                <Button
                                  loading={isSending && invitingUserIds.includes(user.id as string)}
                                  onClick={() => sendInviteHandler(user.id as string)}
                                  variant={
                                    user.invite_code_sent_at ?
                                      'secondary'
                                      :
                                      'primary'
                                  }>
                                  {
                                    user.invite_code_sent_at ?
                                      'Resend invite'
                                      :
                                      'Invite'
                                  }
                                </Button>
                                :
                                null


                            }


                          </Table.Cell>
                        </Table.Row>
                      )
                    })
                    :
                    <div className="p-5">There are no users ready to invite</div>
              }
            </Table.Body>
          </Table>
        </Card.Body>
      </Card>
    </div>
  )
}

export default Invites
