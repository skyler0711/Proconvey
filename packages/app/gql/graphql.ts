/* eslint-disable */
import { TypedDocumentNode as DocumentNode } from '@graphql-typed-document-node/core';
export type Maybe<T> = T | null;
export type InputMaybe<T> = Maybe<T>;
export type Exact<T extends { [key: string]: unknown }> = { [K in keyof T]: T[K] };
export type MakeOptional<T, K extends keyof T> = Omit<T, K> & { [SubKey in K]?: Maybe<T[SubKey]> };
export type MakeMaybe<T, K extends keyof T> = Omit<T, K> & { [SubKey in K]: Maybe<T[SubKey]> };
/** All built-in and custom scalars, mapped to their actual values */
export type Scalars = {
  ID: string;
  String: string;
  Boolean: boolean;
  Int: number;
  Float: number;
  /** A datetime string with format `Y-m-d H:i:s`, e.g. `2018-05-23 13:43:32`. */
  DateTime: any;
  /**
   * Loose type that allows any value. Be careful when passing in large `Int` or `Float` literals,
   * as they may not be parsed correctly on the server side. Use `String` literals if you are
   * dealing with really large numbers to be on the safe side.
   */
  Mixed: any;
};

export type ActiveFormsPivot = {
  __typename?: 'ActiveFormsPivot';
  id: Scalars['ID'];
  title?: Maybe<Scalars['String']>;
};

export type AddNewPartyInput = {
  client_care_letter?: InputMaybe<Scalars['Boolean']>;
  client_id?: InputMaybe<Scalars['ID']>;
  email?: InputMaybe<Scalars['String']>;
  first_name?: InputMaybe<Scalars['String']>;
  id_check?: InputMaybe<Scalars['Boolean']>;
  last_name?: InputMaybe<Scalars['String']>;
  owner_email?: InputMaybe<Scalars['String']>;
  owner_first_name?: InputMaybe<Scalars['String']>;
  owner_last_name?: InputMaybe<Scalars['String']>;
  owner_type?: InputMaybe<Scalars['String']>;
  party_type?: InputMaybe<Scalars['String']>;
  representation?: InputMaybe<Scalars['String']>;
  representative_email?: InputMaybe<Scalars['String']>;
  representative_first_name?: InputMaybe<Scalars['String']>;
  representative_last_name?: InputMaybe<Scalars['String']>;
};

export type Address = {
  __typename?: 'Address';
  city: Scalars['String'];
  id: Scalars['ID'];
  line_1: Scalars['String'];
  line_2?: Maybe<Scalars['String']>;
  postcode: Scalars['String'];
};

export type Answer = {
  __typename?: 'Answer';
  conditions: Array<Condition>;
  details?: Maybe<AnswerDetailsUnion>;
  id: Scalars['ID'];
  provided_answers?: Maybe<Array<Maybe<ProvidedAnswer>>>;
  step: Step;
  type: AnswerType;
};


export type AnswerProvided_AnswersArgs = {
  active_form_id: Scalars['ID'];
  property_id: Scalars['ID'];
};

export type AnswerDetailsAddress = {
  __typename?: 'AnswerDetailsAddress';
  label?: Maybe<Scalars['String']>;
};

export type AnswerDetailsCheckbox = {
  __typename?: 'AnswerDetailsCheckbox';
  label?: Maybe<Scalars['String']>;
};

export type AnswerDetailsDataTable = {
  __typename?: 'AnswerDetailsDataTable';
  addMoreLabel?: Maybe<Scalars['String']>;
  allowsAddMore: Scalars['Boolean'];
  columns: Array<AnswerDetailsDataTableColumn>;
  label?: Maybe<Scalars['String']>;
  rows: Array<AnswerDetailsDataTableRow>;
};

export type AnswerDetailsDataTableColumn = {
  __typename?: 'AnswerDetailsDataTableColumn';
  name: Scalars['String'];
  placeholder?: Maybe<Scalars['String']>;
  type: AnswerType;
};

export type AnswerDetailsDataTableRow = {
  __typename?: 'AnswerDetailsDataTableRow';
  name: Scalars['String'];
};

export type AnswerDetailsDropdown = {
  __typename?: 'AnswerDetailsDropdown';
  label?: Maybe<Scalars['String']>;
  options: Array<AnswerDetailsDropdownOption>;
};

export type AnswerDetailsDropdownOption = {
  __typename?: 'AnswerDetailsDropdownOption';
  value: Scalars['String'];
};

export type AnswerDetailsFile = {
  __typename?: 'AnswerDetailsFile';
  label?: Maybe<Scalars['String']>;
};

export type AnswerDetailsMultiSelect = {
  __typename?: 'AnswerDetailsMultiSelect';
  label?: Maybe<Scalars['String']>;
  options: Array<AnswerDetailsMultiSelectOption>;
};

export type AnswerDetailsMultiSelectOption = {
  __typename?: 'AnswerDetailsMultiSelectOption';
  value: Scalars['String'];
};

export type AnswerDetailsOwnerDropdown = {
  __typename?: 'AnswerDetailsOwnerDropdown';
  label?: Maybe<Scalars['String']>;
  options: Array<AnswerDetailsOwnerDropdownOption>;
};

export type AnswerDetailsOwnerDropdownOption = {
  __typename?: 'AnswerDetailsOwnerDropdownOption';
  value: Scalars['String'];
};

export type AnswerDetailsPersonMultiSelect = {
  __typename?: 'AnswerDetailsPersonMultiSelect';
  label?: Maybe<Scalars['String']>;
  options: Array<AnswerDetailsPersonMultiSelectOption>;
};

export type AnswerDetailsPersonMultiSelectOption = {
  __typename?: 'AnswerDetailsPersonMultiSelectOption';
  value: Scalars['String'];
};

export type AnswerDetailsSingleSelect = {
  __typename?: 'AnswerDetailsSingleSelect';
  label?: Maybe<Scalars['String']>;
  options: Array<AnswerDetailsSingleSelectOption>;
};

export type AnswerDetailsSingleSelectOption = {
  __typename?: 'AnswerDetailsSingleSelectOption';
  value: Scalars['String'];
};

export type AnswerDetailsText = {
  __typename?: 'AnswerDetailsText';
  label?: Maybe<Scalars['String']>;
  placeholder?: Maybe<Scalars['String']>;
};

export type AnswerDetailsTextarea = {
  __typename?: 'AnswerDetailsTextarea';
  label?: Maybe<Scalars['String']>;
  placeholder?: Maybe<Scalars['String']>;
};

export type AnswerDetailsUnion = AnswerDetailsAddress | AnswerDetailsCheckbox | AnswerDetailsDataTable | AnswerDetailsDropdown | AnswerDetailsFile | AnswerDetailsMultiSelect | AnswerDetailsOwnerDropdown | AnswerDetailsPersonMultiSelect | AnswerDetailsSingleSelect | AnswerDetailsText | AnswerDetailsTextarea;

/** Answer type */
export enum AnswerType {
  /** Address */
  Address = 'Address',
  /** Checkbox */
  Checkbox = 'Checkbox',
  /** Data table */
  DataTable = 'DataTable',
  /** Dropdown */
  Dropdown = 'Dropdown',
  /** File */
  File = 'File',
  /** Multi select */
  MultiSelect = 'MultiSelect',
  /** Number */
  Number = 'Number',
  /** Owner dropdown */
  OwnerDropdown = 'OwnerDropdown',
  /** Person multi select */
  PersonMultiSelect = 'PersonMultiSelect',
  /** Single select */
  SingleSelect = 'SingleSelect',
  /** Text */
  Text = 'Text',
  /** Textarea */
  Textarea = 'Textarea'
}

export type CompleteSetupIntentInput = {
  email: Scalars['String'];
  payment_method: Scalars['String'];
};

export type Condition = {
  __typename?: 'Condition';
  answer: Answer;
  id: Scalars['ID'];
  selected_value?: Maybe<Scalars['String']>;
  type: ConditionType;
};

/** Condition type */
export enum ConditionType {
  /** And */
  And = 'AND',
  /** Or */
  Or = 'OR'
}

export type Conveyancer = {
  __typename?: 'Conveyancer';
  address?: Maybe<Address>;
  all_invoices_link: Scalars['String'];
  client_care_letter?: Maybe<Scalars['String']>;
  client_care_letter_purchase?: Maybe<Scalars['String']>;
  client_care_letter_remortgage?: Maybe<Scalars['String']>;
  client_care_letter_sale?: Maybe<Scalars['String']>;
  company_number?: Maybe<Scalars['String']>;
  email_address?: Maybe<Scalars['String']>;
  id: Scalars['ID'];
  invoices?: Maybe<Array<Invoice>>;
  letter_footer?: Maybe<Scalars['String']>;
  letter_header?: Maybe<Scalars['String']>;
  location?: Maybe<Scalars['String']>;
  logo_image?: Maybe<Media>;
  name: Scalars['String'];
  payment_on_account_amount?: Maybe<Scalars['Int']>;
  sra_clc_number: Scalars['String'];
  stripe_account_id?: Maybe<Scalars['String']>;
  subscription?: Maybe<ConveyancerSubscription>;
  team_member_count: Scalars['Int'];
  team_members?: Maybe<Array<User>>;
  telephone_number?: Maybe<Scalars['String']>;
  terms_and_conditions?: Maybe<Scalars['String']>;
  trading_name?: Maybe<Scalars['String']>;
  type: Scalars['String'];
  vat_number?: Maybe<Scalars['String']>;
  website?: Maybe<Scalars['String']>;
};


export type ConveyancerInvoicesArgs = {
  limit: Scalars['Int'];
  starting_after?: InputMaybe<Scalars['String']>;
};

export type ConveyancerSubscription = {
  __typename?: 'ConveyancerSubscription';
  billing_email?: Maybe<Scalars['String']>;
  payment_method?: Maybe<ConveyancerSubscriptionPaymentMethod>;
  plan_name?: Maybe<Scalars['String']>;
  plan_price?: Maybe<Scalars['Int']>;
};

export type ConveyancerSubscriptionPaymentMethod = {
  __typename?: 'ConveyancerSubscriptionPaymentMethod';
  brand?: Maybe<Scalars['String']>;
  exp_month?: Maybe<Scalars['Int']>;
  exp_year?: Maybe<Scalars['Int']>;
  last4?: Maybe<Scalars['String']>;
  sort_code?: Maybe<Scalars['String']>;
  type: Scalars['String'];
};

export type CreateAddressInput = {
  city: Scalars['String'];
  line_1: Scalars['String'];
  line_2?: InputMaybe<Scalars['String']>;
  postcode: Scalars['String'];
};

export type CreateConveyancerInput = {
  address: CreateAddressInput;
  company_number?: InputMaybe<Scalars['String']>;
  email_address?: InputMaybe<Scalars['String']>;
  location?: InputMaybe<Scalars['String']>;
  logo_image?: InputMaybe<UploadFileInput>;
  name: Scalars['String'];
  sra_clc_number?: InputMaybe<Scalars['String']>;
  telephone_number?: InputMaybe<Scalars['String']>;
  trading_name?: InputMaybe<Scalars['String']>;
  type: Scalars['String'];
  vat_number?: InputMaybe<Scalars['String']>;
  website?: InputMaybe<Scalars['String']>;
};

export type CreateInviteAddressInput = {
  city: Scalars['String'];
  line_1: Scalars['String'];
  line_2?: InputMaybe<Scalars['String']>;
  postcode: Scalars['String'];
  uprn: Scalars['String'];
};

export type CreateSetupIntentInput = {
  address: CreateAddressInput;
  card_cvv?: InputMaybe<Scalars['Boolean']>;
  card_expiry_date?: InputMaybe<Scalars['Boolean']>;
  card_number?: InputMaybe<Scalars['Boolean']>;
  email: Scalars['String'];
  name: Scalars['String'];
};

/** Document type */
export enum DocumentType {
  /** Additional */
  Additional = 'Additional',
  /** Client care letter */
  ClientCareLetter = 'ClientCareLetter',
  /** Client information */
  ClientInformation = 'ClientInformation',
  /** Evidence enquiry */
  EvidenceEnquiry = 'EvidenceEnquiry',
  /** Evidence getting started */
  EvidenceGettingStarted = 'EvidenceGettingStarted',
  /** Evidence protocol */
  EvidenceProtocol = 'EvidenceProtocol',
  /** Form */
  Form = 'Form',
  /** Giftor declaration */
  GiftorDeclaration = 'GiftorDeclaration',
  /** Idv */
  Idv = 'Idv',
  /** Protocol and enquiry */
  ProtocolAndEnquiry = 'ProtocolAndEnquiry',
  /** Sof check */
  SofCheck = 'SofCheck',
  /** Terms and conditions */
  TermsAndConditions = 'TermsAndConditions',
  /** Unknown */
  Unknown = 'Unknown'
}

export type Form = {
  __typename?: 'Form';
  conditions: Array<Condition>;
  description: Scalars['String'];
  group: FormGroup;
  id: Scalars['ID'];
  image?: Maybe<Media>;
  name?: Maybe<Scalars['String']>;
  order_number?: Maybe<Scalars['Int']>;
  pivot?: Maybe<ActiveFormsPivot>;
  repeatable_answer?: Maybe<Answer>;
  sections: Array<Section>;
  signed?: Maybe<Scalars['Boolean']>;
  ta_form_template?: Maybe<FormType>;
};

/** Form group */
export enum FormGroup {
  /** Enquiry */
  Enquiry = 'Enquiry',
  /** Getting started */
  GettingStarted = 'GettingStarted',
  /** Protocol */
  Protocol = 'Protocol'
}

