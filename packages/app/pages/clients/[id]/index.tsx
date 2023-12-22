import Button from '@proconvey/ui/src/components/Button'
import { BinIcon, BriefcaseIcon, BuildingIcon, ChevronDownIcon, EnvelopeIcon, PencilIcon, PhoneIcon, PoundIcon, ChevronLeftIcon, PinIcon, CrossIcon, TickIcon, UserIcon } from '@proconvey/ui/src/icons'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import { useMutation, useQuery } from 'urql'
import { graphql } from 'gql'
import { useRouter } from 'next/router'
import Link from 'next/link'
import { H1, H2, H3 } from '@proconvey/ui/src/components/Headers'
import Skeleton from 'react-loading-skeleton'
import Card from '@proconvey/ui/src/components/Card'
import Table from '@proconvey/ui/src/components/Table'
import { NextSeo } from 'next-seo'
import { useMemo, useState } from 'react'
import { Address, Property, PropertyUserRole, Step, StepType, User, PropertyType } from 'gql/graphql'
import { Subset } from 'types/subset'
import PropertyTag from '@proconvey/ui/src/components/PropertyTag'
import formatPrice from 'hooks/helpers/formatPrice'
import Modal from '@proconvey/ui/src/components/Modals'
import Form from '@proconvey/ui/src/components/Form'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { toast } from 'react-hot-toast'
import { UserTitle } from 'types/enums/UserTitle'
import useErrorHandler from 'hooks/useErrorHandler'
import GiftorsTable from 'components/GiftorsTable'
import RemovePartyModal from 'components/ClientOverview/Modals/RemovePartyModal'
import InvitePartyModal from 'components/ClientOverview/Modals/InvitePartyModal'
import MortgageCharges, { Charge } from 'components/ClientOverview/Sections/MortgageCharges'
import SDLTDecleration from 'components/ClientOverview/Sections/SDLTDeclaration'

type UpdatePartyInputProps = {
  property_id: string
  user_id: string
  first_name: string
  middle_name: string
  last_name: string
  email: string
  phone: string
  title: UserTitle
  occupation: string
  representation: string
  address: Address
}

