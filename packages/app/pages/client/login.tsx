import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import Alert from '@proconvey/ui/src/components/Alert'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import Screen from '../../public/screen.png'
import Image from 'next/image'
import Link from 'next/link'
import { useMutation } from 'urql'
import { SubmitHandler, useForm } from 'react-hook-form'
import { useDispatch } from 'react-redux'
import { useRouter } from 'next/router'
import { useEffect, useState } from 'react'
import { login } from 'slices/auth'
import AuthLayout from 'layouts/AuthLayout'
import { graphql } from 'gql'
import toast from 'react-hot-toast'
import useErrorHandler from 'hooks/useErrorHandler'
import { NextSeo } from 'next-seo'

type LoginProps = {
  email: string
  password: string
}

export default function LoginPage () {
  const [isLoading, setIsLoading] = useState(false)
  const errorHandler = useErrorHandler()
  const router = useRouter()

  const { register, handleSubmit, setError, formState: { errors } } = useForm<LoginProps>()
  const dispatch = useDispatch()

  const [loginMutationResult, loginMutation] = useMutation(graphql(`
    mutation loginClient($input: LoginInput!) {
      login(input: $input) {
        id
        title
        first_name
        last_name
        suffix
        phone
        email
        role
      }
    } 
  `))

  const onSubmit: SubmitHandler<LoginProps> = async (form) => {
    setIsLoading(true)
    const response = await loginMutation({
      input: {
        email: form.email,
        password: form.password,
      },
    })

    if (response.error) {
      toast.error('Something went wrong. Please try again!')
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      toast.success('You have successfully accepted your invitation!')
      router.push('/')
    }
  }

  useEffect(() => {
    if (loginMutationResult?.data?.login) {
      dispatch(login(loginMutationResult.data.login))
    }
  }, [loginMutationResult.data, dispatch])

  return (
    <>
      <NextSeo
        title="Log in"
      />
      <AuthLayout>
        <AuthLayout.Left>
          <div className="max-w-[28.125rem] mx-auto my-5 flex flex-col min-h-full justify-center">
            <div className="mb-[5rem]">
              <Logo className="w-[8.4375rem] h-[3.125rem]" />
            </div>

            <H1>Welcome back!</H1>
            <p className="text-lg leading-[1.375rem] mt-[18px] mb-[3.125rem] text-body opacity-60">Please enter your details below to login</p>

            <Form onSubmit={handleSubmit(onSubmit)}>
              <Form.Input
                label="Email"
                placeholder="name@company.com"
                type="email"
                error={errors.email?.message}
                {...register('email')}
              />

              <Form.Input
                label="Password"
                placeholder="•••••••••"
                type="password"
                className="mt-[0.625rem]"
                error={errors.password?.message}
                {...register('password')}
              />

              <Link href="/client/forgot-password" className="text-base text-primary">Forgot Password?</Link>

              {
                loginMutationResult.error &&
                <Alert variant="danger">Invalid Email Address or Password</Alert>
              }

              <Button type="submit" block className="mt-[1.825rem]" loading={isLoading}>Login</Button>
            </Form>

            <div className="flex items-center mt-[1.875rem] gap-1">
              <p className="text-sm text-body opacity-80">Do not have a ProConvey account?</p>
              <Link href="/client/register" className="text-sm text-primary">
                Sign Up
              </Link>
            </div>

          </div>
        </AuthLayout.Left>

        <AuthLayout.Right>
          <Image src={Screen} alt="" className="object-cover object-right h-full" />
        </AuthLayout.Right>
      </AuthLayout>
    </>
  )
}
