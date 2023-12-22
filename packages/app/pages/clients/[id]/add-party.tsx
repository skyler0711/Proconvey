import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import { WarningIcon } from '@proconvey/ui/src/icons'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import React, { useState } from 'react'
import Form from '@proconvey/ui/src/components/Form'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { useMutation, useQuery } from 'urql'
import useErrorHandler from 'hooks/useErrorHandler'
import { toast } from 'react-hot-toast'
import { graphql } from 'gql'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'
import { NextSeo } from 'next-seo'
import { useRouter } from 'next/router'
import { PropertyUserRole } from 'gql/graphql'
import Label from '@proconvey/ui/src/components/Form/Label'
import AnswerRadioGroup from '@proconvey/ui/src/components/AnswerRadioGroup'

const AddPartyPage = () => {

  type AddNewPartyInputProps = {
    owner_first_name: string
    owner_last_name: string
    owner_email: string
    representation: string
    representing_owner: string
    representative_first_name: string
    representative_last_name: string
    representative_email: string
    first_name: string
    last_name: string
    email: string
    party_type: string
    owner_type: string
    client_id: string
    id_check: boolean
  }

  const errorHandler = useErrorHandler()
  const router = useRouter()

  const { register, handleSubmit, watch, control, setError, clearErrors, setValue, formState: { errors } } = useForm<AddNewPartyInputProps>()
  const [selectedTypes, setSelectedTypes] = useState<string[]>([])

  const [{ fetching }, addNewPartyMutation] = useMutation(graphql(`
    mutation addNewParty($input: AddNewPartyInput!) {
      addNewParty(input: $input)
      }
  `))

  const [{ data }] = useQuery({
    query: graphql(`
    query getPropertyUsers($id: ID!) {
      property(id: $id) {
        id
        letters_required
        type
        users {
          id
          email
          first_name
          last_name
          pivot {
            ...on PropertyUserPivot {
              role
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


  const onSubmit: SubmitHandler<AddNewPartyInputProps> = async (form) => {
    clearErrors()
    const response = await addNewPartyMutation({
      input: {
        party_type: form.party_type,
        owner_type: form.owner_type,
        owner_first_name: form.owner_first_name,
        owner_last_name: form.owner_last_name,
        owner_email: form.owner_email,
        representation: form.representation,
        representative_first_name: form.representative_first_name,
        representative_last_name: form.representative_last_name,
        representative_email: form.representative_email,
        first_name: form.first_name,
        last_name: form.last_name,
        email: form.email,
        client_id: router?.query?.id as string,
        id_check: form.id_check,
      },
    })

    if (response.error) {
      errorHandler(response.error, setError)
    } else {
      toast.success('Party has been added successfully')
      router.push(`/clients/${router?.query?.id}`)
    }
  }

  return (
    <>
      <NextSeo
        title="Add new party"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="ml-[1.875rem]">
              <div className="md:mb-[1.875rem] flex justify-between">
                <H1>Add new party</H1>
                <Button onClick={(e) => {
                  e.preventDefault()
                  clearErrors()
                  handleSubmit(onSubmit)(e)
                }} loading={fetching} variant="primary">Invite party</Button>
              </div>

              <div className="flex items-center w-full gap-5 p-5 bg-white rounded-[10px]">
                <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                  <WarningIcon className="w-9 text-mull" />
                </div>
                <p className="text-base text-body">
                  Add the details of the party you would like to add to the case. ProConvey will ask them further questions to obtain relevant information and supporting documents.
                </p>
              </div>


              <div className="bg-white rounded-[10px] p-5 mt-5 gap-y-8 flex flex-col">
                <Controller
                  control={control}
                  name="party_type"
                  render={({ field }) => {
                    const handleOnChange = (e: SelectOption) => {
                      field.onChange(e.value)
                    }
                    return (
                      <Form.Select
                        placeholder="Select party type"
                        label="Party Type"
                        error={errors.party_type?.message}
                        onChange={handleOnChange}
                        options={
                          [
                            { text: 'Owner', value: 'owner' },
                            { text: 'Attorney', value: 'attorney' },
                            { text: 'Deputy', value: 'deputy' },
                            { text: 'Executor', value: 'executor' },
                          ]
                        }
                      />
                    )
                  }}
                />
                {/* Party type Owner */}
                {
                  watch('party_type') === 'owner' && (
                    <>
                      <Controller
                        control={control}
                        name="owner_type"
                        render={({ field }) => {
                          const handleOnChange = (e: SelectOption) => {
                            field.onChange(e.value)
                          }
                          return (
                            <Form.Select
                              placeholder="Select owner type"
                              label="Owner type"
                              error={errors.owner_type?.message}
                              onChange={handleOnChange}
                              options={
                                [
                                  { text: 'Individual', value: 'individual' },
                                  { text: 'Company', value: 'company' },
                                ]
                              }
                            />
                          )
                        }}
                      />
                    </>
                  )
                }

                {
                  watch('owner_type') === 'company' && (
                    <>
                      <div className="flex items-center w-full gap-5 bg-white rounded-[10px]">
                        <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                          <WarningIcon className="w-9 text-mull" />
                        </div>
                        <p className="text-base text-body">
                          Please add the details of the person who is responsible for providing the company’s information. The representative will be invited to ProConvey to add the new owners details as well as their own.
                        </p>
                      </div>
                      <Form.Group>
                        <Form.Input
                          label="First name"
                          placeholder="First name"
                          {...register('first_name')}
                          error={errors.first_name?.message}
                        />
                        <Form.Input
                          label="Last name"
                          placeholder="Last name"
                          {...register('last_name')}
                          error={errors.last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Email"
                        placeholder="Email"
                        {...register('email')}
                        error={errors.email?.message}
                      />
                      <Label>ID and AML verification</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('id_check', Boolean(selection))}
                        error={errors.id_check?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </>
                  )
                }
                {
                  watch('owner_type') === 'individual' && (
                    <>
                      <Controller
                        control={control}
                        name="representation"
                        render={({ field }) => {
                          const handleOnChange = (e: SelectOption) => {
                            field.onChange(e.value)
                          }
                          return (
                            <Form.Select
                              placeholder="Select representation"
                              label="Representation"
                              {...register('representation')}
                              error={errors.representation?.message}
                              onChange={handleOnChange}
                              options={
                                [
                                  { text: 'Acting for themselves', value: 'acting_for_themselves' },
                                  { text: 'Attorney', value: 'attorney' },
                                  { text: 'Deputy', value: 'deputy' },
                                  { text: 'Executor', value: 'executor' },
                                ]
                              }
                            />
                          )
                        }}
                      />
                    </>
                  )
                }
                {
                  watch('representation') === 'attorney' && (
                    <>
                      <Form.Group>
                        <Form.Input
                          label="Owner first name"
                          placeholder="Enter first name"
                          {...register('owner_first_name')}
                          error={errors.owner_first_name?.message}
                        />
                        <Form.Input
                          label="Owner last name"
                          placeholder="Enter last name"
                          {...register('owner_last_name')}
                          error={errors.owner_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Owner email"
                        placeholder="Enter owner email"
                        {...register('owner_email')}
                        error={errors.owner_last_name?.message}
                      />
                      <div className="flex items-center w-full gap-5 bg-white rounded-[10px]">
                        <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                          <WarningIcon className="w-9 text-mull" />
                        </div>
                        <p className="text-base text-body">
                          Please add the details of the person who is responsible for providing the company’s information. The representative will be invited to ProConvey to add the new owners details as well as their own.
                        </p>
                      </div>
                      <Form.Group>
                        <Form.Input
                          label="Representative first name"
                          placeholder="Enter first name"
                          {...register('representative_first_name')}
                          error={errors.representative_first_name?.message}
                        />
                        <Form.Input
                          label="Representative last name"
                          placeholder="Enter last name"
                          {...register('representative_last_name')}
                          error={errors.representative_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Representative email"
                        placeholder="name@mail.com"
                        {...register('representative_email')}
                        error={errors.representative_email?.message}
                      />
                      <Label>ID and AML verification</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('id_check', Boolean(selection))}
                        error={errors.id_check?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </>
                  )
                }
                {
                  watch('representation') === 'acting_for_themselves' && (
                    <>
                      <Form.Group>
                        <Form.Input
                          label="Owner first name"
                          placeholder="Enter first name"
                          {...register('owner_first_name')}
                          error={errors.owner_first_name?.message}
                        />
                        <Form.Input
                          label="Owner last name"
                          placeholder="Enter last name"
                          {...register('owner_last_name')}
                          error={errors.owner_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Owner email"
                        placeholder="name@mail.com"
                        {...register('owner_email')}
                        error={errors.owner_email?.message}
                      />
                      <Label>ID and AML verification</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('id_check', Boolean(selection))}
                        error={errors.id_check?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </>
                  )
                }
                {
                  watch('representation') === 'deputy' && (
                    <>
                      <Form.Group>
                        <Form.Input
                          label="Owner first name"
                          placeholder="Enter first name"
                          {...register('owner_first_name')}
                          error={errors.owner_first_name?.message}
                        />
                        <Form.Input
                          label="Owner last name"
                          placeholder="Enter last name"
                          {...register('owner_last_name')}
                          error={errors.owner_last_name?.message}
                        />
                      </Form.Group>
                      <div className="flex items-center w-full gap-5 bg-white rounded-[10px]">
                        <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                          <WarningIcon className="w-9 text-mull" />
                        </div>
                        <p className="text-base text-body">
                          Please add the details of the person who is responsible for providing the company’s information. The representative will be invited to ProConvey to add the new owners details as well as their own.
                        </p>
                      </div>
                      <Form.Group>
                        <Form.Input
                          label="Representative first name"
                          placeholder="Enter first name"
                          {...register('representative_first_name')}
                          error={errors.representative_first_name?.message}
                        />
                        <Form.Input
                          label="Representative last name"
                          placeholder="Enter last name"
                          {...register('representative_last_name')}
                          error={errors.representative_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Representative email"
                        placeholder="name@mail.com"
                        {...register('representative_email')}
                        error={errors.representative_email?.message}
                      />
                    </>
                  )
                }
                {
                  watch('representation') === 'executor' && (
                    <>
                      <Form.Group>
                        <Form.Input
                          label="Owner first name"
                          placeholder="Enter first name"
                          {...register('owner_first_name')}
                          error={errors.owner_first_name?.message}
                        />
                        <Form.Input
                          label="Owner last name"
                          placeholder="Enter last name"
                          {...register('owner_last_name')}
                          error={errors.owner_last_name?.message}
                        />
                      </Form.Group>
                      <div className="flex items-center w-full gap-5 bg-white rounded-[10px]">
                        <div className="w-10 h-10 rounded-lg bg-chalkie p-[0.625rem] items-center justify-center flex">
                          <WarningIcon className="w-9 text-mull" />
                        </div>
                        <p className="text-base text-body">
                          Please add the details of the person who is responsible for providing the company’s information. The representative will be invited to ProConvey to add the new owners details as well as their own.
                        </p>
                      </div>
                      <Form.Group>
                        <Form.Input
                          label="Representative first name"
                          placeholder="Enter first name"
                          {...register('representative_first_name')}
                          error={errors.representative_first_name?.message}
                        />
                        <Form.Input
                          label="Representative last name"
                          placeholder="Enter last name"
                          {...register('representative_last_name')}
                          error={errors.representative_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Representative email"
                        placeholder="name@mail.com"
                        {...register('representative_email')}
                        error={errors.representative_email?.message}
                      />
                    </>
                  )
                }
                {/* Party type Attorney */}
                {
                  watch('party_type') === 'attorney' && (
                    <>
                      <Controller
                        control={control}
                        name="representation"
                        render={({ field }) => {
                          const handleOnChange = (e: SelectOption) => {
                            field.onChange(e.value)
                            setSelectedTypes(e.value as any)
                          }
                          return (
                            <Form.Select
                              label="Who are they representing?"
                              placeholder="Select representation"
                              onChange={handleOnChange}
                              options={
                                data?.property?.users?.filter(user => [
                                  PropertyUserRole.Owner,
                                  PropertyUserRole.Buyer,
                                  PropertyUserRole.Remortgager,
                                ].includes(user.pivot?.role as PropertyUserRole)).map(user => ({
                                  text: `${user.first_name} ${user.last_name}`,
                                  value: `${user.first_name} ${user.last_name}`,
                                }))
                              }
                            />
                          )
                        }}
                      />
                    </>
                  )
                }
                {/* Party type Deputy */}
                {
                  watch('party_type') === 'deputy' && (
                    <>
                      <Controller
                        control={control}
                        name="representation"
                        render={({ field }) => {
                          const handleOnChange = (e: SelectOption) => {
                            field.onChange(e.value)
                            setSelectedTypes(e.value as any)
                          }
                          return (
                            <Form.Select
                              label="Who are they representing?"
                              placeholder="Select representation"
                              onChange={handleOnChange}
                              options={
                                data?.property?.users?.filter(user => [
                                  PropertyUserRole.Owner,
                                  PropertyUserRole.Buyer,
                                  PropertyUserRole.Remortgager,
                                ].includes(user.pivot?.role as PropertyUserRole)).map(user => ({
                                  text: `${user.first_name} ${user.last_name}`,
                                  value: `${user.first_name} ${user.last_name}`,
                                }))
                              }
                            />
                          )
                        }}
                      />
                    </>
                  )
                }
                {/* Party type Executor */}
                {
                  watch('party_type') === 'executor' && (
                    <>
                      <Controller
                        control={control}
                        name="representation"
                        render={({ field }) => {
                          const handleOnChange = (e: SelectOption) => {
                            field.onChange(e.value)
                            setSelectedTypes(e.value as any)
                          }
                          return (
                            <Form.Select
                              label="Who are they representing?"
                              placeholder="Select representation"
                              onChange={handleOnChange}
                              options={
                                data?.property?.users?.filter(user => [
                                  PropertyUserRole.Owner,
                                  PropertyUserRole.Buyer,
                                  PropertyUserRole.Remortgager,
                                ].includes(user.pivot?.role as PropertyUserRole)).map(user => ({
                                  text: `${user.first_name} ${user.last_name}`,
                                  value: `${user.first_name} ${user.last_name}`,
                                }))
                              }
                            />
                          )
                        }}
                      />
                    </>
                  )
                }
                {
                  selectedTypes && selectedTypes.length > 0 && (
                    <>
                      <Form.Group>
                        <Form.Input
                          label="Representative first name "
                          placeholder="Enter first name"
                          {...register('representative_first_name')}
                          error={errors.representative_first_name?.message}
                        />
                        <Form.Input
                          label="Representative last name"
                          placeholder="Enter last name"
                          {...register('representative_last_name')}
                          error={errors.representative_last_name?.message}
                        />
                      </Form.Group>
                      <Form.Input
                        label="Representative email"
                        placeholder="name@mail.com"
                        {...register('representative_email')}
                        error={errors.representative_email?.message}
                      />
                      <Label>ID and AML verification</Label>
                      <AnswerRadioGroup
                        onChange={(selection) => setValue('id_check', Boolean(selection))}
                        error={errors.id_check?.message}
                      >
                        <AnswerRadioGroup.Radio value={true}>Yes</AnswerRadioGroup.Radio>
                        <AnswerRadioGroup.Radio value={false}>No</AnswerRadioGroup.Radio>
                      </AnswerRadioGroup>
                    </>
                  )
                }

              </div>

            </div>

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>

    </>
  )
}

export default AddPartyPage