/** Form type */
export enum FormType {
  /** Company */
  Company = 'Company',
  /** Getting started */
  GettingStarted = 'GettingStarted',
  /** Getting started mortgages */
  GettingStartedMortgages = 'GettingStartedMortgages',
  /** Giftor */
  Giftor = 'Giftor',
  /** Individual */
  Individual = 'Individual',
  /** T a6 property information */
  Ta6PropertyInformation = 'TA6PropertyInformation',
  /** T a7 leasehold information */
  Ta7LeaseholdInformation = 'TA7LeaseholdInformation',
  /** T a9 commonhold information */
  Ta9CommonholdInformation = 'TA9CommonholdInformation',
  /** T a10 fittings and contents */
  Ta10FittingsAndContents = 'TA10FittingsAndContents'
}

export type GiftorDepositDeclarationProgress = {
  __typename?: 'GiftorDepositDeclarationProgress';
  completed?: Maybe<Scalars['Boolean']>;
  required: Scalars['Boolean'];
};

export type GlobalSearchInput = {
  conveyancerId?: InputMaybe<Scalars['Int']>;
  search?: InputMaybe<Scalars['String']>;
};

export type IdvProgress = {
  __typename?: 'IdvProgress';
  completed?: Maybe<Scalars['Boolean']>;
  mobile_connected: Scalars['Boolean'];
  required: Scalars['Boolean'];
};

export type InviteAddress = {
  __typename?: 'InviteAddress';
  city: Scalars['String'];
  id: Scalars['ID'];
  line_1: Scalars['String'];
  line_2?: Maybe<Scalars['String']>;
  postcode: Scalars['String'];
  uprn: Scalars['String'];
};

export type InviteGiftorInput = {
  party_id: Scalars['ID'];
  property_id: Scalars['ID'];
};

export type InviteNewClientInput = {
  address: CreateInviteAddressInput;
  case_reference: Scalars['String'];
  conveyancing_fee?: InputMaybe<Scalars['String']>;
  email: Scalars['String'];
  fee_earner_id?: InputMaybe<Scalars['ID']>;
  first_name: Scalars['String'];
  id_check_required?: InputMaybe<Scalars['Boolean']>;
  last_name: Scalars['String'];
  letters_required?: InputMaybe<Scalars['Boolean']>;
  payment_amount?: InputMaybe<Scalars['Int']>;
  payment_required?: InputMaybe<Scalars['Boolean']>;
  sale_price?: InputMaybe<Scalars['String']>;
  sof_check_required?: InputMaybe<Scalars['Boolean']>;
  type: Scalars['String'];
};

export type InvitePartyInput = {
  party_id: Scalars['ID'];
  property_id: Scalars['ID'];
};

export type InviteTeamMemberInput = {
  email: Scalars['String'];
  job_role: Scalars['String'];
};

export type InviteTeamMembersInput = {
  team_members: Array<InviteTeamMemberInput>;
};

export type Invoice = {
  __typename?: 'Invoice';
  amount: Scalars['Int'];
  date: Scalars['DateTime'];
  number?: Maybe<Scalars['String']>;
  pdf_url?: Maybe<Scalars['String']>;
  plan_name: Scalars['String'];
  status: Scalars['String'];
};

export type LoginInput = {
  email: Scalars['String'];
  password: Scalars['String'];
};

export type Media = {
  __typename?: 'Media';
  custom_properties?: Maybe<Scalars['Mixed']>;
  id: Scalars['ID'];
  name?: Maybe<Scalars['String']>;
  url: Scalars['String'];
};

export type Mutation = {
  __typename?: 'Mutation';
  addNewParty: Scalars['Boolean'];
  archiveProperty: Property;
  completeSetupIntent: Scalars['Boolean'];
  createAddress?: Maybe<Address>;
  createConveyancer: Conveyancer;
  createFormSigningUrl?: Maybe<Scalars['String']>;
  createGiftorDeclarationSigningUrl?: Maybe<Scalars['String']>;
  createIdvQrCode?: Maybe<Scalars['String']>;
  createLettersSigningUrl?: Maybe<Scalars['String']>;
  createPaymentOnAccountPaymentIntent?: Maybe<Scalars['String']>;
  createSetupIntent: Scalars['String'];
  deleteMortgage?: Maybe<Scalars['Boolean']>;
  deleteOtherUser: Scalars['Boolean'];
  deleteUser: Scalars['Boolean'];
  disconnectStripe: Scalars['Boolean'];
  forgottenPassword?: Maybe<Scalars['Boolean']>;
  idvMobileConnected?: Maybe<Scalars['Boolean']>;
  inviteGiftor: Scalars['Boolean'];
  inviteNewClient: Property;
  inviteParty: Scalars['Boolean'];
  inviteTeamMember: Scalars['Boolean'];
  login: User;
  logout: Scalars['Boolean'];
  markAllNotificationsRead?: Maybe<User>;
  register: User;
  registerClient: User;
  registerTeamMember: User;
  removeGiftor: Scalars['Boolean'];
  removeParty: Scalars['Boolean'];
  resendInvite?: Maybe<Scalars['Boolean']>;
  resetPassword: Scalars['Boolean'];
  reuploadAdditionalDocuments: Media;
  saveProvidedAnswers: MyProgress;
  sendInvite: User;
  updateAddress?: Maybe<Address>;
  updateBillingEmail: Scalars['Boolean'];
  updateClientDetails: User;
  updateConveyancer: Conveyancer;
  updateConveyancerDetails?: Maybe<Conveyancer>;
  updateExistingParty: Scalars['Boolean'];
  updateIDProvider: Scalars['Boolean'];
  updateInvitedTeamMember: User;
  updateStripeCode: Scalars['Boolean'];
  updateUserDetails: User;
  updateUserNotificationPreferences: User;
  updateUserProfile: User;
  uploadAdditionalDocuments: Media;
  uploadSofCheckDocuments: Array<Media>;
};


export type MutationAddNewPartyArgs = {
  input: AddNewPartyInput;
};


export type MutationArchivePropertyArgs = {
  id: Scalars['ID'];
};


export type MutationCompleteSetupIntentArgs = {
  input: CompleteSetupIntentInput;
};


export type MutationCreateAddressArgs = {
  input: CreateAddressInput;
};


export type MutationCreateConveyancerArgs = {
  input: CreateConveyancerInput;
};


export type MutationCreateFormSigningUrlArgs = {
  form_id: Scalars['ID'];
  property_id: Scalars['ID'];
};


export type MutationCreateGiftorDeclarationSigningUrlArgs = {
  property_id: Scalars['ID'];
};


export type MutationCreateIdvQrCodeArgs = {
  property_id: Scalars['ID'];
};


export type MutationCreateLettersSigningUrlArgs = {
  property_id: Scalars['ID'];
};


export type MutationCreatePaymentOnAccountPaymentIntentArgs = {
  property_id: Scalars['ID'];
};


export type MutationCreateSetupIntentArgs = {
  input: CreateSetupIntentInput;
};


export type MutationDeleteMortgageArgs = {
  charge_index: Scalars['ID'];
  property_id: Scalars['ID'];
  step_id: Scalars['ID'];
};


export type MutationDeleteOtherUserArgs = {
  id: Scalars['ID'];
};


export type MutationForgottenPasswordArgs = {
  email: Scalars['String'];
};


export type MutationIdvMobileConnectedArgs = {
  reset: Scalars['Boolean'];
  session_id: Scalars['ID'];
};


export type MutationInviteGiftorArgs = {
  input: InviteGiftorInput;
};


export type MutationInviteNewClientArgs = {
  input: InviteNewClientInput;
};


export type MutationInvitePartyArgs = {
  input: InvitePartyInput;
};


export type MutationInviteTeamMemberArgs = {
  input: InviteTeamMembersInput;
};


export type MutationLoginArgs = {
  input: LoginInput;
};


export type MutationRegisterArgs = {
  input: RegisterInput;
};


export type MutationRegisterClientArgs = {
  input: RegisterClientInput;
};


export type MutationRegisterTeamMemberArgs = {
  input: RegisterTeamMemberInput;
};


export type MutationRemoveGiftorArgs = {
  input: RemoveGiftorInput;
};


export type MutationRemovePartyArgs = {
  input: RemovePartyInput;
};


export type MutationResendInviteArgs = {
  email: Scalars['String'];
};


export type MutationResetPasswordArgs = {
  input: ResetPasswordInput;
};


export type MutationReuploadAdditionalDocumentsArgs = {
  input: ReuploadAdditionalDocumentsInput;
  property_id: Scalars['ID'];
};


export type MutationSaveProvidedAnswersArgs = {
  input: SaveProvidedAnswersInput;
};


export type MutationSendInviteArgs = {
  input: SendInviteInput;
};


export type MutationUpdateAddressArgs = {
  input: UpdateAddressInput;
};


export type MutationUpdateBillingEmailArgs = {
  input: UpdateBillingEmailInput;
};


export type MutationUpdateClientDetailsArgs = {
  input: UpdateClientDetailsInput;
};


export type MutationUpdateConveyancerArgs = {
  input: UpdateConveyancerInput;
};


export type MutationUpdateConveyancerDetailsArgs = {
  input: UpdateConveyancerDetailsInput;
};


export type MutationUpdateExistingPartyArgs = {
  input: UpdateExistingPartyInput;
};


export type MutationUpdateIdProviderArgs = {
  input: UpdateIdProviderInput;
};


export type MutationUpdateInvitedTeamMemberArgs = {
  input: UpdateInvitedTeamMemberInput;
};


export type MutationUpdateStripeCodeArgs = {
  input: UpdateStripeCodeInput;
};


export type MutationUpdateUserDetailsArgs = {
  input: UpdateUserDetailsInput;
};


export type MutationUpdateUserNotificationPreferencesArgs = {
  input: UpdateNotificationPreferencesInput;
};


export type MutationUpdateUserProfileArgs = {
  input: UpdateUserProfileInput;
};


export type MutationUploadAdditionalDocumentsArgs = {
  input: UploadAdditionalDocumentsInput;
  property_id: Scalars['ID'];
};


export type MutationUploadSofCheckDocumentsArgs = {
  input: UploadSofCheckDocumentsInput;
  property_id: Scalars['ID'];
};

export type MyProgress = {
  __typename?: 'MyProgress';
  forms: Array<Form>;
  giftor_deposit_declaration: GiftorDepositDeclarationProgress;
  idv: IdvProgress;
  onboarding_letters: OnboardingLettersProgress;
  pack_progress: PackProgress;
  payment: PaymentProgress;
  provided_answers: Array<ProvidedAnswer>;
  sof: SofProgress;
};

export type Notification = {
  __typename?: 'Notification';
  created_at: Scalars['DateTime'];
  data?: Maybe<NotificationData>;
  id: Scalars['ID'];
  notifiable_id: Scalars['Int'];
  notifiable_type: Scalars['String'];
  read_at?: Maybe<Scalars['DateTime']>;
  type: Scalars['String'];
};

export type NotificationData = {
  __typename?: 'NotificationData';
  id?: Maybe<Scalars['Int']>;
  message?: Maybe<Scalars['String']>;
  type?: Maybe<Scalars['String']>;
};

export type NotificationPreferences = {
  __typename?: 'NotificationPreferences';
  client_new_document_uploads: Scalars['Boolean'];
  getting_started_forms_completed: Scalars['Boolean'];
  id: Scalars['Int'];
  onboarding_completed: Scalars['Boolean'];
};

export type OnboardingLetterPreview = {
  __typename?: 'OnboardingLetterPreview';
  html: Scalars['String'];
};

export type OnboardingLettersProgress = {
  __typename?: 'OnboardingLettersProgress';
  completed?: Maybe<Scalars['Boolean']>;
  required: Scalars['Boolean'];
};

/** Allows ordering a list of records. */
export type OrderByClause = {
  /** The column that is used for ordering. */
  column: Scalars['String'];
  /** The direction that is used for ordering. */
  order: SortOrder;
};

/** Aggregate functions when ordering by a relation without specifying a column. */
export enum OrderByRelationAggregateFunction {
  /** Amount of items. */
  Count = 'COUNT'
}

/** Aggregate functions when ordering by a relation that may specify a column. */
export enum OrderByRelationWithColumnAggregateFunction {
  /** Average. */
  Avg = 'AVG',
  /** Amount of items. */
  Count = 'COUNT',
  /** Maximum. */
  Max = 'MAX',
  /** Minimum. */
  Min = 'MIN',
  /** Sum. */
  Sum = 'SUM'
}

export type PackProgress = {
  __typename?: 'PackProgress';
  completed: Scalars['Boolean'];
};

/** Information about pagination using a Relay style cursor connection. */
export type PageInfo = {
  __typename?: 'PageInfo';
  /** Number of nodes in the current page. */
  count: Scalars['Int'];
  /** Index of the current page. */
  currentPage: Scalars['Int'];
  /** The cursor to continue paginating forwards. */
  endCursor?: Maybe<Scalars['String']>;
  /** When paginating forwards, are there more items? */
  hasNextPage: Scalars['Boolean'];
  /** When paginating backwards, are there more items? */
  hasPreviousPage: Scalars['Boolean'];
  /** Index of the last available page. */
  lastPage: Scalars['Int'];
  /** The cursor to continue paginating backwards. */
  startCursor?: Maybe<Scalars['String']>;
  /** Total number of nodes in the paginated connection. */
  total: Scalars['Int'];
};

/** Information about pagination using a fully featured paginator. */
export type PaginatorInfo = {
  __typename?: 'PaginatorInfo';
  /** Number of items in the current page. */
  count: Scalars['Int'];
  /** Index of the current page. */
  currentPage: Scalars['Int'];
  /** Index of the first item in the current page. */
  firstItem?: Maybe<Scalars['Int']>;
  /** Are there more pages after this one? */
  hasMorePages: Scalars['Boolean'];
  /** Index of the last item in the current page. */
  lastItem?: Maybe<Scalars['Int']>;
  /** Index of the last available page. */
  lastPage: Scalars['Int'];
  /** Number of items per page. */
  perPage: Scalars['Int'];
  /** Number of total available items. */
  total: Scalars['Int'];
};

