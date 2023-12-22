import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import Screen from '../../public/screen.png'
import Image from 'next/image'
import AuthLayout from 'layouts/AuthLayout'
import { useRouter } from 'next/router'
import { SubmitHandler, useForm } from 'react-hook-form'
import { useMutation } from 'urql'
import { useState } from 'react'
import { useNotifier } from 'react-headless-notifier'
import Alert from '@proconvey/ui/src/components/Alert'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import { NextSeo } from 'next-seo'

type ResetPasswordProps = {
  email: string
  password: string
  password_confirmation: string
  token: string | null
}

export default function ResetPassword () {
  const router = useRouter()
  const { notify } = useNotifier()
  const [isLoading, setIsLoading] = useState(false)
  const errorHandler = useErrorHandler()

  const { register, handleSubmit, watch, setError, formState: { errors } } = useForm<ResetPasswordProps>()

  const [_, resetPasswordMutation] = useMutation(graphql(`
    mutation resetPassword($input: ResetPasswordInput!) {
      resetPassword(input: $input) 
    }
  `))

  const onSubmit: SubmitHandler<ResetPasswordProps> = async (form) => {
    setIsLoading(true)
    const response = await resetPasswordMutation({
      input: {
        token: router.query.token as string,
        email: router.query.email as string,
        password: form.password,
        password_confirmation: form.password_confirmation,
      },
    })

    if (response.error) {
      notify({
        title: 'Error',
        message: response.error.message,
        type: 'error',
      })
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      notify({
        title: 'Success',
        message: 'Password reset successfully',
        type: 'success',
      })
      router.push('/client/login')
    }
  }

  return (
    <>
      <NextSeo
        title="Reset password"
      />
      <AuthLayout>
        <AuthLayout.Left>
          <div className="max-w-[28.125rem] mx-auto">
            <div className="mt-[8.8125rem] mb-[5rem]">
              <Logo className="w-[8.4375rem] h-[3.125rem]" />
            </div>
            <H1>Reset password</H1>
            <p className="text-lg leading-[1.375rem] mt-[18px] mb-[3.125rem] text-body opacity-60">Your new password must be different to previously used passwords</p>

            <Form onSubmit={handleSubmit(onSubmit)}>
              <Form.Input
                label="Password"
                type="password"
                help="Must be a mix of at least 8 upper and lower case characters"
                error={errors.password?.message}
                {...register('password')}
              />

              <Form.Input
                label="Confirm password"
                type="password"
                error={errors.password_confirmation?.message}
                {...register('password_confirmation')}
              />

              {
                (watch('password')?.length > 1 && watch('password_confirmation')?.length > 1)
                  ? (watch('password') === watch('password_confirmation'))
                    ? <p className="text-sm text-green-700">These passwords match</p>
                    : <Alert variant="danger">These passwords do not match</Alert>
                  : null
              }

              <Button loading={isLoading} type="submit" block>Reset Password</Button>
            </Form>

          </div>

        </AuthLayout.Left>
        <AuthLayout.Right>
          <Image src={Screen} alt="" className="object-cover object-right h-full" />
        </AuthLayout.Right>
      </AuthLayout>
    </>
  )
}
