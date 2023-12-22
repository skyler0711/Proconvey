import Button from '@proconvey/ui/src/components/Button'
import Modal from '@proconvey/ui/src/components/Modals'
import { graphql } from 'gql'
import { User } from 'gql/graphql'
import { SubmitHandler, useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import { useMutation } from 'urql'

type RemovePartyInputProps = {
  party_id: string
  property_id: string
}

type PropTypes = {
  propertyId: string
  onClose: () => void
  party: User | undefined
  refetch: Function
}

const RemovePartyModal = ({ propertyId, onClose, party, refetch }: PropTypes) => {
  const { handleSubmit } = useForm<RemovePartyInputProps>()

  const [{ fetching: isRemovePartyLoading }, removePartyMutation] = useMutation(graphql(`
  mutation removeParty($input: RemovePartyInput!) {
    removeParty(input: $input)
    }
`))

  const handleRemoveParty: SubmitHandler<RemovePartyInputProps> = async () => {
    const removeParty = await removePartyMutation({
      input: {
        party_id: party?.id ?? '',
        property_id: propertyId,
      },
    })

    if (removeParty.error) {
      toast.error('Something went wrong, please try again')
    } else {
      toast.success('Removed party successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={!!party} onClose={onClose}>
      <Modal.ContentTitle>Remove party</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">
        <p className="text-sm text-gray-500">Are you sure you want to remove {party?.first_name} {party?.last_name} from the property?</p>
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" onClick={handleSubmit(handleRemoveParty)} loading={isRemovePartyLoading}>Remove</Button>
        <Button size="small" onClick={onClose} variant="secondary">Cancel</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default RemovePartyModal