export type PaymentIntent = {
  __typename?: 'PaymentIntent';
  client_secret: Scalars['String'];
  intent_id: Scalars['String'];
};

export type PaymentProgress = {
  __typename?: 'PaymentProgress';
  paid?: Maybe<Scalars['Boolean']>;
  required: Scalars['Boolean'];
};

export type PreviewOnboardingLetterInput = {
  content: Scalars['String'];
  footer: Scalars['String'];
  header: Scalars['String'];
};

export type Property = {
  __typename?: 'Property';
  active_forms: Array<Form>;
  address: Address;
  all_documents_link?: Maybe<Scalars['String']>;
  archived_at?: Maybe<Scalars['DateTime']>;
  case_reference: Scalars['String'];
  conveyancer: Conveyancer;
  conveyancing_fee?: Maybe<Scalars['Int']>;
  documents: Array<Media>;
  fee_earner_id?: Maybe<Scalars['ID']>;
  id: Scalars['ID'];
  id_check_required: Scalars['Boolean'];
  letters_required: Scalars['Boolean'];
  my_progress?: Maybe<MyProgress>;
  sale_price?: Maybe<Scalars['Int']>;
  type: PropertyType;
  users: Array<User>;
};

export type PropertyFilterInputs = {
  filter_option?: InputMaybe<Scalars['String']>;
  search?: InputMaybe<Scalars['String']>;
};

/** A paginated list of Property items. */
export type PropertyPaginator = {
  __typename?: 'PropertyPaginator';
  /** A list of Property items. */
  data: Array<Property>;
  /** Pagination information about the list of items. */
  paginatorInfo: PaginatorInfo;
};

/** Property type */
export enum PropertyType {
  /** Purchase */
  Purchase = 'Purchase',
  /** Remortgage */
  Remortgage = 'Remortgage',
  /** Sale */
  Sale = 'Sale'
}

export type PropertyUserPivot = {
  __typename?: 'PropertyUserPivot';
  id_verification_completed_at?: Maybe<Scalars['DateTime']>;
  is_primary_user: Scalars['Boolean'];
  onboarding_forms_completed_at?: Maybe<Scalars['DateTime']>;
  payment_on_account_completed_at?: Maybe<Scalars['DateTime']>;
  representation?: Maybe<Scalars['String']>;
  role: PropertyUserRole;
  sof_completed_at?: Maybe<Scalars['DateTime']>;
};

/** Property user role */
export enum PropertyUserRole {
  /** Attorney */
  Attorney = 'Attorney',
  /** Buyer */
  Buyer = 'Buyer',
  /** Deputy */
  Deputy = 'Deputy',
  /** Executor */
  Executor = 'Executor',
  /** Giftor */
  Giftor = 'Giftor',
  /** Owner */
  Owner = 'Owner',
  /** Remortgager */
  Remortgager = 'Remortgager'
}

export type ProvidedAnswer = {
  __typename?: 'ProvidedAnswer';
  active_form_id: Scalars['ID'];
  answer: Answer;
  id: Scalars['ID'];
  value?: Maybe<Scalars['Mixed']>;
};

export type Query = {
  __typename?: 'Query';
  getAddressFromOS2API: InviteAddress;
  getClientProperties: PropertyPaginator;
  globalSearch: Array<SearchResult>;
  health: Scalars['Boolean'];
  me?: Maybe<User>;
  media: Media;
  previewOnboardingLetter: OnboardingLetterPreview;
  properties: PropertyPaginator;
  property: Property;
  step: Step;
};


export type QueryGetAddressFromOs2ApiArgs = {
  input: SearchAddress;
};


export type QueryGetClientPropertiesArgs = {
  first: Scalars['Int'];
  page?: InputMaybe<Scalars['Int']>;
};


export type QueryGlobalSearchArgs = {
  input: GlobalSearchInput;
};


export type QueryMediaArgs = {
  id: Scalars['ID'];
};


export type QueryPreviewOnboardingLetterArgs = {
  input: PreviewOnboardingLetterInput;
};


export type QueryPropertiesArgs = {
  filters?: InputMaybe<PropertyFilterInputs>;
  first: Scalars['Int'];
  page?: InputMaybe<Scalars['Int']>;
};


export type QueryPropertyArgs = {
  id: Scalars['ID'];
};


export type QueryStepArgs = {
  id: Scalars['ID'];
};

export type RegisterClientInput = {
  email: Scalars['String'];
  invite_code: Scalars['String'];
  password: Scalars['String'];
  user_id: Scalars['ID'];
};

export type RegisterInput = {
  email: Scalars['String'];
  password: Scalars['String'];
};

export type RegisterTeamMemberInput = {
  email: Scalars['String'];
  first_name: Scalars['String'];
  invite_code?: InputMaybe<Scalars['String']>;
  last_name: Scalars['String'];
  password: Scalars['String'];
  phone: Scalars['String'];
  suffix?: InputMaybe<Scalars['String']>;
  title: Scalars['String'];
  user_id?: InputMaybe<Scalars['ID']>;
};

export type RemoveGiftorInput = {
  active_form_id: Scalars['String'];
  giftor_id: Scalars['ID'];
  giftor_index: Scalars['String'];
  property_id: Scalars['ID'];
  step_id: Scalars['ID'];
};

export type RemovePartyInput = {
  party_id: Scalars['ID'];
  property_id: Scalars['ID'];
};

export type ResetPasswordInput = {
  email: Scalars['String'];
  password: Scalars['String'];
  password_confirmation: Scalars['String'];
  token: Scalars['String'];
};

export type ReuploadAdditionalDocumentsInput = {
  file_id: Scalars['ID'];
  name: Scalars['String'];
  uploaded_document?: InputMaybe<UploadFileInput>;
};

export type SaveProvidedAnswerInput = {
  active_form_id: Scalars['ID'];
  answer_id: Scalars['ID'];
  property_id: Scalars['ID'];
  value?: InputMaybe<Scalars['Mixed']>;
};

export type SaveProvidedAnswersInput = {
  current_property_id?: InputMaybe<Scalars['ID']>;
  current_step_id?: InputMaybe<Scalars['ID']>;
  provided_answers: Array<SaveProvidedAnswerInput>;
};

export type SearchAddress = {
  address: Scalars['String'];
};

export type SearchResult = {
  __typename?: 'SearchResult';
  display_text?: Maybe<Scalars['String']>;
  id?: Maybe<Scalars['ID']>;
  line_1?: Maybe<Scalars['String']>;
  type?: Maybe<Scalars['String']>;
  users: Array<User>;
};

export type Section = {
  __typename?: 'Section';
  conditions: Array<Condition>;
  form: Form;
  id: Scalars['ID'];
  name: Scalars['String'];
  steps: Array<Step>;
};

/** Select person type */
export enum SelectPersonType {
  /** Owner */
  Owner = 'Owner'
}

export type SendInviteInput = {
  property_id: Scalars['ID'];
  user_id: Scalars['ID'];
};

/** Information about pagination using a simple paginator. */
export type SimplePaginatorInfo = {
  __typename?: 'SimplePaginatorInfo';
  /** Number of items in the current page. */
  count: Scalars['Int'];
  /** Index of the current page. */
  currentPage: Scalars['Int'];
  /** Index of the first item in the current page. */
  firstItem?: Maybe<Scalars['Int']>;
  /** Are there more pages after this one? */
  hasMorePages: Scalars['Boolean'];
  /** Index of the last item in the current page. */
  lastItem?: Maybe<Scalars['Int']>;
  /** Number of items per page. */
  perPage: Scalars['Int'];
};

export type SofProgress = {
  __typename?: 'SofProgress';
  completed?: Maybe<Scalars['Boolean']>;
  files?: Maybe<Array<Media>>;
  required: Scalars['Boolean'];
};

/** Directions for ordering a list of records. */
export enum SortOrder {
  /** Sort records in ascending order. */
  Asc = 'ASC',
  /** Sort records in descending order. */
  Desc = 'DESC'
}

export type Step = {
  __typename?: 'Step';
  answers: Array<Answer>;
  compiled_answer?: Maybe<Scalars['Mixed']>;
  conditions: Array<Condition>;
  help_text?: Maybe<Scalars['String']>;
  help_video_link?: Maybe<Scalars['String']>;
  id: Scalars['ID'];
  image?: Maybe<Media>;
  provided_answers?: Maybe<Array<Maybe<ProvidedAnswer>>>;
  question: Scalars['String'];
  repeatable_answer?: Maybe<Answer>;
  section: Section;
  sub_heading?: Maybe<Scalars['String']>;
  type: StepType;
};


export type StepProvided_AnswersArgs = {
  active_form_id: Scalars['ID'];
  property_id: Scalars['ID'];
};

/** Step type */
export enum StepType {
  /** Address */
  Address = 'Address',
  /** Attorney */
  Attorney = 'Attorney',
  /** Buyer */
  Buyer = 'Buyer',
  /** Buyer expanded */
  BuyerExpanded = 'BuyerExpanded',
  /** Buyer giftor */
  BuyerGiftor = 'BuyerGiftor',
  /** Buyers solicitor */
  BuyersSolicitor = 'BuyersSolicitor',
  /** Charges */
  Charges = 'Charges',
  /** Company form deputyship order representative */
  CompanyFormDeputyshipOrderRepresentative = 'CompanyFormDeputyshipOrderRepresentative',
  /** Company form grant of probate representative */
  CompanyFormGrantOfProbateRepresentative = 'CompanyFormGrantOfProbateRepresentative',
  /** Company form power of attorney representative */
  CompanyFormPowerOfAttorneyRepresentative = 'CompanyFormPowerOfAttorneyRepresentative',
  /** Company representative */
  CompanyRepresentative = 'CompanyRepresentative',
  /** Custom */
  Custom = 'Custom',
  /** Deputy */
  Deputy = 'Deputy',
  /** Deputy dropdown */
  DeputyDropdown = 'DeputyDropdown',
  /** Director details */
  DirectorDetails = 'DirectorDetails',
  /** Estate agent */
  EstateAgent = 'EstateAgent',
  /** Loaner */
  Loaner = 'Loaner',
  /** Mortgage amount */
  MortgageAmount = 'MortgageAmount',
  /** Mortgage broker */
  MortgageBroker = 'MortgageBroker',
  /** Mortgage charge loan */
  MortgageChargeLoan = 'MortgageChargeLoan',
  /** Mortgage lender */
  MortgageLender = 'MortgageLender',
  /** Mortgage related transactions */
  MortgageRelatedTransactions = 'MortgageRelatedTransactions',
  /** Mortgager */
  Mortgager = 'Mortgager',
  /** Name change */
  NameChange = 'NameChange',
  /** Owner */
  Owner = 'Owner',
  /** Owner form power of attorney */
  OwnerFormPowerOfAttorney = 'OwnerFormPowerOfAttorney',
  /** Owner name */
  OwnerName = 'OwnerName',
  /** Remortgage giftor */
  RemortgageGiftor = 'RemortgageGiftor',
  /** Repeatable name change attorney */
  RepeatableNameChangeAttorney = 'RepeatableNameChangeAttorney',
  /** Repeatable name change deputy */
  RepeatableNameChangeDeputy = 'RepeatableNameChangeDeputy',
  /** Repeatable name change executor */
  RepeatableNameChangeExecutor = 'RepeatableNameChangeExecutor',
  /** Repeatable name change owner */
  RepeatableNameChangeOwner = 'RepeatableNameChangeOwner',
  /** Sdlt */
  Sdlt = 'SDLT',
  /** Sale price */
  SalePrice = 'SalePrice',
  /** Savings amount */
  SavingsAmount = 'SavingsAmount',
  /** Seller */
  Seller = 'Seller',
  /** Sellers solicitor selectable */
  SellersSolicitorSelectable = 'SellersSolicitorSelectable',
  /** Sold status */
  SoldStatus = 'SoldStatus',
  /** T a9 managing agent */
  Ta9ManagingAgent = 'TA9ManagingAgent',
  /** T a9 secretary */
  Ta9Secretary = 'TA9Secretary',
  /** Tenure */
  Tenure = 'Tenure'
}

/** Specify if you want to include or exclude trashed results from a query. */
export enum Trashed {
  /** Only return trashed results. */
  Only = 'ONLY',
  /** Return both trashed and non-trashed results. */
  With = 'WITH',
  /** Only return non-trashed results. */
  Without = 'WITHOUT'
}

export type UpdateAddressInput = {
  city: Scalars['String'];
  line_1: Scalars['String'];
  line_2?: InputMaybe<Scalars['String']>;
  postcode: Scalars['String'];
};

export type UpdateBillingEmailInput = {
  email: Scalars['String'];
};

export type UpdateClientDetailsInput = {
  first_name: Scalars['String'];
  last_name: Scalars['String'];
  phone: Scalars['String'];
  profile_image?: InputMaybe<UploadFileInput>;
  title: Scalars['String'];
};

export type UpdateConveyancerDetailsInput = {
  address?: InputMaybe<UpdateAddressInput>;
  company_number?: InputMaybe<Scalars['String']>;
  email_address?: InputMaybe<Scalars['String']>;
  location?: InputMaybe<Scalars['String']>;
  logo_image?: InputMaybe<UploadFileInput>;
  name: Scalars['String'];
  sra_clc_number?: InputMaybe<Scalars['String']>;
  telephone_number?: InputMaybe<Scalars['String']>;
  trading_name?: InputMaybe<Scalars['String']>;
  vat_number?: InputMaybe<Scalars['String']>;
  website?: InputMaybe<Scalars['String']>;
};

