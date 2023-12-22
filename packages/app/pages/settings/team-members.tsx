import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import Button from '@proconvey/ui/src/components/Button'
import { useEffect, useState } from 'react'
import { JobRole } from 'types/enums/JobRole'
import { gql, useMutation, useQuery } from 'urql'
import { SubmitHandler, useForm } from 'react-hook-form'
import { graphql } from 'gql'
import dayjs from 'dayjs'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import toast from 'react-hot-toast'
import { NextSeo } from 'next-seo'
import classNames from 'classnames'
import InviteTeamMemberModal from '../../components/TeamMembers/Modals/InviteTeamMemberModal'
import RemoveTeamMemberModal from '../../components/TeamMembers/Modals/RemoveTeamMemberModal'

type UpdateConveyancerDetailsProps = {
  name: string
  company_number: string
  sra_clc_number: string
  logo_image: File[]
  address: {
    line_1: string
    line_2?: string | null
    city: string
    postcode: string
  }
  trading_name: string
  vat_number: string
  website: string
  location: string
  telephone_number: string
  email_address: string
}

type ResendInviteTeamMemberProps = {
  email: string
  job_role: string
}

export default function TeamMembers () {
  const [isInviteTeamMemberModalOpen, setIsInviteTeamMemberModalOpen] = useState(false)
  const [userToRemove, setUserToRemove] = useState<string | undefined>()
  const [invitedUserEmail, setInvitedUserEmail] = useState<string | undefined>()

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const [{ data: conveyancer }, refetch] = useQuery({
    query: graphql(`
    query settingsBusinessConveyancer {
      me {
        id
        conveyancer {
          id
          name
          type
          sra_clc_number
          team_members {
            id
            first_name
            last_name
            email
            phone
            invite_code_sent_at
            job_role
            email_verified_at
          }

          team_member_count
          company_number
          address {
            id
            line_1
            line_2
            city
            postcode
          }
          logo_image {
            id
            url
          }
          trading_name
          vat_number
          website
          location
          telephone_number
          email_address
        }
      }
    }
  `),
  })

  const teamMember = conveyancer?.me?.conveyancer?.team_members?.find(tm => tm.id === userToRemove)

  const teamMembers = conveyancer?.me?.conveyancer?.team_members

  const {
    reset: resetConveyancer,

  } = useForm<UpdateConveyancerDetailsProps>({
    defaultValues: {
      name: conveyancer?.me?.conveyancer?.name ?? '',
      company_number: conveyancer?.me?.conveyancer?.company_number ?? '',
      sra_clc_number: conveyancer?.me?.conveyancer?.sra_clc_number ?? '',
      address: {
        line_1: conveyancer?.me?.conveyancer?.address?.line_1 ?? '',
        line_2: conveyancer?.me?.conveyancer?.address?.line_2 ?? '',
        city: conveyancer?.me?.conveyancer?.address?.city ?? '',
        postcode: conveyancer?.me?.conveyancer?.address?.postcode ?? '',
      },
      trading_name: conveyancer?.me?.conveyancer?.trading_name ?? '',
      vat_number: conveyancer?.me?.conveyancer?.vat_number ?? '',
      website: conveyancer?.me?.conveyancer?.website ?? '',
      location: conveyancer?.me?.conveyancer?.location ?? '',
      telephone_number: conveyancer?.me?.conveyancer?.telephone_number ?? '',
      email_address: conveyancer?.me?.conveyancer?.email_address ?? '',
    },
  })

  useEffect(() => {
    if (conveyancer) {
      resetConveyancer({
        name: conveyancer?.me?.conveyancer?.name,
        company_number: conveyancer?.me?.conveyancer?.company_number ?? undefined,
        sra_clc_number: conveyancer?.me?.conveyancer?.sra_clc_number,
        address: {
          line_1: conveyancer?.me?.conveyancer?.address?.line_1,
          line_2: conveyancer?.me?.conveyancer?.address?.line_2,
          city: conveyancer?.me?.conveyancer?.address?.city,
          postcode: conveyancer?.me?.conveyancer?.address?.postcode,
        },
        trading_name: conveyancer?.me?.conveyancer?.trading_name ?? '',
        vat_number: conveyancer?.me?.conveyancer?.vat_number ?? '',
        website: conveyancer?.me?.conveyancer?.website ?? '',
        location: conveyancer?.me?.conveyancer?.location ?? '',
        telephone_number: conveyancer?.me?.conveyancer?.telephone_number ?? '',
        email_address: conveyancer?.me?.conveyancer?.email_address ?? '',
      })
    }
  }, [conveyancer, resetConveyancer])

  const { handleSubmit: resendInviteUser } = useForm({
    defaultValues: {
      email: '',
    },
  })

  const [{ fetching: isResendInviteLoading }, resendInviteMutation] = useMutation(gql(`
    mutation resendInvite($email: String!) {
      resendInvite(email: $email)
    }
  `))

  const handleResendInvite: SubmitHandler<ResendInviteTeamMemberProps> = async (form) => {
    setInvitedUserEmail(form.email)
    const resendInvite = await resendInviteMutation({
      email: form.email,
    })

    if (resendInvite.error) {
      toast.error(`There was a problem re-sending the invite to ${form.email}, please try again`)
    } else {
      toast.success(`Invitation successfully re-sent to ${form.email}`)
    }
  }


  const TeamMemberCount = conveyancer?.me?.conveyancer?.team_members?.filter((member: { id: string | undefined }) => member.id !== user?.id)?.length ?? 0

  return (
    <>
      <NextSeo
        title="Business settings"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[3.125rem]">
              <div className="mb-[1.875rem] flex justify-between sm:items-center items-start flex-col sm:flex-row gap-5">
                <H1>Team members</H1>
              </div>

              <div className="flex flex-col gap-5 mb-5">
                <div className="flex flex-col justify-between w-full bg-white rounded-xl">
                  <div className="flex flex-col sm:flex-row sm:items-center items-start justify-between px-5 py-6">
                    <div className="flex gap-1">
                      <H3>Team Members</H3>
                      <p className="mt-2 text-sm text-opacity-50 text-body">({TeamMemberCount})</p>
                    </div>
                    <Button onClick={() => setIsInviteTeamMemberModalOpen(true)} variant="link" className="sm:ml-auto">Invite team member</Button>
                  </div>
                  <hr />

                  {
                    teamMembers && TeamMemberCount > 0
                      ? teamMembers?.filter((member: { id: string | undefined }) => member.id !== user?.id).map((member, index) => (
                        <div className="flex flex-col" key={index}>
                          <div className="flex sm:items-center items-start flex-col sm:flex-row gap-5 p-5">
                            <div className="flex flex-1 md:items-center items-start flex-col md:flex-row gap-2">
                              {
                                (member.first_name != null || member.last_name != null) ?
                                  <p className="max-w-[13.125rem] w-full md:order-1">{member.first_name} {member.last_name}</p>
                                  :
                                  null
                              }
                              <div className="flex max-w-[12.5rem] w-full md:order-2 order-0">
                                {
                                  member.job_role ? (
                                    <div
                                      className={classNames({
                                        'bg-sentimental-pink': member.job_role === JobRole.Conveyancer,
                                        'bg-peach bg-opacity-10': member.job_role === JobRole.Paralegal,
                                        'bg-mint bg-opacity-10': member.job_role === JobRole.Assistant,
                                        'bg-body bg-opacity-5': member.job_role === JobRole.Other,
                                      })}
                                    >
                                      <p className={classNames({
                                        'text-base font-medium capitalize text-mull': member.job_role === JobRole.Conveyancer,
                                        'text-base font-medium capitalize text-peach': member.job_role === JobRole.Paralegal,
                                        'text-base font-medium capitalize text-mint': member.job_role === JobRole.Assistant,
                                        'text-base font-medium capitalize text-body text-opacity-60': member.job_role === JobRole.Other,
                                      })}>
                                        {member.job_role}
                                      </p>
                                    </div>
                                  ) : null
                                }
                              </div>

                              <p className="text-body text-opacity-60 md:order-3">
                                {member.email}
                              </p>
                            </div>


                            <div className="flex sm:ml-auto">
                              {
                                member.email_verified_at === null && member.invite_code_sent_at
                                  ? (
                                    <div className="flex flex-col sm:flex-row sm:items-center text-xs sm:text-right gap-2 sm:gap-7 text-primary">
                                      {
                                        member.invite_code_sent_at
                                          ? <p>Invite sent on {dayjs(member.invite_code_sent_at).format('DD.MM.YYYY')}</p>
                                          : null
                                      }
                                      <Button
                                        loading={isResendInviteLoading && member.email === invitedUserEmail}
                                        onClick={() => resendInviteUser(handleResendInvite({ email: member.email, job_role: member.job_role as JobRole }))}
                                        variant="primary">Resend Invite</Button>
                                    </div>
                                  )
                                  : <Button onClick={() => setUserToRemove(member.id)} variant="secondary">Remove</Button>
                              }
                            </div>
                          </div>
                          <hr />
                        </div>
                      ))
                      :
                      <H4 className="my-5 text-center">No team members</H4>
                  }
                </div>
              </div>
            </div>

            <InviteTeamMemberModal
              isInviteTeamMemberModalOpen={isInviteTeamMemberModalOpen}
              setIsInviteTeamMemberModalOpen={setIsInviteTeamMemberModalOpen}
              refetch={refetch}
            />

            <RemoveTeamMemberModal
              userToRemove={userToRemove}
              setUserToRemove={setUserToRemove}
              teamMember={teamMember}
              refetch={refetch}
            />

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
