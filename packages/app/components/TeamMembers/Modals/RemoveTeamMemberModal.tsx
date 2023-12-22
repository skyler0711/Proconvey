import { H3 } from '@proconvey/ui/src/components/Headers'
import Button from '@proconvey/ui/src/components/Button'
import Modal from '@proconvey/ui/src/components/Modals'
import { gql, useMutation } from 'urql'
import { useForm } from 'react-hook-form'
import toast from 'react-hot-toast'

type PropTypes = {
  userToRemove?: string
  setUserToRemove: Function
  teamMember: any
  refetch: Function
}

type RemoveTeamMemberProps = {
  team_members: {
    email: string
    job_role: string
  }[]
}

export default function RemoveTeamMemberModal ({ userToRemove, setUserToRemove, teamMember, refetch }: PropTypes) {

  const { handleSubmit } = useForm<RemoveTeamMemberProps>({
    defaultValues: {
      team_members: [
        { email: '', job_role: '' },
      ],
    },
  })

  const [{ fetching: deleteOtherUserIsLoading }, deleteOtherUserMutation] = useMutation(gql(`
    mutation deleteOtherUser($id: ID!) {
      deleteOtherUser(id: $id)
    }
  `))

  const deleteTeamMember = async () => {
    await deleteOtherUserMutation({
      id: userToRemove,
    })

    if (teamMember?.first_name && teamMember?.last_name) {
      toast.success(`${teamMember?.first_name} ${teamMember?.last_name} has been removed`)
    } else {
      toast.success('User has been successfully removed')
    }
    refetch()

    setUserToRemove(undefined)
  }

  return (
    <>
      <Modal isOpen={!!userToRemove} onClose={() => setUserToRemove(undefined)}>
        <Modal.Title>Remove team member</Modal.Title>
        <H3>
          Are you sure you want to remove {teamMember?.first_name} {teamMember?.last_name}?
        </H3>
        <Modal.Footer>
          <Button onClick={handleSubmit(deleteTeamMember)} size="small" loading={deleteOtherUserIsLoading}>Yes, remove team member</Button>
          <Button onClick={() => setUserToRemove(undefined)} variant="secondary" size="small">Cancel</Button>
        </Modal.Footer>
      </Modal>
    </>
  )
}
