import Button from '@proconvey/ui/src/components/Button'
import Modal from '@proconvey/ui/src/components/Modals'
import { graphql } from 'gql'
import { User } from 'gql/graphql'
import { toast } from 'react-hot-toast'
import { useMutation } from 'urql'

type PropTypes = {
  propertyId: string
  onClose: () => void
  party: User | undefined
  refetch: Function
}

const InvitePartyModal = ({ propertyId, onClose, party, refetch }: PropTypes) => {
  const [{ fetching: isInvitePartyLoading }, invitePartyMutation] = useMutation(graphql(`
  mutation inviteParty($input: InvitePartyInput!) {
    inviteParty(input: $input)
    }
`))

  const handleInviteParty = async () => {
    const inviteParty = await invitePartyMutation({
      input: {
        party_id: party?.id ?? '',
        property_id: propertyId,
      },
    })

    if (inviteParty.error) {
      toast.error('Something went wrong, please try again')
    } else {
      toast.success('Invited party successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={!!party} onClose={onClose}>
      <Modal.ContentTitle>Invite {party?.first_name} {party?.last_name}</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">
        <p className="text-sm text-gray-500">Are you sure you want to invite {party?.first_name} {party?.last_name}?</p>
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" loading={isInvitePartyLoading} onClick={handleInviteParty}>Invite</Button>
        <Button size="small" onClick={onClose} variant="secondary">Cancel</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default InvitePartyModal
