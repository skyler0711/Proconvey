import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Form from '@proconvey/ui/src/components/Form'
import Label from '@proconvey/ui/src/components/Form/Label'
import Button from '@proconvey/ui/src/components/Button'
import Vapor from 'laravel-vapor'
import { useEffect, useState } from 'react'
import AddressFinder from '@proconvey/ui/src/components/AddressFinder'
import { gql, useMutation, useQuery } from 'urql'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { LogoUploadIcon } from '@proconvey/ui/src/icons'
import Cookies from 'js-cookie'
import { graphql } from 'gql'
import toast from 'react-hot-toast'
import useErrorHandler from 'hooks/useErrorHandler'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'
import { ConveyancerType } from 'types/enums/ConveyancerType'
import AnswerRadioGroup from '@proconvey/ui/src/components/AnswerRadioGroup'

type UpdateConveyancerDetailsProps = {
  name: string
  company_number: string
  sra_clc_number: string
  logo_image: File[]
  address: {
    line_1: string
    line_2?: string | null
    city: string
    postcode: string
  }
  trading_name: string
  vat_number: string
  website: string
  location: string
  telephone_number: string
  email_address: string
}

export default function Business () {
  const [isSaving, setIsSaving] = useState(false)
  const errorHandler = useErrorHandler()


  const [{ data: conveyancer }, refetch] = useQuery({
    query: graphql(`
    query settingsBusinessConveyancer {
      me {
        id
        conveyancer {
          id
          name
          type
          sra_clc_number
          team_members {
            id
            first_name
            last_name
            email
            phone
            invite_code_sent_at
            job_role
            email_verified_at
          }

          team_member_count
          company_number
          address {
            id
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


  const {
    register: conveyancerRegister,
    handleSubmit: updateConveyancerSubmit,
    control: conveyancerControl,
    setValue,
    reset: resetConveyancer,
    setError,
    clearErrors: clearConveyancerErrors,
    formState: { errors },

  } = useForm<UpdateConveyancerDetailsProps>({
    defaultValues: {
      name: conveyancer?.me?.conveyancer?.name ?? '',
      company_number: conveyancer?.me?.conveyancer?.company_number ?? '',
      sra_clc_number: conveyancer?.me?.conveyancer?.sra_clc_number ?? '',
      address: {
        line_1: conveyancer?.me?.conveyancer?.address?.line_1 ?? '',
        line_2: conveyancer?.me?.conveyancer?.address?.line_2 ?? '',
        city: conveyancer?.me?.conveyancer?.address?.city ?? '',
        postcode: conveyancer?.me?.conveyancer?.address?.postcode ?? '',
      },
      trading_name: conveyancer?.me?.conveyancer?.trading_name ?? '',
      vat_number: conveyancer?.me?.conveyancer?.vat_number ?? '',
      website: conveyancer?.me?.conveyancer?.website ?? '',
      location: conveyancer?.me?.conveyancer?.location ?? '',
      telephone_number: conveyancer?.me?.conveyancer?.telephone_number ?? '',
      email_address: conveyancer?.me?.conveyancer?.email_address ?? '',
    },
  })

  useEffect(() => {
    if (conveyancer) {
      resetConveyancer({
        name: conveyancer?.me?.conveyancer?.name,
        company_number: conveyancer?.me?.conveyancer?.company_number ?? undefined,
        sra_clc_number: conveyancer?.me?.conveyancer?.sra_clc_number,
        address: {
          line_1: conveyancer?.me?.conveyancer?.address?.line_1,
          line_2: conveyancer?.me?.conveyancer?.address?.line_2,
          city: conveyancer?.me?.conveyancer?.address?.city,
          postcode: conveyancer?.me?.conveyancer?.address?.postcode,
        },
        trading_name: conveyancer?.me?.conveyancer?.trading_name ?? '',
        vat_number: conveyancer?.me?.conveyancer?.vat_number ?? '',
        website: conveyancer?.me?.conveyancer?.website ?? '',
        location: conveyancer?.me?.conveyancer?.location ?? '',
        telephone_number: conveyancer?.me?.conveyancer?.telephone_number ?? '',
        email_address: conveyancer?.me?.conveyancer?.email_address ?? '',
      })
    }
  }, [conveyancer, resetConveyancer])

  const [_, updateConveyancerMutation] = useMutation(gql(`
  mutation updateConveyancerDetails($input: UpdateConveyancerDetailsInput!) {
    updateConveyancerDetails(input: $input) {
      name
      company_number
      sra_clc_number
      logo_image {
        id
        url
      }
      address {
        line_1
        line_2
        city
        postcode
      }
      trading_name
      vat_number
      website
      location
      telephone_number
      email_address
    }
  }
`))

  const onSubmit: SubmitHandler<UpdateConveyancerDetailsProps> = async (form) => {

    setIsSaving(true)
    const token = Cookies.get('XSRF-TOKEN')

    let upload
    if (form.logo_image) {
      upload = await Vapor.store(form.logo_image[0], {
        baseURL: process.env.NEXT_PUBLIC_API_ENDPOINT,
        options: {
          withCredentials: 'include',
        },
        headers: {
          'X-XSRF-TOKEN': token,
        },
      })
    }

    const result = await updateConveyancerMutation({
      input: {
        name: form.name,
        company_number: conveyancer?.me?.conveyancer?.type === ConveyancerType.SolePractitioner
          ? undefined
          : form.company_number ?? '',
        sra_clc_number: form.sra_clc_number,
        address: {
          line_1: form.address?.line_1 ?? '',
          line_2: form.address?.line_2 ?? '',
          city: form.address?.city ?? '',
          postcode: form.address?.postcode ?? '',
        },
        ...upload ? {
          logo_image: {
            key: upload.key,
            extension: upload.extension,
          },
        } : {
          logo_image: null,
        },
        trading_name: form.trading_name,
        vat_number: form.vat_number,
        website: form.website,
        location: form.location,
        telephone_number: form.telephone_number,
        email_address: form.email_address,
      },
    })

    if (result.error) {
      setIsSaving(false)
      toast.error('Something went wrong, please try again')
      errorHandler(result.error, setError)
      return
    } else {
      toast.success('Your Business Information have been updated')
    }

    setIsSaving(false)
  }


  return (
    <>
      <NextSeo
        title="Business settings"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[3.125rem]">
              <div className="mb-[1.875rem] flex justify-between sm:items-center items-start flex-col sm:flex-row gap-5">
                <H1>Business settings</H1>
                <Button loading={isSaving} onClick={updateConveyancerSubmit(onSubmit)}>Save Changes</Button>
              </div>

              <div className="flex flex-col gap-5 mb-5">
                <div className="flex flex-col justify-between w-full px-5 py-6 bg-white rounded-xl">
                  <H3>Business Information</H3>
                  <hr className="my-6" />

                  <Form>
                    <Form.Group>
                      <div className="flex flex-col-reverse w-full gap-5 xl:flex-row">
                        <div className="flex flex-col">
                          <Label>Status</Label>
                          <AnswerRadioGroup
                            selected={conveyancer?.me?.conveyancer?.type ?? undefined}
                          >
                            <AnswerRadioGroup.Radio value="sole_practitioner">Sole Practice</AnswerRadioGroup.Radio>
                            <AnswerRadioGroup.Radio value="company">Company</AnswerRadioGroup.Radio>
                          </AnswerRadioGroup>
                        </div>
                      </div>
                    </Form.Group>
                    <Form.Group>
                      <Form.Input
                        label="Registered company name"
                        placeholder="e.g. Best company"
                        defaultValue={conveyancer?.me?.conveyancer?.name}
                        error={errors.name?.message}
                        {...conveyancerRegister('name')}
                      />
                    </Form.Group>

                    <Form.Group>
                      <Form.Input
                        label="Registered company number"
                        placeholder="e.g. 12345678"
                        error={errors.name?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.company_number || undefined}
                        {...conveyancerRegister('company_number')}>
                      </Form.Input>

                      <Form.Input
                        label="Company SRA/CLC number"
                        placeholder="e.g. 12345678"
                        error={errors.sra_clc_number?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.sra_clc_number || undefined}
                        {...conveyancerRegister('sra_clc_number')}
                      />
                    </Form.Group>

                    <Form.Group>
                      <Form.Input
                        label="Trading name"
                        placeholder="e.g. Best company"
                        error={errors.trading_name?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.trading_name || undefined}
                        {...conveyancerRegister('trading_name')}
                        SubLabel="Optional"
                      />

                      <Form.Input
                        label="VAT number"
                        placeholder="e.g. GB12345678"
                        error={errors.vat_number?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.vat_number || undefined}
                        {...conveyancerRegister('vat_number')}
                        SubLabel="Optional"
                      />
                    </Form.Group>

                    <Form.Group>
                      <Form.Input
                        label="Website address"
                        placeholder="www.mycompany.co.uk"
                        error={errors.website?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.website || undefined}
                        {...conveyancerRegister('website')}
                        SubLabel="Optional"
                      />

                      <Form.Input
                        label="Branch location"
                        placeholder="e.g. Notting Hill"
                        error={errors.location?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.location || undefined}
                        {...conveyancerRegister('location')}
                        SubLabel="Optional"
                      />
                    </Form.Group>

                    <Form.Group>
                      <Form.Input
                        label="Branch telephone number"
                        placeholder="+44 ---- -- -- --"
                        error={errors.telephone_number?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.telephone_number || undefined}
                        {...conveyancerRegister('telephone_number')}
                      />

                      <Form.Input
                        label="Branch email address"
                        placeholder="e.g. info@mycompany.co.uk"
                        error={errors.email_address?.message}
                        defaultValue={conveyancer?.me?.conveyancer?.email_address || undefined}
                        {...conveyancerRegister('email_address')}
                      />
                    </Form.Group>

                    <AddressFinder
                      label="Branch Address"
                      onChange={(address) => setValue('address', address)}
                      error={errors.address}
                      address={conveyancer?.me?.conveyancer?.address ?? undefined}
                    />

                    <Label>Logo</Label>

                    <Controller
                      name="logo_image"
                      control={conveyancerControl}
                      render={({ field }) => {
                        return (
                          <ImageUpload
                            onChange={field.onChange}
                            onRemove={() =>
                              field.onChange(null)}
                            error={errors.logo_image?.message}
                            type="logo_image"
                            defaultPreviewUrl={conveyancer?.me?.conveyancer?.logo_image?.url}
                            placeholder={
                              <div className="w-[8.75rem] h-20 flex items-center justify-center">
                                <LogoUploadIcon className="w-10 h-10 text-primary" />
                              </div>
                            }
                          />
                        )
                      }}
                    />

                    <AddressFinder
                      error={errors.address}
                      label="Business Address"
                      {...conveyancerRegister('address')}
                      address={conveyancer?.me?.conveyancer?.address ?? undefined}
                      onChange={address => {
                        clearConveyancerErrors('address')
                        setValue('address', address)
                      }} />
                  </Form>
                </div>
              </div>
            </div>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
