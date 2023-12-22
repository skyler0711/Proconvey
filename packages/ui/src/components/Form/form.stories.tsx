import { ComponentStory, ComponentMeta } from '@storybook/react'
import Button from '../Button'
import Form from './index'

export default {
  title: 'Components/Forms/Form',
  component: Form,
} as ComponentMeta<typeof Form>

export const RegisterForm: ComponentStory<typeof Form> = () => {
  return (
    <Form>
      <Form.Group>
        <Form.Input label="First Name" />
        <Form.Input label="Last Name" />
      </Form.Group>
      <Form.Input label="Email" type="email" />
      <Form.Input label="Password" type="password" />
      <Button type="submit">Register Account</Button>
    </Form>
  )
}
