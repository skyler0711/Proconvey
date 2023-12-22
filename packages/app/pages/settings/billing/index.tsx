import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3, H4 } from '@proconvey/ui/src/components/Headers'
import Card from '@proconvey/ui/src/components/Card'
import { useMutation, useQuery } from 'urql'
import { graphql } from 'gql'
import Skeleton from 'react-loading-skeleton'
import Button from '@proconvey/ui/src/components/Button'
import Link from 'next/link'
import PaymentLogo from '@proconvey/ui/src/components/PaymentLogo'
import Table from '@proconvey/ui/src/components/Table'
import formatCurrency from 'helpers/formatCurrency'
import dayjs from 'dayjs'
import Tag from '@proconvey/ui/src/components/Tag'
import { BillingEmailIcon, CheckmarkIcon, ClockIcon, CrossIcon, DownloadIcon, InvoiceIcon, SlashCircleIcon } from '@proconvey/ui/src/icons'
import IconButton from '@proconvey/ui/src/components/IconButton'
import useDownload from 'hooks/useDownload'
import { useState } from 'react'
import client from 'helpers/client'
import Modal from '@proconvey/ui/src/components/Modals'
import Input from '@proconvey/ui/src/components/Form/Input'
import toast from 'react-hot-toast'
import { SubmitHandler, useForm } from 'react-hook-form'
import useErrorHandler from 'hooks/useErrorHandler'
import { NextSeo } from 'next-seo'
import { UpdateBillingEmailInput } from 'gql/graphql'

