import ProtectedLayout from 'layouts/ProtectedLayout'
import ConveyancerPortalLayout from 'layouts/ConveyancerPortalLayout'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import Form from '@proconvey/ui/src/components/Form'
import { JobRole } from 'types/enums/JobRole'
import Label from '@proconvey/ui/src/components/Form/Label'
import { PhotoUploadIcon, WarningIcon } from '@proconvey/ui/src/icons'
import Button from '@proconvey/ui/src/components/Button'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import { useEffect, useState } from 'react'
import { useMutation } from 'urql'
import { graphql } from 'gql'
import Modal from '@proconvey/ui/src/components/Modals'
import useErrorHandler from 'hooks/useErrorHandler'
import { useRouter } from 'next/router'
import { UserTitle } from 'types/enums/UserTitle'
import useUpload from 'hooks/useUpload'
import toast from 'react-hot-toast'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'

type UpdateUserProfileProps = {
  first_name: string
  last_name: string
  title: string
  suffix: string
  job_bio: string
  job_role: string
  profile_image: File[]
  phone: string
  password: string
  newPassword: string
  email: string
  sra_clc_number: string
}

export default function Profile () {
  const router = useRouter()
  const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false)
  const [isForgottenPasswordModalOpen, setIsForgottenPasswordModalOpen] = useState(false)
  const errorHandler = useErrorHandler()
  const { uploadFiles } = useUpload()

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const { register, handleSubmit, setError, control, reset, formState: { errors }, watch } = useForm<UpdateUserProfileProps>({
    defaultValues: {
      first_name: user?.first_name ?? '',
      last_name: user?.last_name ?? '',
      title: user?.title ?? '',
      suffix: user?.suffix ?? '',
      job_bio: user?.job_bio ?? '',
      job_role: user?.job_role ?? '',
      profile_image: undefined,
      phone: user?.phone ?? '',
      email: user?.email ?? '',
      password: undefined,
      newPassword: undefined,
      sra_clc_number: user?.sra_clc_number ?? '',
    },
  })

  const newPassword = watch('newPassword')


  useEffect(() => {
    if (user) {
      reset({
        first_name: user.first_name ?? '',
        last_name: user.last_name ?? '',
        title: user.title ?? '',
        suffix: user.suffix ?? '',
        job_bio: user.job_bio ?? '',
        job_role: user.job_role ?? '',
        phone: user.phone ?? '',
        email: user.email,
        sra_clc_number: user.sra_clc_number ?? '',
      })
    }
  }, [user, reset])

  const [{ fetching: isUpdateUserLoading }, updateUserProfileMutation] = useMutation(graphql(`
    mutation updateUserProfile ($input: UpdateUserProfileInput!) {
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
        sra_clc_number
      }
    }
  `))

  const [{ fetching: isDeleteUserLoading }, deleteUserMutation] = useMutation(graphql(`
    mutation deleteUser {
      deleteUser
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
        title: form.title,
        suffix: form.suffix,
        job_bio: form.job_bio,
        job_role: form.job_role,
        phone: form.phone,
        password: form.password,
        newPassword: form.newPassword,
        email: form.email,
        ...upload ? {
          profile_image: {
            key: upload.key,
            extension: upload.extension,
          },
        } : {
          profile_image: null,
        },
        sra_clc_number: form.sra_clc_number,
      },
    })

    if (updateUserProfile.error) {
      errorHandler(updateUserProfile.error, setError)
      toast.error('Something went wrong. Please try again.')
    } else {
      toast.success('Your changes have been saved')
    }
  }

  const handleDeleteUser = async () => {
    await deleteUserMutation({})
    router.push('/register')
  }

  const handleForgottenPassword = async () => {
    setIsForgottenPasswordModalOpen(false)
    const resetPassword = await forgotPasswordMutation({
      email: user?.email as string,
    })

    if (resetPassword.error) {
      toast.error('Something went wrong. Please try again.')
    } else {
      toast.success('Email sent successfully.')
    }
  }

  return (
    <>
      <NextSeo
        title="Profile Settings"
      />
      <ProtectedLayout>
        <ConveyancerPortalLayout>
          <ConveyancerPortalLayout.MainContent>
            <div className="mb-[3.125rem]">
              <div className="mb-[1.875rem] flex justify-between sm:items-center items-start  flex-col sm:flex-row gap-5">
                <H1>Profile Settings</H1>
                <Button loading={isUpdateUserLoading} onClick={handleSubmit(onSubmit)}>Save Changes</Button>
              </div>

              <div className="flex flex-col gap-5 mb-5">
                <div className="flex flex-col justify-between w-full bg-white rounded-xl">
                  <div className="px-5 py-6">
                    <H3>Personal Information</H3>
                  </div>
                  <hr />

                  <div className="px-5 py-6">
                    <Form>
                      <Form.Group>
                        <Form.Input
                          label="First Name"
                          defaultValue={user?.first_name ?? ''}
                          error={errors.first_name?.message}
                          {...register('first_name')}
                        />

                        <Form.Input
                          label="Last Name"
                          defaultValue={user?.last_name ?? ''}
                          error={errors.last_name?.message}
                          {...register('last_name')}
                        />
                      </Form.Group>

                      <Form.Group>
                        <Controller
                          control={control}
                          name="title"
                          render={({ field }) => {
                            return (
                              <Form.Select
                                label="Title"
                                onChange={e => field.onChange(e.value)}
                                defaultValue={{ text: Object.entries(UserTitle).find(([_, v]) => v === user?.title)?.[0] ?? '', value: user?.title ?? '' }}
                                error={errors.title?.message}
                                options={
                                  (Object.keys(UserTitle) as Array<keyof typeof UserTitle>)
                                    .map(k => ({ text: k, value: UserTitle[k] }))
                                }
                              />
                            )
                          }}
                        />

                        <Form.Input
                          label="Suffix"
                          defaultValue={user?.suffix ?? ''}
                          error={errors.suffix?.message}
                          {...register('suffix')}
                        />
                      </Form.Group>
                      <Form.Group>
                        <Controller
                          control={control}
                          name="job_role"
                          defaultValue={user?.role}
                          render={({ field }) => {
                            return (
                              <Form.Select
                                label="Job role"
                                onChange={(e) => field.onChange(e.value)}
                                defaultValue={{
                                  text: Object.entries(JobRole).find(([_, v]) => v === user?.job_role)?.[0] ?? '',
                                  value: user?.job_role ?? '',
                                }}
                                error={errors.job_role?.message}
                                options={(Object.keys(JobRole) as Array<keyof typeof JobRole>).map(k => ({ text: k, value: JobRole[k] }))}
                              />
                            )
                          }}
                        />
                        <Form.Input
                          label="Individual SRA/CLC number"
                          placeholder="e.g. 12345678"
                          error={errors.sra_clc_number?.message}
                          defaultValue={user?.sra_clc_number || undefined}
                          {...register('sra_clc_number')}
                          SubLabel="Optional"
                        />
                      </Form.Group>

                      <Form.Input
                        label="Job bio"
                        defaultValue={user?.job_bio ?? ''}
                        error={errors.job_bio?.message}
                        {...register('job_bio')}
                      />


                      <Label>Profile Photo</Label>
                      <Controller
                        name="profile_image"
                        control={control}
                        render={({ field }) => {
                          return (
                            <ImageUpload
                              onChange={field.onChange}
                              onRemove={() =>
                                field.onChange(null)}
                              error={errors.profile_image?.message}
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
                    </Form>
                  </div>
                </div>

                <div className="flex flex-col justify-between w-full bg-white rounded-xl">
                  <div className="flex items-center justify-between px-5 py-6">
                    <H3>Account</H3>

                  </div>
                  <hr />

                  <div className="px-5 py-6">
                    <Form>
                      <Form.Group>
                        <Form.Input
                          label="Direct dial phone number"
                          defaultValue={user?.phone ?? ''}
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
                    </Form>
                  </div>
                </div>

                <div className="flex flex-col justify-between w-full bg-white rounded-xl">
                  <div className="flex flex-col items-start justify-between px-5 py-6 sm:items-center sm:flex-row">


                    <H3>Password Change</H3>

                    <Button loading={isForgotPasswordLoading} variant="link" onClick={handleSubmit(handleForgottenPassword)}>Forgot Password?</Button>
                  </div>
                  <hr />

                  <div className="px-5 py-6">
                    <Form>
                      <Form.Group>
                        <Form.Input
                          label="Current Password"
                          placeholder="⦁⦁⦁⦁⦁⦁⦁⦁⦁"
                          type="password"
                          error={errors.password?.message}
                          {...register('password', {
                            required: newPassword ? true : false,
                          })}
                        />

                        <Form.Input
                          label="New Password"
                          placeholder="⦁⦁⦁⦁⦁⦁⦁⦁⦁"
                          type="password"
                          error={errors.newPassword?.message}
                          {...register('newPassword')}
                        />
                      </Form.Group>
                    </Form>
                  </div>
                </div>

                <div className="flex flex-col justify-between w-full bg-white rounded-xl">
                  <div className="px-5 py-6">
                    <H3>Delete account</H3>
                  </div>
                  <hr />

                  <div className="flex flex-col items-center justify-between gap-5 px-5 py-6 md:flex-row">
                    <div className="flex items-center gap-4">
                      <WarningIcon className="w-5 h-5 text-danger min-w-[1.25rem]" />
                      <p className="text-base text-body">If you delete your ProConvey account, your data will be gone forever.</p>
                    </div>
                    <Button onClick={() => setIsDeleteModalOpen(true)} variant="danger">Delete Account</Button>
                  </div>
                </div>
              </div>


            </div>
            <Modal isOpen={isDeleteModalOpen} onClose={() => setIsDeleteModalOpen(false)}>
              <Modal.Title>Delete ProConvey account</Modal.Title>
              <Modal.ContentTitle>
                Are you sure you want to delete your ProConvey account?
              </Modal.ContentTitle>
              <Modal.Content>
                <div className="flex items-center gap-4 mt-6 text-body">
                  <WarningIcon className="w-5 h-5 text-danger  min-w-[1.25rem]" />
                  If you delete your ProConvey account, your data will be gone forever.
                </div>
              </Modal.Content>
              <Modal.Footer>
                <div className="flex flex-wrap justify-end gap-5">
                  <Button onClick={handleSubmit(handleDeleteUser)} loading={isDeleteUserLoading} size="small">Yes, delete account</Button>
                  <Button onClick={() => setIsDeleteModalOpen(false)} size="small" variant="secondary">Cancel</Button>
                </div>
              </Modal.Footer>
            </Modal>

            <Modal isOpen={isForgottenPasswordModalOpen} onClose={() => setIsForgottenPasswordModalOpen(false)}>
              <Modal.Title>Forgot your password?</Modal.Title>
              <Modal.Content>
                An email has been sent to <b>{user?.email}</b> with instructions to reset your password.
              </Modal.Content>
              <Modal.Footer>
                <Button onClick={() => setIsForgottenPasswordModalOpen(false)} size="small">OK</Button>
              </Modal.Footer>
            </Modal>
          </ConveyancerPortalLayout.MainContent>
        </ConveyancerPortalLayout>
      </ProtectedLayout>
    </>
  )
}
