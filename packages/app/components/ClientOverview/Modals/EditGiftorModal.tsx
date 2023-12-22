import Button from '@proconvey/ui/src/components/Button'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import Modal from '@proconvey/ui/src/components/Modals'
import { Giftor } from 'components/GiftorsTable'
import QuestionCard from 'components/QuestionCard'
import { graphql } from 'gql'
import { User } from 'gql/graphql'
import useErrorHandler from 'hooks/useErrorHandler'
import { ProvidedAnswerProps } from 'pages/properties/[id]/forms/[formId]/sections/[sectionId]/steps/[stepId]'
import { useEffect, useMemo, useState } from 'react'
import { useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import { useMutation, useQuery } from 'urql'

type PropTypes = {
  propertyId: string
  onClose: () => void
  refetch: Function
  giftorUser?: User
  giftor?: Giftor
}

const EditGiftorModal = ({
  propertyId,
  onClose,
  refetch,
  giftorUser,
  giftor,
}: PropTypes) => {
  const { setError, clearErrors, formState: { errors } } = useForm<ProvidedAnswerProps>()
  const errorHandler = useErrorHandler()

  const shouldPause = useMemo(() => {
    if (!propertyId || !giftor?.step_id || !giftor?.active_form_id) {
      return true
    } else {
      return false
    }
  }, [propertyId, giftor?.step_id, giftor?.active_form_id])

  const [{ fetching, data: stepData }] = useQuery({
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
      id: giftor?.step_id as string,
      propertyId: propertyId,
      activeFormId: giftor?.active_form_id as string,
    },
    pause: shouldPause,
  })

  const repeatableValue = useMemo(() => {
    if (stepData) {
      return parseInt(stepData.step.repeatable_answer?.provided_answers?.[0]?.value) ?? 1
    }
    return undefined
  }, [stepData])

  const [giftorAnswerValues, setGiftorAnswersValues] = useState<{ id: string, value: any }[]>([])

  const updateAnswerValues = (answerId: string, answerValue: string) => {
    if (!stepData || !giftor?.index) {
      toast.error('Something went wrong updating giftor details, please try again.')
      return
    }

    const answers = giftorAnswerValues
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
    return setGiftorAnswersValues(answers)
  }


  const myProgressAnswers = useMemo(() => {
    if (stepData && giftor?.active_form_id) {
      return stepData?.step?.provided_answers?.filter((item) => item?.active_form_id === giftor?.active_form_id.toString())
    }
  }, [stepData, giftor?.active_form_id])


  useEffect(() => {
    if (stepData?.step?.provided_answers) {
      setGiftorAnswersValues(stepData.step.provided_answers.map((pa) => ({ id: pa?.answer.id!, value: pa?.value })))
    }

  }, [stepData])

  const [{ fetching: fetchingSavingAnswer }, saveAnswerMutation] = useMutation(graphql(`
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

  const onGiftorDetailsSubmit = async () => {
    const response = await saveAnswerMutation({
      input: {
        provided_answers: giftorAnswerValues.filter(answer => answer.value !== null).map((answer) => ({
          answer_id: answer.id,
          property_id: propertyId,
          value: answer.value,
          active_form_id: giftor?.active_form_id as string,
        })),
        current_property_id: propertyId,
        current_step_id: giftor?.step_id as string,
      },
    })

    if (response.error) {
      toast.error('Something went wrong, please try again.')
      errorHandler(response.error, setError)
    } else {
      toast.success('Giftor has been updated successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={!!giftorUser} onClose={onClose}>
      {
        fetching
          ?
          <div className="flex m-auto">
            <LoadingSpinner />
          </div>
          :
          !fetching && stepData &&
          <div>
            <QuestionCard
              question={stepData.step.question as string}
              subHeading={stepData.step.sub_heading}
              repeatableValue={repeatableValue}
              currentRepeatableIndex={giftor?.index ? parseInt(giftor?.index) : undefined}
              image={stepData.step.image?.url as string}
              isLoading={fetching}
              answerType={stepData.step.answers.map((item) => item.type) as any}
              myProgressAnswers={myProgressAnswers as any}
              answers={stepData.step.answers as any[]}
              onChange={(value, providedAnswerID) => updateAnswerValues(providedAnswerID, value)}
              errors={errors}
              isImagePresent={false}
              clearErrors={clearErrors}
              stepType={stepData.step?.type}
            />
          </div>
      }
      <Modal.Footer>
        <Button size="small" onClick={() => onGiftorDetailsSubmit()} loading={fetchingSavingAnswer}>Save</Button>
        <Button size="small" onClick={onClose} variant="secondary">Cancel</Button>
      </Modal.Footer>
    </Modal>
  )
}

export default EditGiftorModal