const Client = () => {
  const router = useRouter()
  const clientId = router.query.id as string
  const [userToEdit, setUserToEdit] = useState<string | undefined>()
  const [userToRemove, setUserToRemove] = useState<string | undefined>()
  const [userToInvite, setUserToInvite] = useState<string | undefined>()
  const errorHandler = useErrorHandler()

  const { register, handleSubmit: updateHandleSubmit, control, setError, reset, formState: { errors } } = useForm<UpdatePartyInputProps>()

  const [{ fetching, data }, refetch] = useQuery({
    query: graphql(/* GraphQL */`
      query getProperty($id: ID!) {
        property(id: $id) {
          id
          sale_price
          letters_required
          id_check_required
          conveyancing_fee
          active_forms {
            id
            pivot {
              ... on ActiveFormsPivot {
                id
              }
            }
            ta_form_template
            sections {
              id
              steps {
                id
                type
                compiled_answer
                answers {
                  id
                }
              }
            }
          }
          my_progress {
            provided_answers {
              id
              value
              answer {
                id
              }
            }
          }
          users {
            pivot {
              ...on PropertyUserPivot {
                role
                sof_completed_at
                representation
              }
            }
            address {
              line_1
              line_2
              city
              postcode
            }
            id
            email
            title
            first_name
            middle_name
            last_name
            phone
            occupation
            invite_code_sent_at
            email_verified_at
          }
          address {
            id
            line_1
            line_2
            city
            postcode
          }
          type
        }
      }
    `),
    variables: {
      id: clientId,
    },
  })

  const [{ fetching: isUpdatePartyLoading }, updatePartyMutation] = useMutation(graphql(`
  mutation updateExistingParty($input: UpdateExistingPartyInput!) {
    updateExistingParty(input: $input)
    }
`))

  const partyEdit = data?.property?.users?.find(party => party.id === userToEdit)

  const handleUpdateParty: SubmitHandler<UpdatePartyInputProps> = async (form) => {
    const updateParty = await updatePartyMutation({
      input: {
        user_id: partyEdit?.id ?? '',
        property_id: data?.property?.id ?? '',
        first_name: form.first_name,
        middle_name: form.middle_name,
        last_name: form.last_name,
        email: form.email,
        phone: form.phone,
        title: form.title,
        occupation: form.occupation,
        representation: form.representation,
        address: {
          line_1: form.address.line_1,
          line_2: form.address.line_2,
          city: form.address.city,
          postcode: form.address.postcode,
        },
      },
    })

    if (updateParty.error) {
      toast.error('Something went wrong, please try again')
      errorHandler(updateParty.error, setError)
    } else {
      toast.success('Party updated successfully')
      setUserToEdit(undefined)
      refetch()
    }
  }

  const details = useMemo(() => {
    const allSteps = data?.property?.active_forms
      ?.reduce((acc: Subset<Step>[], activeForm) => [
        ...acc,
        ...activeForm.sections.reduce((acc: Subset<Step>[], section) => [...acc, ...section.steps], []),
      ], []) as Step[]

    const allAnswers = data?.property?.my_progress?.provided_answers ?? []

    const buyers: string[] = (data?.property?.type === PropertyType.Sale
      ? allSteps?.find(step => step.type === StepType.Buyer)?.compiled_answer
      : allSteps?.find(step => step.type === StepType.BuyerExpanded)?.compiled_answer) ?? []

    const solicitors: string[] = allSteps?.find(step => [
      StepType.CompanyFormPowerOfAttorneyRepresentative,
      StepType.CompanyFormDeputyshipOrderRepresentative,
      StepType.CompanyFormGrantOfProbateRepresentative,
    ].includes(step.type))?.compiled_answer ?? []

    const isPropertyRemortgage = data?.property?.type === PropertyType.Remortgage
    const isPropertySale = data?.property?.type === PropertyType.Sale

    return {
      isPropertyRemortgage,
      isPropertySale,

      salePrice: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.SalePrice)?.answers[0]?.id)?.value,
      saleStatus: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.SoldStatus)?.answers[0]?.id)?.value,
      tenure: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.Tenure)?.answers[0]?.id)?.value,
      estateAgent: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.EstateAgent)?.answers[0]?.id)?.value,
      mortgageLender: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.MortgageLender)?.answers[0]?.id)?.value,
      mortgageAmount: allAnswers.find(a => a.answer.id === allSteps?.find(step => step.type === StepType.MortgageAmount)?.answers[0]?.id)?.value,
      buyers: buyers,
      buyersSolicitor: allSteps?.find(step => step.type === StepType.BuyersSolicitor)?.compiled_answer,
      propertyType: data?.property?.type === PropertyType.Sale ? 'Buyer' : 'Seller',

      outstandingMortgages: (allSteps?.find((step) => [StepType.MortgageChargeLoan].includes(step.type))?.compiled_answer ?? []) as Charge[],
      charges: (allSteps?.find((step) => [StepType.Charges].includes(step.type))?.compiled_answer ?? []) as Charge[],
      giftors: (allSteps?.find((step) => [StepType.BuyerGiftor, StepType.RemortgageGiftor].includes(step.type))?.compiled_answer ?? []),
      sdlt: (allSteps?.find((step) => [StepType.Sdlt].includes(step.type))?.compiled_answer ?? []),

      parties: [
        ...buyers.map((buyer, index) => ({
          id: index,
          name: buyer,
          email: '',
          role: 'Buyer',
          representing: null,
          onboarding_letters: null,
          id_check: null,
          forms_signed: null,
        })),
        ...solicitors.map((buyer, index) => ({
          id: index,
          name: buyer,
          email: '',
          role: 'Attorney',
          representing: null,
          onboarding_letters: null,
          id_check: null,
          forms_signed: null,
        })),
      ],
    }
  }, [data])

  const resolveSoldStatusText = (statusText: string) => {
    if (statusText?.toLowerCase() === 'yes') {
      return 'Sold'
    }

    if (statusText?.toLowerCase() === 'no') {
      return 'Not Sold'
    }

    return '-'
  }

  const resolveSalePrice = (salePrice: any) => {
    if (!isNaN(salePrice)) {
      return formatPrice(salePrice)
    }

    return salePrice ?? '-'
  }

  return <>
    <NextSeo
      title={data?.property?.address?.line_1}
      defaultTitle="Loading..."
    />
    <ProtectedLayout>
      <ConveyancerPortalLayout>
        <ConveyancerPortalLayout.MainContent>
          <div className="md:ml-[1.875rem]">
            <div className="mb-[1.875rem]">
              <Link href="/clients">
                <Button variant="link" className="mb-[1.375rem]">
                  <ChevronLeftIcon className="inline mr-[0.75rem]" /> Back to All Clients
                </Button>
              </Link>

              <div className="flex flex-col items-start xl:items-center gap-[1.25rem] justify-between xl:flex-row">
                <div>
                  {
                    fetching
                      ? <Skeleton height={50} width={363} />
                      : <H1>{data?.property.users[0].first_name} {data?.property.users[0].last_name}</H1>
                  }

                  <div className="mt-[1.375rem]">
                    {
                      fetching
                        ? <Skeleton height={30} width={484} />
                        : <h2 className="text-[1.375rem]">
                          <PinIcon className="inline mr-[1rem]" />
                          {[
                            data?.property?.address?.line_1,
                            data?.property?.address?.line_2,
                            data?.property?.address?.city,
                            data?.property?.address?.postcode,
                          ].filter(Boolean).join(', ')}
                        </h2>
                    }
                  </div>
                </div>

                {
                  fetching
                    ? <Skeleton height={52} width={183} />
                    : (
                      <div className="flex flex-wrap items-center md:flex-nowrap gap-[1.25rem]">
                        <Button icon={<EnvelopeIcon className="w-[1.5rem]" />}>
                          <a href={`mailto:${data?.property.users[0].email}`}>Message</a>
                        </Button>
                        <Button icon={<PhoneIcon className="w-[1.5rem]" />}>
                          <a href={`tel:${data?.property.users[0].phone}`}>Call</a>
                        </Button>
                      </div>
                    )
                }
              </div>
            </div>

            {/* Case Summary */}
            <Card>
              <Card.Header>
                <div className="flex gap-5">
                  <H3>Case summary</H3>
                  {
                    data?.property?.type &&
                    <PropertyTag type={data?.property?.type}>{data?.property?.type}</PropertyTag>
                  }
                </div>
              </Card.Header>

              <div className="flex flex-wrap gap-5 px-5 pb-5">
                <Card className="flex-1 p-5">
                  {
                    fetching
                      ? <Skeleton width="60%" height={33} />
                      : details.isPropertyRemortgage
                        ? <p>Remortgage Amount</p>
                        : <p>Sale price</p>
                  }
                  <p className="text-primary text-[1.875rem] font-bold">
                    {
                      fetching
                        ? <Skeleton width="60%" height={33} />
                        : details.isPropertyRemortgage
                          ? (resolveSalePrice(details.mortgageAmount ?? data?.property?.sale_price))
                          : details?.isPropertySale
                            ? (resolveSalePrice(details.salePrice ?? data?.property?.sale_price))
                            : (resolveSalePrice(data?.property?.sale_price))
                    }
                  </p>
                </Card>
                <Card className="flex-1 p-5">
                  {
                    fetching
                      ? <Skeleton width="60%" height={33} />
                      : details.isPropertyRemortgage
                        ? <p>Lender</p>
                        : <p>Sale status</p>
                  }
                  <p className="text-primary text-[1.875rem] font-bold">
                    {
                      fetching
                        ? <Skeleton width="60%" height={33} />
                        : details.isPropertyRemortgage && details
                          ? (details.mortgageLender ?? '-')
                          : (resolveSoldStatusText(details.saleStatus))
                    }
                  </p>
                </Card>
                <Card className="flex-1 p-5">
                  {
                    fetching
                      ? <Skeleton width="60%" height={33} />
                      : <p>Tenure</p>
                  }
                  <p className="text-primary text-[1.875rem] font-bold">
                    {
                      fetching
                        ? <Skeleton width="60%" height={33} />
                        : (details.tenure ?? '-')
                    }
                  </p>
                </Card>
              </div>
            </Card>

            <Card className="mt-5">
              <Card.Header>
                <div className="flex flex-col items-start justify-between gap-5 sm:items-center sm:flex-row">
                  <H3>Parties</H3>
                  <Link href={`/clients/${router?.query?.id}/add-party`}>
                    <Button variant="link" className="text-sm font-normal text-primary">Add party</Button>
                  </Link>
                </div>
              </Card.Header>
              <Card.Body padContent={false}>
                <Table>
                  <Table.Head>
                    <Table.Row>
                      <Table.Cell as="th" className="!text-[0.75rem]">
                        Name
                      </Table.Cell>
                      <Table.Cell as="th" className="!text-[0.75rem]">
                        Role
                      </Table.Cell>
                      <Table.Cell as="th" className="!text-[0.75rem]">
                        Representing
                      </Table.Cell>
                      <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                        Onboarding letters
                      </Table.Cell>
                      <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                        ID check
                      </Table.Cell>
                      <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                        SOF check
                      </Table.Cell>
                      <Table.Cell as="th" className="w-[4.8125rem] !text-[0.75rem]">
                        Registered
                      </Table.Cell>
                      <Table.Cell as="th" className="w-[3.125rem]"></Table.Cell>
                      <Table.Cell as="th" className="w-[3.125rem]"></Table.Cell>
                    </Table.Row>
                  </Table.Head>

                  <Table.Body>
                    {
                      fetching && <>
                        <Table.Row>
                          <Table.Cell><Skeleton width="60%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="70%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="80%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="30%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                          <Table.Cell><Skeleton width="20%" height={27} /></Table.Cell>
                        </Table.Row>
                      </>
                    }

                    {
                      !fetching && data?.property?.users.length === 0 &&
                      <Table.Row>
                        <Table.Cell colSpan={9}>
                          <p className="text-center">No Parties</p>
                        </Table.Cell>
                      </Table.Row>
                    }

                    {
                      data?.property?.users.filter(party => party.pivot?.role !== PropertyUserRole.Giftor).map(party => (
                        <Table.Row key={party.id}>
                          <Table.Cell className="text-[0.875rem]">
                            {party.first_name} {party.last_name}<br />
                            <span className="text-[0.75rem] text-body/60">{party.email}</span>
                          </Table.Cell>
                          <Table.Cell className="text-[0.875rem] capitalize">
                            <PropertyTag type={party.pivot?.role as any}>
                              {
                                party.pivot?.role === PropertyUserRole.Owner
                                  ? 'Owner (Active)'
                                  : party.pivot?.role
                              }
                            </PropertyTag>
                          </Table.Cell>
                          <Table.Cell className="text-[0.875rem]">
                            {party.pivot?.representation}
                          </Table.Cell>
                          <Table.Cell>
                            {
                              data?.property?.letters_required
                                ? <TickIcon className="text-mint w-[1rem]" />
                                : <CrossIcon className="text-mull w-[0.625rem]" />
                            }
                          </Table.Cell>
                          <Table.Cell>
                            {
                              data?.property?.id_check_required
                                ? <TickIcon className="text-mint w-[1rem]" />
                                : <CrossIcon className="text-mull w-[0.625rem]" />
                            }
                          </Table.Cell>
                          <Table.Cell>
                            {
                              party.pivot?.role === PropertyUserRole.Buyer
                                ? party?.pivot?.sof_completed_at
                                  ? <TickIcon className="text-mint w-[1rem]" />
                                  : <CrossIcon className="text-mull w-[0.625rem]" />
                                : 'N/A'

                            }
                          </Table.Cell>
                          <Table.Cell>
                            {
                              party?.email_verified_at
                                ? <TickIcon className="text-mint w-[1rem]" />
                                : <button type="button" onClick={() =>
                                  setUserToInvite(party.id)
                                } className="text-sm font-normal text-primary">Invite</button>
                            }
                          </Table.Cell>
                          <Table.Cell>
                            <Button variant="plain" onClick={() => setUserToRemove(party.id)}>
                              <BinIcon className="w-4 h-5" />
                            </Button>
                          </Table.Cell>
                          <Table.Cell>
                            <Button variant="plain" onClick={() => {
                              reset()
                              setUserToEdit(party.id)
                            }}>
                              <PencilIcon className="w-4 h-5" />
                            </Button>
                          </Table.Cell>
                        </Table.Row>
                      ))
                    }
                  </Table.Body>
                </Table>
              </Card.Body>
            </Card>

            <GiftorsTable
              property={data?.property as Property}
              fetching={fetching}
              refetch={refetch}
              details={details}
            />

            {
              details.isPropertyRemortgage ?
                <Card className="mt-5">
                  <Card.Header>
                    <div className="flex flex-row items-center">
                      <UserIcon className="inline mr-[0.75rem]" /> <H3>Contacts</H3>
                    </div>
                  </Card.Header>
                  <Card.Body padContent={false}>
                    <div className="flex flex-wrap gap-5 px-5 pb-5">
                      <Card className="flex-1 p-5 flex flex-col gap-[0.625rem]">
                        <H3 className="flex items-center text-primary">
                          <BuildingIcon className="mr-[0.8125rem]" />Current owners
                        </H3>

                        <p className="text-body/80 text-[1.125rem]">
                          {
                            fetching
                              ? <Skeleton width="60%" height={27} />
                              : (details.estateAgent ?? '-')
                          }
                        </p>
                      </Card>

                      <Card className="flex-1 p-5 flex flex-col gap-[0.625rem]">
                        <H3 className="flex items-center text-primary">
                          <BriefcaseIcon className="mr-[0.8125rem]" />Broker
                        </H3>

                        <p className="text-body/80 text-[1.125rem]">
                          {
                            fetching
                              ? <Skeleton width="60%" height={27} />
                              : (details.buyersSolicitor ?? '-')
                          }
                        </p>
                      </Card>
                    </div>
                  </Card.Body>
                </Card>
                :
                <Card className="mt-5">
                  <Card.Header>
                    <div className="flex flex-row items-center">
                      <UserIcon className="inline mr-[0.75rem]" /> <H3>Contacts</H3>
                    </div>
                  </Card.Header>
                  <Card.Body padContent={false}>
                    <div className="flex flex-wrap gap-5 px-5 pb-5">
                      <Card className="flex-1 p-5 flex flex-col gap-[0.625rem]">
                        <H3 className="flex items-center text-primary">
                          <BuildingIcon className="mr-[0.8125rem]" />Estate agent
                        </H3>

                        <p className="text-body/80 text-[1.125rem]">
                          {
                            fetching
                              ? <Skeleton width="60%" height={27} />
                              : (details.estateAgent ?? '-')
                          }
                        </p>
                      </Card>

                      <Card className="flex-1 p-5 flex flex-col gap-[0.625rem]">
                        <H3 className="flex items-center text-primary">
                          <PoundIcon className="mr-[0.8125rem]" />{details.propertyType}
                        </H3>
                        <p className="text-body/80 text-[1.125rem]">
                          {
                            fetching
                              ? <Skeleton width="60%" height={27} />
                              : (details.buyers ?? []).length === 0
                                ? '-'
                                : (details.buyers ?? []).map((buyer) => Object.values(buyer).filter((detail) => detail !== undefined && detail !== null).join(', ')).join('. ') + '.'
                          }
                        </p>
                      </Card>

                      <Card className="flex-1 p-5 flex flex-col gap-[0.625rem]">
                        <H3 className="flex items-center text-primary">
                          <BriefcaseIcon className="mr-[0.8125rem]" />{details.propertyType}s&apos; solicitor
                        </H3>

                        <p className="text-body/80 text-[1.125rem]">
                          {
                            fetching
                              ? <Skeleton width="60%" height={27} />
                              : (details.buyersSolicitor ?? '-')
                          }
                        </p>
                      </Card>
                    </div>
                  </Card.Body>
                </Card>
            }

            {
              !details.isPropertySale &&
              <MortgageCharges
                isLoading={fetching}
                property={data?.property as Property | undefined}
                details={details}
                refetch={refetch}
              />
            }

            {
              !details.isPropertySale &&
              <SDLTDecleration
                details={details}
                fetching={fetching}
              />
            }

          </div>
        </ConveyancerPortalLayout.MainContent>
      </ConveyancerPortalLayout>
    </ProtectedLayout>

    {/* Edit Party Modal */}
    <Modal size="large" isOpen={!!userToEdit} onClose={() => setUserToEdit(undefined)}
    >
      <Modal.ContentTitle>Edit details</Modal.ContentTitle>
      <Modal.Content className="mt-[1.25rem] flex flex-col gap-y-5">
        <Form.Group>
          <Form.Input
            label="First name"
            placeholder="First name"
            defaultValue={partyEdit?.first_name ?? ''}
            {...register('first_name')}
            error={errors.first_name?.message}
          />
          <Form.Input
            label="Last name"
            placeholder="Last name"
            defaultValue={partyEdit?.last_name ?? ''}
            {...register('last_name')}
            error={errors.last_name?.message}
          />
        </Form.Group>
        <Form.Group>
          <Form.Input
            label="Middle name(s)"
            placeholder="Enter middle name(s)"
            defaultValue={partyEdit?.middle_name ?? ''}
            {...register('middle_name')}
            error={errors.middle_name?.message}
          />

          <Controller
            control={control}
            name="title"
            render={({ field }) => {
              return (
                <Form.Select
                  label="Title"
                  onChange={e => field.onChange(e.value)}
                  defaultValue={
                    partyEdit?.title
                      ? { text: partyEdit.title, value: partyEdit.title }
                      : undefined
                  }
                  error={errors.title?.message}
                  options={
                    (Object.keys(UserTitle) as Array<keyof typeof UserTitle>)
                      .map(k => ({ text: k, value: UserTitle[k] }))
                  }
                />
              )
            }}
          />
        </Form.Group>
        <Form.Group>
          <Form.Input
            label="Email"
            placeholder="name@mail.com"
            defaultValue={partyEdit?.email ?? ''}
            {...register('email')}
            error={errors.email?.message}
          />
          <Form.Input
            label="Main Contact Number"
            placeholder="Enter last name"
            defaultValue={partyEdit?.phone ?? ''}
            {...register('phone')}
            error={errors.phone?.message}
          />
        </Form.Group>
        <Form.Input
          label="Occupation"
          placeholder="Occupation"
          defaultValue={partyEdit?.occupation ?? ''}
          {...register('occupation')}
          error={errors.occupation?.message}
        />
        <H2>
          <span className="text-sm font-medium text-gray-500">Correspondence Address</span>
        </H2>
        <Form.Group>
          <Form.Input
            label="Address Line 1"
            placeholder="Address Line 1"
            defaultValue={partyEdit?.address?.line_1 ?? ''}
            {...register('address.line_1')}
            error={errors.address?.line_1?.message}
          />
          <Form.Input
            label="Address Line 2"
            placeholder="Address Line 2"
            defaultValue={partyEdit?.address?.line_2 ?? ''}
            {...register('address.line_2')}
            error={errors.address?.line_2?.message}
          />
        </Form.Group>
        <Form.Group>
          <Form.Input
            label="Town/City"
            placeholder="Town/City"
            defaultValue={partyEdit?.address?.city ?? ''}
            {...register('address.city')}
            error={errors.address?.city?.message}
          />
          <Form.Input
            label="Postcode"
            placeholder="Postcode"
            defaultValue={partyEdit?.address?.postcode ?? ''}
            {...register('address.postcode')}
            error={errors.address?.postcode?.message}
          />
        </Form.Group>

        <Controller
          control={control}
          name="representation"
          render={({ field }) => {
            return (
              <Form.Select
                label="Representation"
                placeholder="Select representation"
                defaultValue={
                  partyEdit?.pivot?.representation
                    ? { text: partyEdit.pivot.representation, value: partyEdit.pivot.representation }
                    : undefined
                }
                onChange={e => field.onChange(e.value)}
                options={
                  data?.property?.users?.filter(user => user?.pivot?.role === PropertyUserRole.Owner).map(user => ({
                    text: `${user.first_name} ${user.last_name}`,
                    value: `${user.first_name} ${user.last_name}`,
                  }))
                }
              />
            )
          }}
        />

        <Modal.Footer>
          <Button loading={isUpdatePartyLoading} onClick={updateHandleSubmit(handleUpdateParty)} size="small">Save</Button>
          <Button onClick={() => setUserToEdit(undefined)} size="small" variant="secondary">Cancel</Button>
        </Modal.Footer>
      </Modal.Content>
    </Modal>

    {/* Delete Party Modal */}
    <RemovePartyModal
      party={data?.property?.users.find(party => party.id === userToRemove) as User}
      propertyId={data?.property.id as string}
      onClose={() => setUserToRemove(undefined)}
      refetch={refetch}
    />

    {/* Invite Party Modal */}
    <InvitePartyModal
      party={data?.property?.users.find(party => party.id === userToInvite) as User}
      propertyId={data?.property.id as string}
      onClose={() => setUserToInvite(undefined)}
      refetch={refetch}
    />
  </>
}

export default Client
