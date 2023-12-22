import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Label from '@proconvey/ui/src/components/Form/Label'
import Link from 'next/link'
import SetupLayout from 'layouts/SetupLayout'
import AnswerRadioGroup from '@proconvey/ui/src/components/AnswerRadioGroup'
import { useEffect, useState } from 'react'
import AddressFinder from '@proconvey/ui/src/components/AddressFinder'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { useMutation, useQuery } from 'urql'
import { LogoUploadIcon } from '@proconvey/ui/src/icons'
import { OnboardingBusinessImage } from '@proconvey/ui/src/images'
import Vapor from 'laravel-vapor'
import Cookies from 'js-cookie'
import { useRouter } from 'next/router'
import useErrorHandler from 'hooks/useErrorHandler'
import Alert from '@proconvey/ui/src/components/Alert'
import ProtectedLayout from 'layouts/ProtectedLayout'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'
import { CreateConveyancerInput } from 'gql/graphql'
import { graphql } from 'gql'

type CreateConveyancerProps = Omit<CreateConveyancerInput, 'logo_image'> & {
  logo_image: File[]
}

export default function BusinessSetup () {
  const router = useRouter()
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const errorHandler = useErrorHandler()

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

  const { register, handleSubmit, setError, setValue, watch, control, reset: resetConveyancer, formState: { errors }, clearErrors } = useForm<CreateConveyancerProps>({
    defaultValues: {
      type: conveyancer?.me?.conveyancer?.type ?? '',
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

  const [_, conveyancerMutation] = useMutation(graphql(`
    mutation createConveyancer ($input: CreateConveyancerInput!) {
      createConveyancer(input: $input) {
        id,
        name,
        type,
        company_number,
        sra_clc_number,
        address {
          line_1,
          line_2,
          city,
          postcode,
        },
        trading_name
        vat_number
        website
        location
        telephone_number
        email_address
      }
    }
  `))

  useEffect(() => {
    if (conveyancer) {
      resetConveyancer({
        type: conveyancer?.me?.conveyancer?.type ?? '',
        name: conveyancer?.me?.conveyancer?.name,
        company_number: conveyancer?.me?.conveyancer?.company_number ?? undefined,
        sra_clc_number: conveyancer?.me?.conveyancer?.sra_clc_number,
        trading_name: conveyancer?.me?.conveyancer?.trading_name ?? '',
        vat_number: conveyancer?.me?.conveyancer?.vat_number ?? '',
        website: conveyancer?.me?.conveyancer?.website ?? '',
        location: conveyancer?.me?.conveyancer?.location ?? '',
        telephone_number: conveyancer?.me?.conveyancer?.telephone_number ?? '',
        email_address: conveyancer?.me?.conveyancer?.email_address ?? '',
        address: {
          line_1: conveyancer?.me?.conveyancer?.address?.line_1 ?? '',
          line_2: conveyancer?.me?.conveyancer?.address?.line_2 ?? '',
          city: conveyancer?.me?.conveyancer?.address?.city ?? '',
          postcode: conveyancer?.me?.conveyancer?.address?.postcode ?? '',
        },
      })
    }
  }, [conveyancer, resetConveyancer])
  const onSubmit: SubmitHandler<CreateConveyancerProps> = async (form) => {
    setIsLoading(true)

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

    const input: CreateConveyancerInput = {
      name: form.name,
      type: form.type,
      company_number: form.company_number,
      sra_clc_number: form.sra_clc_number,
      address: {
        line_1: form.address?.line_1,
        line_2: form.address?.line_2,
        city: form.address?.city,
        postcode: form.address?.postcode,
      },
      trading_name: form.trading_name,
      vat_number: form.vat_number,
      website: form.website,
      location: form.location,
      telephone_number: form.telephone_number,
      email_address: form.email_address,
    }

    if (upload) {
      input.logo_image = {
        key: upload.key,
        extension: upload.extension,
      }
    }

    const response = await conveyancerMutation({
      input,
    })

    if (response.error) {
      errorHandler(response.error, setError)
      setIsLoading(false)
      return
    }

    router.push('/register/onboarding-letters')
  }

  return (
    <>
      <NextSeo
        title="Business Setup"
      />
      <ProtectedLayout>
        <SetupLayout currentStep={2}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Create your account</H1>
            </div>


            <Form onSubmit={(e) => {
              e.preventDefault()
              clearErrors()
              handleSubmit(onSubmit)(e)
            }}>
              <div className="pb-[20px] bg-white rounded-[0.625rem]">
                <div className="py-[1.5rem] px-[1.25rem]">
                  <H3>Business Information</H3>
                </div>

                <hr />
                <div className="flex flex-col justify-between lg:flex-row mt-[2.5rem] mb-[3.5rem] mx-[1.25rem]">
                  {/* Desktop View */}
                  <div className="w-full max-w-[40.5rem] flex-col hidden lg:flex">
                    <p className="text-base text-body">Please enter your company information</p>
                    <br />
                    <p className="text-base text-body text-opacity-80">Enter the address for your branch. If you have more than one branch, you will need to create a new ProConvey account for each branch with an admin (main user) for each branch.</p>
                  </div>

                  {/* Mobile View + Image */}
                  <p className="text-base lg:hidden text-body">Please enter your company information</p>
                  <br className="lg:hidden" />
                  <OnboardingBusinessImage className="w-full max-w-[320px] h-full max-h-[180px] lg:mr-[2.4375rem] mx-auto" />
                  <br className="lg:hidden" />
                  <p className="text-base lg:hidden text-body text-opacity-80">Enter the address for your branch. If you have more than one branch, you will need to create a new ProConvey account for each branch with an admin (main user) for each branch.</p>
                </div>
                <div className="mt-[1.5rem] mx-[1.25rem] flex flex-col gap-5">

                  <Form.Group>
                    <div className="flex flex-col-reverse w-full gap-5 xl:flex-row">
                      <div className="flex flex-col">
                        <Label>Are you a sole practice or a company?</Label>
                        <AnswerRadioGroup
                          onChange={(selection) => setValue('type', selection.toString())}
                          error={errors.type?.message}
                          selected={watch('type') ?? undefined}
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
                      error={errors.name?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.name || undefined}
                      {...register('name')}
                    />
                  </Form.Group>
                  <Form.Group>
                    <Form.Input
                      label="Registered company number"
                      placeholder="e.g. 12345678"
                      error={errors.sra_clc_number?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.company_number || undefined}
                      {...register('company_number')}
                    />
                    <Form.Input
                      label="Company SRA/CLC number"
                      placeholder="e.g. 12345678"
                      error={errors.sra_clc_number?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.sra_clc_number || undefined}
                      {...register('sra_clc_number')}
                    />
                  </Form.Group>
                  <Form.Group>
                    <Form.Input
                      label="Trading name"
                      placeholder="e.g. Best company"
                      error={errors.trading_name?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.trading_name || undefined}
                      {...register('trading_name')}
                      SubLabel="Optional"
                    />
                    <Form.Input
                      label="VAT number"
                      placeholder="e.g. GB12345678"
                      error={errors.vat_number?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.vat_number || undefined}
                      {...register('vat_number')}
                      SubLabel="Optional"
                    />
                  </Form.Group>
                  <Form.Group>
                    <Form.Input
                      label="Website address"
                      placeholder="www.mycompany.co.uk"
                      error={errors.website?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.website || undefined}
                      {...register('website')}
                      SubLabel="Optional"
                    />
                    <Form.Input
                      label="Branch location"
                      placeholder="e.g. Notting Hill"
                      error={errors.location?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.location || undefined}
                      {...register('location')}
                      SubLabel="Optional"
                    />
                  </Form.Group>
                  <Form.Group>
                    <Form.Input
                      label="Branch telephone number"
                      placeholder="+44 ---- -- -- --"
                      error={errors.telephone_number?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.telephone_number || undefined}
                      {...register('telephone_number')}
                    />
                    <Form.Input
                      label="Branch email address"
                      placeholder="e.g. info@mycompany.co.uk"
                      error={errors.email_address?.message}
                      defaultValue={conveyancer?.me?.conveyancer?.email_address || undefined}
                      {...register('email_address')}
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
                    control={control}
                    render={({ field }) => {
                      return (
                        <ImageUpload
                          onChange={field.onChange}
                          error={errors.logo_image?.message}
                          defaultPreviewUrl={conveyancer?.me?.conveyancer?.logo_image?.url}
                          placeholder={<div className="w-[8.75rem] h-20 flex items-center justify-center">
                            <LogoUploadIcon className="w-10 h-10 text-primary" />
                          </div>} type="logo_image"                        />
                      )
                    }}
                  />

                  {
                    Object.keys(errors).length > 0 &&
                    <Alert variant="danger">
                      There was a problem updating your details. Please try again.
                    </Alert>
                  }

                </div>
              </div>

              <div className="flex justify-between mt-[2.5rem]">
                <Link href="/register/profile">
                  <Button variant="outlined">Back</Button>
                </Link>

                <Button type="submit" loading={isLoading}>Next</Button>
              </div>
            </Form>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>
    </>
  )
}
