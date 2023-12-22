import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import Label from '@proconvey/ui/src/components/Form/Label'
import { PhotoUploadIcon } from '@proconvey/ui/src/icons'
import { OnboardingProfileImage } from '@proconvey/ui/src/images'
import Link from 'next/link'
import { gql, useMutation } from 'urql'
import { useEffect, useState } from 'react'
import { SubmitHandler, useForm } from 'react-hook-form'
import SetupLayout from 'layouts/SetupLayout'
import { Controller } from 'react-hook-form'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'
import Vapor from 'laravel-vapor'
import Cookies from 'js-cookie'
import Alert from '@proconvey/ui/src/components/Alert'
import { useRouter } from 'next/router'
import useErrorHandler from 'hooks/useErrorHandler'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import ProtectedLayout from 'layouts/ProtectedLayout'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'
import ClientProfileLayout from 'layouts/ClientProfileLayout'
import { ClientUserTitle } from 'types/enums/ClientUserTitle'


type UpdateClientDetailsProps = {
  first_name: string
  last_name: string
  phone: string
  profile_image: File[]
  title: string
}

export default function ClientProfileSetup () {
  const router = useRouter()
  const errorHandler = useErrorHandler()
  const [isLoading, setIsLoading] = useState(false)
  const [previewUrl, setPreviewUrl] = useState<string | undefined>()

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  const { register, handleSubmit, setError, control, reset, formState: { errors } } = useForm<UpdateClientDetailsProps>({
    defaultValues: {
      first_name: user?.first_name ?? '',
      last_name: user?.last_name ?? '',
      title: user?.title ?? '',
      phone: user?.phone ?? '',
    },
  })

  useEffect(() => {
    if (user) {
      setPreviewUrl(user?.profile_image?.url)
      reset({
        first_name: user?.first_name ?? '',
        last_name: user?.last_name ?? '',
        title: user?.title ?? '',
        phone: user?.phone ?? '',
      })
    }
  }, [user, reset])

  const [_, updateClientDetailsMutation] = useMutation(gql(`
    mutation updateClientDetails ($input: UpdateClientDetailsInput!) {
      updateClientDetails(input: $input) {
        first_name
        last_name
        title
        phone
      }
    }
  `))

  const onSubmit: SubmitHandler<UpdateClientDetailsProps> = async (form) => {
    setIsLoading(true)
    const token = Cookies.get('XSRF-TOKEN')

    let upload
    if (form.profile_image) {
      upload = await Vapor.store(form.profile_image[0], {
        baseURL: process.env.NEXT_PUBLIC_API_ENDPOINT,
        options: {
          withCredentials: 'include',
        },
        headers: {
          'X-XSRF-TOKEN': token,
        },
      })
    }

    const result = await updateClientDetailsMutation({
      input: {
        first_name: form.first_name,
        last_name: form.last_name,
        title: form.title,
        phone: form.phone,
        ...(upload
          ? {
            profile_image: {
              key: upload.key,
              extension: upload.extension,
            },
          }
          : {
            profile_image: null,
          }),
      },
    })

    if (result.error) {
      errorHandler(result.error, setError)
      setIsLoading(false)
      return
    } else {
      router.push('/properties')
    }

  }

  return (
    <>
      <NextSeo
        title="Complete your profile"
      />
      <ProtectedLayout>
        <ClientProfileLayout>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Add your details</H1>
            </div>

            <div className="pb-[20px] bg-white rounded-[0.625rem]">

              <div className="py-[1.5rem] px-[1.25rem]">
                <H3>Profile information</H3>
              </div>

              <hr />
              <div className="flex flex-col justify-between lg:flex-row mt-[2.5rem] mb-[3.5rem] mx-[1.25rem] gap-x-5">
                {/* Desktop View */}
                <div className="w-full max-w-[40.5rem] flex-col hidden lg:flex">
                  <p className="text-base text-body">Enter your personal information</p>
                  <br />
                  <p className="text-base text-body text-opacity-80">This is the basic information for your account that only your conveyancer will see.</p>
                  <br />
                  <p className="text-base text-body text-opacity-80">You can also add a picture of you for your account if you want to.</p>
                </div>

                {/* Mobile View + Image */}
                <p className="text-base lg:hidden text-body">Enter your personal information</p>
                <br className="lg:hidden" />
                <OnboardingProfileImage className="w-full max-w-[320px] h-full max-h-[180px] lg:mr-[2.4375rem] mx-auto" />
                <br className="lg:hidden" />
                <p className="text-base lg:hidden text-body text-opacity-80">This is the basic information for your account that only your conveyancer will see.</p>
                <br className="lg:hidden" />
                <p className="text-base lg:hidden text-body text-opacity-80">You can also add a picture of you for your account if you want to.</p>
              </div>

              <div className="mt-[1.5rem] mx-[1.25rem]">
                <Form>
                  <Form.Group>
                    <Form.Input
                      label="First Name"
                      placeholder="Enter your first name"
                      error={errors.first_name?.message}
                      {...register('first_name')}
                    />

                    <Form.Input
                      label="Last Name"
                      placeholder="Enter your last name"
                      error={errors.last_name?.message}
                      {...register('last_name')}
                    />
                  </Form.Group>

                  <Form.Group>
                    <Controller
                      control={control}
                      name="title"
                      render={({ field }) => {
                        const handleOnChange = (e: SelectOption) => {
                          field.onChange(e.value)
                        }

                        return (
                          <Form.Select
                            placeholder="Select"
                            label="Title"
                            onChange={handleOnChange}
                            error={errors.title?.message}
                            options={
                              (Object.keys(ClientUserTitle) as Array<keyof typeof ClientUserTitle>)
                                .map(k => ({ text: k, value: ClientUserTitle[k] }))
                            }
                          />
                        )
                      }}
                    />

                    <Form.Input
                      label="Phone number"
                      placeholder="+44 1234 567890"
                      error={errors.phone?.message}
                      {...register('phone')}
                    />
                  </Form.Group>

                  <Label>Profile photo</Label>
                  <Controller
                    name="profile_image"
                    control={control}
                    render={({ field }) => {
                      return (
                        <ImageUpload
                          onChange={field.onChange}
                          error={errors.profile_image?.message}
                          type="profile_image"
                          defaultPreviewUrl={user?.profile_image?.url}
                          //SubLabel="Optional" (Implemented in PPBS-7)
                          placeholder={
                            <div className="flex items-center justify-center w-20 h-20">
                              <PhotoUploadIcon className="w-10 h-10 text-primary" />
                            </div>
                          }
                        />
                      )
                    }}
                  />

                  {
                    Object.keys(errors).length > 0 &&
                    <Alert variant="danger">
                      There was a problem updating your details. Please try again.
                    </Alert>
                  }
                </Form>
              </div>
            </div>

            <div className="flex justify-end mt-[2.5rem]">
              <Link href="/business-setup" onClick={handleSubmit(onSubmit)}>
                <Button loading={isLoading}>Complete</Button>
              </Link>
            </div>
          </SetupLayout.MainContent>
        </ClientProfileLayout>
      </ProtectedLayout>
    </>
  )
}
