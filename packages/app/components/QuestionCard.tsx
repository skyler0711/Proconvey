import AddressFinder from '@proconvey/ui/src/components/AddressFinder'
import Button from '@proconvey/ui/src/components/Button'
import Form from '@proconvey/ui/src/components/Form'
import Checkbox from '@proconvey/ui/src/components/Form/Checkbox'
import Input from '@proconvey/ui/src/components/Form/Input'
import Label from '@proconvey/ui/src/components/Form/Label'
import { H2, H3 } from '@proconvey/ui/src/components/Headers'
import Modal from '@proconvey/ui/src/components/Modals'
import AnswerCheckboxGroup from '@proconvey/ui/src/components/AnswerCheckboxGroup'
import { BinIcon, ChevronDownIcon, CirclePlayIcon, FormHomeIcon, HelpVideoIcon, QuestionCircleIcon } from '@proconvey/ui/src/icons'
import { Answer, AnswerDetailsSingleSelect, AnswerDetailsText, AnswerDetailsDropdown, AnswerDetailsMultiSelect, AnswerType, StepType, ProvidedAnswer } from 'gql/graphql'
import { ReactNode, useEffect, useMemo, useState } from 'react'
import Skeleton from 'react-loading-skeleton'
import Repeatable from '@proconvey/ui/src/components/Repeatable'
import Link from 'next/link'
import AnswerRadioGroup from '@proconvey/ui/src/components/AnswerRadioGroup'
import Upload from '@proconvey/ui/src/components/Form/Upload'
import { Textarea } from '@proconvey/ui/src/components/Textarea'
import DataTable from './DataTable'
import { FieldErrorsImpl, UseFormClearErrors } from 'react-hook-form'
import { ProvidedAnswerProps } from 'pages/properties/[id]/forms/[formId]/sections/[sectionId]/steps/[stepId]'
import { checkGivenAnswersConditionsMet } from 'helpers/steps'
import { getRepeatableTabTitle } from 'helpers/repeatable'
import IconButton from '@proconvey/ui/src/components/IconButton'

type PropertyPropTypes = {
  question: string
  subHeading?: string | null
  isLoading: boolean
  helpText?: string
  repeatableValue?: number
  currentRepeatableIndex?: number
  helpVideoLink?: string
  answers: Answer[]
  answerType?: string
  myProgressAnswers: Array<ProvidedAnswer>
  image?: string
  onChange: (value: any, answer_id: string) => void
  errors?: Partial<FieldErrorsImpl<ProvidedAnswerProps>>
  clearErrors: UseFormClearErrors<ProvidedAnswerProps>
  stepType: StepType
  isImagePresent?: boolean
  isTA10?: boolean
}

