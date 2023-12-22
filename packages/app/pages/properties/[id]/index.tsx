import ProtectedLayout from 'layouts/ProtectedLayout'
import { H1, H2, H3, H4 } from '@proconvey/ui/src/components/Headers'
import { useQuery } from 'urql'
import { PropsWithChildren, useEffect, useMemo, useState } from 'react'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { useRouter } from 'next/router'
import { BoardWithSignature, CrossIcon, FileIcon, HomeSmileIcon, IDVerificationIcon, NoteIcon, PackIcon, PropertyImageIcon, SourceOfFundsIcon, StackIcon, TickIcon, WalletIcon } from '@proconvey/ui/src/icons'
import Button from '@proconvey/ui/src/components/Button'
import PropertyFormCard from 'components/PropertyFormCard'
import { graphql } from 'gql'
import Skeleton from 'react-loading-skeleton'
import { Form, FormGroup, PropertyType } from 'gql/graphql'
import calculatePercentage from 'hooks/helpers/calculatePercentage'
import calculateItemsNotCompleted from 'hooks/helpers/calculateItemsNotCompleted'
import Link from 'next/link'
import { NextSeo } from 'next-seo'
import { checkConditionsMet } from 'helpers/steps'

type StaticTaskCard = PropsWithChildren<{
  completed: boolean
  icon: JSX.Element
  title: string
  description: string
}>

export const StaticTaskCard = ({ icon, title, description, completed, children }: StaticTaskCard) => {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 py-[1.25rem] gap-3">
      <div className="flex items-center justify-start flex-grow">
        {icon}
        <div className="flex flex-col flex-grow gap-1 max-w-[600px] w-full mr-3">
          <H4 className="leading-[1.4375rem]">{title}</H4>
          <p className="leading-4 text-body text-opacity-60">
            {description}
          </p>
        </div>
      </div>

      <div className="flex items-center flex-grow md:justify-end">
        {
          completed ? (
            <div className="flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-lg text-mint bg-mint bg-opacity-10">
              <TickIcon className="flex-shrink-0 w-4 h-3 text-mint" />
              <p className="text-base font-normal leading-4 rounded-lg">Completed</p>
            </div>
          ) : (
            <>{children}</>
          )
        }
      </div>
    </div>
  )
}

