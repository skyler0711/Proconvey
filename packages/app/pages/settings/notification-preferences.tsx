import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H4 } from '@proconvey/ui/src/components/Headers'
import Card from '@proconvey/ui/src/components/Card'
import Table from '@proconvey/ui/src/components/Table'
import Switch from '@proconvey/ui/src/components/Switch'
import { useMutation, useQuery } from 'urql'
import { graphql } from 'gql'
import Button from '@proconvey/ui/src/components/Button'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import { useForm, SubmitHandler } from 'react-hook-form'
import { useEffect, useState } from 'react'
import toast from 'react-hot-toast'
import useErrorHandler from 'hooks/useErrorHandler'
import { NextSeo } from 'next-seo'

type UpdateUserNotificationPreferencesValues = {
  getting_started_forms_completed: boolean
  onboarding_completed: boolean
  client_new_document_uploads: boolean
}

export default function NotificationPreferences () {
  const errorHandler = useErrorHandler()
  const [buttonDisabled, setButtonDisabled] = useState(true)

  const { handleSubmit, setValue, reset, watch, setError, formState: { errors } } = useForm<UpdateUserNotificationPreferencesValues>()

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query notificationPreferences {
        me {
          id
          notification_preferences {
            getting_started_forms_completed
            onboarding_completed
            client_new_document_uploads
          }
        }
      }
    `),
  })

  const [{ fetching: updateNotificationLoading }, updateUserNotificationPreferences] = useMutation(graphql(`
    mutation updateUserNotificationPreferences($input: UpdateNotificationPreferencesInput!) {
      updateUserNotificationPreferences(input: $input) {
        id
      }
    }
  `))

  const onSubmit: SubmitHandler<UpdateUserNotificationPreferencesValues> = async (form) => {
    setButtonDisabled(true)
    const response = await updateUserNotificationPreferences({
      input: {
        getting_started_forms_completed: form.getting_started_forms_completed,
        onboarding_completed: form.onboarding_completed,
        client_new_document_uploads: form.client_new_document_uploads,
      },
    })

    if (response.error) {
      setButtonDisabled(false)
      errorHandler(response.error, setError)
      toast.error('Something went wrong! Please try again.')
    } else {
      setButtonDisabled(false)
      toast.success('Your changes have been saved')
    }
  }

  useEffect(() => {
    if (data?.me?.notification_preferences) {
      reset({
        client_new_document_uploads: data?.me?.notification_preferences?.client_new_document_uploads,
        getting_started_forms_completed: data?.me?.notification_preferences?.getting_started_forms_completed,
        onboarding_completed: data?.me?.notification_preferences?.onboarding_completed,
      })
    }
  }, [data?.me?.notification_preferences, reset])

  return (
    <>
      <NextSeo
        title="Notification Preferences"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>

            <form onSubmit={handleSubmit(onSubmit)}>
              <div className="flex flex-col sm:flex-row sm:items-center items-start justify-between mb-[1.875rem] gap-5">
                <H1>Notification Settings</H1>

                <Button disabled={buttonDisabled} loading={updateNotificationLoading} type="submit">Save Changes</Button>
              </div>

              {
                fetching
                  ? <LoadingSpinner className={'flex justify-center pt-10'} />
                  : (
                    <Card>
                      <Card.Body padContent={false}>
                        <Table>
                          <Table.Body>
                            <Table.Row className="flex flex-col sm:flex-row justify-between">
                              <Table.Cell className="border-t-0 border-b-0"><H4>Getting started forms completed</H4></Table.Cell>
                              <Table.Cell className="border-t-0 border-b-0">
                                <Switch
                                  disabled={updateNotificationLoading}
                                  value={watch('getting_started_forms_completed') ?? false}
                                  onChange={checked => {
                                    setValue('getting_started_forms_completed', checked)
                                    setButtonDisabled(false)
                                  }}
                                  error={errors.getting_started_forms_completed?.message}
                                />
                              </Table.Cell>
                            </Table.Row>
                            <hr />
                            <Table.Row className="flex flex-col sm:flex-row justify-between">
                              <Table.Cell className="border-t-0 border-b-0"><H4>Onboarding completed</H4></Table.Cell>
                              <Table.Cell className="border-t-0 border-b-0">
                                <Switch
                                  disabled={updateNotificationLoading}
                                  value={watch('onboarding_completed') ?? false}
                                  onChange={checked => {
                                    setValue('onboarding_completed', checked)
                                    setButtonDisabled(false)
                                  }}
                                  error={errors.onboarding_completed?.message}
                                />
                              </Table.Cell>
                            </Table.Row>
                            <hr />
                            <Table.Row className="flex flex-col sm:flex-row justify-between">
                              <Table.Cell className="border-t-0 border-b-0"><H4>Client new document uploads</H4></Table.Cell>
                              <Table.Cell className="border-t-0 border-b-0">
                                <Switch
                                  disabled={updateNotificationLoading}
                                  value={watch('client_new_document_uploads') ?? false}
                                  onChange={checked => {
                                    setValue('client_new_document_uploads', checked)
                                    setButtonDisabled(false)
                                  }}
                                  error={errors.client_new_document_uploads?.message}
                                />
                              </Table.Cell>
                            </Table.Row>
                            <hr />
                          </Table.Body>
                        </Table>
                      </Card.Body>
                    </Card>
                  )
              }
            </form>

          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
