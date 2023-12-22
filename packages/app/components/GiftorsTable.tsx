import Table from '@proconvey/ui/src/components/Table'
import { useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import PropertyTag from '@proconvey/ui/src/components/PropertyTag'
import { BinIcon, CrossIcon, PencilIcon, TickIcon } from '@proconvey/ui/src/icons'
import { Property, PropertyUserRole, User } from 'gql/graphql'
import Card from '@proconvey/ui/src/components/Card'
import { H3 } from '@proconvey/ui/src/components/Headers'
import Button from '@proconvey/ui/src/components/Button'
import RemoveGiftorModal from './ClientOverview/Modals/RemoveGiftorModal'
import InviteGiftorModal from './ClientOverview/Modals/InviteGiftorModal'
import EditGiftorModal from './ClientOverview/Modals/EditGiftorModal'
import AddGiftorModal from './ClientOverview/Modals/AddGiftorModal'

export type Giftor = {
  index: string
  name: string
  email: string
  phone: string
  amount_being_loaned: string
  address: string
  step_id: string
  active_form_id: string
}

type PropTypes = {
  property: Property
  fetching: boolean
  refetch: Function
  details: any
}

const GiftorsTable = ({ property, fetching, refetch, details }: PropTypes) => {
  const [userToEdit, setUserToEdit] = useState<User | undefined>()
  const [userToRemove, setUserToRemove] = useState<User | undefined>()
  const [userToInvite, setUserToInvite] = useState<User | undefined>()
  const [addGiftorModalOpen, setAddGiftorModalOpen] = useState(false)
  const [giftor, setGiftor] = useState<Giftor | undefined>(undefined)

  if (!property || property?.users.filter(party => party.pivot?.role === PropertyUserRole.Giftor).length === 0) {
    return null
  }

  return (
    <>
      <Card className="mt-5">
        <Card.Header>
          <div className="flex flex-col items-start justify-between gap-5 sm:items-center sm:flex-row">
            <H3>Giftors</H3>
            {// Only allow for 4 Giftors to be added as that the maximum allowed in the forms
              property?.users.filter(party => party.pivot?.role === PropertyUserRole.Giftor).length <= 4 &&
            <Button
              variant="link"
              className="text-sm font-normal text-primary"
              onClick={() => setAddGiftorModalOpen(true)}
            >
                Add giftor
            </Button>
            }
          </div>
        </Card.Header>
        <Card.Body padContent={false}>
          <Table>
            <Table.Head>
              <Table.Row>
                <Table.Cell as="th" className="!text-[0.75rem]">
                  Name
                </Table.Cell>
                <Table.Cell as="th" className="!text-[0.75rem]">
                  Role
                </Table.Cell>
                <Table.Cell as="th" className="!text-[0.75rem]">
                  Representing
                </Table.Cell>
                <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                  ID check
                </Table.Cell>
                <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                  SOF Check
                </Table.Cell>
                <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                  Gifted Deposit Declaration
                </Table.Cell>
                <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                  Registered
                </Table.Cell>
                <Table.Cell as="th" className="w-[3.125rem]"></Table.Cell>
                <Table.Cell as="th" className="w-[3.125rem]"></Table.Cell>
              </Table.Row>
            </Table.Head>

            <Table.Body>
              {
                fetching && <>
                  <Table.Row>
                    <Table.Cell>
                      <Skeleton width="60%" height={27} />
                      <Skeleton width="80%" height={27} />
                    </Table.Cell>
                    <Table.Cell><Skeleton width="70%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="80%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="30%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                    <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                  </Table.Row>
                </>
              }

              {
                !fetching && property?.users.length === 0 &&
                <Table.Row>
                  <Table.Cell colSpan={9}>
                    <p className="text-center">No Giftors</p>
                  </Table.Cell>
                </Table.Row>
              }

              {
                property?.users.filter(party => party.pivot?.role === PropertyUserRole.Giftor).map(party => (
                  <Table.Row key={party.id}>
                    <Table.Cell className="text-[0.875rem]">
                      {party.first_name} {party.last_name}<br />
                      <span className="text-[0.75rem] text-body/60">{party.email}</span>
                    </Table.Cell>
                    <Table.Cell className="text-[0.875rem] capitalize">
                      <PropertyTag type={party.pivot?.role as any}>
                        {
                          party.pivot?.role === PropertyUserRole.Owner
                            ? 'Owner (Active)'
                            : party.pivot?.role
                        }
                      </PropertyTag>
                    </Table.Cell>
                    <Table.Cell className="text-[0.875rem]">
                      {party.pivot?.representation}
                    </Table.Cell>
                    <Table.Cell>
                      {
                        property?.letters_required
                          ? <TickIcon className="text-mint w-[1rem]" />
                          : <CrossIcon className="text-mull w-[0.625rem]" />
                      }
                    </Table.Cell>
                    <Table.Cell>
                      {
                        property?.id_check_required
                          ? <TickIcon className="text-mint w-[1rem]" />
                          : <CrossIcon className="text-mull w-[0.625rem]" />
                      }
                    </Table.Cell>
                    <Table.Cell>
                      {
                        party?.pivot?.onboarding_forms_completed_at
                          ? <TickIcon className="text-mint w-[1rem]" />
                          : <CrossIcon className="text-mull w-[0.625rem]" />
                      }
                    </Table.Cell>
                    <Table.Cell>
                      {
                        party?.email_verified_at
                          ? <TickIcon className="text-mint w-[1rem]" />
                          : <Button variant="plain" onClick={() =>
                            setUserToInvite(party)
                          } className="text-sm font-normal text-primary">Invite</Button>
                      }
                    </Table.Cell>
                    <Table.Cell>
                      <Button variant="plain" onClick={() => {
                        setGiftor(details?.giftors?.find((giftor: Giftor) => giftor.email === party?.email))
                        setUserToRemove(party)
                      }}>
                        <BinIcon className="h-4" />
                      </Button>
                    </Table.Cell>
                    <Table.Cell>
                      <Button variant="plain" onClick={() => {
                        setGiftor(details?.giftors?.find((giftor: Giftor) => giftor.email === party?.email))
                        setUserToEdit(party)
                      }}>
                        <PencilIcon className="h-4" />
                      </Button>
                    </Table.Cell>
                  </Table.Row>
                ))
              }
            </Table.Body>

            <InviteGiftorModal
              giftor={userToInvite}
              propertyId={property.id}
              onClose={() => setUserToInvite(undefined)}
              refetch={refetch}
            />

            <RemoveGiftorModal
              party={userToRemove}
              propertyId={property.id}
              onClose={() => setUserToRemove(undefined)}
              refetch={refetch}
              giftor={giftor}
            />

            <EditGiftorModal
              giftor={giftor}
              giftorUser={userToEdit}
              propertyId={property.id}
              onClose={() => setUserToEdit(undefined)}
              refetch={refetch}
            />

            <AddGiftorModal
              isOpen={addGiftorModalOpen}
              onClose={() => setAddGiftorModalOpen(false)}
              property={property}
              refetch={refetch}
            />

          </Table>
        </Card.Body>
      </Card>
    </>
  )
}

export default GiftorsTable
