import Button from '@proconvey/ui/src/components/Button'
import Card from '@proconvey/ui/src/components/Card'
import Form from '@proconvey/ui/src/components/Form'
import Label from '@proconvey/ui/src/components/Form/Label'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import { PhotoUploadIcon } from '@proconvey/ui/src/icons'
import ClientPortalLayout from 'layouts/ClientPortalLayout'
import ProtectedLayout from 'layouts/ProtectedLayout'
import React, { useEffect, useState } from 'react'
import Modal from '@proconvey/ui/src/components/Modals'
import { Controller } from 'react-hook-form'
import { graphql } from 'gql'
import { SubmitHandler, useForm } from 'react-hook-form'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { useMutation } from 'urql'
import useUpload from 'hooks/useUpload'
import useErrorHandler from 'hooks/useErrorHandler'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'
import { toast } from 'react-hot-toast'

type UpdateUserProfileProps = {
  first_name: string | null
  last_name: string | null
  profile_image: File[]
  phone: string
  password: string
  newPassword: string
  email: string
}

const UserProfile = () => {
  const [isForgottenPasswordModalOpen, setIsForgottenPasswordModalOpen] = useState(false)
  const { uploadFiles } = useUpload()
  const errorHandler = useErrorHandler()

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const { register, handleSubmit, setError, control, reset, formState: { errors } } = useForm<UpdateUserProfileProps>({
    defaultValues: {
      first_name: user?.first_name ?? '',
      last_name: user?.last_name ?? '',
      profile_image: undefined,
      phone: user?.phone ?? '',
      email: user?.email ?? '',
      password: undefined,
      newPassword: undefined,
    },
  })

  useEffect(() => {
    if (user) {
      reset({
        first_name: user.first_name,
        last_name: user.last_name,
        phone: user.phone ?? '',
        email: user.email,
      })
    }
  }, [user, reset])

  const [{ fetching: isUpdateUserLoading }, updateUserProfileMutation] = useMutation(graphql(`
    mutation updateClientProfile ($input: UpdateUserProfileInput!) {
      updateUserProfile(input: $input) {
        first_name
        last_name
        title
        suffix
        job_bio
        role
        profile_image {
          url
        }
        phone
        email
      }
    }
  `))

  const [{ fetching: isForgotPasswordLoading }, forgotPasswordMutation] = useMutation(graphql(`
    mutation forgottenPassword ($email: String!) {
      forgottenPassword(email: $email) 
    }
  `))

  const onSubmit: SubmitHandler<UpdateUserProfileProps> = async (form) => {
    let upload = await uploadFiles(form.profile_image)

    const updateUserProfile = await updateUserProfileMutation({
      input: {
        first_name: form.first_name,
        last_name: form.last_name,
        phone: form.phone,
        password: form.password,
        newPassword: form.newPassword,
        email: form.email,
        ...upload && {
          profile_image: {
            key: upload.key,
            extension: upload.extension,
          },
        },
      },
    })

    if (updateUserProfile.error) {
      errorHandler(updateUserProfile.error, setError)
      return
    }

    toast.success('Profile updated successfully')
  }

  const handleForgottenPassword = async () => {
    setIsForgottenPasswordModalOpen(true)
    await forgotPasswordMutation({
      email: user?.email as string,
    })
  }

  return (
    <>
      <NextSeo
        title="Profile"
      />
      <ProtectedLayout>
        <ClientPortalLayout>
          <ClientPortalLayout.MainContent>
            <div className="ml-[1.875rem] mr-[3.125rem] mt-[3.125rem]">
              <div className="flex justify-between mb-[1.875rem]">
                <H1>Profile</H1>
                <Button loading={isUpdateUserLoading} onClick={handleSubmit(onSubmit)} type="submit">Save Changes</Button>
              </div>

              <Card>
                <Card.Header>
                  <H3>Personal Information</H3>
                </Card.Header>
                <hr />
                <Card.Body padContent={true}>
                  <Form>
                    <Form.Group>
                      <Form.Input
                        label="First Name"
                        defaultValue={user?.first_name ?? undefined}
                        error={errors.first_name?.message}
                        {...register('first_name')}
                      />

                      <Form.Input
                        label="Last Name"
                        defaultValue={user?.last_name ?? undefined}
                        error={errors.last_name?.message}
                        {...register('last_name')}
                      />
                    </Form.Group>

                    <Form.Group>
                      <Form.Input
                        label="Phone number"
                        error={errors.phone?.message}
                        {...register('phone')}
                      />

                      <Form.Input
                        label="Email"
                        defaultValue={user?.email}
                        error={errors.email?.message}
                        {...register('email')}
                      />
                    </Form.Group>

                    <Label>Profile Photo</Label>
                    <Form.Group>
                      <Controller
                        name="profile_image"
                        control={control}
                        render={({ field }) => {
                          return (
                            <ImageUpload
                              onChange={field.onChange}
                              type="profile_image"
                              defaultPreviewUrl={user?.profile_image?.url}
                              placeholder={
                                <div className="flex items-center justify-center w-20 h-20">
                                  <PhotoUploadIcon className="w-10 h-10 text-primary" />
                                </div>
                              }
                            />
                          )
                        }}
                      />
                    </Form.Group>
                  </Form>

                </Card.Body>
              </Card>

              <Card className="mt-[1.25rem]">
                <Card.Header>
                  <div className="flex flex-col items-start justify-between sm:items-center sm:flex-row">
                    <H3>Password Change</H3>
                    <Button loading={isForgotPasswordLoading} variant="link" onClick={handleSubmit(handleForgottenPassword)}>Forgot Password?</Button>
                  </div>
                </Card.Header>
                <Card.Body padContent={true}>
                  <Form.Group>
                    <Form.Input
                      label="Current Password"
                      placeholder="⦁⦁⦁⦁⦁⦁⦁⦁⦁"
                      type="password"
                      error={errors.password?.message}
                      {...register('password')}
                    />

                    <Form.Input
                      label="New Password"
                      placeholder="⦁⦁⦁⦁⦁⦁⦁⦁⦁"
                      type="password"
                      error={errors.newPassword?.message}
                      {...register('newPassword')}
                    />
                  </Form.Group>
                </Card.Body>
              </Card>

            </div>
            <Modal isOpen={isForgottenPasswordModalOpen} onClose={() => setIsForgottenPasswordModalOpen(false)}>
              <Modal.Title>Forgot your password?</Modal.Title>
              <Modal.Content>
                An email has been sent to <b>{user?.email}</b> with instructions to reset your password.
              </Modal.Content>
              <Modal.Footer>
                <Button onClick={() => setIsForgottenPasswordModalOpen(false)} size="small">OK</Button>
              </Modal.Footer>
            </Modal>
          </ClientPortalLayout.MainContent>
        </ClientPortalLayout>
      </ProtectedLayout>
    </>
  )
}

export default UserProfile
