import Button from '@proconvey/ui/src/components/Button'
import Modal from '@proconvey/ui/src/components/Modals'
import { Giftor } from 'components/GiftorsTable'
import { graphql } from 'gql'
import { User } from 'gql/graphql'
import { SubmitHandler, useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import { useMutation } from 'urql'

type RemoveGiftorInputProps = {
  giftor_id: string
  property_id: string
  giftor_index: string
  active_form_id: string
}

type PropTypes = {
  propertyId: string
  onClose: () => void
  party: User | undefined
  refetch: Function
  giftor?: Giftor
}

const RemoveGiftorModal = ({
  propertyId,
  onClose,
  refetch,
  party,
  giftor,
}: PropTypes) => {
  const { handleSubmit } = useForm<RemoveGiftorInputProps>()

  const [{ fetching }, removeGiftorMutation] = useMutation(graphql(`
  mutation removeGiftor($input: RemoveGiftorInput!) {
    removeGiftor(input: $input)
    }
`))

  const handleRemoveParty: SubmitHandler<RemoveGiftorInputProps> = async () => {
    if (giftor === undefined) {
      toast.error('Something went wrong, please try again')
      return
    }

    const removeGiftor = await removeGiftorMutation({
      input: {
        giftor_id: party?.id ?? '',
        property_id: propertyId,
        step_id: giftor?.step_id.toString(),
        giftor_index: giftor?.index.toString(),
        active_form_id: giftor?.active_form_id.toString(),
      },
    })

    if (removeGiftor.error) {
      toast.error('Something went wrong, please try again')
    } else {
      toast.success('Removed giftor successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={!!party} onClose={onClose}>
      <Modal.ContentTitle>Remove giftor</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem]">
        <p className="text-sm text-gray-500">Are you sure you want to remove {party?.first_name} {party?.last_name} from the property?</p>
      </Modal.Content>
      <Modal.Footer>
        <Button size="small" onClick={handleSubmit(handleRemoveParty)} loading={fetching}>Remove</Button>
        <Button size="small" onClick={onClose} variant="secondary">Cancel</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default RemoveGiftorModal
