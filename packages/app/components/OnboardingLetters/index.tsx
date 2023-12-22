import classNames from 'classnames'
import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import { H3 } from '@proconvey/ui/src/components/Headers'
import HtmlEditor from '@proconvey/ui/src/components/HtmlEditor'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import { useCallback, useEffect, useState } from 'react'
import { useForm } from 'react-hook-form'
import { CombinedError, useQuery, useMutation } from 'urql'
import { OnboardingLettersProfessional, OnboardingLettersStylish, OnboardingLettersContemporary, OnboardingLettersFunctional, OnboardingLettersMinimalistic } from '@proconvey/ui/src/images'
import { toast } from 'react-hot-toast'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import CheckedIconSVG  from '@proconvey/ui/src/svgs/CheckedIconSVG'
import UnCheckedIconSVG  from '@proconvey/ui/src/svgs/UnCheckedIconSVG'
import Modal from '@proconvey/ui/src/components/Modals'

export type OnboardingLettersData = {
  client_care_letter: string
  client_care_letter_sale: string
  client_care_letter_purchase: string
  client_care_letter_remortgage: string
  terms_and_conditions: string
  letter_header: string
  letter_footer: string
}

type PropTypes = {
  onChange?: (values: OnboardingLettersData) => void
  formErrors?: CombinedError | undefined
  currentType: number
  setCurrentType: (value: number) => void
}

type HeaderTypes = {
  Professional: string,
  Stylish: string,
  Contemporary: string,
  Functional: string,
  Minimalistic: string,
  Own: string,
}

