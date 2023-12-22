import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { JobRole } from 'types/enums/JobRole'
import Modal from '@proconvey/ui/src/components/Modals'
import { gql, useMutation } from 'urql'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'
import toast from 'react-hot-toast'
import useErrorHandler from 'hooks/useErrorHandler'
import Alert from '@proconvey/ui/src/components/Alert'

type PropTypes = {
  isInviteTeamMemberModalOpen: boolean
  setIsInviteTeamMemberModalOpen: Function
  refetch: Function
}

type InviteTeamMemberProps = {
  team_members: {
    email: string
    job_role: string
  }[]
}

export default function InviteTeamMemberModal ({ isInviteTeamMemberModalOpen, setIsInviteTeamMemberModalOpen, refetch }: PropTypes) {
  const errorHandler = useErrorHandler()

  const { register, handleSubmit, reset, control, setError: setTeamMemberError, formState: { errors: teamMemberErrors }, clearErrors: clearMemberErrors } = useForm<InviteTeamMemberProps>({
    defaultValues: {
      team_members: [
        { email: '', job_role: '' },
      ],
    },
  })

  const [{ fetching: isInviteTeamMemberLoading }, inviteTeamMemberMutation] = useMutation(gql(`
    mutation inviteTeamMember($input: InviteTeamMembersInput!) {
      inviteTeamMember(input: $input)
    }
 `))

  const handleInviteTeamMember: SubmitHandler<InviteTeamMemberProps> = async (form) => {
    const result = await inviteTeamMemberMutation({
      input: form,
    })

    if (result.error) {
      errorHandler(result.error, setTeamMemberError)
      toast.error('There was a problem sending an invite to this user, please try again')
    } else {
      toast.success(`Invitation successfully sent to ${form.team_members[0].email}`)
      reset()
      refetch()
      setIsInviteTeamMemberModalOpen(false)
    }

  }

  return (
    <>
      <Modal isOpen={isInviteTeamMemberModalOpen} onClose={() => setIsInviteTeamMemberModalOpen(false)}>
        <Modal.Title>Invite team member</Modal.Title>
        <Modal.Content>
          Enter email address of team member and their role to invite them to ProConvey
        </Modal.Content>


        <Form onSubmit={(e) => {
          e.preventDefault()
          clearMemberErrors()
          handleSubmit(handleInviteTeamMember)(e)
        }}>

          <Form.Input
            placeholder="name@company.com"
            error={teamMemberErrors.team_members?.[0]?.email?.message}
            {...register(`team_members.${0}.email`)}
          />

          <Controller
            control={control}
            name={`team_members.${0}.job_role`}
            render={({ field }) => {
              const handleOnChange = (e: SelectOption) => {
                field.onChange(e.value)
              }

              return (
                <Form.Select
                  placeholder="Select team member&apos;s role"
                  onChange={handleOnChange}
                  error={teamMemberErrors.team_members?.[0]?.job_role?.message}
                  options={
                    (Object.keys(JobRole) as Array<keyof typeof JobRole>)
                      .map(k => ({ text: k, value: JobRole[k] }))
                  }
                />
              )
            }}
          />
          <Modal.Footer>
            <Button type="submit" loading={isInviteTeamMemberLoading} className="mt-4" size="small">Invite</Button>
            <Button onClick={() => setIsInviteTeamMemberModalOpen(false)} variant="secondary" className="mt-4" size="small">Cancel</Button>
          </Modal.Footer>
        </Form>

        {
          Object.keys(teamMemberErrors).length > 0 &&
          <div className="mt-[1.25rem]">
            <Alert variant="danger">
              There was a problem inviting this team member. Please try again.
            </Alert>
          </div>
        }
      </Modal>
    </>
  )
}
