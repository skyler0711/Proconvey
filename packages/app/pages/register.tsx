import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import Screen from '../public/screen.png'
import Image from 'next/image'
import AuthLayout from 'layouts/AuthLayout'
import { useForm, SubmitHandler } from 'react-hook-form'
import { useMutation } from 'urql'
import { gql } from 'urql'
import { useRouter } from 'next/router'
import { useState } from 'react'
import useErrorHandler from 'hooks/useErrorHandler'
import Alert from '@proconvey/ui/src/components/Alert'
import { useDispatch } from 'react-redux'
import { login } from 'slices/auth'
import Link from 'next/link'
import Checkbox from '@proconvey/ui/src/components/Form/Checkbox'
import toast from 'react-hot-toast'
import { NextSeo } from 'next-seo'
import { RegisterInput } from 'gql/graphql'

export default function Register () {
  const router = useRouter()
  const errorHandler = useErrorHandler()
  const dispatch = useDispatch()
  const [isLoading, setIsLoading] = useState(false)
  const [isCheckboxSelected, setIsCheckboxSelected] = useState(false)

  const [_, registerMutation] = useMutation(gql`
    mutation register($input: RegisterInput!) {
      register(input: $input) {
          id
          email
        }
    }
  `)

  const { register, handleSubmit, setError, formState: { errors } } = useForm<RegisterInput>()

  const onSubmit: SubmitHandler<RegisterInput> = async (form) => {
    setIsLoading(true)

    const response = await registerMutation({
      input: {
        password: form.password,
        email: form.email,
      },
    })

    if (response.error) {
      toast.error('Something went wrong. Please try again.')
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      toast.success('Account created successfully')
      dispatch(login(response.data.register))
      router.push('/register/profile')
    }
  }


  return (
    <>
      <NextSeo
        title="Register"
      />
      <AuthLayout>
        <AuthLayout.Left>
          <div className="max-w-[28.125rem] mx-auto">
            <div className="mt-5 mb-20">
              <Logo className="w-[8.4375rem] h-[3.125rem]" />
            </div>
            <H1>Create your account</H1>
            <p className="text-lg mt-[1.25rem] leading-[2.375rem] mb-[3.125rem] text-body opacity-60">Please enter your details below to create account
              <br />
              Already have a ProConvey account? <Link href="/login" className="text-sm text-primary">Login</Link>
            </p>
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
                error={errors.password?.message}
                help="The password must be at least 8 characters, and be a mix of upper and lower case characters."
                {...register('password')}
              />

              <Checkbox.Group>
                {({ selected, onChange: onSelectChange }) => (
                  <Checkbox
                    value="1"
                    size="small"
                    selected={selected}
                    onChange={(value) => {
                      onSelectChange(value)
                      setIsCheckboxSelected(!isCheckboxSelected)
                    }}
                  >

                    Agree with<Link target="_blank" href="https://proconvey.co.uk/terms-and-conditions" className="ml-1 mr-1 text-primary">Terms & Conditions</Link> and <Link target="_blank" href="https://proconvey.co.uk/privacy-policy" className="ml-1 text-primary">Privacy policy</Link>
                  </Checkbox>
                )}
              </Checkbox.Group>

              {
                Object.keys(errors).length > 0 &&
                <Alert variant="danger">
                  There was a problem registering your account. Please try again.
                </Alert>
              }

              {
                isCheckboxSelected
                  ? <Button type="submit" block loading={isLoading}>Next</Button>
                  : <Button type="submit" block loading={isLoading} disabled>Next</Button>
              }
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
