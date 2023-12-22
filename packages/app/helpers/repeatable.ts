import { StepType } from 'gql/graphql'

export const getRepeatableTabTitle = (stepType: StepType) => {
  switch (stepType) {
    case StepType.OwnerFormPowerOfAttorney:
    case StepType.CompanyFormPowerOfAttorneyRepresentative:
    case StepType.Attorney:
    case StepType.RepeatableNameChangeAttorney:
      return 'Attorney'
    case StepType.CompanyFormDeputyshipOrderRepresentative:
    case StepType.Deputy:
    case StepType.RepeatableNameChangeDeputy:
      return 'Deputy'
    case StepType.CompanyRepresentative:
      return 'Company Representative'
    case StepType.BuyerGiftor:
    case StepType.DirectorDetails:
      return 'Director'
    case StepType.CompanyFormGrantOfProbateRepresentative:
    case StepType.RepeatableNameChangeExecutor:
    case StepType.DeputyDropdown:
      return 'Executor'
    case StepType.Loaner:
      return 'Loaner'
    case StepType.RemortgageGiftor:
      return 'Giftor'
    case StepType.Charges:
    case StepType.MortgageChargeLoan:
      return 'Mortgage, Charge or Loan'
    case StepType.BuyerExpanded:
    case StepType.Buyer:
    case StepType.SavingsAmount:
    case StepType.Custom:
      return 'Buyer'
    case StepType.Mortgager:
      return 'Remortgager'
    case StepType.Seller:
    case StepType.Sdlt:
      return 'Seller'
    case StepType.OwnerName:
    default:
      return 'Owner'
  }
}