export default function Billing () {
  const download = useDownload()
  const errorHandler = useErrorHandler()
  const [downloadingAll, setDownloadingAll] = useState(false)
  const [showUpdateEmailModal, setShowUpdateEmailModal] = useState(false)

  const { register, setError, handleSubmit, formState: { errors } } = useForm<UpdateBillingEmailInput>()

  const [{ fetching: isUpdateBillingEmailLoading }, updateBillingEmailMutation] = useMutation(graphql(`
    mutation updateBillingEmail($input: UpdateBillingEmailInput!) {
      updateBillingEmail(input: $input)
    }
  `))

  const [{ data, fetching }, refetch] = useQuery({
    query: graphql(`
      query settingsBillingConveyancer($invoicesStartingAfter: String) {
        me {
          id
          conveyancer {
            id
            subscription {
              plan_name
              plan_price
              billing_email
              payment_method {
                type
                brand
                exp_month
                exp_year
                last4
                sort_code
              }
            }
            invoices(limit: 6, starting_after: $invoicesStartingAfter) {
              plan_name
              number
              amount
              date
              status
              pdf_url
            }
          }
        }
      }
    `),
    variables: {
      invoicesStartingAfter: null,
    },
  })

  const handleDownloadAll = async () => {
    setDownloadingAll(true)

    const result = await client.query(
      graphql(`
        query settingsBillingDownloadAll {
          me {
            conveyancer {
              all_invoices_link
            }
          }
        }
      `),
      {},
    ).toPromise()

    if (result.error) {
      toast.error('There was a problem downloading your invoices')
    } else {
      toast.success('Invoices downloaded!')
      download(result.data!.me!.conveyancer!.all_invoices_link)
    }
    setDownloadingAll(false)
  }

  const onSubmit: SubmitHandler<UpdateBillingEmailInput> = async (form) => {
    const response = await updateBillingEmailMutation({
      input: {
        email: form.email,
      },
    })

    if (response.error) {
      toast.error('Failed to update billing email')
      errorHandler(response.error, setError)
    } else {
      toast.success('Billing email updated')
      refetch()
      setShowUpdateEmailModal(false)
    }
  }

  return (
    <>
      <NextSeo
        title="Billing Settings"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[1.875rem] flex justify-between">
              <H1>Billing</H1>
            </div>

            <div className="space-y-[1.25rem]">
              <Card>
                <Card.Header>
                  <H3>Plan</H3>
                </Card.Header>
                <hr />
                <Card.Body>
                  <div className="flex flex-col items-start justify-between gap-5 sm:flex-row sm:items-center">
                    <div>
                      {
                        fetching
                          ? <Skeleton width={180} />
                          : <H4>{data?.me?.conveyancer?.subscription?.plan_name}</H4>
                      }
                      <p className="text-body/60 mt-[0.5rem] text-[1rem]">
                        {
                          fetching
                            ? <Skeleton width={150} />
                            : 'Our most popular plan'
                        }
                      </p>
                    </div>

                    {
                      fetching
                        ? <Skeleton width={186} height={57} />
                        : (
                          <div className="bg-primary/10 rounded-[0.5rem] px-[1.25rem] py-[0.625rem]">
                            <span className="text-primary text-[1.875rem] font-bold">£{(data?.me?.conveyancer?.subscription?.plan_price ?? 0) / 100}</span> <span>/ per pack</span>
                          </div>
                        )
                    }
                  </div>
                </Card.Body>
              </Card>


              <div className="flex flex-wrap gap-[1.25rem]">
                <div className="w-full max-w-[715px]">
                  <Card>
                    <Card.Header>
                      <H3>Payment Method</H3>
                    </Card.Header>
                    <hr />
                    <Card.Body>
                      <div className="flex flex-wrap items-center justify-between gap-5">
                        {
                          fetching
                            ? <>
                              <div className="flex gap-[1.25rem]">
                                <Skeleton width={80} height={40} />
                                <div>
                                  <Skeleton width={134} />
                                  <Skeleton width={84} />
                                </div>
                              </div>
                              <Skeleton width={102} height={37} />
                            </>
                            : (
                              data?.me?.conveyancer?.subscription?.payment_method
                                ? <>
                                  <div className="flex items-center gap-[1.25rem]">
                                    <PaymentLogo logo={data.me.conveyancer.subscription.payment_method.brand ?? 'direct_debit' as any} />
                                    <div>
                                      {
                                        data.me.conveyancer.subscription.payment_method.type === 'card'
                                          ? <>
                                            <p className="font-bold">
                                              <span className="capitalize">{data.me.conveyancer.subscription.payment_method.brand}</span> ending {data.me.conveyancer.subscription.payment_method.last4}
                                            </p>
                                            <p className="text-[0.875rem] text-body/60">
                                              Expiry {data.me.conveyancer.subscription.payment_method.exp_month?.toString().padStart(2, '0')}/{data.me.conveyancer.subscription.payment_method.exp_year}
                                            </p>
                                          </>
                                          : <>
                                            <p className="font-bold">
                                              {data.me.conveyancer.subscription.payment_method.sort_code} / ••••{data.me.conveyancer.subscription.payment_method.last4}
                                            </p>
                                          </>
                                      }
                                    </div>
                                  </div>
                                  <Link href="/settings/billing/payment-method">
                                    <Button size="small">
                                      Change
                                    </Button>
                                  </Link>
                                </>
                                : <>
                                  <p className="font-bold text-body">
                                    There is no added payment method yet
                                  </p>
                                  <Link href="/settings/billing/payment-method">
                                    <Button size="small">
                                      Add payment method
                                    </Button>
                                  </Link>
                                </>
                            )
                        }
                      </div>
                    </Card.Body>
                  </Card>
                </div>

                <div className="w-full max-w-[715px]">
                  <Card>
                    <Card.Header>
                      <H3>Billing Email</H3>
                    </Card.Header>
                    <hr />
                    <Card.Body>
                      <div className="flex flex-wrap gap-[1.25rem] items-center justify-between">
                        <div className="flex items-center gap-[1.25rem]">
                          {
                            fetching
                              ? <>
                                <Skeleton width={40} height={40} />
                                <Skeleton width={280} height={16} />
                              </>
                              : <>
                                <BillingEmailIcon />
                                <div className="break-all sm:break-normal">{data?.me?.conveyancer?.subscription?.billing_email}</div>
                              </>
                          }
                        </div>
                        {
                          fetching
                            ? <Skeleton width={102} height={37} />
                            : (
                              <Button size="small" onClick={() => setShowUpdateEmailModal(true)}>
                                Change
                              </Button>
                            )
                        }
                      </div>
                    </Card.Body>
                  </Card>
                </div>
              </div>

              <Card>
                <Card.Header>
                  <div className="flex items-center justify-between">
                    <H3>Billing History</H3>
                    {
                      fetching
                        ? <Skeleton width={118} height={18} />
                        : (
                          data?.me?.conveyancer?.invoices?.length ?
                            <Button variant="link" loading={downloadingAll} onClick={handleDownloadAll}>
                              <div className="flex text-[0.875rem]">
                                <DownloadIcon className="w-[1rem] mr-[0.625rem]" />
                                Download all
                              </div>
                            </Button>
                            :
                            null
                        )
                    }
                  </div>
                </Card.Header>
                <Card.Body padContent={false}>
                  <Table>
                    <Table.Head>
                      <Table.Row>
                        <Table.Cell as="th">Invoice</Table.Cell>
                        <Table.Cell as="th">Amount</Table.Cell>
                        <Table.Cell as="th">Date</Table.Cell>
                        <Table.Cell as="th">Status</Table.Cell>
                        <Table.Cell as="th"></Table.Cell>
                      </Table.Row>
                    </Table.Head>
                    <Table.Body>
                      {
                        fetching
                          ? Array.from({ length: 5 }).map((_, index) => (
                            <Table.Row key={index}>
                              <Table.Cell>
                                <Skeleton width={238} />
                                <Skeleton width={100} />
                              </Table.Cell>
                              <Table.Cell>
                                <Skeleton width={80} />
                              </Table.Cell>
                              <Table.Cell>
                                <Skeleton width={80} />
                              </Table.Cell>
                              <Table.Cell>
                                <Skeleton width={80} />
                              </Table.Cell>
                              <Table.Cell>
                                <Skeleton width={20} />
                              </Table.Cell>
                            </Table.Row>
                          ))
                          : data?.me?.conveyancer?.invoices?.map((invoice) => (
                            <Table.Row key={invoice.number}>
                              <Table.Cell>
                                <div className="flex items-center gap-[0.75rem]">
                                  <InvoiceIcon />

                                  <div>
                                    <p className="font-medium">{invoice.plan_name} &mdash; {dayjs(invoice.date).format('MMM YYYY')}</p>
                                    {
                                      invoice.number &&
                                      <p className="text-[0.75rem] text-body/60">Invoice {invoice.number}</p>
                                    }
                                  </div>
                                </div>
                              </Table.Cell>
                              <Table.Cell>
                                <p>{formatCurrency(invoice.amount)}</p>
                              </Table.Cell>
                              <Table.Cell>
                                <p>{dayjs(invoice.date).format('DD MMMM YYYY')}</p>
                              </Table.Cell>
                              <Table.Cell>
                                {
                                  {
                                    'draft': <Tag variant="warning"><ClockIcon className="w-[1rem]" /> Upcoming</Tag>,
                                    'open': <Tag variant="warning"><ClockIcon className="w-[1rem]" /> Upcoming</Tag>,
                                    'paid': <Tag variant="success"><CheckmarkIcon className="w-[1rem]" /> Paid</Tag>,
                                    'uncollectable': <Tag variant="danger"><CrossIcon className="w-[1rem]" /> Failed</Tag>,
                                    'void': <Tag><SlashCircleIcon className="w-[1rem]" /> Void</Tag>,
                                  }[invoice.status]
                                }
                              </Table.Cell>
                              <Table.Cell>
                                {
                                  invoice.pdf_url &&
                                  <IconButton
                                    icon={<DownloadIcon className="w-[1.25rem]" />}
                                    onClick={() => download(invoice.pdf_url!)}
                                  />
                                }
                              </Table.Cell>
                            </Table.Row>
                          ))
                      }
                    </Table.Body>
                  </Table>
                </Card.Body>
              </Card>
            </div>

            {/* Update billing email modal */}
            <Modal
              isOpen={showUpdateEmailModal}
              onClose={() => setShowUpdateEmailModal(false)}
            >
              <Modal.Title>Change billing email</Modal.Title>
              <Modal.Content>
                <label>Enter new email address</label>
                <Input
                  className="mt-[1.25rem]"
                  placeholder="name@company.com"
                  type="email"
                  error={errors.email?.message}
                  defaultValue={data?.me?.conveyancer?.subscription?.billing_email || ''}
                  {...register('email')}
                />
              </Modal.Content>
              <Modal.Footer>
                <Button variant="secondary" size="small" onClick={() => { setShowUpdateEmailModal(false)}}>Cancel</Button>
                <Button variant="primary" size="small" onClick={handleSubmit(onSubmit)} loading={isUpdateBillingEmailLoading}>Change</Button>
              </Modal.Footer>
            </Modal>

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
