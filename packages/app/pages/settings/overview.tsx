import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import { ChevronDownIcon, TickIcon, CrossIcon } from '@proconvey/ui/src/icons'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import Button from '@proconvey/ui/src/components/Button'
import Link from 'next/link'
import { useQuery } from 'urql'
import { graphql } from 'gql'
import Tag from '@proconvey/ui/src/components/Tag'
import Skeleton from 'react-loading-skeleton'
import { NextSeo } from 'next-seo'
import { JobRole } from 'types/enums/JobRole'
import classNames from 'classnames'

export default function Overview () {

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query settingsOverviewConveyancer {
        me {
          id
          conveyancer {
            id
            name
            sra_clc_number
            team_member_count
            stripe_account_id
            client_care_letter
            terms_and_conditions
            subscription {
              plan_name
              payment_method {
                type
                brand
                last4
              }
            }
          }
          notification_preferences {
            getting_started_forms_completed
            onboarding_completed
            client_new_document_uploads
          }
        }
      }
    `),
  })

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

  const teamMembers = conveyancer?.me?.conveyancer?.team_members

  const TeamMemberCount = conveyancer?.me?.conveyancer?.team_members?.filter((member: { id: string | undefined }) => member.id !== user?.id)?.length ?? 0

  return (
    <>
      <NextSeo
        title="Settings Overview"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[1.875rem]">
              <H1>Overview</H1>
            </div>


            <div className="grid grid-cols-1 gap-5 mb-5 sm:grid-cols-2">
              <div className="w-full max-w-[715px] min-w-[300px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Profile</H3>
                  <Link href="/settings/profile" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[100px]">Name</p>
                  <p className="text-sm text-left">{user?.first_name} {user?.last_name}</p>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[100px]">Email</p>
                  <p className="text-sm break-all sm:break-normal">{user?.email}</p>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[100px]">Password</p>
                  <p className="text-sm font-extrabold">•••••••••</p>
                </div>
              </div>

              <div className="w-full max-w-[715px] min-w-[300px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Business</H3>
                  <Link href="/settings/business" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[150px]">Name</p>
                  <p className="flex-1 text-sm">{fetching ? <Skeleton /> : (data?.me?.conveyancer?.name || 'Not provided')}</p>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[150px]">SRA/CLC number</p>
                  <p className="flex-1 text-sm">{fetching ? <Skeleton /> : (data?.me?.conveyancer?.sra_clc_number || 'Not provided')}</p>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[150px]">Team Members</p>
                  <p className="flex-1 text-sm">{fetching ? <Skeleton /> : (data?.me?.conveyancer?.team_member_count || 'Not provided')}</p>
                </div>
              </div>
            </div>


            <div className="flex flex-wrap gap-5 mb-5 md:flex-nowrap">
              <div className="w-full min-w-[300px] max-w-[715px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Onboarding</H3>
                  <Link href="/settings/onboarding" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                <div className="flex items-center justify-between h-full px-5 py-6">
                  <p className="text-sm font-medium text-body">Onboarding letters setup</p>
                  {
                    fetching
                      ? <div className="text-right"><Skeleton width={150} /></div>
                      : (
                        data?.me?.conveyancer?.client_care_letter && data?.me?.conveyancer?.terms_and_conditions
                          ? (
                            <Tag variant="success">
                              <TickIcon className="w-3 h-3" />
                              <p>Completed</p>
                            </Tag>
                          )
                          : (
                            <Tag>
                              <CrossIcon className="w-3 h-3" />
                              <p>Not completed</p>
                            </Tag>
                          )
                      )
                  }
                </div>
                <hr />
                <div className="flex items-center justify-between h-full px-5 py-6">
                  <p className="text-sm font-medium text-body">Payment on account setup</p>
                  {
                    fetching
                      ? <div className="text-right"><Skeleton width={150} /></div>
                      : (
                        data?.me?.conveyancer?.stripe_account_id
                          ? (
                            <Tag variant="success">
                              <TickIcon className="w-3 h-3" />
                              <p>Connected</p>
                            </Tag>
                          )
                          : (
                            <Tag>
                              <CrossIcon className="w-3 h-3" />
                              <p>Not connected</p>
                            </Tag>
                          )
                      )
                  }
                </div>
              </div>

              <div className="w-full max-w-[715px] min-w-[300px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Billing</H3>
                  <Link href="/settings/billing" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[100px]">Plan</p>
                  <p className="text-sm text-left">{data?.me?.conveyancer?.subscription?.plan_name || <Skeleton width={150} />}</p>
                </div>
                <hr />
                <div className="flex px-5 py-6">
                  <p className="text-body text-opacity-60 text-sm w-[100px]">Payment</p>
                  <p className="text-sm">

                    {
                      fetching
                        ? <Skeleton width={150} />
                        : (
                          data?.me?.conveyancer?.subscription?.payment_method
                            ? <><span className="capitalize">{data?.me?.conveyancer?.subscription?.payment_method?.brand}</span> ending {data?.me?.conveyancer?.subscription?.payment_method?.last4}</>
                            : <span>No payment method</span>
                        )
                    }
                  </p>
                </div>
              </div>
            </div>

            <div className="flex flex-wrap gap-5 mb-20 md:flex-nowrap">
              <div className="w-full max-w-[715px] min-w-[300px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Notifications</H3>
                  <Link href="/settings/notification-preferences" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                <div className="flex items-center justify-between h-full px-5">
                  <p className="flex py-6 text-sm font-medium text-body">Getting started forms completed</p>
                  {
                    fetching
                      ? <div className="text-right"><Skeleton width={150} /></div>
                      : (
                        data?.me?.notification_preferences?.getting_started_forms_completed
                          ? (
                            <Tag variant="success">
                              <TickIcon className="w-3 h-3" />
                              <p>On</p>
                            </Tag>
                          )
                          : (
                            <Tag>
                              <CrossIcon className="w-3 h-3" />
                              <p>Off</p>
                            </Tag>
                          )
                      )
                  }
                </div>
                <hr />

                <div className="flex items-center justify-between h-full px-5">
                  <p className="flex py-6 text-sm font-medium text-body">Onboarding completed</p>
                  {
                    fetching
                      ? <div className="text-right"><Skeleton width={150} /></div>
                      : (
                        data?.me?.notification_preferences?.onboarding_completed
                          ? (
                            <Tag variant="success">
                              <TickIcon className="w-3 h-3" />
                              <p>On</p>
                            </Tag>
                          )
                          : (
                            <Tag>
                              <CrossIcon className="w-3 h-3" />
                              <p>Off</p>
                            </Tag>
                          )
                      )
                  }
                </div>
                <hr />

                <div className="flex items-center justify-between h-full px-5">
                  <p className="flex py-6 text-sm font-medium text-body">Client new document uploads</p>
                  {
                    fetching
                      ? <div className="text-right"><Skeleton width={150} /></div>
                      : (
                        data?.me?.notification_preferences?.client_new_document_uploads
                          ? (
                            <Tag variant="success">
                              <TickIcon className="w-3 h-3" />
                              <p>On</p>
                            </Tag>
                          )
                          : (
                            <Tag>
                              <CrossIcon className="w-3 h-3" />
                              <p>Off</p>
                            </Tag>
                          )
                      )
                  }
                </div>
                <hr />

              </div>

              <div className="w-full max-w-[715px] min-w-[300px] bg-white rounded-xl flex flex-col">
                <div className="flex justify-between px-5 py-6">
                  <H3>Team members</H3>
                  <Link href="/settings/team-members" className="flex items-center gap-1">
                    <Button variant="link" className="text-sm font-medium text-primary">See more</Button>
                    <ChevronDownIcon className="w-4 h-3 -rotate-90 text-primary" />
                  </Link>
                </div>
                <hr />
                {
                  teamMembers && TeamMemberCount > 0
                    ? teamMembers?.filter((member: { id: string | undefined }) => member.id !== user?.id).map((member, index) => (
                      <>
                        <div className="flex items-center justify-between h-full px-5">
                          {
                            (member.first_name != null || member.last_name != null) ?
                              <p className="flex py-6 text-sm font-medium text-body">{member.first_name} {member.last_name}</p>
                              :
                              <p> - </p>
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
                        </div>
                        <hr />
                      </>
                    ))
                    :
                    <p className="my-5 text-center">No team members</p>
                }
              </div>
            </div>

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
