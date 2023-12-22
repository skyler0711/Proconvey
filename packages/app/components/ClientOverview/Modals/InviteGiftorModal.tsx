import Button from '@proconvey/ui/src/components/Button'
import Modal from '@proconvey/ui/src/components/Modals'
import { graphql } from 'gql'
import { User } from 'gql/graphql'
import { toast } from 'react-hot-toast'
import { useMutation } from 'urql'

type PropTypes = {
  propertyId: string
  onClose: () => void
  giftor: User | undefined
  refetch: Function
}

const InviteGiftorModal = ({ propertyId, onClose, giftor, refetch }: PropTypes) => {
  const [{ fetching: isInvitePartyLoading }, invitePartyMutation] = useMutation(graphql(`
  mutation inviteGiftor($input: InviteGiftorInput!) {
    inviteGiftor(input: $input)
    }
`))

  const handleInviteGiftor = async () => {
    const inviteGiftor = await invitePartyMutation({
      input: {
        party_id: giftor?.id ?? '',
        property_id: propertyId,
      },
    })

    if (inviteGiftor.error) {
      toast.error('Something went wrong, please try again')
    } else {
      toast.success('Invited giftor successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={!!giftor} onClose={onClose}>
      <Modal.ContentTitle>Invite {giftor?.first_name} {giftor?.last_name}</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">
        <p className="text-sm text-gray-500">Are you sure you want to invite {giftor?.first_name} {giftor?.last_name}?</p>
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" loading={isInvitePartyLoading} onClick={handleInviteGiftor}>Invite</Button>
        <Button size="small" onClick={onClose} variant="secondary">Cancel</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default InviteGiftorModal