const OnboardingLetters = ({ onChange, formErrors, currentType, setCurrentType }: PropTypes) => {

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const [{ data: conveyancer }] = useQuery({
    query: graphql(`
    query registerBusinessConveyancer {
      me {
        id
        conveyancer {
          id
          name
          type
          sra_clc_number
          company_number
          address {
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

  const [{ fetching: fetchingLetters, data: defaultData }] = useQuery({
    query: graphql(`
      query onboardingLetters {
        me {
          id
          conveyancer {
            id
            client_care_letter
            client_care_letter_sale
            client_care_letter_purchase
            client_care_letter_remortgage
            terms_and_conditions
            letter_header
            letter_footer
          }
        }
      }
    `),
  })

  const [{ fetching }, updateConveyancerMutation] = useMutation(graphql(`
    mutation updateConveyancer ($input: UpdateConveyancerInput!) {
      updateConveyancer(input: $input) {
        id
      }
    }
  `))

  const errorHandler = useErrorHandler()
  const [selectedTemplate, setSelectedTemplate] = useState('')
  const [selectedTemplateForAll, setSelectedTemplateForAll] = useState('')
  const [templateContents, setTemplateContents] = useState('')
  const [completedSaleLetter, setCompletedSaleLetter] = useState(false)
  const [completedPurchaseLetter, setCompletedPurchaseLetter] = useState(false)
  const [completedRemortgageLetter, setCompletedRemortgageLetter] = useState(false)
  const [completedTermsLetter, setCompletedTermsLetter] = useState(false)
  const [isModalOpen, setIsModalOpen] = useState<boolean>(false)
  const [lastestTemplateContents, setLastestTemplateContents] = useState<string>('')

  const { setValue, getValues, watch, setError, clearErrors, formState: { errors } } = useForm<OnboardingLettersData>({
    defaultValues: {
      client_care_letter: '',
      client_care_letter_sale: '',
      client_care_letter_purchase: '',
      client_care_letter_remortgage: '',
      terms_and_conditions: '',
      letter_header: '',
      letter_footer: '',
    },
  })

  const [letterData, setLetterData] = useState<OnboardingLettersData>({
    client_care_letter: '',
    client_care_letter_sale: '',
    client_care_letter_purchase: '',
    client_care_letter_remortgage: '',
    terms_and_conditions: '',
    letter_header: '',
    letter_footer: '',
  })

  const setValues = useCallback((key: keyof OnboardingLettersData, value: string) => {
    setValue(key, value)
  }, [setValue])

  useEffect(() => {
    if (defaultData) {
      setValues('client_care_letter', defaultData.me?.conveyancer?.client_care_letter ?? '')
      setValues('client_care_letter_sale', defaultData.me?.conveyancer?.client_care_letter_sale ?? '')
      setValues('client_care_letter_purchase', defaultData.me?.conveyancer?.client_care_letter_purchase ?? '')
      setValues('client_care_letter_remortgage', defaultData.me?.conveyancer?.client_care_letter_remortgage ?? '')
      setValues('terms_and_conditions', defaultData.me?.conveyancer?.terms_and_conditions ?? '')
      setValues('letter_header', defaultData.me?.conveyancer?.letter_header ?? '')
      setValues('letter_footer', defaultData.me?.conveyancer?.letter_footer ?? '')
      setCompletedSaleLetter(defaultData.me?.conveyancer?.client_care_letter_sale ? true : false)
      setCompletedPurchaseLetter(defaultData.me?.conveyancer?.client_care_letter_purchase ? true : false)
      setCompletedRemortgageLetter(defaultData.me?.conveyancer?.client_care_letter_remortgage ? true : false)
      setCompletedTermsLetter(defaultData.me?.conveyancer?.terms_and_conditions ? true : false)
    }
  }, [defaultData, setValues])

  useEffect(() => {
    if (formErrors && !Object.keys(errors).length) {
      errorHandler(formErrors, setError)
    }

    if (formErrors === undefined && Object.keys(errors).length) {
      clearErrors()
    }
  }, [formErrors, clearErrors, errorHandler, setError, errors])

  useEffect(() => {
    if (onChange) {
      const subscription = watch(value => onChange({ ...getValues(), ...value }))
      return () => subscription.unsubscribe()
    }
  }, [onChange, watch, getValues])

  useEffect(() => {
    if (letterData) {
      const subscription = watch(value => setLetterData({ ...getValues(), ...value }))
      return () => subscription.unsubscribe()
    }
  }, [letterData, watch, getValues])

  useEffect(() => {
    if (currentType === 0) {
      defaultData?.me?.conveyancer?.client_care_letter_sale === null || defaultData?.me?.conveyancer?.client_care_letter_sale === '' ? handleSelectStyle(selectedTemplate) : setTemplateContents(defaultData?.me?.conveyancer?.client_care_letter_sale ?? '')
    } else if (currentType === 1) {
      defaultData?.me?.conveyancer?.client_care_letter_purchase === null || defaultData?.me?.conveyancer?.client_care_letter_purchase === '' ? handleSelectStyle(selectedTemplate) : setTemplateContents(defaultData?.me?.conveyancer?.client_care_letter_purchase ?? '')
    } else if (currentType === 2) {
      defaultData?.me?.conveyancer?.client_care_letter_remortgage === null || defaultData?.me?.conveyancer?.client_care_letter_remortgage === '' ? handleSelectStyle(selectedTemplate) : setTemplateContents(defaultData?.me?.conveyancer?.client_care_letter_remortgage ?? '')
    } else if (currentType === 3) {
      defaultData?.me?.conveyancer?.terms_and_conditions === null || defaultData?.me?.conveyancer?.terms_and_conditions === '' ? handleSelectStyle(selectedTemplate) : setTemplateContents(defaultData?.me?.conveyancer?.terms_and_conditions ?? '')
    }
  }, [currentType, defaultData])

  useEffect(() => {
    if (currentType === 0) {
      setLastestTemplateContents(letterData?.client_care_letter_sale)
    } else if (currentType === 1) {
      setLastestTemplateContents(letterData?.client_care_letter_purchase)
    } else if (currentType === 2) {
      setLastestTemplateContents(letterData?.client_care_letter_remortgage)
    }
  }, [templateContents])

  const handleApplyAll = async () => {
    setSelectedTemplateForAll(selectedTemplate)
    toast.success('Your all care letters are selected as ' + selectedTemplate + ' now')
  }

  const handleSubmit = async () => {
    const result = await updateConveyancerMutation({
      input: letterData,
    })

    if (result.error) {
      toast.error('Something went wrong, please try again later')
      return
    }
    toast.success('Successfully saved')
    setCurrentType(currentType + 1)
    selectedTemplateForAll === '' ? setSelectedTemplate('') : setSelectedTemplate(selectedTemplateForAll)
    setIsModalOpen(false)
  }

  const handleSave = async () => {
    let changedFlag = true
    if (currentType === 0) {
      if (letterData?.client_care_letter_sale === lastestTemplateContents) {
        changedFlag = false
      }
    } else if (currentType === 1) {
      if (letterData?.client_care_letter_purchase === lastestTemplateContents) {
        changedFlag = false
      }
    } else if (currentType === 2) {
      if (letterData?.client_care_letter_remortgage === lastestTemplateContents) {
        changedFlag = false
      }
    } else if (currentType === 3) {
      if (letterData?.terms_and_conditions === lastestTemplateContents) {
        changedFlag = false
      }
    }

    if (changedFlag) {
      const result = await updateConveyancerMutation({
        input: letterData,
      })

      if (result.error) {
        toast.error('Something went wrong, please try again later')
        return
      }
      toast.success('Successfully saved')
      setCurrentType(currentType + 1)
      selectedTemplateForAll === '' ? setSelectedTemplate('') : setSelectedTemplate(selectedTemplateForAll)
    } else {
      setIsModalOpen(true)
    }
  }

  const handleSelectStyle = async (style: any) => {
    let contentString = ''
    if (style) {
      setSelectedTemplate(style)
      let keyToIndex: keyof HeaderTypes = style
      if (currentType === 0) {
        contentString = TemplateHeader[keyToIndex] + SaleTemplateHTML
      } else if (currentType === 1) {
        contentString = TemplateHeader[keyToIndex] + PurchaseTemplateHTML
      } else if (currentType === 2) {
        contentString = TemplateHeader[keyToIndex] + RemortgageTemplateHTML
      } if (style === 'Own') {
        contentString = ''
      }
    }
    setTemplateContents(contentString)
  }

  const ProfessionalTemplateHeader = `
    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; padding:  0 3rem; -webkit-user-modify: read-only;">
      <div style="width: 35%;height: 100%;background-color: #00154B;border-radius: 0 0 24px 0;padding: 3rem;display: flex;flex-direction: column;gap: 2rem;">
          <div style=" display: flex; gap: 12px; align-items: center; gap: 3rem; -webkit-user-modify: read-only;">
              <img style="border-radius: 50%;width: 7rem; height: 7rem;" src="${user?.profile_image?.url}">
              <div style="color: white;">
                  <b>{{ conveyancer.name }}</b>
                  <p>{{ conveyancer.email_address }}</p>
                  <p>{{ conveyancer.telephone_number }}</p>
              </div>
          </div>
          <div style="display: flex; flex-direction: column; gap: 2rem; color: white;">
              <div style="display: flex; gap: 2rem; align-items: center;">
                <img alt="" src="../whitehouse.png" width="27" height="23" decoding="async" data-nimg="1" loading="lazy" style="color: transparent;">
                <b>{{  property.type }}</b>
              </div>
              <div style="display: flex; flex-direction: column;">
                  <b>Property address:</b>
                  {{ property.address.line_1 }}<br>
                  {{ property.address.line_2 }}<br>
                  {{ property.address.city }}<br>
                  {{ property.address.postcode }}<br>
              </div>
              <div style="display: flex; flex-direction: column;">
                  <p>Case reference: {{ property.case_reference }}</p>
              </div>
              <div style="display: flex; flex-direction: column;">
                  {{  user.full_name }}<br>
                  Date: {{ user.email_verified_at }}<br>
              </div>
          </div>
      </div>
      <div style="display: flex;flex-direction: column; width: 20%; height: 100%;padding: 3rem; gap: 2rem;">
          <img style="max-width: 10rem; height: auto;" src="${conveyancer?.me?.conveyancer?.logo_image?.url}">
          <div style="display: flex; flex-direction: column; gap: 2rem; color: #3D403D;">
              <div style="display: flex; flex-direction: column;">
                  Branch Address:<br>
                  ${conveyancer?.me?.conveyancer?.address?.line_1}<br>
                  ${conveyancer?.me?.conveyancer?.address?.line_2 ? conveyancer?.me?.conveyancer?.address?.line_2 + '<br>' : ''}<br>
                  ${conveyancer?.me?.conveyancer?.address?.city}<br>
                  ${conveyancer?.me?.conveyancer?.address?.postcode}<br>
              </div>
              <div style="display: flex; flex-direction: column;">
                  ${conveyancer?.me?.conveyancer?.website ?? '-'}<br>
                  ${conveyancer?.me?.conveyancer?.telephone_number ?? '-'}<br>
              </div>
          </div>
      </div>
    </div>
  `
  const StylishTemplateHeader = `
    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;  -webkit-user-modify: read-only;">
      <div style="display: flex; width: 100%; height: 100%;padding: 3rem; gap: 2rem; justify-content: space-between; background-color: #F0F0F2; align-items: center;">
          <img style="max-width: 10rem; height: auto;" src="${conveyancer?.me?.conveyancer?.logo_image?.url}">
          <div style="display: flex; flex-direction: column;">
              Branch Address:<br>
              ${conveyancer?.me?.conveyancer?.address?.line_1}<br>
              ${conveyancer?.me?.conveyancer?.address?.line_2 ? conveyancer?.me?.conveyancer?.address?.line_2 + '<br>' : ''}<br>
              ${conveyancer?.me?.conveyancer?.address?.city}<br>
              ${conveyancer?.me?.conveyancer?.address?.postcode}<br>
          </div>
          <div style="display: flex; flex-direction: column;">
            ${conveyancer?.me?.conveyancer?.website ?? '-'}<br>
            ${conveyancer?.me?.conveyancer?.telephone_number ?? '-'}<br>
          </div>
        </div>
        <div style="width: 100%;height: 100%;background-color: #00154B;padding: 3rem;display: flex; gap: 2rem; justify-content: space-between;">
          <div style=" display: flex; gap: 12px; align-items: center; gap: 3rem; -webkit-user-modify: read-only;">
            <img style="border-radius: 50%;width: 7rem; height: 7rem;" src="${user?.profile_image?.url}">
            <div style="color: white;">
                <b>{{ conveyancer.name }}</b>
                <p>{{ conveyancer.email_address }}</p>
                <p>{{ conveyancer.telephone_number }}</p>
            </div>
          </div>
          <div style="display: flex; flex-direction: column; gap: 2rem; color: white;">
            <div style="display: flex; gap: 2rem; align-items: center;">
              <img alt="" src="../whitehouse.png" width="27" height="23" decoding="async" data-nimg="1" loading="lazy" style="color: transparent;">
              <b>{{  property.type }}</b>
            </div>
            <div style="display: flex; flex-direction: column;">
                <b>Property address:</b>
                {{ property.address.line_1 }}<br>
                {{ property.address.line_2 }}<br>
                {{ property.address.city }}<br>
                {{ property.address.postcode }}<br>
            </div>
          </div>
          <div style="display: flex; flex-direction: column;">
            <br>
            <br>
          </div>
        </div>
        <div style="display: flex; width: 100%; height: 100%;padding: 3rem; gap: 2rem; justify-content: space-between; background-color: #F0F0F2;">
            <div style="display: flex; flex-direction: column;">
            {{  user.full_name }}<br>
            Date: {{ user.email_verified_at }}<br>
          </div>
      </div>
    </div>
  `
  const ContemporaryTemplateHeader = `
    <div style="display: flex;justify-content: space-between;flex-wrap: wrap;background-color: #F0F0F2;gap: 5rem;z-index: -100; -webkit-user-modify: read-only;">
      <div style="display: flex; height: 100%;padding: 3rem; gap: 3rem; justify-content: space-between; flex-direction: column;">
        <div style="margin: -3rem 0 0 -3rem;display: flex;width: 15rem;height: 10rem;background-color: #CFD5E5;border-radius: 0 0 150% 20%;justify-content: flex-start;place-items: center;">
          <img style="max-width: 10rem; height: auto; margin-left: 1rem;" src="${conveyancer?.me?.conveyancer?.logo_image?.url}">
        </div>
        <div style="display: flex;gap: 2rem;flex-direction: column;z-index: 5;">
            <div style="display: flex; flex-direction: column; gap: 2rem; color: #3D403D;">
            <div style="display: flex; gap: 2rem; align-items: center;">
              <img alt="" src="../blackhouse.png" width="27" height="23" decoding="async" data-nimg="1" loading="lazy" style="color: transparent;">
              <b>{{  property.type }}</b>
            </div>
            <div style="display: flex; flex-direction: column;">
              <b>Property address:</b>
              {{ property.address.line_1 }}<br>
              {{ property.address.line_2 }}<br>
              {{ property.address.city }}<br>
              {{ property.address.postcode }}<br>
            </div>
            <div style="display: flex; flex-direction: column;">
                {{  user.full_name }}<br>
                Date: {{ user.email_verified_at }}<br>
              </div>
          </div>
        </div>
        </div>
        <div style="height: 100%; padding: 3rem;display: flex; gap: 2rem; justify-content: space-between; flex-direction: column; gap: 5rem;">
          <div style="padding: 0 0 3rem 0;border-bottom: 1px solid #BDBDBD;display: flex;flex-direction: row;justify-content: space-between; gap: 2rem;">
            <div style="display: flex; flex-direction: column;">
                Branch Address:<br>
                ${conveyancer?.me?.conveyancer?.address?.line_1}<br>
                ${conveyancer?.me?.conveyancer?.address?.line_2 ? conveyancer?.me?.conveyancer?.address?.line_2 + '<br>' : ''}<br>
                ${conveyancer?.me?.conveyancer?.address?.city}<br>
                ${conveyancer?.me?.conveyancer?.address?.postcode}<br>
            </div>
            <div style="display: flex; flex-direction: column;">
              ${conveyancer?.me?.conveyancer?.website ??  '-'}<br>
              ${conveyancer?.me?.conveyancer?.telephone_number ?? '-'}<br>
            </div>
          </div>
          <div style=" display: flex; gap: 12px; align-items: center; gap: 3rem; -webkit-user-modify: read-only;">
            <img style="border-radius: 50%;width: 7rem; height: 7rem;" src="${user?.profile_image?.url}">
            <div style="color: #3D403D;">
                <b>{{ conveyancer.name }}</b>
                <p>{{ conveyancer.email_address }}</p>
                <p>{{ conveyancer.telephone_number }}</p>
            </div>
          </div>
      </div>
    </div>
  `
  const FunctionalTemplateHeader = `
    <div style="display: flex;justify-content: space-between;flex-wrap: wrap; background-color: #FDFDFD; gap: 5rem; -webkit-user-modify: read-only;">
      <div style="display: flex; height: 100%;padding: 3rem; justify-content: space-between; flex-direction: column; -webkit-user-modify: read-only;">
        <div style=" display: flex; gap: 12px; align-items: center; gap: 3rem; padding: 0 0 3rem">
        <div style="color: #3D403D; border-bottom: 1px solid #BDBDBD;">
            <b>{{ conveyancer.name }}</b>
            <p>{{ conveyancer.email_address }}</p>
            <p>{{ conveyancer.telephone_number }}</p>
        </div>
      </div>
      <div style="display: flex;gap: 2rem;flex-direction: column;z-index: 5;border-bottom: 1px solid #BDBDBD;padding: 0 0 2rem;">
          <div style="display: flex; flex-direction: column; gap: 2rem; color: #3D403D;">
              <div style="display: flex; gap: 2rem; align-items: center;">
                <img alt="" src="../blackhouse.png" width="27" height="23" decoding="async" data-nimg="1" loading="lazy" style="color: transparent;">
                <b>{{  property.type }}</b>
              </div>
              <div style="display: flex; flex-direction: column;">
                <b>Property address:</b>
                {{ property.address.line_1 }}<br>
                {{ property.address.line_2 }}<br>
                {{ property.address.city }}<br>
                {{ property.address.postcode }}<br>
              </div>
            </div>
          </div>
      </div>
      <div style="height: 100%; padding: 3rem;display: flex; gap: 2rem; justify-content: space-between; flex-direction: column;">
        <div style="padding: 0 0 2rem 0;border-bottom: 1px solid #BDBDBD;display: flex;flex-direction: row;justify-content: space-between;">
        <img style="max-width: 10rem; height: auto;" src="${conveyancer?.me?.conveyancer?.logo_image?.url}">
      </div>
        <div style="padding: 0 0 2rem 0;border-bottom: 1px solid #BDBDBD;display: flex;flex-direction: row;justify-content: space-between;">
        <div style="display: flex; flex-direction: column;">
            Branch Address:<br>
            ${conveyancer?.me?.conveyancer?.address?.line_1}<br>
            ${conveyancer?.me?.conveyancer?.address?.line_2 ? conveyancer?.me?.conveyancer?.address?.line_2 + '<br>' : ''}<br>
            ${conveyancer?.me?.conveyancer?.address?.city}<br>
            ${conveyancer?.me?.conveyancer?.address?.postcode}<br>
        </div>
      </div>
      <div style="padding: 0 0 3rem 0; display: flex;flex-direction: row;justify-content: space-between;">
        <div style="display: flex; flex-direction: column;">
          ${conveyancer?.me?.conveyancer?.website ??  '-'}<br>
          ${conveyancer?.me?.conveyancer?.telephone_number ?? '-'}<br>
        </div>
      </div>
      </div>
    </div>
    <div style="display: flex;flex-direction: column;gap: 3rem;background-color: #FDFDFD;">
      <div style="display: flex;justify-content: space-between;flex-wrap: wrap; gap: 5rem;">
          <div style="display: flex; width: 100%; padding:  0 3rem; -webkit-user-modify: read-only; gap: 3rem; justify-content: space-between; flex-direction: column;">
            <div style="padding: 0 0 2rem 0;border-bottom: 1px solid #BDBDBD;display: flex;flex-direction: row;justify-content: space-between;">
                Case reference: {{ property.case_reference }}<br>
              </div>
          </div>
      </div>
      <div style="display: flex;justify-content: space-between;flex-wrap: wrap;gap: 0rem;">
          <div style="display: flex; width: 100%; padding:  0 3rem; -webkit-user-modify: read-only; gap: 3rem; justify-content: space-between; flex-direction: column;">
            <div style="padding: 0 0 2rem 0;border-bottom: 1px solid #BDBDBD;display: flex;flex-direction: row;justify-content: space-between;">
                {{  user.full_name }}<br>
                Date: {{ user.email_verified_at }}<br>
              </div>
          </div>
      </div>
    </div>
  `
  const MinimalisticTemplateHeader = `
    <div style="display: flex;justify-content: space-between;flex-wrap: wrap; background-color: #FDFDFD; gap: 5rem; -webkit-user-modify: read-only;">
      <div style="display: flex; height: 100%;padding: 3rem; justify-content: space-between; flex-direction: column; -webkit-user-modify: read-only;">
        <div style=" display: flex; gap: 12px; align-items: center; gap: 3rem; padding: 0 0 3rem">
          <img style="border-radius: 50%;width: 7rem; height: 7rem;" src="${user?.profile_image?.url}">
        <div style="color: #3D403D;">
          <b>{{ conveyancer.name }}</b>
          <p>{{ conveyancer.email_address }}</p>
          <p>{{ conveyancer.telephone_number }}</p>
        </div>
      </div>
      <div style="display: flex;gap: 2rem;flex-direction: column;z-index: 5; padding: 0 0 2rem;">
          <div style="display: flex; flex-direction: column; gap: 2rem; color: #3D403D;">
          <div style="display: flex; gap: 2rem; align-items: center;">
            <img alt="" src="../blackhouse.png" width="27" height="23" decoding="async" data-nimg="1" loading="lazy" style="color: transparent;">
            <b>{{  property.type }}</b>
          </div>
          <div style="display: flex; flex-direction: column;">
            <b>Property address:</b>
            {{ property.address.line_1 }}<br>
            {{ property.address.line_2 }}<br>
            {{ property.address.city }}<br>
            {{ property.address.postcode }}<br>
          </div>
        </div>
      </div>
      </div>
      <div style="height: 100%; padding: 3rem;display: flex; gap: 2rem; justify-content: space-between; flex-direction: column;">
        <div style="padding: 0 0 2rem 0; display: flex;flex-direction: row;justify-content: space-between;">
        <img style="max-width: 10rem; height: auto;" src="${conveyancer?.me?.conveyancer?.logo_image?.url}">
      </div>
        <div style="padding: 0 0 2rem 0; display: flex;flex-direction: row;justify-content: space-between;">
        <div style="display: flex; flex-direction: column;">
            Branch Address:<br>
            ${conveyancer?.me?.conveyancer?.address?.line_1}<br>
            ${conveyancer?.me?.conveyancer?.address?.line_2 ? conveyancer?.me?.conveyancer?.address?.line_2 + '<br>' : ''}<br>
            ${conveyancer?.me?.conveyancer?.address?.city}<br>
            ${conveyancer?.me?.conveyancer?.address?.postcode}<br>
        </div>
      </div>
      <div style="padding: 0 0 3rem 0; display: flex;flex-direction: row;justify-content: space-between;">
        <div style="display: flex; flex-direction: column;">
          ${conveyancer?.me?.conveyancer?.website ??  '-'}<br>
          ${conveyancer?.me?.conveyancer?.telephone_number ?? '-'}<br>
        </div>
      </div>
      </div>
    </div>
    <div style="display: flex;flex-direction: column;gap: 3rem;background-color: #FDFDFD;">
      <div style="display: flex;justify-content: space-between;flex-wrap: wrap; gap: 5rem;">
          <div style="display: flex; width: 100%; padding:  0 3rem; -webkit-user-modify: read-only; gap: 3rem; justify-content: space-between; flex-direction: column;">
            <div style="padding: 0 0 2rem 0; display: flex;flex-direction: row;justify-content: space-between;">
                Case reference: {{ property.case_reference }}<br>
              </div>
          </div>
      </div>
      <div style="display: flex;justify-content: space-between;flex-wrap: wrap;gap: 0rem;">
          <div style="display: flex; width: 100%; padding:  0 3rem; -webkit-user-modify: read-only; gap: 3rem; justify-content: space-between; flex-direction: column;">
            <div style="padding: 0 0 2rem 0; display: flex;flex-direction: row;justify-content: space-between;">
                {{  user.full_name }}<br>
                Date: {{ user.email_verified_at }}<br>
              </div>
          </div>
      </div>
    </div>
  `

  const SaleTemplateHTML = `
    <div style="padding: 3.5rem;"><p>Dear <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ user.full_name }}</span>,</p><p>Re: Sale of <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.address.single_line }}</span></p><p>We are delighted to welcome you to <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span> and extend our heartfelt thanks for choosing our services for your property transaction needs. We understand the significance of this transaction and the importance it holds for you. Rest assured, we are committed to providing you with exceptional service and guiding you through every step of the process.</p><p>To provide you with clear and transparent information, we have prepared this Client Care Letter that outlines important details regarding our engagement and your specific transaction. We kindly request you to carefully review the following information:</p><p><strong>Scope of Work</strong><br>Our services will encompass the complete conveyancing process, including but not limited to:</p><ul><li>Reviewing and advising on the terms of the sale contract</li><li>Gathering the relevant property information</li><li>Liaising with the buyer's solicitor to facilitate a smooth transaction</li><li>Drafting and exchanging legal documentation, including the contract of sale and transfer deed</li><li>Facilitating the transfer of funds, including payment of outstanding mortgages and apportionments</li><li>Registering the transfer of property with the relevant land registry authorities.</li><li>Providing regular updates and progress reports to keep you informed throughout the process</li></ul><p>Please note that this is a general overview of the scope of work for a sale transaction, and specific details may vary depending on your individual case.</p><p><strong>Price of the Property</strong><br>The agreed sale price for the property is <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.sale_price }}</span>.</p><p><strong>Fees and Charges</strong><br>The fees for our conveyancing services regarding the above property are outlined below:</p><p>Professional fees: <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.conveyancing_fee }}</span>  + VAT</p><p>Please note that in addition to the fees outlined above, there may be other disbursements applicable to your specific transaction. For a comprehensive breakdown of these additional costs, we kindly ask you to refer to the estimate provided to you.</p><p>These fees are based on the information provided to us at this stage. However, if any unforeseen circumstances or complexities arise during the transaction, we will inform you promptly and discuss any necessary adjustments to the fees.</p><p><strong>Timescales</strong><br>While we strive to complete your transaction as efficiently as possible, please be aware that the timescales can vary depending on various factors such as the chain of transactions, the responsiveness of all parties involved, and the complexity of the matter. We will keep you informed of any significant developments and provide estimated timescales for each stage of the process.</p><p><strong>Confidentiality and Data Protection</strong><br>We treat all information you provide to us with the utmost confidentiality and in compliance with relevant data protection laws. We will not disclose any confidential information without your explicit consent, except where required by law or necessary for the completion of your transaction.</p><p><strong>Communication</strong><br>Open and regular communication is essential for a smooth and successful transaction. We are committed to keeping you informed and will be available to answer any queries or concerns you may have. We will primarily communicate through email and phone, but we can also arrange meetings if required.</p><p><strong>Onboarding with ProConvey Software</strong><br>To streamline and enhance your conveyancing experience, we utilise software called ProConvey. This platform allows you to conveniently complete various processes and requirements. Payment on account, ID verification, and the completion of Protocol Forms will all be facilitated through the ProConvey software. This user-friendly tool ensures secure and efficient handling of these tasks, providing you with a seamless experience throughout your property transaction journey.</p><p><strong>Payment on Account</strong><br>To ensure a smooth and efficient handling of your property transaction, we require payment on account of<br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.payment_on_account_amount }}</span> towards the legal costs. The purpose of this payment is to cover initial expenses and secure our services on your behalf. Any unused portion of the payment will be credited towards your final invoice.</p><p><strong>ID Verification</strong><br>As part of our commitment to compliance and security, we employ digital ID verification methods to validate your identity. We kindly request your cooperation in providing the necessary documentation and participating in the ID verification process to ensure a secure and reliable transaction.</p><p><strong>Completion of Protocol Forms</strong><br>In accordance with industry standards and the Conveyancing Protocol, we require the completion of Protocol Forms for your sale transaction. These forms, prescribed by the Law Society, provide crucial guidance and information necessary for the successful completion of your transaction.</p><p>Please review this Client Care Letter carefully, and if you agree to the terms and conditions outlined, please sign below as confirmation of your acceptance.</p><p>Should you have any questions or require further clarification regarding the content of this letter, please feel free to contact us by telephone. We are available to discuss and address any concerns you may have.</p><p>Yours sincerely,<br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.fee_earner }}</span><br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span></p></div>
  `
  const PurchaseTemplateHTML = `
    <div style="padding: 3.5rem;"><p>Dear <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ user.full_name }}</span>,</p><p>Re: Purchase of <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.address.single_line }}</span></p><p>We are delighted to welcome you to <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span> and extend our heartfelt thanks for choosing our services for your property transaction needs. We understand the significance of this transaction and the importance it holds for you. Rest assured, we are committed to providing you with exceptional service and guiding you through every step of the process.</p><p>To ensure transparency and clarity, we have prepared this Client Care Letter that outlines the terms and conditions of our engagement, as well as pertinent details relating to your specific transaction. Please review the following information carefully.</p><p><strong>Scope of Work</strong><br>Our services will encompass the complete conveyancing process, including but not limited to:</p><ul><li>Reviewing and advising on the terms of the contract</li><li>Carrying out relevant property searches</li><li>Liaising with the seller’s solicitors to facilitate a smooth transaction</li><li>Liaising with mortgage lenders, if applicable drafting and exchanging legal documentation</li><li>Facilitating the transfer of funds</li><li>Providing guidance on Stamp Duty Land Tax (SDLT)</li><li>Providing you with regular updates and progress reports</li><li>Registering the property in your name</li></ul><p>Please note that this is a general overview of the scope of work for a purchase transaction, and specific details may vary depending on your individual case.</p><p><br><strong>Price of the Property</strong><br>The agreed purchase price for the property is <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.sale_price }}</span>. Please note that any changes to the purchase price may have implications for the Stamp Duty Land Tax (SDLT). We will provide you with comprehensive guidance and assistance on these matters.</p><p><strong>Fees and Charges</strong><br>The fees for our conveyancing services regarding the above property are outlined below:</p><p>Professional fees: <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.conveyancing_fee }}</span>  + VAT</p><p>Please note that in addition to the fees outlined above, there may be other disbursements applicable to your specific transaction. For a comprehensive breakdown of these additional costs, we kindly ask you to refer to the estimate provided to you.</p><p>These fees are based on the information provided to us at this stage. However, if any unforeseen circumstances or complexities arise during the transaction, we will inform you promptly and discuss any necessary adjustments to the fees.</p><p><strong>Timescales</strong><br>While we strive to complete your transaction as efficiently as possible, please be aware that the timescales can vary depending on various factors such as the chain of transactions, the responsiveness of all parties involved, and the complexity of the matter. We will keep you informed of any significant developments and provide estimated timescales for each stage of the process.</p><p><strong>Confidentiality and Data Protection</strong><br>We treat all information you provide to us with the utmost confidentiality and in compliance with relevant data protection laws. We will not disclose any confidential information without your explicit consent, except where required by law or necessary for the completion of your transaction.</p><p><strong>Communication</strong><br>Open and regular communication is essential for a smooth and successful transaction. We are committed to keeping you informed and will be available to answer any queries or concerns you may have. We will primarily communicate through email and phone, but we can also arrange meetings if required.</p><p><strong>Onboarding with ProConvey Software</strong><br>To streamline and enhance your conveyancing experience, we utilise software called ProConvey. This platform allows you to conveniently complete various processes and requirements. Payment on account, IDverification, and source of funds checks will all be facilitated through the ProConvey software. This user-friendly tool ensures secure and efficient handling of these tasks, providing you with a seamless experience throughout your property transaction journey</p><p><strong>Payment on Account</strong><br>To ensure a smooth and efficient handling of your property transaction, we require payment on account of <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.payment_on_account_amount }}</span> towards the legal costs. The purpose of this payment is to cover initial expenses and secure our services on your behalf. Any unused portion of the payment will be credited towards your final invoice.</p><p><strong>ID Verification</strong><br>As part of our commitment to compliance and security, we employ digital ID verification methods to validate your identity. We kindly request your cooperation in providing the necessary documentation and participating in the ID verification process to ensure a secure and reliable transaction.</p><p><strong>Source of Funds</strong><br>As part of our compliance with anti-money laundering legislation, we are obligated to conduct a thorough "source of funds" check. This check is necessary to verify the origin of the funds being used for the property purchase. It encompasses various sources such as savings, mortgages, gifts from relatives, inheritances, and other legitimate means. Your cooperation in providing the necessary information and documentation for the source of funds check is greatly appreciated.</p><p>Please review this Client Care Letter carefully, and if you agree to the terms and conditions outlined, please sign below as confirmation of your acceptance. Should you have any questions or require further clarification regarding the content of this letter, please feel free to contact us by telephone. We are available to discuss and address any concerns you may have.</p><p>Yours sincerely,<br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.fee_earner }}</span><br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span></p></div>
  `
  const RemortgageTemplateHTML = `
    <div style="padding: 3.5rem;"><p>Dear <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ user.full_name }}</span>,</p><p>Re: Remortgage of <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.address.single_line }}</span></p><p>We are delighted to welcome you to <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span> and extend our heartfelt thanks for choosing our services for your property transaction needs. We understand the significance of this transaction and the importance it holds for you. Rest assured, we are committed to providing you with exceptional service and guiding you through every step of the process.</p><p>To ensure transparency and clarity, we have prepared this Client Care Letter that outlines the terms and conditions of our engagement, as well as pertinent details relating to your specific transaction. Please review the following information carefully:</p><p><strong>Scope of Work</strong><br>Our services will encompass the complete conveyancing process, including but not limited to:</p><ul><li>Reviewing and advising on the terms of the remortgage.</li><li>Carrying out relevant property searches.</li><li>Liaising with mortgage lenders, if applicable, and coordinating necessary communications.</li><li>Drafting and exchanging legal documentation.</li><li>Facilitating the transfer of funds.</li><li>Providing guidance on Stamp Duty Land Tax (SDLT).</li><li>Providing you with regular updates and progress reports.</li><li>Registering the property in your name.</li></ul><p>Please note that this is a general overview of the scope of work for a remortgage transaction, and specific details may vary depending on your individual case.</p><p><strong>Price of the Property</strong><br>Please note that any changes to the remortgage price may have implications for the Stamp Duty Land Tax (SDLT). We will provide you with comprehensive guidance and assistance on these matters.</p><p><strong>Fees and Charges</strong><br>The fees for our conveyancing services regarding the above property are outlined below:</p><p>Professional fees: <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.conveyancing_fee }}</span>  + VAT</p><p>Please note that in addition to the fees outlined above, there may be other disbursements applicable to your specific transaction. For a comprehensive breakdown of these additional costs, we kindly ask you to refer to the estimate provided to you.</p><p>These fees are based on the information provided to us at this stage. However, if any unforeseen circumstances or complexities arise during the transaction, we will inform you promptly and discuss any necessary adjustments to the fees.</p><p><strong>Timescales</strong><br>While we strive to complete your transaction as efficiently as possible, please be aware that the timescales can vary depending on various factors such as the chain of transactions, the responsiveness of all parties involved, and the complexity of the matter. We will keep you informed of any significant developments and provide estimated timescales for each stage of the process.</p><p><strong>Confidentiality and Data Protection</strong><br>We treat all information you provide to us with the utmost confidentiality and in compliance with relevant data protection laws. We will not disclose any confidential information without your explicit consent, except where required by law or necessary for the completion of your transaction.</p><p><strong>Communication</strong><br>Open and regular communication is essential for a smooth and successful transaction. We are committed to keeping you informed and will be available to answer any queries or concerns you may have. We will primarily communicate through email and phone, but we can also arrange meetings if required.</p><p><strong>Onboarding with ProConvey Software</strong><br>To streamline and enhance your conveyancing experience, we utilise software called ProConvey. This platform allows you to conveniently complete various processes and requirements. Payment on account, ID verification, and the completion of Protocol Forms will all be facilitated through the ProConvey software. This user-friendly tool ensures secure and efficient handling of these tasks, providing you with a seamless experience throughout your property transaction journey.</p><p><strong>Payment on Account</strong><br>To ensure a smooth and efficient handling of your property transaction, we require payment on account of <span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.payment_on_account_amount }}</span> towards the legal costs. The purpose of this payment is to cover initial expenses and secure our services on your behalf. Any unused portion of the payment will be credited towards your final invoice.</p><p><strong>ID Verification</strong><br>As part of our commitment to compliance and security, we employ digital ID verification methods to validate your identity. We kindly request your cooperation in providing the necessary documentation and participating in the ID verification process to ensure a secure and reliable transaction.</p><p>Please review this Client Care Letter carefully, and if you agree to the terms and conditions outlined, please sign below as confirmation of your acceptance.</p><p>Should you have any questions or require further clarification regarding the content of this letter, please feel free to contact us by telephone. We are available to discuss and address any concerns you may have.</p><p>Yours sincerely,<br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ property.fee_earner }}</span><br><span style="background-color: #BF4A8E; padding: 1.5px 6px 1.5px 6px; border-radius: 4px; color: white;">{{ conveyancer.name }}</span></p></div>
  `

  const TemplateHeader: HeaderTypes = {
    Professional: ProfessionalTemplateHeader,
    Stylish: StylishTemplateHeader,
    Contemporary: ContemporaryTemplateHeader,
    Functional: FunctionalTemplateHeader,
    Minimalistic: MinimalisticTemplateHeader,
    Own: '',
  }

  const saleClassName = classNames('flex items-center min-w-[180px] gap-1 bg-white  px-2 text-base text-body leading-[1.4375rem] font-medium text-[#674186]', {
    'text-opacity-100': currentType === 0,
    'text-opacity-20': currentType !== 0,
  })

  const puchaseClassName = classNames('flex items-center min-w-[180px] gap-1 bg-white  px-2 text-base text-body leading-[1.4375rem] font-medium text-[#674186]', {
    'text-opacity-100': currentType === 1,
    'text-opacity-20': currentType !== 1,
  })

  const remortgageClassName = classNames('flex items-center min-w-[180px] gap-1 bg-white  px-2 text-base text-body leading-[1.4375rem] font-medium text-[#674186]', {
    'text-opacity-100': currentType === 2,
    'text-opacity-20': currentType !== 2,
  })

  const conditionClassName = classNames('flex items-center min-w-[180px] gap-1 bg-white  px-2 text-base text-body leading-[1.4375rem] font-medium text-[#674186]', {
    'text-opacity-100': currentType === 3,
    'text-opacity-20': currentType !== 3,
  })

  return (
    <>
      <Card>
        <Card.Header>
          <H3>Onboarding Letters</H3>
          <hr className="mt-[1.4063rem] mb-6 -mx-auto" />
          <div className="flex flex-col justify-between lg:flex-row">
            {/* Desktop View */}
            <div className="w-full max-w-[58em] flex-col hidden lg:flex">
              <p className="text-base text-body">Generate client onboarding letters, on autopilot</p>
              <br />
              <p className="text-base text-body text-opacity-80">Choose a template or paste your own letter and ProConvey will dynamically fill in the relevant fields for each new client (i.e client name, property address, fees).</p>
              <br />
              <p className="text-base text-body text-opacity-80">ProConvey will generate onboarding letters automatically for each client and party in the transaction to eSign.</p>
            </div>

            {/* Mobile View + Image */}
            <p className="text-base lg:hidden text-body">Generate client onboarding letters, on autopilot</p>
            <br className="lg:hidden" />
            {/* <OnboardingLettersImage className="w-full max-w-[244px] h-full max-h-[180px] lg:mr-[2.4375rem] mx-auto" /> */}
            <br className="lg:hidden" />
            <p className="text-base lg:hidden text-body text-opacity-80">Choose a template or paste your own letter and ProConvey will dynamically fill in the relevant fields for each new client (i.e client name, property address, fees).</p>
            <br className="lg:hidden" />
            <p className="text-base lg:hidden text-body text-opacity-80">ProConvey will generate onboarding letters automatically for each client and party in the transaction to eSign.</p>
          </div>
          <br />
          <br />
          <ul className="flex flex-wrap gap-[1.875rem]">
            <li className="flex flex-row">
              { !completedSaleLetter ? <UnCheckedIconSVG /> : <CheckedIconSVG /> }
              <p className={saleClassName}>Client care letter (Sale)</p>
            </li>
            <li className="flex flex-row">
              { !completedPurchaseLetter ? <UnCheckedIconSVG /> : <CheckedIconSVG /> }
              <p  className={puchaseClassName}>Client care letter (Purchase)</p>
            </li>
            <li className="flex flex-row">
              { !completedRemortgageLetter ? <UnCheckedIconSVG /> : <CheckedIconSVG /> }
              <p  className={remortgageClassName}>Client care letter (Remortgage)</p>
            </li>
            <li className="flex flex-row">
              { !completedTermsLetter ? <UnCheckedIconSVG /> : <CheckedIconSVG /> }
              <p  className={conditionClassName}>Terms and Conditions</p>
            </li>
          </ul>
          <hr className="mt-[1.4063rem] mb-6 -mx-auto" />
          <br />
          <br />
          { currentType < 3 &&
            <>
              <div className="flex flex-wrap gap-6 p-10">
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Professional' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Professional')}>
                  <p className="font-bold text-center">Professional</p>
                  <OnboardingLettersProfessional className="w-full max-w-[93px] h-full max-h-[132px] mx-auto" />
                </div>
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Stylish' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Stylish')}>
                  <p className="font-bold text-center">Stylish</p>
                  <OnboardingLettersStylish className="w-full max-w-[93px] h-full max-h-[132px] mx-auto" />
                </div>
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Contemporary' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Contemporary')}>
                  <p className="font-bold text-center">Contemporary</p>
                  <OnboardingLettersContemporary className="w-full max-w-[93px] h-full max-h-[132px] mx-auto" />
                </div>
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Functional' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Functional')}>
                  <p className="font-bold text-center">Functional</p>
                  <OnboardingLettersFunctional className="w-full max-w-[93px] h-full max-h-[132px] mx-auto" />
                </div>
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Minimalistic' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Minimalistic')}>
                  <p className="font-bold text-center">Minimalistic</p>
                  <OnboardingLettersMinimalistic className="w-full max-w-[93px] h-full max-h-[132px] mx-auto" />
                </div>
                <div className={`flex flex-col flex-1 border rounded-lg border-solid p-5 gap-3 hover:cursor-pointer ${selectedTemplate === 'Own' ? 'border-[#674186] border-2' : ''}`} onClick={() => handleSelectStyle('Own')}>
                  <p className="font-bold text-center">Add your own</p>
                  <div className="border p-10 flex justify-center"><h1 className="text-2xl">+</h1></div>
                </div>
              </div>
              <br />
              <div className="flex items-center justify-center">
                { selectedTemplate && <Button variant="secondary" size="small" className="text-opacity-100" onClick={handleApplyAll}>Apply template to all</Button> || <Button variant="secondary" size="small" className="text-opacity-25">Apply template to all</Button> }
              </div>
            </>
          }
        </Card.Header>
        <Card.Body padContent={false}>
          <div className="relative">
            <div className="absolute z-10 top-1 right-4">
              <Button size="small" loading={fetching} onClick={handleSave}>Save</Button>
            </div>
            {
              currentType === 0 &&
              <HtmlEditor content={templateContents} onChange={v => setValue('client_care_letter_sale', v)} />
            }
            {
              currentType === 1 &&
              <HtmlEditor content={templateContents} onChange={v => setValue('client_care_letter_purchase', v)} />
            }
            {
              currentType === 2 &&
              <HtmlEditor content={templateContents} onChange={v => setValue('client_care_letter_remortgage', v)} />
            }
            {
              currentType >= 3 &&
              <HtmlEditor content={templateContents} onChange={v => setValue('terms_and_conditions', v)} />
            }
          </div>
        </Card.Body>
      </Card>
      <Modal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)}>
        <Modal.Title>Alert</Modal.Title>
        <Modal.Content>
          You are able to edit the template client care letter. Would you like to use the template as is without any changes?
        </Modal.Content>
        <Modal.Footer>
          <Button size="small" variant="secondary" onClick={() => setIsModalOpen(false)}>No, I will change the template</Button>
          <Button size="small" onClick={() => handleSubmit()}>Yes, I will use it as is</Button>
        </Modal.Footer>
      </Modal>
    </>
  )
}

export default OnboardingLetters
