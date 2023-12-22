import ProtectedLayout from 'layouts/ProtectedLayout'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Form from '@proconvey/ui/src/components/Form'
import Label from '@proconvey/ui/src/components/Form/Label'
import AnswerRadioGroup from '@proconvey/ui/src/components/AnswerRadioGroup'
import Button from '@proconvey/ui/src/components/Button'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { useMutation, useQuery } from 'urql'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { graphql } from 'gql'
import { InviteAddress, PropertyType } from 'gql/graphql'
import { PropertyIcon, PropertyPersonIconPink, RemortgageIcon, WarningIcon } from '@proconvey/ui/src/icons'
import useErrorHandler from 'hooks/useErrorHandler'
import { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import { useRouter } from 'next/router'
import { NextSeo } from 'next-seo'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'

type InviteNewClientProps = {
  first_name: string
  last_name: string
  email: string
  case_reference: string
  letters_required: boolean
  id_check_required: boolean
  sof_check_required: boolean
  payment_required: boolean
  sale_price: string
  conveyancing_fee: string
  fee_earner_id: string
  address: Partial<InviteAddress>
  type: PropertyType
  payment_amount: number
}

export default function InviteNewClient () {
  const router = useRouter()
  const errorHandler = useErrorHandler()

  const [pauseAddressFetch, setPauseAddressFetch] = useState(true)

  const [isLoading, setIsLoading] = useState(false)
  const [propertyType, setPropertyType] = useState<PropertyType | undefined>(undefined)

  const {
    register,
    handleSubmit,
    setValue,
    watch,
    control,
    setError,
    clearErrors,
    formState: { errors },
  } = useForm<InviteNewClientProps & { search_address: string | null}>({
    defaultValues: {
      search_address: null,
    },
  })

  const [_, inviteNewClientMutation] = useMutation(graphql(`
    mutation inviteNewClient($input: InviteNewClientInput!) {
        inviteNewClient(input: $input) {
          id
        }
      }
  `))

  const [{ data: teamMembers }] = useQuery({
    query: graphql(`
    query teamMembersNameQuery {
      me {
        id
        conveyancer {
          id
          team_members {
            id
            first_name
            last_name
          }
        }
      }
    }
  `),
  })

  const QUERY_ADDRESS = graphql(`
    query GetAddress($input: SearchAddress!) {
      getAddressFromOS2API(input: $input) {
        line_1
        line_2
        city
        postcode
        uprn
      }
    }
  `)

  const [addressSearchResults] = useQuery({
    pause: pauseAddressFetch,
    query: QUERY_ADDRESS,
    variables: {
      input: {
        address: watch('search_address') ?? '',
      },
    },
  })

  const onSubmit: SubmitHandler<InviteNewClientProps> = async (form) => {
    clearErrors()
    setIsLoading(true)

    const response = await inviteNewClientMutation({
      input: {
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        case_reference: form.case_reference,
        type: propertyType as string,
        sale_price: form.sale_price === '' ? null : form.sale_price,
        conveyancing_fee: form.conveyancing_fee === '' ? null : form.conveyancing_fee,
        fee_earner_id: form.fee_earner_id,
        address: {
          line_1: form.address?.line_1 ?? '',
          line_2: form.address?.line_2 ?? '',
          city: form.address?.city ?? '',
          postcode: form.address?.postcode ?? '',
          uprn: form.address?.uprn ?? '',
        },
        letters_required: form.letters_required,
        id_check_required: form.id_check_required,
        sof_check_required: form.sof_check_required,
        payment_required: form.payment_required,
        payment_amount: form.payment_amount === 0 ? null : (Number(form.payment_amount) * 100),

      },
    })

    if (response.error) {
      toast.error('There was an error inviting the client')
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      toast.success('Client invited successfully')
      setIsLoading(false)
      router.push(`/clients/${response?.data?.inviteNewClient?.id}`)
    }
  }

  useEffect(() => {
    // Update the pauseAddressFetch flag to prevent address fetching on each render
    if (!addressSearchResults.fetching && !pauseAddressFetch) {
      setPauseAddressFetch(true)
      setValue('search_address', null)

      if (addressSearchResults?.data?.getAddressFromOS2API) {
        setValue('address', addressSearchResults.data.getAddressFromOS2API)
      }
    }
  }, [addressSearchResults, pauseAddressFetch])

  return (
    <>
      <NextSeo
        title="Invite New Client"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <Form onSubmit={(e) => {
              e.preventDefault()
              clearErrors()
              handleSubmit(onSubmit)(e)
            }}>
              <div className="mb-[1.875rem] flex justify-between sm:items-center items-start flex-col sm:flex-row gap-5">
                <H1>Invite New Client</H1>

                {
                  propertyType !== undefined && (
                    <Button type="submit" loading={isLoading}>Invite client</Button>
                  )
                }
              </div>

              {
                propertyType === undefined ? (
                  <div className="bg-white rounded-[0.625rem] border border-primary border-opacity-10">
                    <H3 className="py-[1.4688rem] px-5">
                      Case type
                    </H3>

                    <hr className="mb-[1.125rem]" />

                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 px-5 gap-5 mb-[1.125rem]">
                      <div className="rounded-[0.625rem] border border-primary border-opacity-10 flex flex-col">
                        <H3 className="mt-5 text-center">
                          Sale
                        </H3>
                        <PropertyIcon className="m-auto my-[1.875rem] w-[130px] h-[130px]" />
                        <Button
                          variant="primary"
                          className="mt-auto w-[129px] mx-auto mb-5"
                          onClick={() => {
                            setPropertyType(PropertyType.Sale)
                          }}
                        >
                          Invite client
                        </Button>
                      </div>

                      <div className="rounded-[0.625rem] border border-primary border-opacity-10 flex flex-col">
                        <H3 className="mt-5 text-center">
                          Purchase
                        </H3>
                        <PropertyPersonIconPink className="m-auto my-[1.875rem] w-[130px] h-[130px]" />
                        <Button
                          variant="primary"
                          className="mt-auto w-[129px] mx-auto mb-5"
                          onClick={() => {
                            setPropertyType(PropertyType.Purchase)
                          }}
                        >
                          Invite client
                        </Button>
                      </div>

                      <div className="rounded-[0.625rem] border border-primary border-opacity-10 flex flex-col">
                        <H3 className="mt-5 text-center">
                          Remortgage
                        </H3>
                        <RemortgageIcon className="m-auto my-[1.875rem] w-[130px] h-[130px]" />
                        <Button
                          variant="primary"
                          className="mt-auto w-[129px] mx-auto mb-5"
                          onClick={() => {
                            setPropertyType(PropertyType.Remortgage)
                          }}
                        >
                          Invite client
                        </Button>
                      </div>
                    </div>

                  </div>
                ) : (
                  <div className="bg-white rounded-[0.625rem]">
                    <div className="flex items-center w-full gap-5 p-5">
                      <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                        <WarningIcon className="w-9 text-mull" />
                      </div>
                      <p className="text-base text-body">
                        This is the main user who will be responsible for completing the conveyancing pack. Other parties will be invited once the getting started form has been completed.
                      </p>
                    </div>

                    <div className="flex flex-col gap-5 p-5">
                      <Form.Group>
                        <Form.Input
                          label="Case Reference"
                          placeholder="Enter case reference"
                          error={errors.case_reference?.message}
                          {...register('case_reference')}
                        />

                        <Form.Input
                          label="Email"
                          placeholder="name@mail.com"
                          error={errors.email?.message}
                          {...register('email')}
                        />
                      </Form.Group>

                      <Form.Group>
                        <Form.Input
                          label="Client first name"
                          placeholder="Enter client first name"
                          error={errors.first_name?.message}
                          {...register('first_name')}
                        />

                        <Form.Input
                          label="Client last name"
                          placeholder="Enter client last name"
                          error={errors.last_name?.message}
                          {...register('last_name')}
                        />
                      </Form.Group>

                      <div className="mt-[15px]">
                        <Form as="div">
                          <Form.Group>
                            <Form.Input
                              label="Property Address"
                              placeholder="Start searching for your address"
                              {...register('search_address')}
                            />
                            <Button
                              type="button"
                              className="mt-auto h-min"
                              disabled={!watch('search_address')}
                              onClick={() => setPauseAddressFetch(false)}
                            >
                              Find Address
                            </Button>
                          </Form.Group>
                        </Form>
                      </div>
                      <div className="mt-[15px]">
                        <Form as="div">
                          <Form.Group className="grid grid-cols-1 lg:grid-cols-2">
                            <Form.Input
                              placeholder="Address Line 1"
                              error={errors.address?.line_1?.message}
                              {...register('address.line_1')}
                            />
                            <Form.Input
                              placeholder="Address Line 2 (Optional)"
                              error={errors.address?.line_2?.message}
                              {...register('address.line_2')}
                            />
                            <Form.Input
                              placeholder="Town/City"
                              error={errors.address?.city?.message}
                              {...register('address.city')}
                            />
                            <Form.Input
                              placeholder="Postcode"
                              error={errors.address?.postcode?.message}
                              {...register('address.postcode')}
                            />
                            <Form.Input
                              placeholder="UPRN"
                              error={errors.address?.uprn?.message}
                              {...register('address.uprn')}
                            />
                          </Form.Group>
                        </Form>
                      </div>
                    </div>

                    <div className="p-5">
                      <Label>Create Terms and Conditions and client care letter</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('letters_required', Boolean(selection))}
                        error={errors.letters_required?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                      {
                        watch('letters_required') && (
                          <div className="mt-9">
                            <Form.Group>
                              <Form.Input
                                label="Property sale price"
                                placeholder="eg: £500,000"
                                type="number"
                                error={errors.sale_price?.message}
                                {...register('sale_price')}
                              />
                              <Form.Input
                                label="Conveyancing fee (+VAT)"
                                placeholder="eg: £50"
                                type="number"
                                error={errors.conveyancing_fee?.message}
                                {...register('conveyancing_fee')}
                              />

                              <Controller
                                control={control}
                                name="fee_earner_id"
                                render={({ field }) => {
                                  const handleOnChange = (e: SelectOption) => {
                                    field.onChange(e.value)
                                  }
                                  return (
                                    <Form.Select
                                      placeholder="Select fee earner"
                                      label="Fee earner"
                                      onChange={handleOnChange}
                                      error={errors.fee_earner_id?.message}
                                      options={teamMembers?.me?.conveyancer?.team_members?.map((member) => ({
                                        text: member.first_name + ' ' + member.last_name,
                                        value: member.id,
                                      }))}
                                    />
                                  )
                                }}
                              />
                            </Form.Group>
                          </div>
                        )

                      }
                    </div>

                    <hr />

                    <div className="p-5">
                      <Label>Perform ID checks</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('id_check_required', Boolean(selection))}
                        error={errors.id_check_required?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </div>

                    <hr />

                    <div className="p-5">
                      <Label>Perform Source of funds checks</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('sof_check_required', Boolean(selection))}
                        error={errors.sof_check_required?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </div>

                    <hr />
                    <div className="p-5">
                      <Label>Take payment on account</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('payment_required', Boolean(selection))}
                        error={errors.payment_required?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </div>
                    {
                      watch('payment_required') && (
                        <div className="p-5">
                          <Form.Group>
                            <Form.Input
                              label="Enter Amount"
                              placeholder="eg: £500"
                              type="number"
                              error={errors.payment_amount?.message}
                              {...register('payment_amount')}
                            />
                          </Form.Group>
                        </div>
                      )
                    }
                  </div>
                )
              }
            </Form>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
