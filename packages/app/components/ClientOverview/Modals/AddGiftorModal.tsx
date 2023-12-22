import Button from '@proconvey/ui/src/components/Button'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import Modal from '@proconvey/ui/src/components/Modals'
import QuestionCard from 'components/QuestionCard'
import { graphql } from 'gql'
import { FormType, Property, PropertyType, Step, StepType } from 'gql/graphql'
import useErrorHandler from 'hooks/useErrorHandler'
import { ProvidedAnswerProps } from 'pages/properties/[id]/forms/[formId]/sections/[sectionId]/steps/[stepId]'
import { useCallback, useEffect, useMemo, useState } from 'react'
import { useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import { useMutation, useQuery } from 'urql'

type PropTypes = {
  isOpen: boolean
  onClose: () => void
  property?: Property
  refetch: Function
}

const AddGiftorModal = ({
  isOpen,
  onClose,
  property,
  refetch,
}: PropTypes) => {
  const { setError, clearErrors, formState: { errors } } = useForm<ProvidedAnswerProps>()
  const errorHandler = useErrorHandler()

  const activeFormId = property?.active_forms?.find((activeForm) => activeForm.ta_form_template === FormType.GettingStarted)?.pivot?.id

  const shouldPause = useMemo(() => {
    if (!property?.id) {
      return true
    } else {
      return false
    }
  }, [property?.id])

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query getFormQuestion ($id: ID!) {
        property(id: $id) {
          id
          my_progress {
            provided_answers {
              id
              active_form_id
              value
              answer {
                id
                step {
                  id
                  section {
                    id
                  }
                }
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
            ta_form_template
            group
            conditions {
              id
              type
              answer {
                id
                type
              }
            }
            sections {
              id
              name
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
                  conditions {
                    id
                  }
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
                      ...on AnswerDetailsSingleSelect {
                        label
                        options {
                          value
                        }
                      }
                      ...on AnswerDetailsText {
                        label
                        placeholder
                      }
                      ...on AnswerDetailsTextarea {
                        label
                        placeholder
                      }
                      ...on AnswerDetailsAddress {
                        label
                      }
                      ...on AnswerDetailsDropdown {
                        label
                        options {
                          value
                        }
                      }
                      ...on AnswerDetailsOwnerDropdown {
                        label
                        options {
                          value
                        }
                      }
                      ...on AnswerDetailsCheckbox {
                        label
                      }
                      ...on AnswerDetailsDataTable {
                        allowsAddMore
                        addMoreLabel
                        rows {
                          name
                        }
                        columns {
                          name
                          type
                          placeholder
                        }
                      }
                      ...on AnswerDetailsPersonMultiSelect {
                        label
                        options {
                          value
                        }
                      }
                    }
                }
              }
            }
          }
        }
      } 
    `),
    variables: {
      id: property?.id as string,
    },
    pause: shouldPause,
  })

  const isGiftorStep = useCallback((step: Step) => {
    return property?.type === PropertyType.Purchase
      ? step.type === StepType.BuyerGiftor
      : step.type === StepType.RemortgageGiftor
  }, [property?.type])

  const currentForm = useMemo(() => data?.property?.active_forms?.find(
    (activeForm) => activeForm.sections.find(
      (section) => section.steps.find((step) => isGiftorStep(step as Step),
      ),
    ),
  ), [data, isGiftorStep])
  const currentSection = useMemo(() => currentForm?.sections?.find(
    (section) => section.steps.find((step) => isGiftorStep(step as Step)),
  ), [currentForm, isGiftorStep])
  const currentStep = useMemo(() => currentSection?.steps?.find(step => isGiftorStep(step as Step)), [currentSection, isGiftorStep])

  const [newGiftorAnswerValues, setNewGiftorAnswerValues] = useState<{ id: string, value: any }[]>([])

  const updateAnswerValues = (answerId: string, answerValue: string) => {
    if (!currentStep) {
      toast.error('Something went wrong updating giftor details, please try again.')
      return
    }

    const answers = newGiftorAnswerValues
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
    return setNewGiftorAnswerValues(answers)
  }

  const myProgressAnswers = useMemo(() => data?.property?.my_progress?.provided_answers.filter((item) => item.active_form_id === activeFormId), [activeFormId, data?.property?.my_progress?.provided_answers])

  useEffect(() => {
    if (myProgressAnswers) {
      const providedAnswers = myProgressAnswers
        .filter((item) => item.answer.step.id === currentStep?.id)
        .map(item => ({ id: item.answer.id, value: item.value }))

      setNewGiftorAnswerValues(providedAnswers)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data, currentStep?.id])

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
    if (!property?.id || !currentStep?.id || !activeFormId) {
      toast.error('Something went wrong, please try again.')
      return
    }

    const oldAnswers: { id: string, value: any }[] = myProgressAnswers
      ? myProgressAnswers
        .filter((item) => item.answer.step.id === currentStep?.id)
        .map(item => ({ id: item.answer.id, value: item.value }))
      : []

    let answersToSubmit: { id: string, value: any }[] = oldAnswers

    newGiftorAnswerValues?.filter(item => !!item.value)?.forEach(({ id, value }) => {
      if (answersToSubmit.find(answer => answer.id === id)?.value !== value) {
        const existingAnswerIndex = answersToSubmit.findIndex(answer => answer.id === id)

        if (existingAnswerIndex !== -1) {
          answersToSubmit[existingAnswerIndex].value = [
            ...answersToSubmit[existingAnswerIndex].value,
            value,
          ]
        } else {
          answersToSubmit.push({ id, value: [value] })
        }
      }
    })

    // New step repeatable value answer
    let newRepeatableAnswerIndex = undefined

    if (currentStep?.repeatable_answer) {
      const repeatableAnswer = currentStep?.repeatable_answer
      const repeatableAnswerValue = myProgressAnswers?.find((item) => item.answer.id === repeatableAnswer.id)?.value

      if (repeatableAnswerValue) {
        newRepeatableAnswerIndex = parseInt(repeatableAnswerValue)

        answersToSubmit.push({
          id: repeatableAnswer.id,
          value: (newRepeatableAnswerIndex + 1).toString(),
        })
      }
    }

    const response = await saveAnswerMutation({
      input: {
        provided_answers: answersToSubmit.filter(answer => answer.value !== null).map((answer) => ({
          answer_id: answer.id,
          property_id: property.id,
          value: answer.value,
          active_form_id: activeFormId as string,
        })),
        current_property_id: property?.id,
        current_step_id: currentStep?.id as string,
      },
    })

    if (response.error) {
      toast.error('Something went wrong, please try again.')
      errorHandler(response.error, setError, newRepeatableAnswerIndex)
    } else {
      toast.success('Giftor has been added successfully')
      onClose()
      refetch()
    }
  }

  return (
    <Modal size="medium" isOpen={isOpen} onClose={onClose}>
      {
        fetching
          ?
          <div className="flex m-auto">
            <LoadingSpinner />
          </div>
          :
          !fetching && currentStep &&
          <div>
            <QuestionCard
              question={currentStep.question as string}
              subHeading={currentStep.sub_heading}
              image={currentStep.image?.url as string}
              isLoading={fetching}
              answerType={currentStep.answers.map((item) => item.type) as any}
              myProgressAnswers={myProgressAnswers?.filter(providedAnswer =>
                providedAnswer?.answer?.step?.id !== currentStep?.id,
              ) as any}
              answers={currentStep.answers as any[]}
              onChange={(value, providedAnswerID) => updateAnswerValues(providedAnswerID, value)}
              errors={errors}
              isImagePresent={false}
              clearErrors={clearErrors}
              stepType={currentStep?.type}
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

export default AddGiftorModal