export default function Property () {
  const [isVisible, setIsVisible] = useState(true)
  const router = useRouter()
  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user!,
  }))

  const [{ data: property, fetching: isPropertyLoading }] = useQuery({
    query: graphql(/* GraphQL */`
      query clientPropertyOverview($id: ID!) {
        property(id: $id) {
          id
          type
          users {
            id
            pivot {
              ...on PropertyUserPivot {
                role
                onboarding_forms_completed_at
                payment_on_account_completed_at
                id_verification_completed_at
              }
            }
          }
          active_forms {
            id
            pivot {
              ... on ActiveFormsPivot {
                id
                title
              }
            }
            name
            image {
              id
              url
            }
            description
            group
            sections {
              id
              conditions {
                id
                selected_value
                type
                answer {
                  id
                }
              }
              steps {
                id
                question
                answers {
                  id
                }
                conditions {
                  id
                  selected_value
                  type
                  answer {
                    id
                  }
                }
              }
            }
          }
          address {
            line_1
            line_2
            city
            postcode
          }
          my_progress {
            payment {
              required
              paid
            }
            provided_answers {
              id
              active_form_id
              value
              answer {
                id
                step {
                  id
                  question
                  section {
                    id
                    form {
                      id
                      name
                      group
                    }
                  }
                }
              }
            }
            onboarding_letters {
              required
              completed
            }
            idv {
              required
              completed
            }
            sof {
              required
              completed
            }
            giftor_deposit_declaration {
              required
              completed
            }
          }
        }
      }
    `),
    variables: {
      id: router.query.id as string,
    },
  })

  const handleHideClick = () => {
    setIsVisible(false)
    localStorage.setItem('hidePropertyBanner', 'false')
  }

  useEffect(() => {
    const storageValue = localStorage.getItem('hidePropertyBanner')
    if (storageValue === 'false') {
      setIsVisible(false)
    }
  }, [])

  const propertyForms = useMemo(() => {
    return property?.property?.active_forms?.reduce((acc, activeForm) => {
      const formAnswers = property?.property?.my_progress?.provided_answers
        ?.filter(providedAnswer => providedAnswer.answer.step.section.form.id === activeForm.id && providedAnswer.active_form_id === activeForm.pivot?.id)

      const totalQuestions = activeForm.sections
        .filter((section) => checkConditionsMet(section.conditions, [], formAnswers ?? []))
        .reduce((total, section) => total + section.steps.filter((step) => checkConditionsMet(step.conditions, [], formAnswers ?? [])).length, 0)

      const totalCompletedQuestions = activeForm.sections
        .filter((section) => checkConditionsMet(section.conditions, [], formAnswers ?? []))
        .reduce((total, section) => total + section.steps.filter((step) => checkConditionsMet(step.conditions, [], formAnswers ?? [])
        && formAnswers?.filter(formAnswer => step.answers.map(answer => answer.id).includes(formAnswer.answer.id) && formAnswer?.value !== null).length,
        ).length, 0)

      // Get the next question that needs to be answerd on the form and generate a URL to it
      const nextQuestion = formAnswers?.find((answer) => answer?.value === null)
      let nextQuestionURL = nextQuestion ? `/${router.query?.id}/forms/${activeForm.pivot?.id}/sections/${nextQuestion.answer.step.section.id}/steps/${nextQuestion.answer.step.id}` : ''

      // Category the active forms by their group type and then their associated form name
      return {
        ...acc,
        [activeForm.group]: [
          ...acc?.[activeForm.group] ?? [],
          {
            ...activeForm,
            nextQuestionURL,
            totalQuestions,
            totalCompletedQuestions,
            isCompleted: totalQuestions === totalCompletedQuestions,
          },
        ],
      }
    }, {} as Record<FormGroup, Array<Form & { isCompleted: boolean, totalQuestions: number, totalCompletedQuestions: number, nextQuestionURL: string }>>)
  }, [property])

  // Calculate the total compeletion for each form group
  const formGroupProgress = useMemo(() => {
    return Object.keys(propertyForms ?? {})?.reduce((prevGroups, propertyGroup) => {
      const groupForms = propertyForms?.[propertyGroup as keyof typeof propertyForms]

      const groupProgress = groupForms?.reduce((prev, curr) => ({
        totalQuestions: (prev?.totalQuestions ?? 0) + curr?.totalQuestions ?? 0,
        totalCompletedQuestions: (prev?.totalCompletedQuestions ?? 0) + curr?.totalCompletedQuestions ?? 0,
      }), { totalQuestions: 0, totalCompletedQuestions: 0 })

      return {
        ...prevGroups,
        [propertyGroup]: groupProgress,
      }}, {} as Record<FormGroup, { totalQuestions: 0, totalCompletedQuestions: 0 }>)
  }, [propertyForms])

  const progressSections = {
    gettingStarted: (property?.property.active_forms ?? []).length > 0
      ? calculatePercentage(formGroupProgress?.GettingStarted?.totalCompletedQuestions ?? 0, formGroupProgress?.GettingStarted?.totalQuestions ?? 0)
      : undefined,
    protocol: property?.property?.type === PropertyType.Sale
      ? calculatePercentage(formGroupProgress?.Protocol?.totalCompletedQuestions ?? 0, formGroupProgress?.Protocol?.totalQuestions ?? 0)
      : undefined,
    enquiry: property?.property?.type === PropertyType.Sale
      ? calculatePercentage(formGroupProgress?.Enquiry?.totalCompletedQuestions ?? 0, formGroupProgress?.Enquiry?.totalQuestions ?? 0)
      : undefined,
    paymentOnAccount: property?.property.my_progress?.payment.required ? (property?.property.my_progress?.payment.paid ? 100 : 0) : undefined,
    idCheck: property?.property?.my_progress?.idv.required ? (property?.property?.my_progress?.idv?.completed ? 100 : 0) : undefined,
    lettersRequired: property?.property?.my_progress?.onboarding_letters?.required ? (property?.property?.my_progress?.onboarding_letters?.completed ? 100 : 0) : undefined,
    sourceOfFunds: property?.property?.my_progress?.sof.required ? (property?.property?.my_progress?.sof?.completed ? 100 : 0) : undefined,
    giftorDeclaration: property?.property.my_progress?.giftor_deposit_declaration.required ? (property?.property?.my_progress?.giftor_deposit_declaration?.completed ? 100 : 0) : undefined,
  }

  const filteredValues = Object.values(progressSections).filter(v => v !== undefined) as number[]
  const totalPercentage = Math.round(filteredValues.reduce((acc, item) => acc + item, 0) / filteredValues.length)
  const numberOfFormsIncomplete = calculateItemsNotCompleted(...filteredValues)

  return (
    <>
      <NextSeo
        title={property?.property.address.line_1 || 'Loading...'}
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <div className="mb-5">
                {isVisible && isPropertyLoading && <Skeleton height={320} />}
                {isVisible && !isPropertyLoading && (
                  <div className="bg-primary bg-opacity-10 w-full rounded-xl flex justify-between pr-[1.375rem] pt-[1.375rem] pl-[1.875rem] pb-[2.8125rem]">
                    <div className="flex justify-between max-w-[510px] w-full">
                      <div className="flex flex-col gap-[30px]">
                        <H1>Welcome to <br /> ProConvey, {user?.first_name}!</H1>
                        <p className="text-base leading-7 text-body">
                          Please complete the tasks below for your conveyancer. The system will take you through each task step by step in order to ensure you are sale or purchase ready.
                        </p>
                      </div>
                    </div>

                    <div className="flex flex-col">
                      <div className="flex justify-end">
                        <Button
                          variant="link"
                          onClick={handleHideClick}
                        >
                          <CrossIcon className="w-3 h-3" />
                        </Button>
                      </div>
                      <PropertyImageIcon className="w-[418px] hidden md:block" />
                    </div>
                  </div>
                )}
              </div>

              <div className="mb-5">
                {
                  isPropertyLoading
                    ?
                    <>
                      <Skeleton height={46} width={200} />
                      <Skeleton height={28} width={130} className="mt-2" />
                    </>
                    :
                    <>
                      <H2>{property?.property?.address?.line_1}, {property?.property?.address?.line_2}</H2>
                      <p className="text-base font-normal leading-7 text-body text-opacity-60">
                        {property?.property?.address?.city}, {property?.property?.address?.postcode}
                      </p>
                    </>
                }
              </div>

              {/* Pack Progress */}
              <div className="bg-white p-5 mb-[1.875rem] rounded-xl border border-primary border-opacity-10">
                <div className="flex items-center justify-between mb-5">
                  <H3 className="flex items-center text-primary">
                    <PackIcon className="mr-[0.8125rem]" />Pack progress
                  </H3>
                </div>

                {
                  isPropertyLoading
                    ? <Skeleton height={180} />
                    : (
                      <div className="w-full mb-4 bg-crystal-blue bg-opacity-10 h-[10px] rounded-[33px]">
                        <div className={'bg-crystal-blue h-[10px] rounded-[33px] text-transparent'} style={{ width: `${Math.min(totalPercentage, 100) ?? 0}%` }}></div>
                      </div>
                    )
                }

                <div className="flex justify-between mb-5">
                  {
                    isPropertyLoading
                      ? <Skeleton height={40} width={171} />
                      : <p className="text-base text-crystal-blue" style={{ fontSize: '30px' }}>{totalPercentage}% completed</p>
                  }

                  {
                    isPropertyLoading
                      ? <Skeleton height={33} width={600} className="mt-5" />
                      : (
                        <p className="text-body/80 text-[1.125rem]">
                        You need to complete {numberOfFormsIncomplete} more section{numberOfFormsIncomplete === 1 ? '' : 's'} to finish your conveyancing pack
                        </p>
                      )
                  }
                </div>
              </div>

              {/* Tasks */}
              {(property?.property?.active_forms.some((activeForm) => activeForm.group === FormGroup.GettingStarted)
                || property?.property.my_progress?.onboarding_letters.required
                || property?.property.my_progress?.payment.required
                || property?.property.my_progress?.idv.required
                || property?.property.my_progress?.sof.required
              ) &&

              <div className="w-full">
                <div className="flex flex-col w-full p-5 bg-white border rounded-lg border-primary border-opacity-10">
                  <H3 className="flex items-center mb-5 text-primary">
                    <HomeSmileIcon className="mr-[0.8125rem]" />Tasks
                  </H3>

                  {
                    isPropertyLoading &&
                    <>
                      <Skeleton height={438} />
                      <Skeleton height={438} />
                      <Skeleton height={438} />
                    </>
                  }

                  <div className="divide-y divide-primary divide-opacity-20">
                    {/* Onboarding Letters - Incomplete */}
                    {
                      property?.property.my_progress?.onboarding_letters.required &&
                      !property?.property.my_progress?.onboarding_letters.completed && (
                        <StaticTaskCard
                          completed={false}
                          title="Onboarding Forms"
                          description="Digitally sign onboarding letters: client care and terms & conditions"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <BoardWithSignature className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        >
                          <Link href={`/properties/${property?.property.id}/onboarding-letters`}>
                            <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]">Continue</Button>
                          </Link>
                        </StaticTaskCard>
                      )
                    }

                    {/* Payment on Account - Incomplete */}
                    {
                      property?.property.my_progress?.payment?.required &&
                      !property?.property.my_progress?.payment.paid && (
                        <StaticTaskCard
                          completed={false}
                          title="Payment on account"
                          description="Send payment to your lawyer to commence work on your case"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-mull bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <WalletIcon className="w-6 h-6 text-mull shrink-0" />
                            </div>
                          }
                        >
                          {
                            property?.property?.my_progress?.onboarding_letters.completed ? (
                              <Link href={`/properties/${property?.property.id}/payment`}>
                                <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]">
                                  Make payment
                                </Button>
                              </Link>
                            ) : (
                              <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]" disabled>
                                Make payment
                              </Button>
                            )
                          }
                        </StaticTaskCard>
                      )
                    }

                    {/* ID Verification - Incomplete */}
                    {
                      property?.property.my_progress?.idv.required &&
                      !property?.property.my_progress?.idv.completed && (
                        <StaticTaskCard
                          completed={false}
                          title="ID verification"
                          description="Digitally verify your identity, perform AML checks for compliance"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <IDVerificationIcon className="w-6 h-6 text-peach shrink-0" />
                            </div>
                          }
                        >
                          {
                            property?.property.my_progress?.payment.paid ? (
                              <Link href={`/properties/${property?.property.id}/idv`}>
                                <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]">
                                  Start ID Check
                                </Button>
                              </Link>
                            ) : (
                              <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]" disabled>
                                Start ID Check
                              </Button>
                            )
                          }
                        </StaticTaskCard>
                      )
                    }

                    {/* Source of Funds - Incomplete */}
                    {
                      property?.property.my_progress?.sof.required &&
                      !property?.property.my_progress?.sof.completed && (
                        <StaticTaskCard
                          completed={false}
                          title="Source of funds"
                          description="We are required by the anti-money laundering legislation to check where the money is coming from to buy the property."
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <SourceOfFundsIcon className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        >
                          {
                            property?.property.my_progress?.idv.completed ? (
                              <Link href={`/properties/${property?.property.id}/sof`}>
                                <Button variant="primary" className="!py-[10px] !px-[25px] !text-sm !leading-4 w-[165px]">
                                  Start SOF check
                                </Button>
                              </Link>
                            ) : (
                              <Button variant="primary" className="!py-[10px] !px-[25px] !text-sm !leading-4 w-[165px]" disabled>
                                Start SOF check
                              </Button>
                            )
                          }
                        </StaticTaskCard>
                      )
                    }


                    {/* Giftor Deposit Declaration - Incomplete */}
                    {
                      property?.property?.my_progress?.giftor_deposit_declaration?.required &&
                      !property?.property.my_progress?.giftor_deposit_declaration.completed && (
                        <StaticTaskCard
                          completed={false}
                          title="Giftor Deposit Declaration"
                          description="You are required to review and sign a gifted deposit declaration for the funds provided in the property purchase."
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <BoardWithSignature className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        >
                          <Link href={`/properties/${property?.property.id}/giftor-declaration`}>
                            <Button variant="primary" className="!py-[10px] !text-sm !leading-4 !px-[25px] w-[165px]">
                              Review and sign
                            </Button>
                          </Link>
                        </StaticTaskCard>
                      )
                    }

                    {/* Getting Started Forms */}
                    {Object.values(propertyForms?.GettingStarted ?? {})
                      .filter(form => !form.isCompleted)
                      .map(groupForm => (
                        <PropertyFormCard
                          key={groupForm.id}
                          title={groupForm.pivot?.title ?? groupForm.name ?? ''}
                          formId={groupForm.pivot?.id}
                          description={groupForm.description}
                          propertyId={router.query.id as string}
                          image={groupForm.image?.url}
                          totalQuestions={groupForm.totalQuestions}
                          completedQuestions={groupForm.totalCompletedQuestions}
                          completed={property?.property.my_progress?.idv.completed as boolean || property?.property.my_progress?.idv.completed === null}
                          url={groupForm.nextQuestionURL}
                          icon={
                            <>
                              {
                                groupForm?.name?.includes('The Owners') ? (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 p-2 mr-3">
                                    <SourceOfFundsIcon className="w-6 h-6 text-peach shrink-0" />
                                  </div>
                                ) : (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-mint bg-opacity-10 p-2 mr-3">
                                    <StackIcon className="w-6 h-6 shrink-0 text-mint" />
                                  </div>
                                )
                              }
                            </>
                          }
                        />
                      ))
                    }

                    {/* Other Form Groups (protocol, enquiry) */}
                    {Object.values(propertyForms ?? {})
                      .flat() // Array of property forms, instead arrays of property group forms
                      .filter(form => (form.group !== FormGroup.GettingStarted) && !form.isCompleted)
                      .map(activeForm => (
                        <PropertyFormCard
                          key={activeForm.id}
                          title={activeForm.pivot?.title ?? activeForm.name ?? ''}
                          formId={activeForm.pivot?.id}
                          description={activeForm.description}
                          propertyId={router.query.id as string}
                          image={activeForm.image?.url}
                          totalQuestions={activeForm.totalQuestions}
                          completedQuestions={activeForm.totalCompletedQuestions}
                          completed={property?.property.my_progress?.idv.completed as boolean || property?.property.my_progress?.idv.completed === null}
                          url={activeForm.nextQuestionURL}
                          icon={
                            <>
                              {
                                activeForm?.group === FormGroup.Protocol ? (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 p-2 mr-3">
                                    <NoteIcon className="w-6 h-6 text-primary shrink-0" />
                                  </div>
                                ) : (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 p-2 mr-3">
                                    <FileIcon className="w-6 h-6 shrink-0 text-peach" />
                                  </div>
                                )
                              }
                            </>
                          }
                        />
                      ))
                    }
                  </div>
                </div>
              </div>
              }

              {/* Completed Tasks */}
              <div className="w-full my-6">
                <div className="flex flex-col w-full p-5 bg-white border rounded-lg min border-primary border-opacity-10">
                  <H3 className="flex items-center mb-5 text-primary">
                    <HomeSmileIcon className="mr-[0.8125rem]" />
                    Completed tasks
                  </H3>

                  <div className="divide-y divide-primary divide-opacity-20">
                    {/* Completed Onboarding Form - Completed */}
                    {
                      property?.property.my_progress?.onboarding_letters.required &&
                      property?.property.my_progress?.onboarding_letters.completed && (
                        <StaticTaskCard
                          completed={true}
                          title="Onboarding Forms"
                          description="Digitally sign onboarding letters: client care and terms & conditions"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <BoardWithSignature className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        />
                      )
                    }

                    {/* Payment on Account Form - Completed */}
                    {
                      property?.property.my_progress?.payment?.required &&
                      property?.property.my_progress?.payment.paid && (
                        <StaticTaskCard
                          completed={true}
                          title="Payment on account"
                          description="Send payment to your lawyer to commence work on your case"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-mull bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <WalletIcon className="w-6 h-6 text-mull shrink-0" />
                            </div>
                          }
                        />
                      )
                    }

                    {/* ID Verification - Completed */}
                    {
                      property?.property.my_progress?.idv.required &&
                      property?.property.my_progress?.idv.completed && (
                        <StaticTaskCard
                          completed={true}
                          title="ID verification"
                          description="Digitally verify your identity, perform AML checks for compliance"
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <IDVerificationIcon className="w-6 h-6 text-peach shrink-0" />
                            </div>
                          }
                        />
                      )
                    }

                    {/* Source of Funds - Completed */}
                    {
                      property?.property.my_progress?.sof.required &&
                      property?.property.my_progress?.sof.completed && (
                        <StaticTaskCard
                          completed={true}
                          title="Source of funds"
                          description="We are required by the anti-money laundering legislation to check where the money is coming from to buy the property."
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <SourceOfFundsIcon className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        />
                      )
                    }

                    {/* Giftor Deposit Declaration - Completd */}
                    {
                      property?.property?.my_progress?.giftor_deposit_declaration?.required &&
                      property?.property.my_progress?.giftor_deposit_declaration.completed && (
                        <StaticTaskCard
                          completed={true}
                          title="Giftor Deposit Declaration"
                          description="You are required to review and sign a gifted deposit declaration for the funds provided in the property purchase."
                          icon={
                            <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 mr-3 w-10 h-10 shrink-0">
                              <BoardWithSignature className="w-6 h-6 text-primary shrink-0" />
                            </div>
                          }
                        />
                      )
                    }

                    {/* Getting Started Forms */}
                    {Object.values(propertyForms?.GettingStarted ?? {})
                      .filter(form => form.isCompleted)
                      .map(groupForm => (
                        <PropertyFormCard
                          key={groupForm.id}
                          title={groupForm.pivot?.title ?? groupForm.name ?? ''}
                          formId={groupForm.pivot?.id}
                          description={groupForm.description}
                          propertyId={router.query.id as string}
                          image={groupForm.image?.url}
                          totalQuestions={groupForm.totalQuestions}
                          completedQuestions={groupForm.totalCompletedQuestions}
                          completed={property?.property.my_progress?.idv.completed as boolean || property?.property.my_progress?.idv.completed === null}
                          url={groupForm.nextQuestionURL}
                          icon={
                            <>
                              {
                                groupForm?.name?.includes('The Owners') ? (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 p-2 mr-3">
                                    <SourceOfFundsIcon className="w-6 h-6 text-peach shrink-0" />
                                  </div>
                                ) : (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-mint bg-opacity-10 p-2 mr-3">
                                    <StackIcon className="w-6 h-6 shrink-0 text-mint" />
                                  </div>
                                )
                              }
                            </>
                          }
                        />
                      ))
                    }

                    {/* Other Form Groups (protocol, enquiry) */}
                    {Object.values(propertyForms ?? {})
                      .flat() // Array of property forms, instead arrays of property group forms
                      .filter(form => (form.group !== FormGroup.GettingStarted) && form.isCompleted)
                      .map(activeForm => (
                        <PropertyFormCard
                          key={activeForm.id}
                          title={activeForm.pivot?.title ?? activeForm.name ?? ''}
                          formId={activeForm.pivot?.id}
                          description={activeForm.description}
                          propertyId={router.query.id as string}
                          image={activeForm.image?.url}
                          totalQuestions={activeForm.totalQuestions}
                          completedQuestions={activeForm.totalCompletedQuestions}
                          completed={property?.property.my_progress?.idv.completed as boolean || property?.property.my_progress?.idv.completed === null}
                          url={activeForm.nextQuestionURL}
                          icon={
                            <>
                              {
                                activeForm?.group === FormGroup.Protocol ? (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-primary bg-opacity-10 p-2 mr-3">
                                    <NoteIcon className="w-6 h-6 text-primary shrink-0" />
                                  </div>
                                ) : (
                                  <div className="grid rounded-[0.625rem] place-content-center bg-peach bg-opacity-10 p-2 mr-3">
                                    <FileIcon className="w-6 h-6 shrink-0 text-peach" />
                                  </div>
                                )
                              }
                            </>
                          }
                        />
                      ))
                    }
                  </div>
                </div>
              </div>
            </div>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}