export type UpdateConveyancerInput = {
  client_care_letter?: InputMaybe<Scalars['String']>;
  client_care_letter_purchase?: InputMaybe<Scalars['String']>;
  client_care_letter_remortgage?: InputMaybe<Scalars['String']>;
  client_care_letter_sale?: InputMaybe<Scalars['String']>;
  letter_footer?: InputMaybe<Scalars['String']>;
  letter_header?: InputMaybe<Scalars['String']>;
  payment_on_account_amount?: InputMaybe<Scalars['Int']>;
  terms_and_conditions?: InputMaybe<Scalars['String']>;
};

export type UpdateExistingPartyInput = {
  address?: InputMaybe<UpdateAddressInput>;
  email?: InputMaybe<Scalars['String']>;
  first_name?: InputMaybe<Scalars['String']>;
  last_name?: InputMaybe<Scalars['String']>;
  middle_name?: InputMaybe<Scalars['String']>;
  occupation?: InputMaybe<Scalars['String']>;
  phone?: InputMaybe<Scalars['String']>;
  property_id: Scalars['ID'];
  representation?: InputMaybe<Scalars['String']>;
  title?: InputMaybe<Scalars['String']>;
  user_id: Scalars['ID'];
};

export type UpdateIdProviderInput = {
  provider: Scalars['String'];
};

export type UpdateInvitedTeamMemberInput = {
  job_bio?: InputMaybe<Scalars['String']>;
  profile_image?: InputMaybe<UploadFileInput>;
};

export type UpdateNotificationPreferencesInput = {
  client_new_document_uploads: Scalars['Boolean'];
  getting_started_forms_completed: Scalars['Boolean'];
  onboarding_completed: Scalars['Boolean'];
};

export type UpdateStripeCodeInput = {
  code: Scalars['String'];
};

export type UpdateUserDetailsInput = {
  first_name: Scalars['String'];
  job_bio?: InputMaybe<Scalars['String']>;
  job_role?: InputMaybe<Scalars['String']>;
  last_name: Scalars['String'];
  phone: Scalars['String'];
  profile_image?: InputMaybe<UploadFileInput>;
  sra_clc_number: Scalars['String'];
  suffix?: InputMaybe<Scalars['String']>;
  title: Scalars['String'];
};

export type UpdateUserProfileInput = {
  email: Scalars['String'];
  first_name: Scalars['String'];
  job_bio?: InputMaybe<Scalars['String']>;
  job_role?: InputMaybe<Scalars['String']>;
  last_name: Scalars['String'];
  newPassword?: InputMaybe<Scalars['String']>;
  password?: InputMaybe<Scalars['String']>;
  phone: Scalars['String'];
  profile_image?: InputMaybe<UploadFileInput>;
  sra_clc_number?: InputMaybe<Scalars['String']>;
  suffix?: InputMaybe<Scalars['String']>;
  title?: InputMaybe<Scalars['String']>;
};

export type UploadAdditionalDocumentsInput = {
  name: Scalars['String'];
  uploaded_document?: InputMaybe<UploadFileInput>;
};

export type UploadFileInput = {
  extension: Scalars['String'];
  key: Scalars['String'];
};

export type UploadSofCheckDocumentsInput = {
  documents: Array<UploadFileInput>;
};

export type User = {
  __typename?: 'User';
  address?: Maybe<Address>;
  business_created_at?: Maybe<Scalars['DateTime']>;
  conveyancer?: Maybe<Conveyancer>;
  email: Scalars['String'];
  email_verified_at?: Maybe<Scalars['DateTime']>;
  first_name?: Maybe<Scalars['String']>;
  id: Scalars['ID'];
  invite_code?: Maybe<Scalars['String']>;
  invite_code_sent_at?: Maybe<Scalars['DateTime']>;
  job_bio?: Maybe<Scalars['String']>;
  job_role?: Maybe<Scalars['String']>;
  last_name?: Maybe<Scalars['String']>;
  middle_name?: Maybe<Scalars['String']>;
  notification_preferences?: Maybe<NotificationPreferences>;
  occupation?: Maybe<Scalars['String']>;
  phone?: Maybe<Scalars['String']>;
  pivot?: Maybe<PropertyUserPivot>;
  profile_image?: Maybe<Media>;
  properties: Array<Property>;
  role: UserRole;
  sra_clc_number?: Maybe<Scalars['String']>;
  suffix?: Maybe<Scalars['String']>;
  title?: Maybe<Scalars['String']>;
  unread_notifications: Array<Notification>;
};

/** User role */
export enum UserRole {
  /** Admin */
  Admin = 'Admin',
  /** Client */
  Client = 'Client',
  /** Conveyancer */
  Conveyancer = 'Conveyancer'
}

export type CreateSetupIntentMutationVariables = Exact<{
  input: CreateSetupIntentInput;
}>;


export type CreateSetupIntentMutation = { __typename?: 'Mutation', createSetupIntent: string };

export type CompleteSetupIntentMutationVariables = Exact<{
  input: CompleteSetupIntentInput;
}>;


export type CompleteSetupIntentMutation = { __typename?: 'Mutation', completeSetupIntent: boolean };

export type GetFormQuestionQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type GetFormQuestionQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, my_progress?: { __typename?: 'MyProgress', provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, active_form_id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string, section: { __typename?: 'Section', id: string } } } }> } | null, active_forms: Array<{ __typename?: 'Form', id: string, name?: string | null, ta_form_template?: FormType | null, group: FormGroup, pivot?: { __typename?: 'ActiveFormsPivot', id: string, title?: string | null } | null, conditions: Array<{ __typename?: 'Condition', id: string, type: ConditionType, answer: { __typename?: 'Answer', id: string, type: AnswerType } }>, sections: Array<{ __typename?: 'Section', id: string, name: string, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, steps: Array<{ __typename?: 'Step', id: string, question: string, sub_heading?: string | null, type: StepType, help_text?: string | null, help_video_link?: string | null, image?: { __typename?: 'Media', id: string, url: string } | null, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, repeatable_answer?: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string }, conditions: Array<{ __typename?: 'Condition', id: string }> } | null, answers: Array<{ __typename?: 'Answer', id: string, type: AnswerType, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, details?: { __typename?: 'AnswerDetailsAddress', label?: string | null } | { __typename?: 'AnswerDetailsCheckbox', label?: string | null } | { __typename?: 'AnswerDetailsDataTable', allowsAddMore: boolean, addMoreLabel?: string | null, rows: Array<{ __typename?: 'AnswerDetailsDataTableRow', name: string }>, columns: Array<{ __typename?: 'AnswerDetailsDataTableColumn', name: string, type: AnswerType, placeholder?: string | null }> } | { __typename?: 'AnswerDetailsDropdown', label?: string | null, options: Array<{ __typename?: 'AnswerDetailsDropdownOption', value: string }> } | { __typename?: 'AnswerDetailsFile' } | { __typename?: 'AnswerDetailsMultiSelect' } | { __typename?: 'AnswerDetailsOwnerDropdown', label?: string | null, options: Array<{ __typename?: 'AnswerDetailsOwnerDropdownOption', value: string }> } | { __typename?: 'AnswerDetailsPersonMultiSelect', label?: string | null, options: Array<{ __typename?: 'AnswerDetailsPersonMultiSelectOption', value: string }> } | { __typename?: 'AnswerDetailsSingleSelect', label?: string | null, options: Array<{ __typename?: 'AnswerDetailsSingleSelectOption', value: string }> } | { __typename?: 'AnswerDetailsText', label?: string | null, placeholder?: string | null } | { __typename?: 'AnswerDetailsTextarea', label?: string | null, placeholder?: string | null } | null }> }> }> }> } };

export type SaveProvidedAnswersMutationVariables = Exact<{
  input: SaveProvidedAnswersInput;
}>;


export type SaveProvidedAnswersMutation = { __typename?: 'Mutation', saveProvidedAnswers: { __typename?: 'MyProgress', provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string } } }> } };

export type StepQueryVariables = Exact<{
  id: Scalars['ID'];
  propertyId: Scalars['ID'];
  activeFormId: Scalars['ID'];
}>;


export type StepQuery = { __typename?: 'Query', step: { __typename?: 'Step', id: string, question: string, sub_heading?: string | null, type: StepType, help_text?: string | null, help_video_link?: string | null, image?: { __typename?: 'Media', id: string, url: string } | null, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, repeatable_answer?: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string }, provided_answers?: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null } | null> | null, conditions: Array<{ __typename?: 'Condition', id: string }> } | null, provided_answers?: Array<{ __typename?: 'ProvidedAnswer', id: string, active_form_id: string, value?: any | null, answer: { __typename?: 'Answer', id: string } } | null> | null, answers: Array<{ __typename?: 'Answer', id: string, type: AnswerType, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, details?: { __typename?: 'AnswerDetailsAddress' } | { __typename?: 'AnswerDetailsCheckbox' } | { __typename?: 'AnswerDetailsDataTable' } | { __typename?: 'AnswerDetailsDropdown', label?: string | null, options: Array<{ __typename?: 'AnswerDetailsDropdownOption', value: string }> } | { __typename?: 'AnswerDetailsFile' } | { __typename?: 'AnswerDetailsMultiSelect' } | { __typename?: 'AnswerDetailsOwnerDropdown' } | { __typename?: 'AnswerDetailsPersonMultiSelect' } | { __typename?: 'AnswerDetailsSingleSelect' } | { __typename?: 'AnswerDetailsText', label?: string | null, placeholder?: string | null } | { __typename?: 'AnswerDetailsTextarea' } | null }> } };

export type InviteGiftorMutationVariables = Exact<{
  input: InviteGiftorInput;
}>;


export type InviteGiftorMutation = { __typename?: 'Mutation', inviteGiftor: boolean };

export type InvitePartyMutationVariables = Exact<{
  input: InvitePartyInput;
}>;


export type InvitePartyMutation = { __typename?: 'Mutation', inviteParty: boolean };

export type RemoveGiftorMutationVariables = Exact<{
  input: RemoveGiftorInput;
}>;


export type RemoveGiftorMutation = { __typename?: 'Mutation', removeGiftor: boolean };

export type RemovePartyMutationVariables = Exact<{
  input: RemovePartyInput;
}>;


export type RemovePartyMutation = { __typename?: 'Mutation', removeParty: boolean };

export type DeleteMortgageMutationVariables = Exact<{
  step_id: Scalars['ID'];
  property_id: Scalars['ID'];
  charge_index: Scalars['ID'];
}>;


export type DeleteMortgageMutation = { __typename?: 'Mutation', deleteMortgage?: boolean | null };

export type SendInviteMutationVariables = Exact<{
  input: SendInviteInput;
}>;


export type SendInviteMutation = { __typename?: 'Mutation', sendInvite: { __typename?: 'User', id: string, invite_code_sent_at?: any | null } };

export type RegisterBusinessConveyancerQueryVariables = Exact<{ [key: string]: never; }>;


export type RegisterBusinessConveyancerQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, name: string, type: string, sra_clc_number: string, company_number?: string | null, trading_name?: string | null, vat_number?: string | null, website?: string | null, location?: string | null, telephone_number?: string | null, email_address?: string | null, address?: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string } | null, logo_image?: { __typename?: 'Media', id: string, url: string } | null } | null } | null };

export type OnboardingLettersQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, client_care_letter?: string | null, client_care_letter_sale?: string | null, client_care_letter_purchase?: string | null, client_care_letter_remortgage?: string | null, terms_and_conditions?: string | null, letter_header?: string | null, letter_footer?: string | null } | null } | null };

export type UpdateConveyancerMutationVariables = Exact<{
  input: UpdateConveyancerInput;
}>;


export type UpdateConveyancerMutation = { __typename?: 'Mutation', updateConveyancer: { __typename?: 'Conveyancer', id: string } };

export type RegistrationPaymentsConveyancerQueryVariables = Exact<{ [key: string]: never; }>;


export type RegistrationPaymentsConveyancerQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, stripe_account_id?: string | null } | null } | null };

export type UpdateStripeCodeMutationVariables = Exact<{
  input: UpdateStripeCodeInput;
}>;


export type UpdateStripeCodeMutation = { __typename?: 'Mutation', updateStripeCode: boolean };

export type DisconnectStripeMutationVariables = Exact<{ [key: string]: never; }>;


export type DisconnectStripeMutation = { __typename?: 'Mutation', disconnectStripe: boolean };

export type InviteTeamMemberMutationVariables = Exact<{
  input: InviteTeamMembersInput;
}>;


export type InviteTeamMemberMutation = { __typename?: 'Mutation', inviteTeamMember: boolean };

export type DeleteOtherUserMutationVariables = Exact<{
  id: Scalars['ID'];
}>;


export type DeleteOtherUserMutation = { __typename?: 'Mutation', deleteOtherUser: boolean };

export type LogoutMutationVariables = Exact<{ [key: string]: never; }>;


export type LogoutMutation = { __typename?: 'Mutation', logout: boolean };

export type MediaQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type MediaQuery = { __typename?: 'Query', media: { __typename?: 'Media', id: string, url: string, name?: string | null, custom_properties?: any | null } };

export type MarkAllNotificationsReadMutationVariables = Exact<{ [key: string]: never; }>;


export type MarkAllNotificationsReadMutation = { __typename?: 'Mutation', markAllNotificationsRead?: { __typename?: 'User', id: string, unread_notifications: Array<{ __typename?: 'Notification', id: string, type: string, notifiable_type: string, notifiable_id: number, read_at?: any | null, created_at: any, data?: { __typename?: 'NotificationData', type?: string | null, id?: number | null, message?: string | null } | null }> } | null };

export type DoGlobalQueryQueryVariables = Exact<{
  filters: GlobalSearchInput;
}>;


export type DoGlobalQueryQuery = { __typename?: 'Query', globalSearch: Array<{ __typename?: 'SearchResult', id?: string | null, type?: string | null, display_text?: string | null, line_1?: string | null, users: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null }> }> };

