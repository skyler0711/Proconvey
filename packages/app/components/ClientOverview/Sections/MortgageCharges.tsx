import Button from '@proconvey/ui/src/components/Button'
import { BinIcon, PencilIcon } from '@proconvey/ui/src/icons'
import { useMutation, useQuery } from 'urql'
import { graphql } from 'gql'
import { useRouter } from 'next/router'
import { H3 } from '@proconvey/ui/src/components/Headers'
import Skeleton from 'react-loading-skeleton'
import Card from '@proconvey/ui/src/components/Card'
import Table from '@proconvey/ui/src/components/Table'
import { useEffect, useMemo, useState } from 'react'
import { AnswerDetailsPersonMultiSelect, AnswerType, StepType, Property, PropertyType } from 'gql/graphql'
import Modal from '@proconvey/ui/src/components/Modals'
import { useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import useErrorHandler from 'hooks/useErrorHandler'
import { ProvidedAnswerProps } from 'pages/properties/[id]/forms/[formId]/sections/[sectionId]/steps/[stepId]'
import QuestionCard from 'components/QuestionCard'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'

type MortgageChargesProps = {
  isLoading: boolean
  property: Property | undefined
  details: any
  refetch: () => void
}

export type Charge = {
  index: string
  account_number: string
  amount_outstanding: string
  approx_repayment_charge: string | null
  chargee: string
  early_repayment_charge: string
  step_id: number
  type: string
  active_form_id: string
}

enum ChargeType {
  MORTGAGE = 'Mortgage',
  LOAN = 'Loan',
  CHARGE = 'Charge',
}

const MortgageCharges = ({
  isLoading,
  property,
  details,
  refetch,
}: MortgageChargesProps) => {
  const router = useRouter()

  const [chargeIndex, setChargeIndex] = useState<string|undefined>(undefined)
  const [stepId, setStepId] = useState<string|undefined>(undefined)
  const [activeFormId, setActiveFormId] = useState<string|undefined>(undefined)

  const [removeMortgageModalOpen, setRemovalMortgageModalOpen] = useState<boolean>(false)
  const [manageMortgageModalOpen, setManageMortgageModalOpen] = useState<boolean>(false)

  const { setError, clearErrors, formState: { errors } } = useForm<ProvidedAnswerProps>()
  const errorHandler = useErrorHandler()

  const handleMortgageModal = (
    isOpen: boolean = false,
    modalType: 'edit' | 'removal',
    stepId: string | undefined = undefined,
    activeFormId: string | undefined = undefined,
    chargeIndex: string | undefined = undefined,
  ) => {
    setStepId(stepId)

    if (modalType === 'edit') {
      setActiveFormId(activeFormId)
      setManageMortgageModalOpen(isOpen)
      setChargeIndex(chargeIndex)
    } else if (modalType === 'removal') {
      setChargeIndex(chargeIndex)
      setRemovalMortgageModalOpen(isOpen)
    }

    if (!isOpen) {
      setStepId(undefined)
      setActiveFormId(undefined)
      setChargeIndex(undefined)
    }
  }

  const [{ fetching: deleteMortgageLoading, error: deleteMortgageError }, deleteMortgageMutation] = useMutation(graphql(`
    mutation deleteMortgage($step_id: ID!, $property_id: ID!, $charge_index: ID!) {
      deleteMortgage(step_id: $step_id, property_id: $property_id, charge_index: $charge_index)
    }
  `))

  const handleDeleteMortgage = async () => {
    if (stepId && property?.id && chargeIndex) {
      await deleteMortgageMutation({
        step_id: stepId,
        property_id: property.id,
        charge_index: chargeIndex,
      })
    } else {
      return toast.error('There was a problem removing the mortgage, please try again soon')
    }

    if (deleteMortgageError) {
      toast.error('There was a problem removing the mortgage, please try again soon')
    } else {
      toast.success('Mortgage has been removed')
      handleMortgageModal(false, 'removal')
      refetch()
    }
  }

  const shouldPause = useMemo(() => {
    if (!stepId || !activeFormId) {
      return true
    } else {
      return false
    }
  }, [stepId, activeFormId])


  const [{ fetching: stepFetching, data: stepData }] = useQuery({
    query: graphql(`
      query step($id: ID!, $propertyId: ID!, $activeFormId: ID!) {
        step(id: $id) {
          id
          question
          sub_heading
          type
          image {
            id
            url
          }
          help_text
          conditions {
            id
            selected_value
            type
            answer {
              id
            }
          }
          help_video_link
          repeatable_answer {
            id
            step {
              id
            }
            provided_answers(property_id: $propertyId, active_form_id: $activeFormId) {
                id
                value
            }
            conditions {
              id
            }
          }
          provided_answers(property_id: $propertyId, active_form_id: $activeFormId) {
            id
            answer {
              id
            }
            active_form_id
            value
          }
          answers {
            id
            conditions {
              id
              selected_value
              type
              answer {
                id
              }
            }
            type
              details {
                ...on AnswerDetailsText {
                  label
                  placeholder
                }
                ...on AnswerDetailsDropdown {
                  label
                  options {
                    value
                  }
                }
              }
            }
        }
      }
    `),
    variables: {
      id: stepId as string,
      propertyId: router.query.id as string,
      activeFormId: activeFormId as string,
    },
    pause: shouldPause,
  })

  const mortgages = useMemo(() => {
    if (details) {
      if (details?.isPropertyRemortgage) {
        return details.outstandingMortgages.filter((charge: Charge) => charge.type === ChargeType.MORTGAGE)
      } else {
        return details.charges.filter((charge: Charge) => charge.type === ChargeType.MORTGAGE)
      }
    }
  }, [details]) ?? []

  const myProgressAnswers = useMemo(() => {
    if (stepData && activeFormId) {
      return stepData?.step?.provided_answers?.filter((item) => item?.active_form_id === activeFormId)
    }
  }, [stepData, activeFormId])

  const repeatableValue = useMemo(() => {
    if (stepData) {
      if (stepData.step.repeatable_answer === null) {
        if (stepData.step.type === StepType.MortgageRelatedTransactions) {
          return (stepData.step.answers?.find(answer => answer.type === AnswerType.PersonMultiSelect)?.details as AnswerDetailsPersonMultiSelect)?.options?.length
        }
      } else {
        return parseInt(stepData.step.repeatable_answer?.provided_answers?.[0]?.value) ?? 1
      }
    }

    return undefined
  }, [stepData])

  const [mortgageAnswerValues, setMortgageAnswersValues] = useState<{ id: string, value: any }[]>([])

  const updateAnswerValues = (answerId: string, answerValue: string) => {
    if (!stepData || !chargeIndex) {
      toast.error('Something went wrong updating mortgage details, please try again.')
      return
    }


    const answers = mortgageAnswerValues
    const existingAnswerIndex = answers.findIndex(answer => answer.id === answerId)

    if (existingAnswerIndex !== -1) {
      if (answerValue === '') {
        answers.splice(existingAnswerIndex, 1)
      } else {
        answers[existingAnswerIndex].value = answerValue
      }
    } else {
      if (answerValue !== '') {
        answers.push({ id: answerId, value: answerValue })
      }
    }
    return setMortgageAnswersValues(answers)
  }

  useEffect(() => {
    if (manageMortgageModalOpen && stepData?.step?.provided_answers) {
      setMortgageAnswersValues(stepData.step.provided_answers.map((pa) => ({ id: pa?.answer.id!, value: pa?.value })))
    }

  }, [manageMortgageModalOpen, stepData])

  const [{ fetching: fetchingSavingAnswer }, saveAnswerMutation] = useMutation(graphql(/* GraphQL */`
    mutation saveProvidedAnswers($input: SaveProvidedAnswersInput!) {
      saveProvidedAnswers(input: $input) {
        provided_answers {
          id
          value
          answer {
            id
            step {
              id
            }
          }
        }
      }
    }
  `))

  const onMortgageDetailsSubmit = async () => {
    const response = await saveAnswerMutation({
      input: {
        provided_answers: mortgageAnswerValues.filter(answer => answer.value !== null).map((answer) => ({
          answer_id: answer.id,
          property_id: router.query.id as string,
          value: answer.value,
          active_form_id: activeFormId as string,
        })),
        current_property_id: router.query.id as string,
        current_step_id: stepId as string,
      },
    })

    if (response.error) {
      errorHandler(response.error, setError)
    } else {
      toast.success('Mortgage has been updated')
      handleMortgageModal(false, 'edit')
    }

  }

  return <>
    {
      isLoading
        ? <Card className="mt-5">
          <Card.Header>
            <Skeleton width="15%" height={33} />
          </Card.Header>
          <Card.Body>
            <Table>
              <Table.Head>
                <Table.Cell><Skeleton width="30%" /></Table.Cell>
                <Table.Cell><Skeleton width="60%" /></Table.Cell>
                <Table.Cell><Skeleton width="80%" /></Table.Cell>
                <Table.Cell><Skeleton width="75%" /></Table.Cell>
                <Table.Cell><Skeleton width="75%" /></Table.Cell>
              </Table.Head>
            </Table>
          </Card.Body>
        </Card>
        /* As mortgages replaces charges on Purchases, conditionally show with skeleton loader  */
        : property && property.type === PropertyType.Purchase && details
          ? <MortgagesCard
            details={details}
            mortgages={mortgages}
            isLoading={isLoading}
            handleRemoveOnClick={(isOpen, id, index) => handleMortgageModal(isOpen, 'removal', id, undefined, index)}
            handleEditOnClick={(isOpen, id, activeFormId, index) => handleMortgageModal(isOpen, 'edit', id, activeFormId, index)}
          />
          : <>
            <ChargesCard
              details={details}
              isLoading={isLoading}
            />
            <MortgagesCard
              details={details}
              mortgages={mortgages}
              isLoading={isLoading}
              handleRemoveOnClick={(isOpen, id, index) => handleMortgageModal(isOpen, 'removal', id, undefined, index)}
              handleEditOnClick={(isOpen, id, activeFormId, index) => handleMortgageModal(isOpen, 'edit', id, activeFormId, index)}
            />
          </>
    }

    {/* Remove mortgage modal */}
    <Modal isOpen={removeMortgageModalOpen} onClose={() => handleMortgageModal(false, 'removal')} onAnimationEnd={() => setStepId(undefined)}>
      <Modal.Title>Remove Mortgage</Modal.Title>
      <p>
        Are you sure you want to remove this mortgage?
      </p>
      <Modal.Footer>
        <Button size="small" onClick={() => handleDeleteMortgage()} loading={deleteMortgageLoading}>Yes, remove this mortgage</Button>
        <Button variant="secondary" size="small" onClick={() => handleMortgageModal(false, 'removal')}>Cancel</Button>
      </Modal.Footer>
    </Modal>

    {/* Update mortgage modal */}
    <Modal isOpen={manageMortgageModalOpen} onClose={() => handleMortgageModal(false, 'edit')} size="medium">
      {
        stepFetching &&
          <div className="flex m-auto">
            <LoadingSpinner />
          </div>
      }
      {
        !stepFetching && stepData &&
          <div>
            <QuestionCard
              question={stepData.step.question as string}
              subHeading={stepData.step.sub_heading}
              repeatableValue={repeatableValue}
              currentRepeatableIndex={chargeIndex ? parseInt(chargeIndex) : undefined}
              image={stepData.step.image?.url as string}
              isLoading={stepFetching}
              answerType={stepData.step.answers.map((item) => item.type) as any}
              myProgressAnswers={myProgressAnswers as any}
              answers={stepData.step.answers as any[]}
              onChange={(value, providedAnswerID) => updateAnswerValues(providedAnswerID, value)}
              errors={errors}
              clearErrors={clearErrors}
              stepType={stepData.step?.type}
              isImagePresent={false}
            />
          </div>
      }
      <Modal.Footer>
        <Button size="small" onClick={() => onMortgageDetailsSubmit()} loading={fetchingSavingAnswer}>Save</Button>
        <Button variant="secondary" size="small" onClick={() => handleMortgageModal(false, 'edit')}>Cancel</Button>
      </Modal.Footer>
    </Modal>
  </>
}

const MortgagesCard = ({ mortgages, isLoading, handleRemoveOnClick, handleEditOnClick, details = undefined }: {
  details: any,
  mortgages: Charge[],
  isLoading: boolean,
  handleRemoveOnClick: (isOpen: boolean, id: string, index: string) => void
  handleEditOnClick: (isOpen: boolean, stepId: string, activeFormId: string, index: string) => void
}) => (
  <Card className="mt-5">
    <Card.Header>
      {
        details && details?.isPropertyRemortgage
          ? <H3>Outstanding Mortgages</H3>
          : <H3>Mortgages</H3>
      }
    </Card.Header>

    <Card.Body padContent={false}>
      <Table>
        <Table.Head>
          <Table.Row>
            <Table.Cell as="th" className="text-[0.75rem]">
              Mortgage Lender
            </Table.Cell>
            <Table.Cell as="th" className="text-[0.75rem]">
              Mortgage Amount
            </Table.Cell>
            <Table.Cell></Table.Cell>
          </Table.Row>
        </Table.Head>

        <Table.Body>


          {
            !isLoading && mortgages.length === 0 &&
            <Table.Row>
              <Table.Cell colSpan={5}>
                <p className="text-center">No mortgages</p>
              </Table.Cell>
            </Table.Row>
          }

          {
            !isLoading && mortgages.map((charge: Charge, index: number) => (
              <Table.Row key={index}>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.chargee}
                </Table.Cell>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.amount_outstanding}
                </Table.Cell>
                <Table.Cell>
                  <div className="flex justify-end gap-2">
                    <Button variant="plain" onClick={() => handleRemoveOnClick(true, charge.step_id.toString(), charge.index)}>
                      <BinIcon className="w-4 h-5" />
                    </Button>
                    <Button variant="plain" onClick={() => handleEditOnClick(true, charge.step_id.toString(), charge.active_form_id, charge.index)}>
                      <PencilIcon className="w-4 h-5" />
                    </Button>
                  </div>
                </Table.Cell>
              </Table.Row>
            ))
          }

        </Table.Body>

      </Table>
    </Card.Body>
  </Card>
)

