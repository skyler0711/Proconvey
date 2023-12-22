import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import { ChevronDownIcon } from '@proconvey/ui/src/icons'
import QuestionCard from 'components/QuestionCard'
import { graphql } from 'gql'
import FormLayout from 'layouts/FormLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import Link from 'next/link'
import { useRouter } from 'next/router'
import { useEffect, useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import { useMutation, useQuery } from 'urql'
import { NextSeo } from 'next-seo'
import { AnswerDetailsPersonMultiSelect, AnswerType, StepType, Property, FormType } from 'gql/graphql'
import useUpload from 'hooks/useUpload'
import { checkConditionsMet, filteredSectionSteps, getNextStep } from 'helpers/steps'
import useErrorHandler from 'hooks/useErrorHandler'
import { useForm } from 'react-hook-form'

export type ProvidedAnswerProps = {
  provided_answers: [{ value: string[] }]
}

export default function Step () {
  const [answerValues, setAnswerValues] = useState<{ id: string, value: any }[]>([])
  const [formAnswers, setFormAnswers] = useState<any[]>([])
  const [isSaving, setIsSaving] = useState(false)
  const [isTA10, setIsTA10] = useState(false)

  const router = useRouter()
  const { uploadFiles } = useUpload()

  const [{ data, fetching: isDataLoading }] = useQuery({
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
      id: router.query.id as string,
    },
  })

  const myProgressAnswers = data?.property?.my_progress?.provided_answers.filter((item) => item.active_form_id === router.query.formId)

  useEffect(() => {
    if (myProgressAnswers) {
      const providedAnswers = myProgressAnswers
        .filter((item) => item.answer.step.id === router.query.stepId)
        .map(item => ({ id: item.answer.id, value: item.value }))

      setAnswerValues(providedAnswers)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data, router.query.stepId])

  useEffect(() => {
    if (myProgressAnswers) {
      const answerIds = data?.property?.active_forms
        .find((activeForm) => activeForm.pivot?.id === router.query.formId)
        ?.sections.flatMap(section =>
          section.steps.flatMap(step =>
            step.answers.map(answer => ({ id: answer.id })),
          ),
        )

      const fullAnswers = myProgressAnswers.filter(answer =>
        answerIds?.some(step => step.id === answer.answer.id),
      ).map(item => ({ id: item.answer.id, value: item.value })).filter(r => r.value !== null)

      setFormAnswers(formAnswers => [
        ...formAnswers.filter(obj => !fullAnswers.some(newObj => newObj.id === obj.id)),
        ...fullAnswers,
      ])
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data, router.query.formId])

  const currentForm = data?.property?.active_forms.filter((activeForm) => activeForm.pivot?.id === router.query.formId)?.[0]!
  useEffect(() => {
    if (currentForm?.ta_form_template === 'TA10FittingsAndContents') {
      setIsTA10(true)
    }
  }, [currentForm])

  const currentSection = currentForm?.sections?.filter((item) => item.id === router.query.sectionId)?.[0]!
  const currentSectionNumber = currentForm?.sections?.findIndex((section) => section?.id === router.query.sectionId) + 1 || undefined
  const totalSections = currentForm?.sections.length
  const currentStep = currentSection?.steps?.filter((item) => item.id === router.query.stepId)?.[0]!
  const steps = filteredSectionSteps(currentSection?.steps, formAnswers)
  const question = currentStep?.question
  const subHeading = currentStep?.sub_heading
  const currentQuestionNumber = steps?.findIndex((step) => step?.id === router.query.stepId) + 1 || undefined
  const totalQuestions = steps?.length
  const formName = currentForm?.pivot?.title ?? currentForm?.name
  const answerType = currentForm?.sections?.[0]?.steps?.[0]?.answers?.map((item) => item.type)
  const helpText = currentStep?.help_text
  const helpVideoLink = currentStep?.help_video_link
  const image = currentStep?.image?.url
  const answers = currentStep?.answers
  const repeatableValue = currentStep?.repeatable_answer === null
    ? currentStep.type === StepType.MortgageRelatedTransactions
      ? (currentStep.answers?.find(answer => answer.type === AnswerType.PersonMultiSelect)?.details as AnswerDetailsPersonMultiSelect)?.options?.length
      : undefined
    : parseInt(data?.property?.my_progress?.provided_answers?.find((item) => item.answer.id === currentStep?.repeatable_answer?.id)?.value) ?? 1

  const { setError, clearErrors, formState: { errors } } = useForm<ProvidedAnswerProps>()
  const errorHandler = useErrorHandler()

  const [_, saveAnswerMutation] = useMutation(graphql(`
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

  const onSubmit = async () => {
    setIsSaving(true)
    clearErrors()

    // Upload any files
    const providedAnswers = answerValues.filter(answer => answer.value !== null)
    const answerPromises = providedAnswers
      .map(answerValue => {
        if (repeatableValue) {
          return new Promise((resolve) => {
            Promise.all(answerValue.value.map((value: any) => {
              if (answers.find(answer => answer.id === answerValue.id)?.type === AnswerType.File && typeof value !== 'string' && typeof value !== 'number') {
                if (value?.id === undefined) {
                  return uploadFiles(value as File[])
                    .then(upload => ({
                      name: (value[0] as File).name,
                      extension: upload.extension,
                      key: upload.key,
                    }))
                } else {
                  return value?.id
                }
              }
              return value
            }))
              .then((value) => resolve({ ...answerValue, value }))
          })
        } else {
          if (answers.find(answer => answer.id === answerValue.id)?.type === AnswerType.File && typeof answerValue.value !== 'string' && typeof answerValue.value !== 'number') {
            if (answerValue.value?.id === undefined) {
              return uploadFiles(answerValue.value as File[])
                .then(upload => ({
                  ...answerValue,
                  value: {
                    name: (answerValue.value[0] as File).name,
                    extension: upload.extension,
                    key: upload.key,
                  },
                }))
            } else {
              return {
                ...answerValue,
                value: answerValue.value?.id,
              }
            }
          }
        }
        return answerValue
      })
      .flatMap(answerValue => {
        return answerValue ? [answerValue] : []
      })

    const resolvedPromises = await Promise.all(answerPromises)

    // Save the answers
    const response = await saveAnswerMutation({
      input: {
        provided_answers: resolvedPromises.map((answer: any) => ({
          answer_id: answer.id,
          property_id: router.query.id as string,
          value: answer.value,
          active_form_id: router.query.formId as string,
        })),
        current_property_id: router.query.id as string,
        current_step_id: router.query.stepId as string,
      },
    })

    setIsSaving(false)

    if (response.error) {
      errorHandler(response.error, setError)
    } else {
      setFormAnswers(formAnswers => [
        ...formAnswers.filter(obj => !answerValues.some(newObj => newObj.id === obj.id)),
        ...answerValues,
      ])

      const currentStep = currentForm?.sections
        ?.filter((item) => item.id === currentSection?.id)?.[0]?.steps
        ?.filter((item) => item.id === router.query.stepId)?.[0]

      const nextStep = getNextStep(
        currentStep,
        currentForm,
        currentSection,
        answerValues,
        myProgressAnswers ?? [],
      )

      const newStepInThisSection = nextStep?.id

      const newSection = newStepInThisSection
        ? router.query.sectionId
        : currentForm?.sections
          ?.filter(section => parseInt(section.id) > parseInt(router.query.sectionId as string ?? 0))
          ?.filter(section => checkConditionsMet(
            section?.conditions,
            answerValues,
            myProgressAnswers ?? [],
          ))
          ?.[0]?.id

      if (!newStepInThisSection && !newSection) {
        router.push(`/properties/${router.query.id}`)
        return
      }

      const newStep = newStepInThisSection ?? currentForm.sections
        ?.filter(section => checkConditionsMet(
          section?.conditions,
          answerValues,
          myProgressAnswers ?? [],
        ))
        ?.filter(section => parseInt(section.id) > parseInt(router.query.sectionId as string ?? 0))
        ?.[0]?.steps?.[0]?.id

      if (!newStep) {
        router.push(`/properties/${router.query.id}`)
        return
      }

      router.push(`/properties/${router.query.id}/forms/${router.query.formId}/sections/${newSection}/steps/${newStep}`)
      setAnswerValues([])
    }
  }

  const updateAnswerValues = (answerId: string, answerValue: string) => {
    const answers = answerValues
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
    return setAnswerValues(answers)
  }

  return (
    <>
      <NextSeo
        title={formName ?? undefined}
      />
      <ProtectedLayout>
        <FormLayout
          property={data?.property as Property}
          isLoading={isDataLoading}
        >
          <FormLayout.MainContent>
            <div className="ml-[30px] mt-[50px] mb-7">
              {
                isDataLoading
                  ? <Skeleton height={25} width={110} />
                  :
                  <Link href={`/properties/${router.query.id}`} className="flex items-center gap-3 mb-[21px]">
                    <ChevronDownIcon className="w-4 h-5 rotate-90 text-primary" />
                    <p className="text-sm text-primary">Back to Overview</p>
                  </Link>
              }
              {
                isDataLoading
                  ? <Skeleton height={50} width={'85%'} />
                  : currentForm?.ta_form_template === FormType.Ta10FittingsAndContents
                    ? <H1>{formName}: {currentSection.name}</H1>
                    : <H1>{formName}</H1>
              }
            </div>

            <div className="bg-white mx-[20px] sm:ml-[30px] sm:mr-[50px] pt-5 pl-5 pb-5 mb-10 rounded-xl border border-primary border-opacity-10">
              {
                isDataLoading
                  ? <Skeleton height={25} width={80} className="mb-10" />
                  :
                  (
                    isTA10
                      ? <p className="mb-4 text-base font-bold text-mull">Question {currentSectionNumber}/{totalSections}</p>
                      : <p className="mb-4 text-base font-bold text-mull">Question {currentQuestionNumber}/{totalQuestions}</p>
                  )
              }

              <QuestionCard
                question={question as string}
                subHeading={subHeading}
                repeatableValue={repeatableValue}
                image={image as string}
                isLoading={isDataLoading}
                answerType={answerType as any}
                helpText={helpText as string}
                helpVideoLink={helpVideoLink as string}
                myProgressAnswers={myProgressAnswers as any}
                answers={answers as any[]}
                onChange={(value, providedAnswerID) => updateAnswerValues(providedAnswerID, value)}
                errors={errors}
                clearErrors={clearErrors}
                stepType={currentStep?.type}
                isTA10={isTA10 as boolean}
              />
            </div>

            <div className="sm:ml-[30px] sm:mr-[50px] mx-5 flex pb-5 justify-between">
              {
                isDataLoading
                  ? <Skeleton height={52} width={200} />
                  : (
                    (() => {
                      const prevStepInThisSection = steps
                        ?.filter(item => item!.id !== router.query.stepId && parseInt(item.id) < parseInt(router.query.stepId as string ?? 0))
                        ?.slice(-1)
                        ?.[0]?.id
                      const prevSection = prevStepInThisSection
                        ? currentForm?.sections?.find(section => section.id === router.query.sectionId)
                        : currentForm?.sections
                          ?.filter(section => parseInt(section.id) < parseInt(router.query.sectionId as string ?? 0))
                          ?.filter(section => checkConditionsMet(section.conditions, answerValues, myProgressAnswers ?? []))
                          ?.slice(-1)?.[0]

                      if (!prevStepInThisSection && !prevSection?.id) {
                        return null
                      }

                      let prevStep = prevStepInThisSection ?? filteredSectionSteps(
                        prevSection?.steps ?? [],
                        myProgressAnswers ?? [],
                      )?.slice(-1)?.[0]?.id

                      if (!prevStepInThisSection) {
                        if (!isTA10) prevStep++
                      }

                      return <Link href={`/properties/${router.query.id}/forms/${router.query.formId}/sections/${prevSection?.id}/steps/${prevStep}`}>
                        <Button variant="outlined">
                          Previous Question
                        </Button>
                      </Link>
                    })()
                  )
              }

              <div className="sm:ml-auto">
                {
                  isDataLoading
                    ? <Skeleton height={52} width={200} />
                    :
                    <Button onClick={onSubmit} loading={isSaving} variant="primary">
                      Save and Continue
                    </Button>
                }
              </div>
            </div>
          </FormLayout.MainContent>
        </FormLayout>
      </ProtectedLayout>
    </>
  )
}
