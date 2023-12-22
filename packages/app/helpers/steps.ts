import { Condition, ConditionType, Property, ProvidedAnswer, Section, Step } from 'gql/graphql'


export default function sectionSteps (steps: any[], property?: Property) {
  return steps?.filter((step: Step) => checkConditionsMet(step.conditions, [], property?.my_progress?.provided_answers ?? []))
}

export const filteredSectionSteps = (steps: any[], answers: any[]) => {
  return steps?.filter((step: Step) => checkConditionsMet(step.conditions, answers, []))
}

export const getNextStep = (
  step: any,
  currentForm: any,
  currentSection: any,
  answerValues: any[],
  providedAnswers: any[],
): any => {
  const nextStep = currentForm?.sections
    ?.filter((item: Section) => item.id === currentSection?.id)?.[0]?.steps
    ?.filter((item: Step) => item.id !== step.id && parseInt(item.id) > parseInt(step.id ?? '0'))
    ?.[0]

  if (!nextStep) return null

  if (checkConditionsMet(nextStep?.conditions, answerValues, providedAnswers)) {
    return nextStep
  } else {
    return getNextStep(
      nextStep,
      currentForm,
      currentSection,
      answerValues,
      providedAnswers,
    )
  }
}

export const checkConditionsMet = (
  conditions: any[],
  answerValues: any[],
  providedAnswers: any[],
): boolean => {
  if (conditions?.length === 0) return true

  return conditions?.reduce((isConditionMet: boolean, condition: Condition) => {
    let answerValue = answerValues.find(answer => answer.id === condition.answer.id)?.value

    if (!answerValue && providedAnswers?.length) {
      answerValue = providedAnswers.find((answer: ProvidedAnswer) => answer?.answer?.id === condition.answer.id)?.value
    }

    return checkAnswer(condition, answerValue, isConditionMet)
  }, null)
}

export const checkGivenAnswersConditionsMet = (
  conditions: any[],
  givenAnswers: Record<any, any>,
  repeatableIndex?: number,
): boolean => {
  if (conditions?.length === 0) return true
  return conditions?.reduce((isConditionMet: boolean, condition: Condition) => {
    let answerValue = repeatableIndex === undefined
      ? givenAnswers[condition.answer.id]
      : givenAnswers[condition.answer.id]?.[repeatableIndex]

    return checkAnswer(condition, answerValue, isConditionMet)
  }, null)
}

export const checkAnswer = (
  condition: any,
  answerValue: string | string[],
  isConditionMet: boolean | null = null,
): boolean => {
  let isAnswerEqual = Array.isArray(answerValue)
    ? answerValue.includes(condition.selected_value)
    : answerValue === condition.selected_value

  switch (condition?.type) {
    case ConditionType.And:
      return (isConditionMet ?? true) && isAnswerEqual
    case ConditionType.Or:
      return (isConditionMet ?? false) || isAnswerEqual
    default:
      return false
  }
}
