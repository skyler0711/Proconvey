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
import { JobRole } from 'types/enums/JobRole'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import ProtectedLayout from 'layouts/ProtectedLayout'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { NextSeo } from 'next-seo'
import { UserTitle } from 'types/enums/UserTitle'

type UpdateUserDetailsProps = {
  first_name: string
  last_name: string
  phone: string
  suffix: string
  job_role: string
  job_bio: string
  profile_image?: File[] | null
  title: string
  sra_clc_number: string
}

export default function ProfileSetup () {
  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))
  const router = useRouter()
  const errorHandler = useErrorHandler()
  const { register, handleSubmit, control, setError, formState: { errors }, reset } = useForm<UpdateUserDetailsProps>({
    defaultValues: {
      first_name: user?.first_name ?? '',
      last_name: user?.last_name ?? '',
      title: user?.title ?? '',
      suffix: user?.suffix ?? '',
      sra_clc_number: user?.sra_clc_number ?? '',
      phone: user?.phone ?? '',
      job_role: user?.job_role ?? '',
      job_bio: user?.job_bio ?? '',
      profile_image: undefined,
    },
  })
  const [isLoading, setIsLoading] = useState(false)

  useEffect(() => {
    if (user) {
      reset({
        first_name: user?.first_name ?? '',
        last_name: user?.last_name ?? '',
        title: user?.title ?? '',
        suffix: user?.suffix ?? '',
        sra_clc_number: user?.sra_clc_number ?? '',
        phone: user?.phone ?? '',
        job_role: user?.job_role ?? '',
        job_bio: user?.job_bio ?? '',
        profile_image: undefined,
      })
    }
  }, [user, reset])

  const [_, updateUserDetailsMutation] = useMutation(gql(`
    mutation updateUserDetails ($input: UpdateUserDetailsInput!) {
      updateUserDetails(input: $input) {
        job_role
        job_bio
        first_name
        last_name
        title
        suffix
        phone
        sra_clc_number
      }
    }
  `))

  const onSubmit: SubmitHandler<UpdateUserDetailsProps> = async (form) => {
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
    } else {
      upload = form.profile_image === undefined
        ? undefined
        : null
    }

    const result = await updateUserDetailsMutation({
      input: {
        job_role: form.job_role,
        job_bio: form.job_bio,
        first_name: form.first_name,
        last_name: form.last_name,
        title: form.title,
        suffix: form.suffix,
        phone: form.phone,
        sra_clc_number: form.sra_clc_number,
        ...upload ? {
          profile_image: {
            key: upload.key,
            extension: upload.extension,
          },
        } : {
          profile_image: upload,
        },
      },
    })

    if (result.error) {
      errorHandler(result.error, setError)
      setIsLoading(false)
      return
    }

    router.push('/register/business')
  }

  return (
    <>
      <NextSeo
        title="Complete your profile"
      />
      <ProtectedLayout>
        <SetupLayout currentStep={1}>
          <SetupLayout.MainContent>
            <div className="mb-[3.125rem]">
              <H1>Create your account</H1>
            </div>

            <div className="pb-[20px] bg-white rounded-[0.625rem]">

              <div className="py-[1.5rem] px-[1.25rem]">
                <H3>Profile information</H3>
              </div>

              <hr />
              <div className="flex flex-col justify-between lg:flex-row mt-[2.5rem] mb-[3.5rem] mx-[1.25rem] gap-x-5">
                {/* Desktop View */}
                <div className="w-full max-w-[40.5rem] flex-col hidden lg:flex">
                  <p className="text-base text-body">Enter your professional information</p>
                  <br />
                  <p className="text-base text-body text-opacity-80">This will be displayed to clients so it is important to select the appropriate job role, write a professional bio description and add a professional profile picture.</p>
                </div>

                {/* Mobile View + Image */}
                <p className="text-base lg:hidden text-body">Enter your professional information</p>
                <br className="lg:hidden" />
                <OnboardingProfileImage className="w-full max-w-[320px] h-full max-h-[180px] lg:mr-[2.4375rem] mx-auto" />
                <br className="lg:hidden" />
                <p className="text-base lg:hidden text-body text-opacity-80">This will be displayed to clients so it is important to select the appropriate job role, write a professional bio description and add a professional profile picture.</p>
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
                        return (
                          <Form.Select
                            placeholder="Select"
                            label="Title"
                            onChange={e => field.onChange(e.value)}
                            defaultValue={
                              user?.title
                                ? { text: user.title, value: user.title }
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

                    <Form.Input
                      label="Suffix"
                      placeholder="e.g. BSc"
                      error={errors.suffix?.message}
                      {...register('suffix')}
                      SubLabel="Optional"
                    />
                  </Form.Group>

                  <Form.Group>
                    <Controller
                      control={control}
                      name="job_role"
                      render={({ field }) => {
                        const handleOnChange = (e: SelectOption) => {
                          field.onChange(e.value)
                        }

                        const options = (Object.keys(JobRole) as Array<keyof typeof JobRole>)
                          .map(k => ({ text: k, value: JobRole[k] }))

                        return (
                          <Form.Select
                            placeholder="Select"
                            label="Job role"
                            defaultValue={user?.job_role ? options.find(o => o.value === user?.job_role) : undefined}
                            onChange={handleOnChange}
                            options={options}
                            error={errors.job_role?.message}
                          />
                        )
                      }}
                    />
                    <Form.Input
                      label="Direct dial phone number"
                      placeholder="+44 1234 567890"
                      error={errors.phone?.message}
                      {...register('phone')}
                      SubLabel="Optional"
                    />
                  </Form.Group>

                  <Form.Group>
                    <Form.Input
                      label="Individual SRA/CLC number"
                      placeholder="e.g. 12345678"
                      error={errors.sra_clc_number?.message}
                      defaultValue={user?.sra_clc_number || undefined}
                      {...register('sra_clc_number')}
                      SubLabel="Optional"
                    />
                  </Form.Group>

                  <Form.Group>
                    <Form.Input
                      label="Job Bio"
                      placeholder="Write something about your role, e.g. I am a director"
                      required
                      error={errors.job_bio?.message}
                      {...register('job_bio')}
                      SubLabel="Optional"
                    />
                  </Form.Group>

                  <Label>Profile photo<span className="opacity-50 text-base opacity-50" style={{ color: 'rgba(61, 64, 61, 0.6)' }}>  (Optional)</span></Label>
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
                <Button loading={isLoading}>Next</Button>
              </Link>
            </div>
          </SetupLayout.MainContent>
        </SetupLayout>
      </ProtectedLayout>
    </>
  )
}
