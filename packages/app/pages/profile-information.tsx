import Logo from '@proconvey/ui/src/svgs/logo'
import Form from '@proconvey/ui/src/components/Form'
import { H1, H3 } from '@proconvey/ui/src/components/Headers'
import { Controller, SubmitHandler, useForm } from 'react-hook-form'
import useErrorHandler from 'hooks/useErrorHandler'
import Button from '@proconvey/ui/src/components/Button'
import useUpload from 'hooks/useUpload'
import { useMutation } from 'urql'
import { graphql } from 'gql'
import Label from '@proconvey/ui/src/components/Form/Label'
import { useRouter } from 'next/router'
import ImageUpload from '@proconvey/ui/src/components/Form/ImageUpload'
import { PhotoUploadIcon } from '@proconvey/ui/src/icons'
import { NextSeo } from 'next-seo'

type UpdateInvitedTeamMemberProps = {
  job_bio: string
  profile_image: File[]
}

export default function ProfileInformation () {
  const { uploadFiles } = useUpload()
  const router = useRouter()
  const errorHandler = useErrorHandler()
  const { register, handleSubmit, control, setError, watch, formState: { errors } } = useForm<UpdateInvitedTeamMemberProps>()

  const [{ fetching: isUpdateTeamMemberLoading }, updateInvitedTeamMemberMutation] = useMutation(graphql(`
    mutation updateInvitedTeamMember($input: UpdateInvitedTeamMemberInput!) {
      updateInvitedTeamMember(input: $input) {
        job_bio
      }
    }
  `))

  const onSubmit: SubmitHandler<UpdateInvitedTeamMemberProps> = async (form) => {
    let upload = await uploadFiles(form.profile_image)

    const updateInvitedTeamMember = await updateInvitedTeamMemberMutation({
      input: {
        job_bio: form.job_bio,
        ...upload && {
          profile_image: {
            key: upload.key,
            extension: upload.extension,
          },
        },
      },
    })

    if (updateInvitedTeamMember.error) {
      errorHandler(updateInvitedTeamMember.error, setError)
    } else {
      router.push('/clients')
    }
  }

  return (
    <>
      <NextSeo
        title="Profile Information"
      />
      <div className="w-full h-full bg-blue-chalk">
        <div className="flex bg-white pt-[1.375rem] px-[3.125rem] pb-[1.125rem]">
          <Logo className="w-[8.4375rem] h-[3.125rem]" />
        </div>

        <div className="w-full max-w-[1066px] mx-auto mt-[3.125rem]">
          <H1 className="mb-[1.75rem]">Complete your account creation</H1>

          <div className="w-full h-full bg-white rounded-xl">
            <div className="flex flex-col px-5 py-[1.5rem]">
              <H3>Profile Information</H3>
            </div>
            <hr />

            <div className="py-[1.5rem] px-5">
              <Form>
                <Form.Input
                  label="Professional bio"
                  placeholder="e.g. I am a CLC Licensed Conveyancer in our Residential Conveyancing Department. I have been qualified for 19 years..."
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
                        error={errors.profile_image?.message}
                        type="profile_image"
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

          <Button
            className="ml-auto mt-[2.5rem]"
            disabled={!watch('job_bio')}
            loading={isUpdateTeamMemberLoading}
            onClick={handleSubmit(onSubmit)}
          >
            Complete
          </Button>

        </div>

      </div>
    </>
  )
}