export type MeQueryVariables = Exact<{ [key: string]: never; }>;


export type MeQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, title?: string | null, first_name?: string | null, last_name?: string | null, suffix?: string | null, phone?: string | null, email: string, sra_clc_number?: string | null, role: UserRole, job_role?: string | null, job_bio?: string | null, conveyancer?: { __typename?: 'Conveyancer', id: string, name: string, sra_clc_number: string, team_member_count: number, type: string } | null, unread_notifications: Array<{ __typename?: 'Notification', id: string, type: string, notifiable_type: string, notifiable_id: number, read_at?: any | null, created_at: any, data?: { __typename?: 'NotificationData', type?: string | null, id?: number | null, message?: string | null } | null }>, profile_image?: { __typename?: 'Media', id: string, url: string } | null } | null };

export type UpdateClientProfileMutationVariables = Exact<{
  input: UpdateUserProfileInput;
}>;


export type UpdateClientProfileMutation = { __typename?: 'Mutation', updateUserProfile: { __typename?: 'User', first_name?: string | null, last_name?: string | null, title?: string | null, suffix?: string | null, job_bio?: string | null, role: UserRole, phone?: string | null, email: string, profile_image?: { __typename?: 'Media', url: string } | null } };

export type ForgottenPasswordMutationVariables = Exact<{
  email: Scalars['String'];
}>;


export type ForgottenPasswordMutation = { __typename?: 'Mutation', forgottenPassword?: boolean | null };

export type LoginClientMutationVariables = Exact<{
  input: LoginInput;
}>;


export type LoginClientMutation = { __typename?: 'Mutation', login: { __typename?: 'User', id: string, title?: string | null, first_name?: string | null, last_name?: string | null, suffix?: string | null, phone?: string | null, email: string, role: UserRole } };

export type UpdateClientDetailsMutationVariables = Exact<{
  input: UpdateClientDetailsInput;
}>;


export type UpdateClientDetailsMutation = { __typename?: 'Mutation', updateClientDetails: { __typename?: 'User', first_name?: string | null, last_name?: string | null, title?: string | null, phone?: string | null } };

export type ResetPasswordMutationVariables = Exact<{
  input: ResetPasswordInput;
}>;


export type ResetPasswordMutation = { __typename?: 'Mutation', resetPassword: boolean };

export type AddNewPartyMutationVariables = Exact<{
  input: AddNewPartyInput;
}>;


export type AddNewPartyMutation = { __typename?: 'Mutation', addNewParty: boolean };

export type GetPropertyUsersQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type GetPropertyUsersQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, letters_required: boolean, type: PropertyType, users: Array<{ __typename?: 'User', id: string, email: string, first_name?: string | null, last_name?: string | null, pivot?: { __typename?: 'PropertyUserPivot', role: PropertyUserRole } | null }> } };

export type GetPropertyQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type GetPropertyQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, sale_price?: number | null, letters_required: boolean, id_check_required: boolean, conveyancing_fee?: number | null, type: PropertyType, active_forms: Array<{ __typename?: 'Form', id: string, ta_form_template?: FormType | null, pivot?: { __typename?: 'ActiveFormsPivot', id: string } | null, sections: Array<{ __typename?: 'Section', id: string, steps: Array<{ __typename?: 'Step', id: string, type: StepType, compiled_answer?: any | null, answers: Array<{ __typename?: 'Answer', id: string }> }> }> }>, my_progress?: { __typename?: 'MyProgress', provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null, answer: { __typename?: 'Answer', id: string } }> } | null, users: Array<{ __typename?: 'User', id: string, email: string, title?: string | null, first_name?: string | null, middle_name?: string | null, last_name?: string | null, phone?: string | null, occupation?: string | null, invite_code_sent_at?: any | null, email_verified_at?: any | null, pivot?: { __typename?: 'PropertyUserPivot', role: PropertyUserRole, sof_completed_at?: any | null, representation?: string | null } | null, address?: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string } | null }>, address: { __typename?: 'Address', id: string, line_1: string, line_2?: string | null, city: string, postcode: string } } };

export type UpdateExistingPartyMutationVariables = Exact<{
  input: UpdateExistingPartyInput;
}>;


export type UpdateExistingPartyMutation = { __typename?: 'Mutation', updateExistingParty: boolean };

export type GetPackQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type GetPackQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, archived_at?: any | null, case_reference: string, users: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null }>, address: { __typename?: 'Address', id: string, line_1: string, line_2?: string | null, city: string, postcode: string }, documents: Array<{ __typename?: 'Media', id: string, url: string, name?: string | null, custom_properties?: any | null }> } };

export type DownloadPackQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type DownloadPackQuery = { __typename?: 'Query', property: { __typename?: 'Property', all_documents_link?: string | null } };

export type ArchivePropertyMutationVariables = Exact<{
  id: Scalars['ID'];
}>;


export type ArchivePropertyMutation = { __typename?: 'Mutation', archiveProperty: { __typename?: 'Property', id: string, archived_at?: any | null } };

export type PropertiesQueryVariables = Exact<{
  first: Scalars['Int'];
  page: Scalars['Int'];
  filters?: InputMaybe<PropertyFilterInputs>;
}>;


export type PropertiesQuery = { __typename?: 'Query', properties: { __typename?: 'PropertyPaginator', data: Array<{ __typename?: 'Property', id: string, type: PropertyType, archived_at?: any | null, users: Array<{ __typename?: 'User', id: string, title?: string | null, first_name?: string | null, last_name?: string | null, email: string, role: UserRole, job_role?: string | null, job_bio?: string | null, suffix?: string | null, phone?: string | null }>, address: { __typename?: 'Address', id: string, line_1: string, line_2?: string | null, city: string, postcode: string } }>, paginatorInfo: { __typename?: 'PaginatorInfo', total: number, lastPage: number } } };

export type ClientLoginMutationVariables = Exact<{
  input: LoginInput;
}>;


export type ClientLoginMutation = { __typename?: 'Mutation', login: { __typename?: 'User', id: string, title?: string | null, first_name?: string | null, last_name?: string | null, suffix?: string | null, phone?: string | null, email: string, role: UserRole, job_role?: string | null, job_bio?: string | null, business_created_at?: any | null, conveyancer?: { __typename?: 'Conveyancer', id: string, name: string, sra_clc_number: string, team_member_count: number, type: string } | null, unread_notifications: Array<{ __typename?: 'Notification', id: string, type: string, notifiable_type: string, notifiable_id: number, read_at?: any | null, created_at: any, data?: { __typename?: 'NotificationData', type?: string | null, id?: number | null, message?: string | null } | null }> } };

export type UpdateInvitedTeamMemberMutationVariables = Exact<{
  input: UpdateInvitedTeamMemberInput;
}>;


export type UpdateInvitedTeamMemberMutation = { __typename?: 'Mutation', updateInvitedTeamMember: { __typename?: 'User', job_bio?: string | null } };

export type ClientPropertyDocumentsQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type ClientPropertyDocumentsQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, address: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string }, documents: Array<{ __typename?: 'Media', id: string, name?: string | null, custom_properties?: any | null }>, active_forms: Array<{ __typename?: 'Form', id: string, name?: string | null, description: string, group: FormGroup, pivot?: { __typename?: 'ActiveFormsPivot', id: string } | null, image?: { __typename?: 'Media', id: string, url: string } | null, sections: Array<{ __typename?: 'Section', id: string, steps: Array<{ __typename?: 'Step', id: string, question: string, answers: Array<{ __typename?: 'Answer', id: string }> }> }> }>, my_progress?: { __typename?: 'MyProgress', pack_progress: { __typename?: 'PackProgress', completed: boolean }, provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, type: AnswerType, step: { __typename?: 'Step', id: string, question: string, section: { __typename?: 'Section', id: string, form: { __typename?: 'Form', id: string, name?: string | null, group: FormGroup } } } } }> } | null, users: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null, email: string, invite_code_sent_at?: any | null, pivot?: { __typename?: 'PropertyUserPivot', role: PropertyUserRole } | null }> } };

export type UploadAdditionalDocumentsMutationVariables = Exact<{
  property_id: Scalars['ID'];
  input: UploadAdditionalDocumentsInput;
}>;


export type UploadAdditionalDocumentsMutation = { __typename?: 'Mutation', uploadAdditionalDocuments: { __typename?: 'Media', name?: string | null } };

export type ReuploadAdditionalDocumentsMutationVariables = Exact<{
  property_id: Scalars['ID'];
  input: ReuploadAdditionalDocumentsInput;
}>;


export type ReuploadAdditionalDocumentsMutation = { __typename?: 'Mutation', reuploadAdditionalDocuments: { __typename?: 'Media', name?: string | null } };

export type GetFormIdPropertyQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type GetFormIdPropertyQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, my_progress?: { __typename?: 'MyProgress', provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string } } }> } | null, active_forms: Array<{ __typename?: 'Form', id: string, pivot?: { __typename?: 'ActiveFormsPivot', id: string } | null, sections: Array<{ __typename?: 'Section', id: string, steps: Array<{ __typename?: 'Step', id: string, answers: Array<{ __typename?: 'Answer', id: string }> }> }> }> } };

export type CreateGiftorDeclarationSigningUrlMutationVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type CreateGiftorDeclarationSigningUrlMutation = { __typename?: 'Mutation', createGiftorDeclarationSigningUrl?: string | null };

export type CreateIdvQrCodeMutationVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type CreateIdvQrCodeMutation = { __typename?: 'Mutation', createIdvQrCode?: string | null };

export type IdvMobileConnectedQueryVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type IdvMobileConnectedQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, my_progress?: { __typename?: 'MyProgress', idv: { __typename?: 'IdvProgress', completed?: boolean | null, mobile_connected: boolean } } | null } };

export type ClientPropertyOverviewQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type ClientPropertyOverviewQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, type: PropertyType, users: Array<{ __typename?: 'User', id: string, pivot?: { __typename?: 'PropertyUserPivot', role: PropertyUserRole, onboarding_forms_completed_at?: any | null, payment_on_account_completed_at?: any | null, id_verification_completed_at?: any | null } | null }>, active_forms: Array<{ __typename?: 'Form', id: string, name?: string | null, description: string, group: FormGroup, pivot?: { __typename?: 'ActiveFormsPivot', id: string, title?: string | null } | null, image?: { __typename?: 'Media', id: string, url: string } | null, sections: Array<{ __typename?: 'Section', id: string, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }>, steps: Array<{ __typename?: 'Step', id: string, question: string, answers: Array<{ __typename?: 'Answer', id: string }>, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, type: ConditionType, answer: { __typename?: 'Answer', id: string } }> }> }> }>, address: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string }, my_progress?: { __typename?: 'MyProgress', payment: { __typename?: 'PaymentProgress', required: boolean, paid?: boolean | null }, provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, active_form_id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string, question: string, section: { __typename?: 'Section', id: string, form: { __typename?: 'Form', id: string, name?: string | null, group: FormGroup } } } } }>, onboarding_letters: { __typename?: 'OnboardingLettersProgress', required: boolean, completed?: boolean | null }, idv: { __typename?: 'IdvProgress', required: boolean, completed?: boolean | null }, sof: { __typename?: 'SofProgress', required: boolean, completed?: boolean | null }, giftor_deposit_declaration: { __typename?: 'GiftorDepositDeclarationProgress', required: boolean, completed?: boolean | null } } | null } };

export type CreateLettersSigningUrlMutationVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type CreateLettersSigningUrlMutation = { __typename?: 'Mutation', createLettersSigningUrl?: string | null };

export type PackPropertyPackQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type PackPropertyPackQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, active_forms: Array<{ __typename?: 'Form', id: string, name?: string | null, pivot?: { __typename?: 'ActiveFormsPivot', id: string, title?: string | null } | null }> } };

export type CreateFormSigningUrlMutationVariables = Exact<{
  property_id: Scalars['ID'];
  form_id: Scalars['ID'];
}>;


export type CreateFormSigningUrlMutation = { __typename?: 'Mutation', createFormSigningUrl?: string | null };

export type ClientPropertyPackQueryVariables = Exact<{
  id: Scalars['ID'];
}>;


export type ClientPropertyPackQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, address: { __typename?: 'Address', id: string, line_1: string }, users: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null, email: string, invite_code_sent_at?: any | null, pivot?: { __typename?: 'PropertyUserPivot', role: PropertyUserRole } | null }>, active_forms: Array<{ __typename?: 'Form', id: string, name?: string | null, group: FormGroup, signed?: boolean | null, pivot?: { __typename?: 'ActiveFormsPivot', id: string, title?: string | null } | null, sections: Array<{ __typename?: 'Section', id: string, steps: Array<{ __typename?: 'Step', id: string, answers: Array<{ __typename?: 'Answer', id: string, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, answer: { __typename?: 'Answer', id: string } }> }>, conditions: Array<{ __typename?: 'Condition', id: string, selected_value?: string | null, answer: { __typename?: 'Answer', id: string } }> }> }> }>, my_progress?: { __typename?: 'MyProgress', provided_answers: Array<{ __typename?: 'ProvidedAnswer', id: string, value?: any | null, answer: { __typename?: 'Answer', id: string, step: { __typename?: 'Step', id: string, section: { __typename?: 'Section', id: string, form: { __typename?: 'Form', id: string } } } } }> } | null } };

export type ClientPaymentConveyancerDetailsQueryVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type ClientPaymentConveyancerDetailsQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, my_progress?: { __typename?: 'MyProgress', payment: { __typename?: 'PaymentProgress', required: boolean, paid?: boolean | null } } | null, conveyancer: { __typename?: 'Conveyancer', id: string, name: string, payment_on_account_amount?: number | null } } };