const QuestionCard = ({
  question,
  subHeading,
  isLoading,
  helpText,
  repeatableValue,
  currentRepeatableIndex,
  helpVideoLink,
  answers,
  answerType,
  myProgressAnswers,
  image,
  onChange,
  errors,
  clearErrors,
  stepType,
  isImagePresent = true,
  isTA10 = false,
}: PropertyPropTypes) => {
  const [isNeedHelpModalOpen, setIsNeedHelpModalOpen] = useState(false)
  const [givenAnswers, setGivenAnswers] = useState<Record<any, any>>({})

  const [iterationArray, setIterationArray] = useState(new Array(repeatableValue || 1).fill(0))
  const [randomValues, setRandomValues] = useState<number[]>([])

  useMemo(() => {
    if (stepType === StepType.MortgageRelatedTransactions && repeatableValue !== undefined && !isNaN(repeatableValue)) {
      let relatedTransactionAnswer = answers.find(ans => ans.type === AnswerType.PersonMultiSelect)
      let relatedTransactionAnswerValue = myProgressAnswers.find((providedAnswer) => providedAnswer.answer.id === relatedTransactionAnswer?.id)
      let arrayLength = relatedTransactionAnswerValue?.value?.length ?? 1

      setRandomValues(new Array(arrayLength).fill(0).map((_) => Math.random()))
      setIterationArray(new Array(arrayLength).fill(0))
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [answers])

  useEffect(() => {
    if (stepType !== StepType.MortgageRelatedTransactions && repeatableValue !== undefined && iterationArray.length !== repeatableValue) {
      setIterationArray(new Array(repeatableValue || 1).fill(0))
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [repeatableValue])

  useEffect(() => {
    if (myProgressAnswers) {
      setGivenAnswers(myProgressAnswers
        ?.filter((providedAnswer) => answers?.map(a => a.id)?.includes(providedAnswer.answer.id))
        ?.reduce((acc, providedAnswer) => ({ ...acc, [providedAnswer.answer.id]: providedAnswer.value }), {}) ?? {})
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [answers])

  const handleOnChange = (value: any, answerId: string, repeatableIndex?: number,columnNumber?: number) => {
    let newValue = Array.isArray(value)
      ? [...value]
      : value

    if (repeatableIndex !== undefined) {
      newValue = Array.isArray(givenAnswers[answerId])
        ? [...givenAnswers[answerId]]
        : [givenAnswers[answerId]]
      newValue[repeatableIndex] = value
    }

    onChange(newValue, answerId)
    // Ignoring Typescript error as this hasn't been updated to use react-hook-form fully and is only being used for error messages.
    // TODO: Update this to use react-hook-form fully (it will probably require useFieldArray) and remove this ts-expect-error.
    // @ts-expect-error
    clearErrors(repeatableIndex !== undefined
      ? `provided_answers.${parseInt(answerId)}.value.${repeatableIndex}`
      : Number.isInteger(columnNumber)
        ? `provided_answers.${parseInt(answerId)}.value.columns.${columnNumber}`
        : `provided_answers.${parseInt(answerId)}.value`,
    )

    setGivenAnswers({ ...givenAnswers, [answerId]: newValue })
  }

  useEffect(() => {
    const newGivenAnswers: typeof givenAnswers = { ...givenAnswers }

    Object.keys(givenAnswers).forEach((key) => {
      const conditionalAnswers = (answers ?? []).filter(a => a.conditions.some(c => c.answer.id === key) && a.id !== key)

      if (conditionalAnswers.length) {
        // Delete / set null all conditional answers if the condition is not met
        conditionalAnswers.forEach((conditionalAnswer) => {
          if (repeatableValue === undefined) {
            // Non-repeatable answers
            if (checkGivenAnswersConditionsMet(conditionalAnswer.conditions, givenAnswers)) {
              if (!Object.hasOwn(givenAnswers, conditionalAnswer.id)) {
                newGivenAnswers[conditionalAnswer.id] = null
              }
            } else {
              delete newGivenAnswers[conditionalAnswer.id]
            }
          } else {
            // Repeatable answers
            if (Array.isArray(givenAnswers[conditionalAnswer.conditions[0].answer.id])
              && givenAnswers[conditionalAnswer.conditions[0].answer.id]?.some((givenAnswer: string) => givenAnswer !== conditionalAnswer.conditions[0].selected_value)
            ) {
              newGivenAnswers[conditionalAnswer.id] = (givenAnswers[conditionalAnswer.id] ?? [])
                .map((givenAnswer: string, repeatableIndex: number) => {
                  if (checkGivenAnswersConditionsMet(conditionalAnswer.conditions, givenAnswers, repeatableIndex)) {
                    return givenAnswer
                  } else {
                    delete givenAnswers[conditionalAnswer.id][repeatableIndex]
                    return givenAnswer
                  }
                })
            }
          }
        })
      }
    })

    if (Object.keys(givenAnswers).join() !== Object.keys(newGivenAnswers).join()) {
      setGivenAnswers(newGivenAnswers)
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [givenAnswers, answers])

  const AnswerComponent: Record<string, (answer: Answer, repeatableIndex?: number) => ReactNode> = {

    'Address': (answer: Answer, repeatableIndex?: number) => (
      <AddressFinder
        label={answer?.details?.label ?? 'Address'}
        onChange={(a) => handleOnChange(a, answer.id, repeatableIndex)}
        address={repeatableIndex !== undefined
          ? givenAnswers[answer.id]?.[repeatableIndex]
          : givenAnswers[answer.id]
        }
        // TODO: Remove this ts-expect-error when we have updated the QuestionCard component to use react-hook-form fully.
        // @ts-expect-error
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]
          : errors?.provided_answers?.[parseInt(answer.id)]?.value
        }
      />
    ),

    'Text': (answer: Answer, repeatableIndex?: number) => (
      <div>
        <Label>{answer?.details?.label}</Label>
        <Input
          type="text"
          onChange={e => handleOnChange(e.target.value, answer.id, repeatableIndex)}
          placeholder={(answer.details as AnswerDetailsText)?.placeholder ?? undefined}
          value={repeatableIndex !== undefined
            ? givenAnswers[answer.id]?.[repeatableIndex]
            : givenAnswers[answer.id]
          }
          error={repeatableIndex !== undefined
            ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
            : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
          }
        />
      </div>
    ),

    'Textarea': (answer: Answer, repeatableIndex?: number) =>
      <Textarea
        placeholder={(answer.details as AnswerDetailsText)?.placeholder ?? undefined}
        onChange={e => handleOnChange(e.target.value, answer.id, repeatableIndex)}
        label={answer?.details?.label ?? undefined}
        value={repeatableIndex !== undefined
          ? givenAnswers[answer.id]?.[repeatableIndex]
          : givenAnswers[answer.id]
        }
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />,

    'Number': (answer: Answer, repeatableIndex?: number) => (
      <Input
        type="number"
        onChange={e => handleOnChange(e.target.value, answer.id, repeatableIndex)}
        value={repeatableIndex !== undefined
          ? givenAnswers[answer.id]?.[repeatableIndex]
          : givenAnswers[answer.id]
        }
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />
    ),

    'Dropdown': (answer: Answer, repeatableIndex?: number) => (
      <Form.Select
        placeholder={(answer.details as AnswerDetailsText)?.placeholder ?? 'Select'}
        label={answer?.details?.label ?? undefined}
        onChange={v => handleOnChange(v.value, answer.id, repeatableIndex)}
        defaultValue={{
          value: repeatableIndex !== undefined ? givenAnswers[answer.id]?.[repeatableIndex] : givenAnswers[answer.id],
          text: repeatableIndex !== undefined ? givenAnswers[answer.id]?.[repeatableIndex] : givenAnswers[answer.id],
        }}
        options={(answer.details as AnswerDetailsDropdown).options?.map((option) => ({
          value: option.value,
          text: option.value,
        })) ?? []}
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />
    ),

    'OwnerDropdown': (answer: Answer, repeatableIndex?: number) => {
      let options = ((answer.details as AnswerDetailsDropdown)?.options ?? []).map((option) => ({
        value: option.value,
        text: option.value,
        disabled: repeatableIndex !== undefined
          ? givenAnswers[answer.id]
            ?.filter((_: any, index: number) => index !== repeatableIndex)
            ?.includes(option.value)
          : false,
      }))

      return <Form.Select
        placeholder={options?.length === 0
          ? 'No existing people found.'
          : (answer.details as AnswerDetailsText)?.placeholder ?? 'Select a person'
        }
        disabled={options?.length === 0}
        label={answer?.details?.label ?? undefined}
        onChange={v => handleOnChange(v.value, answer.id, repeatableIndex)}
        defaultValue={{
          value: repeatableIndex !== undefined ? givenAnswers[answer.id]?.[repeatableIndex] : givenAnswers[answer.id],
          text: repeatableIndex !== undefined ? givenAnswers[answer.id]?.[repeatableIndex] : givenAnswers[answer.id],
        }}
        options={options}
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />
    },

    'SingleSelect': (answer: Answer, repeatableIndex?: number) =>
      <div className="flex flex-col mb-3">
        <Label>{answer?.details?.label}</Label>
        <AnswerRadioGroup
          selected={repeatableIndex !== undefined
            ? givenAnswers[answer.id]?.[repeatableIndex]
            : givenAnswers[answer.id]
          }
          onChange={value => handleOnChange(value, answer.id, repeatableIndex)}
          error={repeatableIndex !== undefined
            ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
            : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
          }
        >
          {(answer.details as AnswerDetailsSingleSelect)?.options?.map((option) => (
            <AnswerRadioGroup.Radio
              key={option.value}
              value={option.value}
              error={repeatableIndex !== undefined
                ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
                : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
              }
            >
              {option.value}
            </AnswerRadioGroup.Radio>
          ))}
        </AnswerRadioGroup>
      </div>,

    'Checkbox': (answer: Answer, repeatableIndex?: number) => {
      if ((repeatableIndex === undefined && givenAnswers[answer.id] === null)
        || (repeatableIndex !== undefined && givenAnswers[answer.id]?.[repeatableIndex] == null)
      ) {
        handleOnChange('0', answer.id, repeatableIndex)
      }

      return <div className="flex flex-col gap-7">
        <Checkbox.Group error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }>
          {({ onChange: onCheckboxChange }) => (
            <Checkbox
              value="1"
              size="small"
              selected={repeatableIndex !== undefined
                ? [givenAnswers[answer.id]?.[repeatableIndex]]
                : [givenAnswers[answer.id]]
              }
              rounded
              onChange={(value) => {
                onCheckboxChange(value)

                if (handleOnChange) {
                  let newAnswer = repeatableIndex !== undefined
                    ? givenAnswers[answer.id]?.[repeatableIndex]
                    : givenAnswers[answer.id]

                  handleOnChange(
                    newAnswer === value ? '0' : value,
                    answer.id,
                    repeatableIndex,
                  )
                }
              }}
            >
              {answer?.details?.label}
            </Checkbox>
          )}
        </Checkbox.Group>
      </div>},

    'File': (answer: Answer, repeatableIndex?: number) => {
      let fileValue = repeatableIndex !== undefined
        ? givenAnswers[answer.id]?.[repeatableIndex]
        : givenAnswers[answer.id]

      return <Upload
        allowLater
        allowNotAvailable
        onChange={files => handleOnChange(files, answer.id, repeatableIndex)}
        label={answer?.details?.label ?? undefined}
        value={
          (fileValue === undefined || fileValue === null)
            ? undefined
            : (typeof fileValue === 'string' ? fileValue : [fileValue])
        }
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />
    },

    'MultiSelect': (answer: Answer, repeatableIndex?: number) =>
      <AnswerCheckboxGroup
        selected={givenAnswers[answer.id]}
        onChange={values => handleOnChange(values, answer.id, repeatableIndex)}
        error={errors?.provided_answers?.[parseInt(answer.id)]?.value?.message}
      >
        {(answer.details as AnswerDetailsMultiSelect)?.options?.map((option) => (
          <AnswerCheckboxGroup.Checkbox
            key={option.value}
            value={option.value}
            error={errors?.provided_answers?.[parseInt(answer.id)]?.value?.message}
          >
            {option.value}
          </AnswerCheckboxGroup.Checkbox>
        ))}
      </AnswerCheckboxGroup>,

    'DataTable': (answer: Answer, repeatableIndex?: number) => {
      return (
        <DataTable
          answer={answer}
          value={givenAnswers[answer.id]}
          onChange={(e: any, column: number) => handleOnChange(e, answer.id, repeatableIndex, column)}
          //TODO: fix this type upon complete react-hook-forms integration
          // @ts-expect-error
          errors={errors?.provided_answers?.[parseInt(answer.id)]?.value}
        />
      )
    },

    'PersonMultiSelect': (answer: Answer, repeatableIndex?: number) => {
      let options = (answer.details as AnswerDetailsDropdown).options?.map((option) => ({
        value: option.value,
        text: option.value,
        disabled: repeatableIndex !== undefined
          ? givenAnswers[answer.id]?.some((answer: any, index: number) => index !== repeatableIndex && answer?.includes(option.value))
          : givenAnswers[answer.id]?.includes(option.value),
      })) ?? []

      return <Form.MultipleSelect
        placeholder={(answer.details as AnswerDetailsText)?.placeholder ?? 'Select'}
        label={[answer?.details?.label, (repeatableIndex ?? 0) + 1].join(' ') ?? undefined}
        onChange={(v) => handleOnChange(v?.map(item => item.value), answer.id, repeatableIndex)}
        disabled={options.length === 0}
        defaultValue={repeatableIndex !== undefined
          ? givenAnswers[answer.id]?.[repeatableIndex]?.map((item: any) => ({ value: item, text: item }))
          : givenAnswers[answer.id]?.map((item: any) => ({ value: item, text: item }))
        }
        options={options}
        error={repeatableIndex !== undefined
          ? errors?.provided_answers?.[parseInt(answer.id)]?.value?.[repeatableIndex]?.message
          : errors?.provided_answers?.[parseInt(answer.id)]?.value?.message
        }
      />
    },
  }

  return (
    <>
      <div className="flex flex-col-reverse sm:flex-row justify-between items-center mb-[50px] gap-5 sm:gap-0">
        <div className="">
          {
            isLoading
              ? <Skeleton width={'80%'} height={47} />
              : isTA10
                ? <><H3 className={`w-full px-5 py-4 ${helpText && 'sm:pr-0 max-w-[60.5625rem]'}`}>{question}</H3></>
                : <H2 className={`w-full px-5 py-4 ${helpText && 'sm:pr-0 max-w-[60.5625rem]'}`}>{question}</H2>
          }

          {!isLoading && subHeading &&
            <><H3 className={`w-full px-5 py-4 ${helpText && 'sm:pr-0 max-w-[60.5625rem]'}`}>{subHeading.split('#')[0]}</H3><H3 className={`w-full px-5 py-4 ${helpText && 'sm:pr-0 max-w-[60.5625rem]'}`}>{subHeading.split('#')[1]}</H3></>
          }
        </div>

        {
          isLoading
            ? <Skeleton width={'30%'} height={66} />
            : (helpText &&
              <button className="p-[19px] bg-mull bg-opacity-5 flex items-center rounded-l-xl gap-[13px] ml-auto" onClick={() => setIsNeedHelpModalOpen(true)}>
                <QuestionCircleIcon className="w-6 h-6 text-mull" />
                <p className="text-xl font-bold text-mull">Need help?</p>
                <ChevronDownIcon className="text-mull text-opacity-60 w-[30px] h-[20px]" />
              </button>
            )
        }
      </div>

      <div className={`flex flex-col-reverse sm:flex-row sm:mx-5 pr-5 sm:pr-0 gap-y-5 ${answerType !== AnswerType.DataTable ? 'justify-between flex-grow gap-1' : ''}`}>
        {
          isLoading
            ? <Skeleton height={304} containerClassName="w-[60%]" />
            : <div className="flex flex-col w-full">
              {repeatableValue && repeatableValue !== null
                ? stepType === StepType.MortgageRelatedTransactions
                  ?
                  <div className="flex flex-col gap-[3.125rem]">
                    {
                      iterationArray.map((_: any, repeatableIndex: number) => (
                        <div key={repeatableIndex} className="flex flex-col gap-5">
                          {
                            iterationArray.length > 1 &&
                              <div className="z-10 ml-auto -mb-10">
                                <IconButton
                                  icon={<BinIcon className="w-6 h-6 text-mull" />}
                                  size="small"
                                  onClick={() => {
                                    let newIterationArray = [...iterationArray]
                                    newIterationArray.splice(repeatableIndex, 1)
                                    setIterationArray(newIterationArray)

                                    let givenAnswersCopy = { ...givenAnswers }

                                    Object.entries(givenAnswers).forEach(([key, givenAnswer]) => {
                                      if (!givenAnswer) {
                                        return
                                      }

                                      let newAnswerValue = [...givenAnswer]
                                      newAnswerValue.splice(repeatableIndex, 1)

                                      givenAnswersCopy[key] = newAnswerValue

                                      let newRandom = [...randomValues]
                                      newRandom.splice(repeatableIndex, 1)
                                      setRandomValues(newRandom)

                                      onChange(newAnswerValue, key)
                                    })

                                    setGivenAnswers(givenAnswersCopy)
                                  }}
                                />
                              </div>
                          }

                          {
                            answers?.map((answer) => checkGivenAnswersConditionsMet(answer.conditions, givenAnswers, repeatableIndex) &&
                              <div className="w-full my-auto" key={answer.id + '-' + randomValues?.[repeatableIndex]}>
                                {AnswerComponent[answer.type](answer, repeatableIndex)}
                              </div>,
                            )}
                        </div>
                      ))

                    }

                    {
                      iterationArray.length !== repeatableValue &&
                      givenAnswers[answers.find(ans => ans.type === AnswerType.PersonMultiSelect)!.id]?.reduce((count: number, answer: any) => count + answer?.length, 0) !== repeatableValue &&
                      <div>
                        <Button
                          variant="tertiary"
                          onClick={() => {
                            setIterationArray([...iterationArray, 0])
                            setRandomValues([...randomValues, Math.random()])
                          }}
                        >
                          + Add property
                        </Button>
                      </div>
                    }
                  </div>
                  :
                  <Repeatable defaultIndex={currentRepeatableIndex}>
                    <Repeatable.Tabs>
                      {
                        iterationArray.map((_, index) => (
                          <Repeatable.Tab
                            key={index}
                            // TODO: fix type error, as with above comments, this is to do with the react hook form types
                            // @ts-expect-error
                            hasError={errors?.provided_answers?.some((error) => error?.value?.[index] != null)}
                          >
                            {getRepeatableTabTitle(stepType)} {index + 1}
                          </Repeatable.Tab>
                        ))
                      }
                    </Repeatable.Tabs>
                    <Repeatable.Panels>
                      {
                        iterationArray.map((_, repeatableIndex) => (
                          <Repeatable.Panel key={repeatableIndex}>
                            <div className="flex flex-col gap-5">
                              {
                                answers?.map((answer, index) => checkGivenAnswersConditionsMet(answer.conditions, givenAnswers, repeatableIndex) &&
                                  <div className="w-full my-auto" key={index}>
                                    {AnswerComponent[answer.type](answer, repeatableIndex)}
                                  </div>,
                                )
                              }
                            </div>
                          </Repeatable.Panel>
                        ))
                      }
                    </Repeatable.Panels>
                  </Repeatable>
                :
                <div className={`mr-[20px] w-full ${answerType !== 'Address' ? 'flex justify-between' : ''}`}>
                  <div className="flex flex-col w-full gap-[1.25rem]">
                    {
                      answers?.map((answer, index) => checkGivenAnswersConditionsMet(answer.conditions, givenAnswers) &&
                          <div className="w-full my-auto" key={index}>
                            {AnswerComponent[answer.type](answer)}
                          </div>,
                      )
                    }
                  </div>
                </div>
              }
            </div>
        }

        {
          isLoading
            ? <Skeleton height={340} containerClassName="w-[40%]" />
            :
            isImagePresent && image && answerType && answerType[0] !== 'DataTable'
              ? <img src={image} className="sm:w-[40%] w-full sm:h-[340px]" alt="Question" />
              :
              answerType && answerType[0] === 'DataTable'
                ? null
                : isImagePresent
                  ? <FormHomeIcon className="sm:w-[40%] w-full sm:h-[340px]" />
                  : null
        }
      </div>

      <Modal isOpen={isNeedHelpModalOpen} onClose={() => setIsNeedHelpModalOpen(false)}>
        <Modal.Title>Need help? Read our guide</Modal.Title>

        {
          helpVideoLink &&
          <div className="flex flex-col w-full h-full mx-auto rounded-lg bg-primary bg-opacity-10">
            <div className="flex flex-col justify-center mx-auto mb-[36px]">
              <HelpVideoIcon className="w-full max-w-[296px] h-full max-h-[198px]" />
            </div>
            <H2 className="mb-6 text-center">Watch video guide</H2>

            <div className="flex justify-center mb-5">
              <Link
                href={helpVideoLink}
                target="_blank"
                rel="noopener noreferrer"
              >
                <Button icon={<CirclePlayIcon />} className="w-[290px]">
                  Watch guide
                </Button>
              </Link>
            </div>
          </div>
        }

        <p className="mt-5 text-sm text-opacity-60 prose-a:text-primary prose-a:underline" dangerouslySetInnerHTML={{ __html: helpText! }} />
      </Modal>
    </>

  )
}

export default QuestionCard