const ChargesCard = ({ details, isLoading }: {details: any, isLoading: boolean}) => (
  <Card className="mt-5">
    <Card.Header>
      <H3>Charges</H3>
    </Card.Header>

    <Card.Body padContent={false}>
      <Table>
        <Table.Head>
          <Table.Row>
            <Table.Cell as="th" className="text-[0.75rem]">
            Chargee
            </Table.Cell>
            <Table.Cell as="th" className="text-[0.75rem]">
            Account number
            </Table.Cell>
            <Table.Cell as="th" className="text-[0.75rem]">
            Approx. amount outstanding
            </Table.Cell>
            <Table.Cell as="th" className="text-[0.75rem]">
            Early repayment charge
            </Table.Cell>
            <Table.Cell as="th" className="text-[0.75rem]">
            Approx. repayment charge
            </Table.Cell>
          </Table.Row>
        </Table.Head>

        <Table.Body>
          {
            !isLoading && details.charges.length === 0 &&
          <Table.Row>
            <Table.Cell colSpan={5}>
              <p className="text-center">No charges</p>
            </Table.Cell>
          </Table.Row>
          }

          {
            !isLoading && details.charges.map((charge: Charge, index: number) => (
              <Table.Row key={index}>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.chargee}
                </Table.Cell>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.account_number}
                </Table.Cell>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.amount_outstanding}
                </Table.Cell>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.early_repayment_charge}
                </Table.Cell>
                <Table.Cell className="!text-[0.875rem]">
                  {charge.approx_repayment_charge ?? ''}
                </Table.Cell>
              </Table.Row>
            ))
          }
        </Table.Body>
      </Table>
    </Card.Body>
  </Card>
)

export default MortgageCharges