export type CreatePaymentOnAccountPaymentIntentMutationVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type CreatePaymentOnAccountPaymentIntentMutation = { __typename?: 'Mutation', createPaymentOnAccountPaymentIntent?: string | null };

export type PaymentOnAccountStripeAccountIdQueryVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type PaymentOnAccountStripeAccountIdQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, conveyancer: { __typename?: 'Conveyancer', id: string, stripe_account_id?: string | null } } };

export type SofProgressQueryVariables = Exact<{
  property_id: Scalars['ID'];
}>;


export type SofProgressQuery = { __typename?: 'Query', property: { __typename?: 'Property', id: string, my_progress?: { __typename?: 'MyProgress', sof: { __typename?: 'SofProgress', required: boolean, completed?: boolean | null, files?: Array<{ __typename?: 'Media', id: string, name?: string | null }> | null } } | null } };

export type UploadSofCheckDocumentsMutationVariables = Exact<{
  property_id: Scalars['ID'];
  input: UploadSofCheckDocumentsInput;
}>;


export type UploadSofCheckDocumentsMutation = { __typename?: 'Mutation', uploadSofCheckDocuments: Array<{ __typename?: 'Media', id: string }> };

export type GetClientPropertiesQueryVariables = Exact<{
  first: Scalars['Int'];
  page: Scalars['Int'];
}>;


export type GetClientPropertiesQuery = { __typename?: 'Query', getClientProperties: { __typename?: 'PropertyPaginator', data: Array<{ __typename?: 'Property', id: string, case_reference: string, type: PropertyType, address: { __typename?: 'Address', id: string, line_1: string, line_2?: string | null, city: string, postcode: string }, conveyancer: { __typename?: 'Conveyancer', id: string, name: string } }>, paginatorInfo: { __typename?: 'PaginatorInfo', total: number } } };

export type InviteNewClientMutationVariables = Exact<{
  input: InviteNewClientInput;
}>;


export type InviteNewClientMutation = { __typename?: 'Mutation', inviteNewClient: { __typename?: 'Property', id: string } };

export type TeamMembersNameQueryQueryVariables = Exact<{ [key: string]: never; }>;


export type TeamMembersNameQueryQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, team_members?: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null }> | null } | null } | null };

export type GetAddressQueryVariables = Exact<{
  input: SearchAddress;
}>;


export type GetAddressQuery = { __typename?: 'Query', getAddressFromOS2API: { __typename?: 'InviteAddress', line_1: string, line_2?: string | null, city: string, postcode: string, uprn: string } };

export type RegisterClientMutationVariables = Exact<{
  input: RegisterClientInput;
}>;


export type RegisterClientMutation = { __typename?: 'Mutation', registerClient: { __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null, role: UserRole, email: string } };

export type RegisterTeamMemberMutationVariables = Exact<{
  input: RegisterTeamMemberInput;
}>;


export type RegisterTeamMemberMutation = { __typename?: 'Mutation', registerTeamMember: { __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null, role: UserRole, email: string } };

export type RegisterMutationVariables = Exact<{
  input: RegisterInput;
}>;


export type RegisterMutation = { __typename?: 'Mutation', register: { __typename?: 'User', id: string, email: string } };

export type CreateConveyancerMutationVariables = Exact<{
  input: CreateConveyancerInput;
}>;


export type CreateConveyancerMutation = { __typename?: 'Mutation', createConveyancer: { __typename?: 'Conveyancer', id: string, name: string, type: string, company_number?: string | null, sra_clc_number: string, trading_name?: string | null, vat_number?: string | null, website?: string | null, location?: string | null, telephone_number?: string | null, email_address?: string | null, address?: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string } | null } };

export type UpdateIdProviderMutationVariables = Exact<{
  input: UpdateIdProviderInput;
}>;


export type UpdateIdProviderMutation = { __typename?: 'Mutation', updateIDProvider: boolean };

export type OnboardingLettersClientCareLetterPurchaseQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersClientCareLetterPurchaseQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, client_care_letter_purchase?: string | null } | null } | null };

export type OnboardingLettersClientCareLetterSaleQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersClientCareLetterSaleQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, client_care_letter_sale?: string | null } | null } | null };

export type OnboardingLettersLetterHeaderAndFooterQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersLetterHeaderAndFooterQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, letter_header?: string | null, letter_footer?: string | null } | null } | null };

export type OnboardingLettersTermsAndConditionsQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersTermsAndConditionsQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, terms_and_conditions?: string | null } | null } | null };

export type UpdateUserDetailsMutationVariables = Exact<{
  input: UpdateUserDetailsInput;
}>;


export type UpdateUserDetailsMutation = { __typename?: 'Mutation', updateUserDetails: { __typename?: 'User', job_role?: string | null, job_bio?: string | null, first_name?: string | null, last_name?: string | null, title?: string | null, suffix?: string | null, phone?: string | null, sra_clc_number?: string | null } };

export type TeamMembersQueryQueryVariables = Exact<{ [key: string]: never; }>;


export type TeamMembersQueryQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, team_members?: Array<{ __typename?: 'User', id: string, job_role?: string | null, email: string, invite_code_sent_at?: any | null, email_verified_at?: any | null }> | null } | null } | null };

export type InviteTeamMembersMutationVariables = Exact<{
  input: InviteTeamMembersInput;
}>;


export type InviteTeamMembersMutation = { __typename?: 'Mutation', inviteTeamMember: boolean };

export type UpdateBillingEmailMutationVariables = Exact<{
  input: UpdateBillingEmailInput;
}>;


export type UpdateBillingEmailMutation = { __typename?: 'Mutation', updateBillingEmail: boolean };

export type SettingsBillingConveyancerQueryVariables = Exact<{
  invoicesStartingAfter?: InputMaybe<Scalars['String']>;
}>;


export type SettingsBillingConveyancerQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, subscription?: { __typename?: 'ConveyancerSubscription', plan_name?: string | null, plan_price?: number | null, billing_email?: string | null, payment_method?: { __typename?: 'ConveyancerSubscriptionPaymentMethod', type: string, brand?: string | null, exp_month?: number | null, exp_year?: number | null, last4?: string | null, sort_code?: string | null } | null } | null, invoices?: Array<{ __typename?: 'Invoice', plan_name: string, number?: string | null, amount: number, date: any, status: string, pdf_url?: string | null }> | null } | null } | null };

export type SettingsBillingDownloadAllQueryVariables = Exact<{ [key: string]: never; }>;


export type SettingsBillingDownloadAllQuery = { __typename?: 'Query', me?: { __typename?: 'User', conveyancer?: { __typename?: 'Conveyancer', all_invoices_link: string } | null } | null };

export type SettingsBusinessConveyancerQueryVariables = Exact<{ [key: string]: never; }>;


export type SettingsBusinessConveyancerQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, name: string, type: string, sra_clc_number: string, team_member_count: number, company_number?: string | null, trading_name?: string | null, vat_number?: string | null, website?: string | null, location?: string | null, telephone_number?: string | null, email_address?: string | null, team_members?: Array<{ __typename?: 'User', id: string, first_name?: string | null, last_name?: string | null, email: string, phone?: string | null, invite_code_sent_at?: any | null, job_role?: string | null, email_verified_at?: any | null }> | null, address?: { __typename?: 'Address', id: string, line_1: string, line_2?: string | null, city: string, postcode: string } | null, logo_image?: { __typename?: 'Media', id: string, url: string } | null } | null } | null };

export type UpdateConveyancerDetailsMutationVariables = Exact<{
  input: UpdateConveyancerDetailsInput;
}>;


export type UpdateConveyancerDetailsMutation = { __typename?: 'Mutation', updateConveyancerDetails?: { __typename?: 'Conveyancer', name: string, company_number?: string | null, sra_clc_number: string, trading_name?: string | null, vat_number?: string | null, website?: string | null, location?: string | null, telephone_number?: string | null, email_address?: string | null, logo_image?: { __typename?: 'Media', id: string, url: string } | null, address?: { __typename?: 'Address', line_1: string, line_2?: string | null, city: string, postcode: string } | null } | null };

export type NotificationPreferencesQueryVariables = Exact<{ [key: string]: never; }>;


export type NotificationPreferencesQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, notification_preferences?: { __typename?: 'NotificationPreferences', getting_started_forms_completed: boolean, onboarding_completed: boolean, client_new_document_uploads: boolean } | null } | null };

export type UpdateUserNotificationPreferencesMutationVariables = Exact<{
  input: UpdateNotificationPreferencesInput;
}>;


export type UpdateUserNotificationPreferencesMutation = { __typename?: 'Mutation', updateUserNotificationPreferences: { __typename?: 'User', id: string } };

export type OnboardingLettersClientCareLetterQueryVariables = Exact<{ [key: string]: never; }>;


export type OnboardingLettersClientCareLetterQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, client_care_letter?: string | null } | null } | null };

export type SettingsOverviewConveyancerQueryVariables = Exact<{ [key: string]: never; }>;


export type SettingsOverviewConveyancerQuery = { __typename?: 'Query', me?: { __typename?: 'User', id: string, conveyancer?: { __typename?: 'Conveyancer', id: string, name: string, sra_clc_number: string, team_member_count: number, stripe_account_id?: string | null, client_care_letter?: string | null, terms_and_conditions?: string | null, subscription?: { __typename?: 'ConveyancerSubscription', plan_name?: string | null, payment_method?: { __typename?: 'ConveyancerSubscriptionPaymentMethod', type: string, brand?: string | null, last4?: string | null } | null } | null } | null, notification_preferences?: { __typename?: 'NotificationPreferences', getting_started_forms_completed: boolean, onboarding_completed: boolean, client_new_document_uploads: boolean } | null } | null };

export type UpdateUserProfileMutationVariables = Exact<{
  input: UpdateUserProfileInput;
}>;


export type UpdateUserProfileMutation = { __typename?: 'Mutation', updateUserProfile: { __typename?: 'User', first_name?: string | null, last_name?: string | null, title?: string | null, suffix?: string | null, job_bio?: string | null, role: UserRole, phone?: string | null, email: string, sra_clc_number?: string | null, profile_image?: { __typename?: 'Media', url: string } | null } };

export type DeleteUserMutationVariables = Exact<{ [key: string]: never; }>;


export type DeleteUserMutation = { __typename?: 'Mutation', deleteUser: boolean };

export type ResendInviteMutationVariables = Exact<{
  email: Scalars['String'];
}>;


export type ResendInviteMutation = { __typename?: 'Mutation', resendInvite?: boolean | null };


