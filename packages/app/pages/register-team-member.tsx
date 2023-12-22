import Form from '@proconvey/ui/src/components/Form'
import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import Screen from '../public/screen.png'
import Image from 'next/image'
import AuthLayout from 'layouts/AuthLayout'
import { useForm, SubmitHandler, Controller } from 'react-hook-form'
import { useMutation } from 'urql'
import { SelectOption } from '@proconvey/ui/src/components/Form/Select'
import { useRouter } from 'next/router'
import { useState } from 'react'
import useErrorHandler from 'hooks/useErrorHandler'
import Alert from '@proconvey/ui/src/components/Alert'
import { useDispatch } from 'react-redux'
import { login } from 'slices/auth'
import { UserTitle } from 'types/enums/UserTitle'
import Link from 'next/link'
import Checkbox from '@proconvey/ui/src/components/Form/Checkbox'
import { graphql } from 'gql'
import toast from 'react-hot-toast'
import { NextSeo } from 'next-seo'

type RegisterTeamMemberProps = {
  first_name: string
  last_name: string
  password: string
  email: string
  phone: string
  role: string
  title: string
  suffix: string
  invite_code: string
  invite_email: string
  checkbox: boolean
  user_id: string
}

export default function RegisterTeamMember () {
  const router = useRouter()
  const errorHandler = useErrorHandler()
  const dispatch = useDispatch()
  const [isLoading, setIsLoading] = useState(false)
  const [isCheckboxSelected, setIsCheckboxSelected] = useState(false)

  const [_, registerTeamMemberMutation] = useMutation(graphql(`
    mutation registerTeamMember($input: RegisterTeamMemberInput!) {
      registerTeamMember(input: $input) {
        id
        first_name
        last_name
        role
        email
      }
    }
  `))


  const { register, handleSubmit, control, setError, formState: { errors }, watch } = useForm<RegisterTeamMemberProps>()

  const onSubmit: SubmitHandler<RegisterTeamMemberProps> = async (form) => {
    setIsLoading(true)

    const response = await registerTeamMemberMutation({
      input: {
        first_name: form.first_name,
        last_name: form.last_name,
        password: form.password,
        email: form.email,
        phone: form.phone,
        suffix: form.suffix,
        title: form.title ?? '',
        invite_code: router?.query?.token as string,
        user_id: router?.query?.id as string,
      },
    })

    if (response.error) {
      toast.error('Something went wrong. Please try again.')
      errorHandler(response.error, setError)
      setIsLoading(false)
    } else {
      toast.success('You have successfully registered!')
      dispatch(login(response.data!.registerTeamMember))
      router.push('/profile-information')
    }
  }

  return (
    <>
      <NextSeo
        title="Create your account"
      />
      <AuthLayout>
        <AuthLayout.Left>
          <div className="max-w-[28.125rem] mx-auto">
            <div className="mt-5 mb-20">
              <Logo className="w-[8.4375rem] h-[3.125rem]" />
            </div>
            <H1>Create your account</H1>
            <p className="text-lg mt-[1.25rem] leading-[1.375rem] mb-[3.125rem] text-body opacity-60">Please enter your details below to create account</p>
            <Form onSubmit={handleSubmit(onSubmit)}>
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
                />
              </Form.Group>

              <Form.Input
                label="Phone Number"
                placeholder="+44 1234 567890"
                error={errors.phone?.message}
                {...register('phone')}
              />

              <Form.Input
                label="Email"
                type="email"
                defaultValue={router?.query?.email as string}
                error={errors.email?.message}
                {...register('email')}
              />

              <Form.Input
                label="Password"
                placeholder="••••••••"
                type="password"
                help="Must be a mix of at least 8 upper and lower case characters"
                error={errors.password?.message}
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
                    Agree with <Link target="_blank" href="https://proconvey.co.uk/terms-and-conditions" className="ml-1 text-primary">Terms and Conditions</Link>
                  </Checkbox>
                )}
              </Checkbox.Group>
              {
                watch('password')?.length > 0 && watch('password')?.length < 8 &&
                <Alert variant="danger">
                  Password must be a mix of at least 8 upper and lower case characters
                </Alert>
              }

              {
                isCheckboxSelected
                  ? <Button type="submit" block loading={isLoading}>Next</Button>
                  : <Button type="submit" block loading={isLoading} disabled>Next</Button>
              }
            </Form>

            {
              Object.keys(errors).length > 0 &&
              <Alert variant="danger">
                There was a problem registering your account. Please try again.
              </Alert>
            }

          </div>

        </AuthLayout.Left>
        <AuthLayout.Right>
          <Image src={Screen} alt="" className="object-cover object-right h-full" />
        </AuthLayout.Right>
      </AuthLayout>
    </>
  )
}
