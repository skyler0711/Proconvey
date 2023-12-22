import { useMutation } from 'urql'
import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import Screen from '../public/screen.png'
import Image from 'next/image'
import { ChevronDownIcon, EmailConfirmationIcon } from '@proconvey/ui/src/icons'
import AuthLayout from 'layouts/AuthLayout'
import { SubmitHandler, useForm } from 'react-hook-form'
import { useState } from 'react'
import Link from 'next/link'
import { graphql } from 'gql'
import useErrorHandler from 'hooks/useErrorHandler'
import { toast } from 'react-hot-toast'
import { NextSeo } from 'next-seo'

type ForgotPasswordProps = {
  email: string
  reset_throttle: {
    message: string | undefined
  }
}

export default function ForgotPassword () {
  const [isLoading, setIsLoading] = useState(false)
  const errorHandler = useErrorHandler()

  const [{ data }, forgotPasswordMutation] = useMutation(graphql(`
    mutation forgottenPassword ($email: String!) {
      forgottenPassword(email: $email) 
    }
  `))

  const { register, handleSubmit, watch, setError, clearErrors, formState: { errors } } = useForm<ForgotPasswordProps>()

  const onSubmit: SubmitHandler<ForgotPasswordProps> = async (form) => {
    setIsLoading(true)

    const result = await forgotPasswordMutation({
      email: form.email,
    })

    if (result.error) {
      errorHandler(result.error, setError)
    }
    setIsLoading(false)
  }

  if (errors.reset_throttle) {
    toast.error('Too many reset attempts. Please try again later.')
    clearErrors()
  }

  return (
    <>
      <NextSeo
        title="Forgot Password"
      />
      <div>
        {
          data && data.forgottenPassword === true ? (
            <div className="max-w-[22.5rem] mx-auto text-center mt-[10.625rem]">
              <div className="flex justify-center mb-[2.1875rem]">
                <EmailConfirmationIcon className="w-[130px] h-[130px]" />
              </div>

              <H1>Check your email</H1>

              <p className="text-lg leading-[1.375rem] mt-[1.125rem] text-body opacity-60">If the account exists a password reset link will be sent to</p>
              <p className="text-body mb-[2.1875rem] mt-[1.125rem]">
                {watch('email')}
              </p>
              <p className="text-lg leading-[1.375rem] mt-[1.125rem] text-body opacity-60">This password reset link will expire in 60 minutes</p>

              <p className="text-lg flex items-center justify-center gap-1 leading-[1.375rem] mt-[1.875rem] mb-[1.9375rem] text-body opacity-80">
                Didn&apos;t receive the email? <Button variant="link" onClick={handleSubmit(onSubmit)}>Resend</Button>
              </p>

              <Link href={'/login'}>
                <div className="flex items-center gap-3 mt-[1.3125rem] justify-center">
                  <ChevronDownIcon className="w-5 h-5 rotate-90 text-primary" />
                  <p className="text-sm text-primary">Back to Login</p>

                </div>
              </Link>

            </div>
          ) :
            (
              <AuthLayout>
                <AuthLayout.Left>
                  <div className="max-w-[28.125rem] mx-auto">
                    <div className="mt-[8.8125rem] mb-[5rem]">
                      <Logo className="w-[8.4375rem] h-[3.125rem]" />
                    </div>

                    <H1>Forgot password?</H1>

                    <p className="text-lg leading-[1.375rem] mt-[18px] mb-[3.125rem] text-body opacity-60">
                      No worries, we&apos;ll send you reset instructions
                    </p>

                    <Form onSubmit={handleSubmit(onSubmit)}>
                      <Form.Input
                        label="Email"
                        placeholder="name@company.com"
                        error={errors.email?.message}
                        {...register('email')}
                      />

                      <Button type="submit" block loading={isLoading}>Reset Password</Button>
                    </Form>

                    <Link href={'/login'}>
                      <div className="flex items-center gap-3 mt-[1.3125rem] justify-center">
                        <ChevronDownIcon className="w-5 h-5 rotate-90 text-primary" />
                        <p className="text-sm text-primary">Back to Login</p>
                      </div>
                    </Link>
                  </div>

                </AuthLayout.Left>
                <AuthLayout.Right>
                  <Image src={Screen} alt="" className="object-cover object-right h-full" />
                </AuthLayout.Right>
              </AuthLayout>
            )
        }
      </div>
    </>
  )
}