export const CreateSetupIntentDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createSetupIntent"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"CreateSetupIntentInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createSetupIntent"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<CreateSetupIntentMutation, CreateSetupIntentMutationVariables>;
export const CompleteSetupIntentDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"completeSetupIntent"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"CompleteSetupIntentInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"completeSetupIntent"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<CompleteSetupIntentMutation, CompleteSetupIntentMutationVariables>;
export const GetFormQuestionDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getFormQuestion"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"active_form_id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"section"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"ta_form_template"}},{"kind":"Field","name":{"kind":"Name","value":"group"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"sub_heading"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"help_text"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"help_video_link"}},{"kind":"Field","name":{"kind":"Name","value":"repeatable_answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"details"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsSingleSelect"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"options"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"value"}}]}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsText"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"placeholder"}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsTextarea"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"placeholder"}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsAddress"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsDropdown"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"options"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"value"}}]}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsOwnerDropdown"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"options"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"value"}}]}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsCheckbox"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsDataTable"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"allowsAddMore"}},{"kind":"Field","name":{"kind":"Name","value":"addMoreLabel"}},{"kind":"Field","name":{"kind":"Name","value":"rows"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"name"}}]}},{"kind":"Field","name":{"kind":"Name","value":"columns"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"placeholder"}}]}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsPersonMultiSelect"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"options"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"value"}}]}}]}}]}}]}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<GetFormQuestionQuery, GetFormQuestionQueryVariables>;
export const SaveProvidedAnswersDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"saveProvidedAnswers"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"SaveProvidedAnswersInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"saveProvidedAnswers"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<SaveProvidedAnswersMutation, SaveProvidedAnswersMutationVariables>;
export const StepDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"step"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"propertyId"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"activeFormId"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"step"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"sub_heading"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"help_text"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"help_video_link"}},{"kind":"Field","name":{"kind":"Name","value":"repeatable_answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}},{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"propertyId"}}},{"kind":"Argument","name":{"kind":"Name","value":"active_form_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"activeFormId"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}}]}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"propertyId"}}},{"kind":"Argument","name":{"kind":"Name","value":"active_form_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"activeFormId"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_form_id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}}]}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"details"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsText"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"placeholder"}}]}},{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"AnswerDetailsDropdown"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"label"}},{"kind":"Field","name":{"kind":"Name","value":"options"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"value"}}]}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<StepQuery, StepQueryVariables>;
export const InviteGiftorDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"inviteGiftor"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"InviteGiftorInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"inviteGiftor"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<InviteGiftorMutation, InviteGiftorMutationVariables>;
export const InvitePartyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"inviteParty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"InvitePartyInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"inviteParty"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<InvitePartyMutation, InvitePartyMutationVariables>;
export const RemoveGiftorDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"removeGiftor"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"RemoveGiftorInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"removeGiftor"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<RemoveGiftorMutation, RemoveGiftorMutationVariables>;
export const RemovePartyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"removeParty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"RemovePartyInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"removeParty"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<RemovePartyMutation, RemovePartyMutationVariables>;
export const DeleteMortgageDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"deleteMortgage"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"step_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"charge_index"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"deleteMortgage"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"step_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"step_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"charge_index"},"value":{"kind":"Variable","name":{"kind":"Name","value":"charge_index"}}}]}]}}]} as unknown as DocumentNode<DeleteMortgageMutation, DeleteMortgageMutationVariables>;
export const SendInviteDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"sendInvite"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"SendInviteInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"sendInvite"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}}]}}]}}]} as unknown as DocumentNode<SendInviteMutation, SendInviteMutationVariables>;
export const RegisterBusinessConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"registerBusinessConveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"company_number"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"logo_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"trading_name"}},{"kind":"Field","name":{"kind":"Name","value":"vat_number"}},{"kind":"Field","name":{"kind":"Name","value":"website"}},{"kind":"Field","name":{"kind":"Name","value":"location"}},{"kind":"Field","name":{"kind":"Name","value":"telephone_number"}},{"kind":"Field","name":{"kind":"Name","value":"email_address"}}]}}]}}]}}]} as unknown as DocumentNode<RegisterBusinessConveyancerQuery, RegisterBusinessConveyancerQueryVariables>;
export const OnboardingLettersDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLetters"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter_sale"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter_purchase"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter_remortgage"}},{"kind":"Field","name":{"kind":"Name","value":"terms_and_conditions"}},{"kind":"Field","name":{"kind":"Name","value":"letter_header"}},{"kind":"Field","name":{"kind":"Name","value":"letter_footer"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersQuery, OnboardingLettersQueryVariables>;
export const UpdateConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateConveyancer"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateConveyancerInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateConveyancer"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]} as unknown as DocumentNode<UpdateConveyancerMutation, UpdateConveyancerMutationVariables>;
export const RegistrationPaymentsConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"registrationPaymentsConveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"stripe_account_id"}}]}}]}}]}}]} as unknown as DocumentNode<RegistrationPaymentsConveyancerQuery, RegistrationPaymentsConveyancerQueryVariables>;
export const UpdateStripeCodeDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateStripeCode"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateStripeCodeInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateStripeCode"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<UpdateStripeCodeMutation, UpdateStripeCodeMutationVariables>;
export const DisconnectStripeDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"disconnectStripe"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"disconnectStripe"}}]}}]} as unknown as DocumentNode<DisconnectStripeMutation, DisconnectStripeMutationVariables>;
export const InviteTeamMemberDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"inviteTeamMember"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"InviteTeamMembersInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"inviteTeamMember"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<InviteTeamMemberMutation, InviteTeamMemberMutationVariables>;
export const DeleteOtherUserDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"deleteOtherUser"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"deleteOtherUser"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}]}]}}]} as unknown as DocumentNode<DeleteOtherUserMutation, DeleteOtherUserMutationVariables>;
export const LogoutDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"logout"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"logout"}}]}}]} as unknown as DocumentNode<LogoutMutation, LogoutMutationVariables>;
export const MediaDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"media"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"media"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"custom_properties"}}]}}]}}]} as unknown as DocumentNode<MediaQuery, MediaQueryVariables>;
export const MarkAllNotificationsReadDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"markAllNotificationsRead"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"markAllNotificationsRead"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"unread_notifications"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_id"}},{"kind":"Field","name":{"kind":"Name","value":"data"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"message"}}]}},{"kind":"Field","name":{"kind":"Name","value":"read_at"}},{"kind":"Field","name":{"kind":"Name","value":"created_at"}}]}}]}}]}}]} as unknown as DocumentNode<MarkAllNotificationsReadMutation, MarkAllNotificationsReadMutationVariables>;
export const DoGlobalQueryDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"doGlobalQuery"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"filters"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"GlobalSearchInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"globalSearch"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"filters"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"display_text"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}}]}}]}}]}}]} as unknown as DocumentNode<DoGlobalQueryQuery, DoGlobalQueryQueryVariables>;
export const MeDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"team_member_count"}},{"kind":"Field","name":{"kind":"Name","value":"type"}}]}},{"kind":"Field","name":{"kind":"Name","value":"unread_notifications"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_id"}},{"kind":"Field","name":{"kind":"Name","value":"data"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"message"}}]}},{"kind":"Field","name":{"kind":"Name","value":"read_at"}},{"kind":"Field","name":{"kind":"Name","value":"created_at"}}]}},{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"profile_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}}]}}]}}]} as unknown as DocumentNode<MeQuery, MeQueryVariables>;
export const UpdateClientProfileDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateClientProfile"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateUserProfileInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateUserProfile"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"profile_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"email"}}]}}]}}]} as unknown as DocumentNode<UpdateClientProfileMutation, UpdateClientProfileMutationVariables>;
export const ForgottenPasswordDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"forgottenPassword"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"email"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"String"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"forgottenPassword"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"email"},"value":{"kind":"Variable","name":{"kind":"Name","value":"email"}}}]}]}}]} as unknown as DocumentNode<ForgottenPasswordMutation, ForgottenPasswordMutationVariables>;
export const LoginClientDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"loginClient"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"LoginInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"login"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"role"}}]}}]}}]} as unknown as DocumentNode<LoginClientMutation, LoginClientMutationVariables>;
export const UpdateClientDetailsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateClientDetails"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateClientDetailsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateClientDetails"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}}]}}]}}]} as unknown as DocumentNode<UpdateClientDetailsMutation, UpdateClientDetailsMutationVariables>;
export const ResetPasswordDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"resetPassword"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ResetPasswordInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"resetPassword"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<ResetPasswordMutation, ResetPasswordMutationVariables>;
export const AddNewPartyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"addNewParty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"AddNewPartyInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"addNewParty"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<AddNewPartyMutation, AddNewPartyMutationVariables>;
export const GetPropertyUsersDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getPropertyUsers"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"letters_required"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyUserPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"role"}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<GetPropertyUsersQuery, GetPropertyUsersQueryVariables>;
export const GetPropertyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getProperty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"sale_price"}},{"kind":"Field","name":{"kind":"Name","value":"letters_required"}},{"kind":"Field","name":{"kind":"Name","value":"id_check_required"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancing_fee"}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"ta_form_template"}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"compiled_answer"}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyUserPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"sof_completed_at"}},{"kind":"Field","name":{"kind":"Name","value":"representation"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"middle_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"occupation"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}},{"kind":"Field","name":{"kind":"Name","value":"email_verified_at"}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"type"}}]}}]}}]} as unknown as DocumentNode<GetPropertyQuery, GetPropertyQueryVariables>;
export const UpdateExistingPartyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateExistingParty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateExistingPartyInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateExistingParty"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<UpdateExistingPartyMutation, UpdateExistingPartyMutationVariables>;
export const GetPackDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getPack"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"archived_at"}},{"kind":"Field","name":{"kind":"Name","value":"case_reference"}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"documents"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"custom_properties"}}]}}]}}]}}]} as unknown as DocumentNode<GetPackQuery, GetPackQueryVariables>;
export const DownloadPackDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"downloadPack"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"all_documents_link"}}]}}]}}]} as unknown as DocumentNode<DownloadPackQuery, DownloadPackQueryVariables>;
export const ArchivePropertyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"archiveProperty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"archiveProperty"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"archived_at"}}]}}]}}]} as unknown as DocumentNode<ArchivePropertyMutation, ArchivePropertyMutationVariables>;
export const PropertiesDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"properties"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"first"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"Int"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"page"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"Int"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"filters"}},"type":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyFilterInputs"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"properties"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"first"},"value":{"kind":"Variable","name":{"kind":"Name","value":"first"}}},{"kind":"Argument","name":{"kind":"Name","value":"page"},"value":{"kind":"Variable","name":{"kind":"Name","value":"page"}}},{"kind":"Argument","name":{"kind":"Name","value":"filters"},"value":{"kind":"Variable","name":{"kind":"Name","value":"filters"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"data"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"archived_at"}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"paginatorInfo"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"total"}},{"kind":"Field","name":{"kind":"Name","value":"lastPage"}}]}}]}}]}}]} as unknown as DocumentNode<PropertiesQuery, PropertiesQueryVariables>;
export const ClientLoginDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"clientLogin"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"LoginInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"login"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"business_created_at"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"team_member_count"}},{"kind":"Field","name":{"kind":"Name","value":"type"}}]}},{"kind":"Field","name":{"kind":"Name","value":"unread_notifications"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_type"}},{"kind":"Field","name":{"kind":"Name","value":"notifiable_id"}},{"kind":"Field","name":{"kind":"Name","value":"data"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"message"}}]}},{"kind":"Field","name":{"kind":"Name","value":"read_at"}},{"kind":"Field","name":{"kind":"Name","value":"created_at"}}]}}]}}]}}]} as unknown as DocumentNode<ClientLoginMutation, ClientLoginMutationVariables>;
export const UpdateInvitedTeamMemberDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateInvitedTeamMember"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateInvitedTeamMemberInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateInvitedTeamMember"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"job_bio"}}]}}]}}]} as unknown as DocumentNode<UpdateInvitedTeamMemberMutation, UpdateInvitedTeamMemberMutationVariables>;
export const ClientPropertyDocumentsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"clientPropertyDocuments"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"documents"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"custom_properties"}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"description"}},{"kind":"Field","name":{"kind":"Name","value":"group"}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"pack_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"completed"}}]}},{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"section"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"form"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"group"}}]}}]}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyUserPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"role"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}}]}}]}}]}}]} as unknown as DocumentNode<ClientPropertyDocumentsQuery, ClientPropertyDocumentsQueryVariables>;
export const UploadAdditionalDocumentsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"uploadAdditionalDocuments"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UploadAdditionalDocumentsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"uploadAdditionalDocuments"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"name"}}]}}]}}]} as unknown as DocumentNode<UploadAdditionalDocumentsMutation, UploadAdditionalDocumentsMutationVariables>;
export const ReuploadAdditionalDocumentsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"reuploadAdditionalDocuments"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ReuploadAdditionalDocumentsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"reuploadAdditionalDocuments"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"name"}}]}}]}}]} as unknown as DocumentNode<ReuploadAdditionalDocumentsMutation, ReuploadAdditionalDocumentsMutationVariables>;
export const GetFormIdPropertyDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getFormIDProperty"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<GetFormIdPropertyQuery, GetFormIdPropertyQueryVariables>;
export const CreateGiftorDeclarationSigningUrlDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createGiftorDeclarationSigningUrl"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createGiftorDeclarationSigningUrl"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}]}]}}]} as unknown as DocumentNode<CreateGiftorDeclarationSigningUrlMutation, CreateGiftorDeclarationSigningUrlMutationVariables>;
export const CreateIdvQrCodeDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createIdvQrCode"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createIdvQrCode"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}]}]}}]} as unknown as DocumentNode<CreateIdvQrCodeMutation, CreateIdvQrCodeMutationVariables>;
export const IdvMobileConnectedDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"idvMobileConnected"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"idv"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"completed"}},{"kind":"Field","name":{"kind":"Name","value":"mobile_connected"}}]}}]}}]}}]}}]} as unknown as DocumentNode<IdvMobileConnectedQuery, IdvMobileConnectedQueryVariables>;
export const ClientPropertyOverviewDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"clientPropertyOverview"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyUserPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"onboarding_forms_completed_at"}},{"kind":"Field","name":{"kind":"Name","value":"payment_on_account_completed_at"}},{"kind":"Field","name":{"kind":"Name","value":"id_verification_completed_at"}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"description"}},{"kind":"Field","name":{"kind":"Name","value":"group"}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"payment"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"paid"}}]}},{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"active_form_id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"question"}},{"kind":"Field","name":{"kind":"Name","value":"section"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"form"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"group"}}]}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"onboarding_letters"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"completed"}}]}},{"kind":"Field","name":{"kind":"Name","value":"idv"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"completed"}}]}},{"kind":"Field","name":{"kind":"Name","value":"sof"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"completed"}}]}},{"kind":"Field","name":{"kind":"Name","value":"giftor_deposit_declaration"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"completed"}}]}}]}}]}}]}}]} as unknown as DocumentNode<ClientPropertyOverviewQuery, ClientPropertyOverviewQueryVariables>;
export const CreateLettersSigningUrlDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createLettersSigningUrl"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createLettersSigningUrl"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}]}]}}]} as unknown as DocumentNode<CreateLettersSigningUrlMutation, CreateLettersSigningUrlMutationVariables>;
export const PackPropertyPackDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"packPropertyPack"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<PackPropertyPackQuery, PackPropertyPackQueryVariables>;
export const CreateFormSigningUrlDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createFormSigningUrl"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"form_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createFormSigningUrl"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"form_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"form_id"}}}]}]}}]} as unknown as DocumentNode<CreateFormSigningUrlMutation, CreateFormSigningUrlMutationVariables>;
export const ClientPropertyPackDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"clientPropertyPack"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}}]}},{"kind":"Field","name":{"kind":"Name","value":"users"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"PropertyUserPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"role"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}}]}},{"kind":"Field","name":{"kind":"Name","value":"active_forms"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"pivot"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"InlineFragment","typeCondition":{"kind":"NamedType","name":{"kind":"Name","value":"ActiveFormsPivot"}},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"title"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"group"}},{"kind":"Field","name":{"kind":"Name","value":"signed"}},{"kind":"Field","name":{"kind":"Name","value":"sections"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"steps"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"conditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"selected_value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"provided_answers"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"value"}},{"kind":"Field","name":{"kind":"Name","value":"answer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"step"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"section"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"form"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<ClientPropertyPackQuery, ClientPropertyPackQueryVariables>;
export const ClientPaymentConveyancerDetailsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"clientPaymentConveyancerDetails"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"payment"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"paid"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"payment_on_account_amount"}}]}}]}}]}}]} as unknown as DocumentNode<ClientPaymentConveyancerDetailsQuery, ClientPaymentConveyancerDetailsQueryVariables>;
export const CreatePaymentOnAccountPaymentIntentDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createPaymentOnAccountPaymentIntent"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createPaymentOnAccountPaymentIntent"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}]}]}}]} as unknown as DocumentNode<CreatePaymentOnAccountPaymentIntentMutation, CreatePaymentOnAccountPaymentIntentMutationVariables>;
export const PaymentOnAccountStripeAccountIdDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"paymentOnAccountStripeAccountId"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"stripe_account_id"}}]}}]}}]}}]} as unknown as DocumentNode<PaymentOnAccountStripeAccountIdQuery, PaymentOnAccountStripeAccountIdQueryVariables>;
export const SofProgressDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"sofProgress"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"property"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"my_progress"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"sof"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"required"}},{"kind":"Field","name":{"kind":"Name","value":"completed"}},{"kind":"Field","name":{"kind":"Name","value":"files"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}}]}}]}}]}}]}}]}}]} as unknown as DocumentNode<SofProgressQuery, SofProgressQueryVariables>;
export const UploadSofCheckDocumentsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"uploadSofCheckDocuments"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"ID"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UploadSofCheckDocumentsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"uploadSofCheckDocuments"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"property_id"},"value":{"kind":"Variable","name":{"kind":"Name","value":"property_id"}}},{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]} as unknown as DocumentNode<UploadSofCheckDocumentsMutation, UploadSofCheckDocumentsMutationVariables>;
export const GetClientPropertiesDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"getClientProperties"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"first"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"Int"}}}},{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"page"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"Int"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"getClientProperties"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"first"},"value":{"kind":"Variable","name":{"kind":"Name","value":"first"}}},{"kind":"Argument","name":{"kind":"Name","value":"page"},"value":{"kind":"Variable","name":{"kind":"Name","value":"page"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"data"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"case_reference"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"paginatorInfo"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"total"}}]}}]}}]}}]} as unknown as DocumentNode<GetClientPropertiesQuery, GetClientPropertiesQueryVariables>;
export const InviteNewClientDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"inviteNewClient"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"InviteNewClientInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"inviteNewClient"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]} as unknown as DocumentNode<InviteNewClientMutation, InviteNewClientMutationVariables>;
export const TeamMembersNameQueryDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"teamMembersNameQuery"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"team_members"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}}]}}]}}]}}]}}]} as unknown as DocumentNode<TeamMembersNameQueryQuery, TeamMembersNameQueryQueryVariables>;
export const GetAddressDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"GetAddress"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"SearchAddress"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"getAddressFromOS2API"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}},{"kind":"Field","name":{"kind":"Name","value":"uprn"}}]}}]}}]} as unknown as DocumentNode<GetAddressQuery, GetAddressQueryVariables>;
export const RegisterClientDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"registerClient"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"RegisterClientInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"registerClient"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"email"}}]}}]}}]} as unknown as DocumentNode<RegisterClientMutation, RegisterClientMutationVariables>;
export const RegisterTeamMemberDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"registerTeamMember"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"RegisterTeamMemberInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"registerTeamMember"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"email"}}]}}]}}]} as unknown as DocumentNode<RegisterTeamMemberMutation, RegisterTeamMemberMutationVariables>;
export const RegisterDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"register"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"RegisterInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"register"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"email"}}]}}]}}]} as unknown as DocumentNode<RegisterMutation, RegisterMutationVariables>;
export const CreateConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"createConveyancer"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"CreateConveyancerInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"createConveyancer"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"company_number"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"trading_name"}},{"kind":"Field","name":{"kind":"Name","value":"vat_number"}},{"kind":"Field","name":{"kind":"Name","value":"website"}},{"kind":"Field","name":{"kind":"Name","value":"location"}},{"kind":"Field","name":{"kind":"Name","value":"telephone_number"}},{"kind":"Field","name":{"kind":"Name","value":"email_address"}}]}}]}}]} as unknown as DocumentNode<CreateConveyancerMutation, CreateConveyancerMutationVariables>;
export const UpdateIdProviderDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateIDProvider"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateIDProviderInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateIDProvider"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<UpdateIdProviderMutation, UpdateIdProviderMutationVariables>;
export const OnboardingLettersClientCareLetterPurchaseDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLettersClientCareLetterPurchase"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter_purchase"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersClientCareLetterPurchaseQuery, OnboardingLettersClientCareLetterPurchaseQueryVariables>;
export const OnboardingLettersClientCareLetterSaleDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLettersClientCareLetterSale"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter_sale"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersClientCareLetterSaleQuery, OnboardingLettersClientCareLetterSaleQueryVariables>;
export const OnboardingLettersLetterHeaderAndFooterDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLettersLetterHeaderAndFooter"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"letter_header"}},{"kind":"Field","name":{"kind":"Name","value":"letter_footer"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersLetterHeaderAndFooterQuery, OnboardingLettersLetterHeaderAndFooterQueryVariables>;
export const OnboardingLettersTermsAndConditionsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLettersTermsAndConditions"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"terms_and_conditions"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersTermsAndConditionsQuery, OnboardingLettersTermsAndConditionsQueryVariables>;
export const UpdateUserDetailsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateUserDetails"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateUserDetailsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateUserDetails"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}}]}}]}}]} as unknown as DocumentNode<UpdateUserDetailsMutation, UpdateUserDetailsMutationVariables>;
export const TeamMembersQueryDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"teamMembersQuery"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"team_members"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}},{"kind":"Field","name":{"kind":"Name","value":"email_verified_at"}}]}}]}}]}}]}}]} as unknown as DocumentNode<TeamMembersQueryQuery, TeamMembersQueryQueryVariables>;
export const InviteTeamMembersDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"inviteTeamMembers"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"InviteTeamMembersInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"inviteTeamMember"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<InviteTeamMembersMutation, InviteTeamMembersMutationVariables>;
export const UpdateBillingEmailDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateBillingEmail"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateBillingEmailInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateBillingEmail"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}]}]}}]} as unknown as DocumentNode<UpdateBillingEmailMutation, UpdateBillingEmailMutationVariables>;
export const SettingsBillingConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"settingsBillingConveyancer"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"invoicesStartingAfter"}},"type":{"kind":"NamedType","name":{"kind":"Name","value":"String"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"subscription"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"plan_name"}},{"kind":"Field","name":{"kind":"Name","value":"plan_price"}},{"kind":"Field","name":{"kind":"Name","value":"billing_email"}},{"kind":"Field","name":{"kind":"Name","value":"payment_method"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"brand"}},{"kind":"Field","name":{"kind":"Name","value":"exp_month"}},{"kind":"Field","name":{"kind":"Name","value":"exp_year"}},{"kind":"Field","name":{"kind":"Name","value":"last4"}},{"kind":"Field","name":{"kind":"Name","value":"sort_code"}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"invoices"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"limit"},"value":{"kind":"IntValue","value":"6"}},{"kind":"Argument","name":{"kind":"Name","value":"starting_after"},"value":{"kind":"Variable","name":{"kind":"Name","value":"invoicesStartingAfter"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"plan_name"}},{"kind":"Field","name":{"kind":"Name","value":"number"}},{"kind":"Field","name":{"kind":"Name","value":"amount"}},{"kind":"Field","name":{"kind":"Name","value":"date"}},{"kind":"Field","name":{"kind":"Name","value":"status"}},{"kind":"Field","name":{"kind":"Name","value":"pdf_url"}}]}}]}}]}}]}}]} as unknown as DocumentNode<SettingsBillingConveyancerQuery, SettingsBillingConveyancerQueryVariables>;
export const SettingsBillingDownloadAllDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"settingsBillingDownloadAll"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"all_invoices_link"}}]}}]}}]}}]} as unknown as DocumentNode<SettingsBillingDownloadAllQuery, SettingsBillingDownloadAllQueryVariables>;
export const SettingsBusinessConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"settingsBusinessConveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"team_members"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"invite_code_sent_at"}},{"kind":"Field","name":{"kind":"Name","value":"job_role"}},{"kind":"Field","name":{"kind":"Name","value":"email_verified_at"}}]}},{"kind":"Field","name":{"kind":"Name","value":"team_member_count"}},{"kind":"Field","name":{"kind":"Name","value":"company_number"}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"logo_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"trading_name"}},{"kind":"Field","name":{"kind":"Name","value":"vat_number"}},{"kind":"Field","name":{"kind":"Name","value":"website"}},{"kind":"Field","name":{"kind":"Name","value":"location"}},{"kind":"Field","name":{"kind":"Name","value":"telephone_number"}},{"kind":"Field","name":{"kind":"Name","value":"email_address"}}]}}]}}]}}]} as unknown as DocumentNode<SettingsBusinessConveyancerQuery, SettingsBusinessConveyancerQueryVariables>;
export const UpdateConveyancerDetailsDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateConveyancerDetails"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateConveyancerDetailsInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateConveyancerDetails"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"company_number"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"logo_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"address"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"line_1"}},{"kind":"Field","name":{"kind":"Name","value":"line_2"}},{"kind":"Field","name":{"kind":"Name","value":"city"}},{"kind":"Field","name":{"kind":"Name","value":"postcode"}}]}},{"kind":"Field","name":{"kind":"Name","value":"trading_name"}},{"kind":"Field","name":{"kind":"Name","value":"vat_number"}},{"kind":"Field","name":{"kind":"Name","value":"website"}},{"kind":"Field","name":{"kind":"Name","value":"location"}},{"kind":"Field","name":{"kind":"Name","value":"telephone_number"}},{"kind":"Field","name":{"kind":"Name","value":"email_address"}}]}}]}}]} as unknown as DocumentNode<UpdateConveyancerDetailsMutation, UpdateConveyancerDetailsMutationVariables>;
export const NotificationPreferencesDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"notificationPreferences"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"notification_preferences"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"getting_started_forms_completed"}},{"kind":"Field","name":{"kind":"Name","value":"onboarding_completed"}},{"kind":"Field","name":{"kind":"Name","value":"client_new_document_uploads"}}]}}]}}]}}]} as unknown as DocumentNode<NotificationPreferencesQuery, NotificationPreferencesQueryVariables>;
export const UpdateUserNotificationPreferencesDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateUserNotificationPreferences"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateNotificationPreferencesInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateUserNotificationPreferences"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}}]}}]}}]} as unknown as DocumentNode<UpdateUserNotificationPreferencesMutation, UpdateUserNotificationPreferencesMutationVariables>;
export const OnboardingLettersClientCareLetterDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"onboardingLettersClientCareLetter"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter"}}]}}]}}]}}]} as unknown as DocumentNode<OnboardingLettersClientCareLetterQuery, OnboardingLettersClientCareLetterQueryVariables>;
export const SettingsOverviewConveyancerDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"query","name":{"kind":"Name","value":"settingsOverviewConveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"me"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"conveyancer"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"id"}},{"kind":"Field","name":{"kind":"Name","value":"name"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}},{"kind":"Field","name":{"kind":"Name","value":"team_member_count"}},{"kind":"Field","name":{"kind":"Name","value":"stripe_account_id"}},{"kind":"Field","name":{"kind":"Name","value":"client_care_letter"}},{"kind":"Field","name":{"kind":"Name","value":"terms_and_conditions"}},{"kind":"Field","name":{"kind":"Name","value":"subscription"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"plan_name"}},{"kind":"Field","name":{"kind":"Name","value":"payment_method"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"type"}},{"kind":"Field","name":{"kind":"Name","value":"brand"}},{"kind":"Field","name":{"kind":"Name","value":"last4"}}]}}]}}]}},{"kind":"Field","name":{"kind":"Name","value":"notification_preferences"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"getting_started_forms_completed"}},{"kind":"Field","name":{"kind":"Name","value":"onboarding_completed"}},{"kind":"Field","name":{"kind":"Name","value":"client_new_document_uploads"}}]}}]}}]}}]} as unknown as DocumentNode<SettingsOverviewConveyancerQuery, SettingsOverviewConveyancerQueryVariables>;
export const UpdateUserProfileDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"updateUserProfile"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"input"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"UpdateUserProfileInput"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"updateUserProfile"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"input"},"value":{"kind":"Variable","name":{"kind":"Name","value":"input"}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"first_name"}},{"kind":"Field","name":{"kind":"Name","value":"last_name"}},{"kind":"Field","name":{"kind":"Name","value":"title"}},{"kind":"Field","name":{"kind":"Name","value":"suffix"}},{"kind":"Field","name":{"kind":"Name","value":"job_bio"}},{"kind":"Field","name":{"kind":"Name","value":"role"}},{"kind":"Field","name":{"kind":"Name","value":"profile_image"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"url"}}]}},{"kind":"Field","name":{"kind":"Name","value":"phone"}},{"kind":"Field","name":{"kind":"Name","value":"email"}},{"kind":"Field","name":{"kind":"Name","value":"sra_clc_number"}}]}}]}}]} as unknown as DocumentNode<UpdateUserProfileMutation, UpdateUserProfileMutationVariables>;
export const DeleteUserDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"deleteUser"},"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"deleteUser"}}]}}]} as unknown as DocumentNode<DeleteUserMutation, DeleteUserMutationVariables>;
export const ResendInviteDocument = {"kind":"Document","definitions":[{"kind":"OperationDefinition","operation":"mutation","name":{"kind":"Name","value":"resendInvite"},"variableDefinitions":[{"kind":"VariableDefinition","variable":{"kind":"Variable","name":{"kind":"Name","value":"email"}},"type":{"kind":"NonNullType","type":{"kind":"NamedType","name":{"kind":"Name","value":"String"}}}}],"selectionSet":{"kind":"SelectionSet","selections":[{"kind":"Field","name":{"kind":"Name","value":"resendInvite"},"arguments":[{"kind":"Argument","name":{"kind":"Name","value":"email"},"value":{"kind":"Variable","name":{"kind":"Name","value":"email"}}}]}]}}]} as unknown as DocumentNode<ResendInviteMutation, ResendInviteMutationVariables>;