import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Link from 'next/link'
import { useForm, useFieldArray, Controller, SubmitHandler } from 'react-hook-form'
import SetupLayout from 'layouts/SetupLayout'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'
import { useMutation, useQuery } from 'urql'
import { useState } from 'react'
import { graphql } from 'gql'
import { useRouter } from 'next/router'
import { JobRole } from 'types/enums/JobRole'
import useErrorHandler from 'hooks/useErrorHandler'
import dayjs from 'dayjs'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import Label from '@proconvey/ui/src/components/Form/Label'
import { NextSeo } from 'next-seo'

type InviteTeamMemberProps = {
  team_members: {
    email: string
    job_role: string
  }[]
}


export default function TeamMembers () {
  const [isLoading, setIsLoading] = useState(false)
  const router = useRouter()
  const errorHandler = useErrorHandler()

  const { register, handleSubmit, setError, control, formState: { errors }, clearErrors } = useForm<InviteTeamMemberProps>({
    defaultValues: {
      team_members: [
        { email: '', job_role: '' },
      ],
    },
  })

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const [{ data: conveyancer }] = useQuery({
    query: graphql(`
    query teamMembersQuery {
      me {
        id
        conveyancer {
          id
          team_members {
            id
            job_role
            email
            invite_code_sent_at
            email_verified_at
          }
        }
      }
    }
  `),
  })


  const [_, inviteTeamMemberMutation] = useMutation(graphql(`
    mutation inviteTeamMembers($input: InviteTeamMembersInput!) {
      inviteTeamMember(input: $input)
    }
  `))

  const onSubmit: SubmitHandler<InviteTeamMemberProps> = async (form) => {
    setIsLoading(true)
    const response = await inviteTeamMemberMutation({
      input: form,
    })

    if (response.error) {
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      router.push('/clients')
    }
  }


  const { fields, append, remove } = useFieldArray({
    name: 'team_members',
    control,
  })

  return (
    <>
      <NextSeo
        title="Invite team members"
      />
      <SetupLayout currentStep={6}>
        <SetupLayout.MainContent>
          <div className="mb-[3.125rem]">
            <H1>Complete your account creation</H1>
          </div>
          <Form onSubmit={(e) => {
            e.preventDefault()
            clearErrors()
            handleSubmit(onSubmit)(e)
          }}>          <div className="mt-[3.125rem] pb-[20px] bg-white rounded-[0.625rem]">
              <div className="py-[1.5rem] px-[1.25rem] flex justify-between items-center">
                <H3>Invite team members</H3>
                <button type="button" className="text-sm font-normal text-primary" onClick={() => append({
                  email: '',
                  job_role: '',
                })}>Invite more team members</button>
              </div>

              <hr />

              <div className="flex flex-col gap-5 mt-[1.5rem] mx-[1.25rem]">


                {
                  fields.map((field, index) => {
                    return (
                      <div key={field.id}>
                        <Form.Group key={field.id}>
                          <Form.Input
                            label="Email"
                            type="email"
                            placeholder="name@company.com"
                            error={errors.team_members?.[index]?.email?.message}
                            {...register(`team_members.${index}.email`)}
                          />

                          <Controller
                            control={control}
                            name={`team_members.${index}.job_role`}
                            render={({ field }) => {
                              const handleOnChange = (e: SelectOption) => {
                                field.onChange(e.value)
                              }

                              return (
                                <Form.Select
                                  placeholder="Select team member&apos;s role"
                                  label="Job role"
                                  onChange={handleOnChange}
                                  error={errors.team_members?.[index]?.job_role?.message}
                                  options={
                                    (Object.keys(JobRole) as Array<keyof typeof JobRole>)
                                      .map(k => ({ text: k, value: JobRole[k] }))
                                  }
                                />
                              )
                            }}
                          />

                          {
                            index === 0
                              ? <button type="button" className="mt-[2.375rem] text-primary text-sm font-normal opacity-0">Remove</button>
                              : <button type="button" className="mt-[2.375rem] text-primary text-sm font-normal" onClick={() => remove(index)}>Remove</button>
                          }
                        </Form.Group>
                      </div>
                    )
                  })
                }
                {
                  conveyancer?.me?.conveyancer?.team_members && conveyancer?.me?.conveyancer?.team_members.length > 0
                    ?
                    <div className="mt-5">
                      <Label>Invited Team Members</Label>
                    </div>
                    : null
                }
                {
                  conveyancer?.me?.conveyancer?.team_members?.filter((member) => member.id !== user?.id).map((teamMember, index) => (
                    <div className="flex flex-col" key={index}>
                      <div className="md:flex items-center p-5">
                        <div className="flex max-w-[12.5rem] w-full">
                          {
                            teamMember.job_role === JobRole.Conveyancer ?
                              <div className="bg-sentimental-pink py-1 px-[0.625rem] rounded-md">
                                <p className="text-base font-medium capitalize text-mull">

                                  {teamMember.job_role}
                                </p>
                              </div>
                              :
                              teamMember.job_role === JobRole.Paralegal ?
                                <div className="bg-peach bg-opacity-10 py-1 px-[0.625rem] rounded-md">
                                  <p className="text-base font-medium capitalize text-peach">
                                    {teamMember.job_role}
                                  </p>
                                </div>
                                :
                                teamMember.job_role === JobRole.Assistant ?
                                  <div className="bg-mint bg-opacity-10 py-1 px-[0.625rem] rounded-md">
                                    <p className="text-base font-medium capitalize text-mint">
                                      {teamMember.job_role}
                                    </p>
                                  </div>
                                  :
                                  teamMember.job_role === JobRole.Other ?
                                    <div className="bg-body bg-opacity-5 py-1 px-[0.625rem] rounded-md">
                                      <p className="text-base font-medium capitalize text-body text-opacity-60">
                                        {teamMember.job_role}
                                      </p>
                                    </div>
                                    : null
                          }
                        </div>

                        <p className="text-body text-opacity-60 break-words">
                          {teamMember.email}
                        </p>

                        <div className="flex ml-auto">


                          {
                            teamMember.email_verified_at === null && teamMember.invite_code_sent_at
                              ? (
                                <div className="flex items-center text-xs gap-7 text-primary">
                                  {
                                    teamMember.invite_code_sent_at
                                      ? <p>Invite sent on {dayjs(teamMember.invite_code_sent_at).format('DD.MM.YYYY')}</p>
                                      : null
                                  }
                                </div>
                              )
                              : null
                          }
                        </div>
                      </div>
                      <hr />
                    </div>
                  ))
                }
              </div>
            </div>

            <div className="flex justify-between mt-[2.5rem]">
              <Link href="/register/id-provider">
                <Button variant="outlined">Back</Button>
              </Link>
              <div className="flex items-center gap-10">
                <Link href="/clients">
                  <Button variant="link">Skip</Button>
                </Link>
                <Button loading={isLoading} type="submit">Next</Button>
              </div>
            </div>

          </Form>
        </SetupLayout.MainContent>
      </SetupLayout>
    </>
  )
}
